<?php

namespace App\Schedule;

use Carbon\Carbon;
use DateTime;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Factory;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\FileUpload\InputFile;
use Dompdf\Dompdf;
use Dompdf\Options;
use Dompdf\Canvas\Factory as CanvasFactory;
use Dompdf\Options as DompdfOptions;
use Dompdf\Adapter\CPDF;
use Amenadiel\JpGraph\Graph\Graph;
use Amenadiel\JpGraph\Plot\BarPlot;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

// Helper
use App\Helpers\Generator;
use App\Helpers\TelegramMessage;

// Model
use App\Models\ErrorModel;
use App\Models\AdminModel;
use App\Models\UserModel;
use App\Models\VehicleModel;
use App\Models\FuelModel;
use App\Models\WashModel;
use App\Models\MultiModel;

class AuditSchedule
{
    public static function audit_error()
    {
        $summary = ErrorModel::getAllErrorAudit();
        
        if($summary){
            $audit = "";
            $total = count($summary);

            foreach($summary as $dt){
                $audit .= "
                    <tr>
                        <td>$dt->message</td>
                        <td style='text-align:center;'>$dt->created_at</td>
                        <td style='text-align:center;'>";
                        if($dt->faced_by){
                            $audit .= $dt->faced_by;
                        } else {
                            $audit .= "-";
                        }
                        $audit.= "</td>
                        <td style='text-align:center;'>$dt->total</td>
                    </tr>
                ";
            }
            
            $admin = AdminModel::getAllContact();

            if($admin){
                $datetime = date("Y-m-d H:i:s");    
                $options = new DompdfOptions();
                $options->set('defaultFont', 'Helvetica');
                $dompdf = new Dompdf($options);
                $header_template = Generator::generateDocTemplate('header');
                $style_template = Generator::generateDocTemplate('style');
                $footer_template = Generator::generateDocTemplate('footer');
        
                $html = "
                <html>
                    <head>
                        $style_template
                    </head>
                    <body>
                        $header_template
                        <h2>Audit - Error</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>Error Message</th>
                                    <th>Datetime</th>
                                    <th>Faced By</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>$audit</tbody>
                        </table>
                        $footer_template
                    </body>
                </html>";
        
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();
        
                $pdfContent = $dompdf->output();
                $pdfFilePath = public_path("audit_error_$datetime.pdf");
                file_put_contents($pdfFilePath, $pdfContent);
                $inputFile = InputFile::create($pdfFilePath, $pdfFilePath);

                foreach($admin as $dt){
                    $message = "[ADMIN] Hello $dt->username, the system just run an audit error, with result of $total error found. Here's the document";
                    
                    if($dt->telegram_user_id && $dt->telegram_is_valid == 1){
                        if(TelegramMessage::checkTelegramID($dt->telegram_user_id)){
                            $response = Telegram::sendDocument([
                                'chat_id' => $dt->telegram_user_id,
                                'document' => $inputFile,
                                'caption' => $message,
                                'parse_mode' => 'HTML'
                            ]);
                        } else {
                            AdminModel::updateAdminById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$dt->id);
                        }
                    }
                }
        
                unlink($pdfFilePath);
            }
        }
    }

    public static function audit_weekly_stats() {
        $listCols = ["vehicle_fuel_status","vehicle_category","vehicle_status","vehicle_transmission"];
        $users = UserModel::getUserBroadcastAll();
    
        foreach ($users as $us) {
            $chartFiles = []; 
    
            foreach ($listCols as $col) {
                // Model
                $res = VehicleModel::getContextTotalStats($col, $us->id);
    
                if ($res == null || $res->isEmpty()) continue;
    
                // Dataset
                $labels = $res->pluck('context')->map(fn($c) => Str::upper(str_replace('_', ' ', $c)))->all();
                $values = $res->pluck('total')->all();
    
                // Filename
                $chartFilename = "bar_chart-$col-$us->id.png";
                $chartPath = storage_path("app/public/$chartFilename");

                // Generate chart
                $graph = new Graph(800, 500);
                $graph->SetScale("textlin");
                $graph->xaxis->SetTickLabels($labels);
                $graph->xaxis->SetLabelAngle(35);
                $graph->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 7);
                $graph->yaxis->SetFont(FF_ARIAL, FS_NORMAL, 7);
                $graph->title->SetFont(FF_ARIAL, FS_BOLD, 10);
                $barPlot = new BarPlot($values);
                $barPlot->SetFillColor("navy");
                $graph->Add($barPlot);
                $graph->title->Set("Total Vehicle By ".Str::headline($col));
                $graph->Stroke($chartPath);

                $chartFiles[] = $chartFilename;
            }
    
            if (empty($chartFiles)) continue;
    
            // Render PDF
            $generatedDate = now()->format('d F Y');
            $datetime = now()->format('d M Y h:i');
            $tmpPdfPath = storage_path("app/public/Weekly Vehicle Audit - ".$us->username.".pdf");

            Pdf::loadView('components.pdf.vehicle_chart', [
                'charts' => $chartFiles,
                'date' => $generatedDate,
                'datetime' => $datetime,
                'username' => $us->username
            ])->save($tmpPdfPath);

            // Send Telegram
            if ($us->telegram_user_id) {
                if(TelegramMessage::checkTelegramID($us->telegram_user_id)){
                    $message = "[ADMIN] Hello {$us->username}, here is your weekly vehicle audit report.";

                    Telegram::sendDocument([
                        'chat_id' => $us->telegram_user_id,
                        'document' => fopen($tmpPdfPath, 'rb'),
                        'caption' => $message,
                        'parse_mode' => 'HTML'
                    ]);
                } else {
                    UserModel::updateUserById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$us->id);
                }
            }

            // Wash up File
            foreach ($chartFiles as $file) {
                $chartPath = storage_path("app/public/$file");
                if (file_exists($chartPath)) {
                    unlink($chartPath);
                }
            }

            if (file_exists($tmpPdfPath)) {
                unlink($tmpPdfPath);
            }
        }
    }

    private static function generateBarChart($label, $value, $context, $year, $user_id){
        // Filename
        $chartFilename = "bar_chart_".$context."_monthly_$year-$user_id.png";
        $chartPath = storage_path("app/public/$chartFilename");

        // Generate chart
        $graph = new Graph(800, 500);
        $graph->SetScale("textlin");
        $graph->xaxis->SetTickLabels($label);
        $graph->xaxis->SetLabelAngle(35);
        $graph->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 7);
        $graph->yaxis->SetFont(FF_ARIAL, FS_NORMAL, 7);
        $graph->title->SetFont(FF_ARIAL, FS_BOLD, 10);
        $barPlot = new BarPlot($value);
        $barPlot->SetFillColor("navy");
        $graph->Add($barPlot);
        $graph->title->Set("Total $context Spending Per Month ($year)");
        $graph->Stroke($chartPath);

        return $chartFilename;
    }

    public static function audit_yearly_stats() {
        $users = UserModel::getUserBroadcastAll();
        $year = 2025;
    
        foreach ($users as $us) {
            $chartFiles = []; 

            // Total Fuel Spending Per Month
            // Model
            $res_fuel_monthly = FuelModel::getTotalFuelSpendingPerMonth($us->id, $year, false);

            if ($res_fuel_monthly == null || $res_fuel_monthly->isEmpty()) continue;
            $res_final_fuel_monthly = [];
            for ($i=1; $i <= 12; $i++) { 
                $total = 0;
                foreach ($res_fuel_monthly as $idx => $val) {
                    if($i == $val->context){
                        $total = $val->total;
                        break;
                    }
                }
                array_push($res_final_fuel_monthly, [
                    'context' => Generator::generateMonthName($i,'short'),
                    'total' => $total,
                ]);
            }

            // Dataset
            $labels_fuel_monthly = collect($res_final_fuel_monthly)->pluck('context')->map(fn($c) => Str::upper(str_replace('_', ' ', $c)))->all();
            $values_fuel_monthly = collect($res_final_fuel_monthly)->pluck('total')->all();
            $fuelChartFilename = self::generateBarChart($labels_fuel_monthly, $values_fuel_monthly, "fuel", $year, $us->id);
    
            $chartFiles[] = $fuelChartFilename;
            if (empty($chartFiles)) continue;

            // Total Wash Spending Per Month
            // Model
            $res_wash_monthly = WashModel::getTotalWashSpendingPerMonth($us->id, $year, false);

            if ($res_wash_monthly == null || $res_wash_monthly->isEmpty()) continue;
            $res_final_wash_monthly = [];
            for ($i=1; $i <= 12; $i++) { 
                $total = 0;
                foreach ($res_wash_monthly as $idx => $val) {
                    if($i == $val->context){
                        $total = $val->total;
                        break;
                    }
                }
                array_push($res_final_wash_monthly, [
                    'context' => Generator::generateMonthName($i,'short'),
                    'total' => $total,
                ]);
            }

            // Dataset
            $labels_wash_monthly = collect($res_final_wash_monthly)->pluck('context')->map(fn($c) => Str::upper(str_replace('_', ' ', $c)))->all();
            $values_wash_monthly = collect($res_final_wash_monthly)->pluck('total')->all();
            $washChartFilename = self::generateBarChart($labels_wash_monthly, $values_wash_monthly, "wash", $year, $us->id);
    
            $chartFiles[] = $washChartFilename;
            if (empty($chartFiles)) continue;
    
            // Render PDF
            $generatedDate = now()->format('d F Y');
            $datetime = now()->format('d M Y h:i');
            $tmpPdfPath = storage_path("app/public/Yearly Fuel & Wash Audit - ".$us->username.".pdf");

            Pdf::loadView('components.pdf.vehicle_chart', [
                'charts' => $chartFiles,
                'date' => $generatedDate,
                'datetime' => $datetime,
                'username' => $us->username
            ])->save($tmpPdfPath);

            // Send Telegram
            if ($us->telegram_user_id) {
                if(TelegramMessage::checkTelegramID($us->telegram_user_id)){
                    $message = "[ADMIN] Hello {$us->username}, here is your yearly fuel audit report.";

                    Telegram::sendDocument([
                        'chat_id' => $us->telegram_user_id,
                        'document' => fopen($tmpPdfPath, 'rb'),
                        'caption' => $message,
                        'parse_mode' => 'HTML'
                    ]);
                } else {
                    UserModel::updateUserById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$us->id);
                }
            }

            // Wash up File
            foreach ($chartFiles as $file) {
                $chartPath = storage_path("app/public/$file");
                if (file_exists($chartPath)) {
                    unlink($chartPath);
                }
            }

            if (file_exists($tmpPdfPath)) {
                unlink($tmpPdfPath);
            }
        }
    }

    public static function audit_apps()
    {
        $days = 7;
        $summary = AdminModel::getAppsSummaryForLastNDays($days);

        if($summary){
            $admin = AdminModel::getAllContact();

            foreach($admin as $dt){
                if($dt->telegram_user_id && $dt->telegram_is_valid == 1){
                    if(TelegramMessage::checkTelegramID($dt->telegram_user_id)){    
                        $message_template = "[ADMIN] Hello $dt->username, here's the apps summary for the last $days days:";
                        $message = "$message_template\n\n- Vehicle Created: $summary->vehicle_created\n- Inventory Created: $summary->inventory_created\n- New User : $summary->new_user\n- Trip Created : $summary->trip_created\n- Fuel Created : $summary->fuel_created\n- Service Created : $summary->service_created\n- Wash Created : $summary->wash_created\n- Error Happen : $summary->error_happen";

                        $response = Telegram::sendMessage([
                            'chat_id' => $dt->telegram_user_id,
                            'text' => $message,
                            'parse_mode' => 'HTML'
                        ]);
                    } else {
                        AdminModel::updateAdminById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$dt->id);
                    }
                }
            }
        }
    }

    public static function audit_dashboard(){
        $users = UserModel::getUserBroadcastAll();

        if($users && count($users) > 0){
            foreach($users as $index => $dt){
                $total_vehicle = MultiModel::countTotalContext('vehicle',$dt->id);
                $total_wash = MultiModel::countTotalContext('wash',$dt->id);
                $total_driver = MultiModel::countTotalContext('driver',$dt->id);
                $total_service = MultiModel::countTotalContext('service',$dt->id);
                $total_trip = MultiModel::countTotalContext('trip',$dt->id);
                
                if($dt->telegram_user_id && $dt->telegram_is_valid == 1){
                    if(TelegramMessage::checkTelegramID($dt->telegram_user_id)){    
                        $message_template = "Hello $dt->username, here's the weekly dashboard we've gathered so far from your account :";
                        $message = "$message_template\n\n- Total Vehicle : $total_vehicle\n- Total Wash : $total_wash\n- Total Driver : $total_driver\n- Total Service : $total_service\n- Total Trip : $total_trip";        

                        $response = Telegram::sendMessage([
                            'chat_id' => $dt->telegram_user_id,
                            'text' => $message,
                            'parse_mode' => 'HTML'
                        ]);
                    } else {
                        UserModel::updateUserById([ 'telegram_user_id' => null, 'telegram_is_valid' => 0],$dt->id);
                    }
                }
            }
        }
    }
}
