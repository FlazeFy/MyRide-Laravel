<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

use App\Models\UserModel;

class Generator
{
    public static function getUserId($role){
        $token = session()->get("token_key");
        $accessToken = PersonalAccessToken::findToken($token);

        if ($accessToken) {
            if($accessToken->tokenable){
                Auth::login($accessToken->tokenable);
                $user = Auth::user();
                
                $res = $user->id;
                return $res;
            } else {
                return redirect("/")->with('failed_message','This account is no longer exist');
            }
        } else {
            return null;
        }
    }

    public static function getUUID(){
        $result = '';
        $bytes = random_bytes(16);
        $hex = bin2hex($bytes);
        $time_low = substr($hex, 0, 8);
        $time_mid = substr($hex, 8, 4);
        $time_hi_and_version = substr($hex, 12, 4);
        $clock_seq_hi_and_reserved = hexdec(substr($hex, 16, 2)) & 0x3f;
        $clock_seq_low = hexdec(substr($hex, 18, 2));
        $node = substr($hex, 20, 12);
        $uuid = sprintf('%s-%s-%s-%02x%02x-%s', $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $clock_seq_low, $node);
        
        return $uuid;
    }

    public static function getTokenValidation($len){
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $res = '';
        
        $charCount = strlen($characters);
        for ($i = 0; $i < $len; $i++) {
            $res .= $characters[rand(0, $charCount - 1)];
        }
        
        return $res;
    }

    public static function getRandomDate($null){
        if($null == 0){
            $start = strtotime('2023-01-01 00:00:00');
            $end = strtotime(date("Y-m-d H:i:s"));
            $random = mt_rand($start, $end); 
            $res = date('Y-m-d H:i:s', $random);
        } else {
            $res = null;
        }

        return $res;
    }

    public static function generateMonthName($idx,$type){
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    
        if($type == 'short'){
            return substr($months[$idx-1], 0, 3);
        } else if($type == 'full'){
            return $months[$idx-1];
        }
    }

    public static function getMessageTemplate($type, $ctx){
        if (in_array($type, ['create', 'update', 'delete', 'permentally delete', 'fetch','recover','analyze','generate'])) {
            $ext = in_array($type, ['fetch','recover']) ? "ed" : "d";
            $res = "$ctx ".$type.$ext;              
        } else if($type == "not_found"){
            $res = "$ctx not found";
        } else if($type == "unknown_error"){
            $res = "something wrong. please contact admin";
        } else if($type == "conflict"){
            $res = "$ctx has been used. try another";
        } else if($type == "custom"){
            $res = "$ctx";
        } else if($type == "validation_failed"){
            $res = "validation failed : $ctx";
        } else if($type == "permission"){
            $res = "permission denied. only $ctx can use this feature";
        } else {
            $res = "failed to get respond message";
        }

        return $res;
    }

    public static function generateDocTemplate($type){
        $datetime = now();

        if($type == "footer"){
            return "
                <br><hr>
                <div>
                    <h6 class='date-text' style='margin: 0;'>Parts of FlazenApps</h6>
                    <h6 class='date-text' style='margin: 0; float:right; margin-top:-12px;'>Generated at $datetime by <span style='color:#3b82f6;'>https://myride.leonardhors.com</span></h6>
                </div>
            ";
        } else if($type == "header"){
            return "
                <div style='text-align:center;'>
                    <h1 style='color:#3b82f6; margin:0;'>MyRide</h1>
                    <h4 style='color:#212121; margin:0; font-style:italic;'>Management Apps for your vehicle</div>
                <hr>
            ";
        } else if($type == "style"){
            return "
                <style>
                    body { font-family: Helvetica; }
                    table { border-collapse: collapse; font-size:10px; width:100%; }
                    td, th { border: 1px solid #dddddd; text-align: left; padding: 8px; }
                    th { text-align:center; }
                    .date-text { font-style:italic; font-weight:normal; color:grey; font-size:11px; }
                    thead { background-color:rgba(59, 131, 246, 0.75); }
                    .text-secondary { font-style:italic; font-weight:normal; color:grey; }
                </style>
            ";
        }
    }
    public static function getPlateNumber() {
        // First Word: 1–2 letters (A–Z)
        $first = '';
        $firstLength = rand(1, 2);
        for ($i = 0; $i < $firstLength; $i++) {
            $first .= chr(rand(65, 90)); 
        }
    
        // Second Word: 2–4 digits (first digit 1–9)
        $secondLength = rand(2, 4);
        $second = strval(rand(1, 9)); 
        for ($i = 1; $i < $secondLength; $i++) {
            $second .= strval(rand(0, 9));
        }
    
        // Last Word: 2–3 letters (A–Z)
        $last = '';
        $lastLength = rand(2, 3);
        for ($i = 0; $i < $lastLength; $i++) {
            $last .= chr(rand(65, 90)); 
        }
    
        return "$first $second $last";
    }
    
}