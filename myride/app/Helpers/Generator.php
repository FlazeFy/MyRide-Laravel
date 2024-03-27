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
}