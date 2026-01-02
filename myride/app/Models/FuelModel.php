<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Helper
use App\Helpers\Generator;

/**
 * @OA\Schema(
 *     schema="Fuel",
 *     type="object",
 *     required={"id","vehicle_id","fuel_volume","fuel_price_total","fuel_brand","fuel_type","fuel_ron","created_at","created_by","fuel_bill"},
 *
 *     @OA\Property(property="id", type="integer", description="Primary key of the fuel record"),
 *     @OA\Property(property="vehicle_id", type="integer", description="Reference ID of the vehicle"),
 *     @OA\Property(property="fuel_volume", type="number", format="float", description="Total fuel volume filled (in liters)"),
 *     @OA\Property(property="fuel_price_total", type="number", format="float", description="Total price paid for the fuel"),
 *     @OA\Property(property="fuel_brand", type="string", description="Brand of the fuel"),
 *     @OA\Property(property="fuel_type", type="string", description="Type of fuel"),
 *     @OA\Property(property="fuel_ron", type="integer", description="Research Octane Number (RON) of the fuel"),
 *     @OA\Property(property="fuel_bill", type="string", format="uri", description="URL or path to the uploaded fuel bill image"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the fuel record was created"),
 *     @OA\Property(property="created_by", type="string", format="uuid", description="ID of the user who created the fuel record"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true, description="Timestamp when the fuel record was last updated")
 * )
 */

class FuelModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'fuel';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'vehicle_id', 'fuel_volume', 'fuel_price_total', 'fuel_brand', 'fuel_type', 'fuel_ron', 'created_at', 'created_by', 'fuel_bill', 'updated_at'];

    public static function getAllFuel($user_id = null, $vehicle_id = null, $paginate){
        $res = FuelModel::select('fuel.id', 'vehicle_plate_number', 'vehicle_type', 'fuel_volume', 'fuel_price_total', 'fuel_brand', 'fuel_type', 'fuel_ron', 'fuel.created_at', 'fuel_bill')
            ->join('vehicle','vehicle.id','=','fuel.vehicle_id');

        if($user_id){
            $res = $res->where('fuel.created_by',$user_id);
        }
        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }

        return $res->orderby('fuel.created_at','desc')
            ->paginate($paginate);
    } 

    public static function getTotalFuelByVehiclePerYear($user_id = null, $vehicle_id = null, $context, $year){
        $res = FuelModel::selectRaw("SUM($context) as total, MONTH(created_at) as context");

        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }
        if($user_id){
            $res = $res->where('created_by',$user_id);
        }

        $res = $res->whereRaw("YEAR(created_at) = '$year'")
            ->groupByRaw('MONTH(created_at)')
            ->get();

        return $res;
    }

    public static function getTotalFuelSpendingPerMonth($user_id = null, $year, $is_admin){
        $res = FuelModel::selectRaw("SUM(fuel_price_total) as total, MONTH(created_at) as context");
        
        if($user_id){
            $res = $res->where('created_by', $user_id);
        }

        $res = $res->whereRaw("YEAR(created_at) = '$year'")
            ->groupByRaw('MONTH(created_at)')
            ->get();

        return $res;
    }

    public static function hardDeleteFuelById($id, $user_id = null){
        $res = FuelModel::where('id',$id);

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }
            
        return $res->delete();
    }

    public static function hardDeleteByVehicleId($vehicle_id, $user_id = null){
        $res = FuelModel::where('vehicle_id',$vehicle_id);

        if($user_id){
            $res = $res->where('created_by',$user_id);
        }
            
        return $res->delete();
    }

    public static function updateFuelById($data, $user_id, $id){
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return FuelModel::where('created_by',$user_id)->where('id',$id)->update($data);
    }

    public static function getLastFuel($user_id, $vehicle_id = null){
        $res = FuelModel::select('vehicle_plate_number', 'vehicle_type', 'fuel_volume', 'fuel_price_total', 'fuel_brand', 'fuel_type', 'fuel_ron', 'fuel.created_at')
            ->join('vehicle','vehicle.id','=','fuel.vehicle_id')
            ->where('fuel.created_by',$user_id);
        
        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }

        return $res->orderby('fuel.created_at','desc')
            ->first();
    }

    public static function createFuel($data, $user_id){
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $user_id;
        $data['id'] = Generator::getUUID();
            
        return FuelModel::create($data);
    }

    public static function getExportData($user_id, $vehicle_id = null){
        $res = FuelModel::select("vehicle_name","vehicle_plate_number", "vehicle_type", "fuel_volume", "fuel_price_total", "fuel_brand", "fuel_type", "fuel_ron", "fuel.created_at as datetime")
            ->join('vehicle','vehicle.id','=','fuel.vehicle_id');
        
        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }

        $res = $res->where('fuel.created_by',$user_id)
            ->orderBy('fuel.created_at', 'desc');

        return $res->get();
    }

    public static function getMonthlyFuelSummary($user_id = null, $vehicle_id = null, $month_year = "all"){
        $res = FuelModel::selectRaw("CAST(SUM(fuel_price_total) as INT) as total_fuel_price, CAST(SUM(fuel_volume) as INT) as total_fuel_volume, COUNT(1) as total_refueling");
        
        if($vehicle_id){
            $res = $res->where('vehicle_id',$vehicle_id);
        }
        if($user_id){
            $res = $res->where('created_by',$user_id);
        }

        if($month_year !== "all"){
            [$month, $year] = explode('-', $month_year);
            $res = $res->whereMonth('created_at', $month)->whereYear('created_at', $year);
        }

        return $res->first();
    }
}
