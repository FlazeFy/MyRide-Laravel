<?php

namespace App\Http\Controllers\Api\ExportApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

// Exports
use App\Exports\CleanExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
// Telegram
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\FileUpload\InputFile;
// Models
use App\Models\CleanModel;
use App\Models\UserModel;
// Helpers
use App\Helpers\Generator;

class Queries extends Controller {
    public function exportCleanHistory(Request $request){
        try {
            $user_id = $request->user()->id;
            $datetime = date('Y-m-d_H-i-s');
            $user = UserModel::getSocial($user_id);
            $file_name = "Clean-$user->username-$datetime.xlsx";

            $res_clean_history = CleanModel::getExportData($user_id,null)->map(function($dt) {
                return [
                    'vehicle_name' => $dt->vehicle_name,
                    'clean_desc' => $dt->clean_desc,
                    'clean_by' => $dt->clean_by,
                    'clean_tools' => $dt->clean_tools,
                    'is_clean_body' => $dt->is_clean_body == 1 ? "Yes" : "No",
                    'is_clean_window' => $dt->is_clean_window == 1 ? "Yes" : "No",
                    'is_clean_dashboard' => $dt->is_clean_dashboard == 1 ? "Yes" : "No",
                    'is_clean_tires' => $dt->is_clean_tires == 1 ? "Yes" : "No",
                    'is_clean_trash' => $dt->is_clean_trash == 1 ? "Yes" : "No",
                    'is_clean_engine' => $dt->is_clean_engine == 1 ? "Yes" : "No",
                    'is_clean_seat' => $dt->is_clean_seat == 1 ? "Yes" : "No",
                    'is_clean_carpet' => $dt->is_clean_carpet == 1 ? "Yes" : "No",
                    'is_clean_pillows' => $dt->is_clean_pillows == 1 ? "Yes" : "No",
                    'clean_address' => $dt->clean_address,
                    'clean_start_time' => $dt->clean_start_time,
                    'clean_end_time' => $dt->clean_end_time,
                    'is_fill_window_cleaning_water' => $dt->is_fill_window_cleaning_water == 1 ? "Yes" : "No",
                    'is_clean_hollow' => $dt->is_clean_hollow == 1 ? "Yes" : "No",
                    'datetime' => $dt->datetime,
                ];
            });

            Excel::store(new class($res_clean_history) implements WithMultipleSheets {
                private $res_clean_history;

                public function __construct($res_clean_history)
                {
                    $this->res_clean_history = $res_clean_history;
                }

                public function sheets(): array
                {
                    return [
                        new CleanExport($this->res_clean_history),
                    ];
                }
            }, $file_name, 'public');
        
            $storagePath = storage_path("app/public/$file_name");
            $publicPath = public_path($file_name);
            if (!file_exists($storagePath)) {
                throw new \Exception("File not found: $storagePath");
            }
            copy($storagePath, $publicPath);

            if ($user && $user->telegram_is_valid == 1 && $user->telegram_user_id) {
                $inputFile = InputFile::create($publicPath, $file_name);

                Telegram::sendDocument([
                    'chat_id' => $user->telegram_user_id,
                    'document' => $inputFile,
                    'caption' => "Your clean export is ready",
                    'parse_mode' => 'HTML',
                ]);
            }

            return response()->download($storagePath, $file_name, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
            ])->deleteFileAfterSend(true);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => Generator::getMessageTemplate("unknown_error", null),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}