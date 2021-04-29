<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repo\UserDb;
use App\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    private $user_db;
    private $device_db;

    public function __construct()
    {
        $this->user_db = new UserDb();
    }

    public function LoginProcess(Request $request)
    {  
        $a  = json_decode($request->getContent());
        $result = $this->user_db->Login($a->user_name, $a->password);
        if($result->status)
        {
            $user = User::where("user_name", $a->user_name)->first();
            $abilities = $request->input('abilities', [
                'order:create',
                'order:view',
                'WLR3:check_availability'
            ]);
            $token = $user->createToken("token", $abilities)->plainTextToken;
            return response()->json(['message' => "OK", 'code' => 200, 'token' => $token, 'result' => $result]);
        }
        else
        {
            return response()->json(['message' => "Unauthorized", 'code' => 401, 'result' => "Check user name or password"]);
        }        
    }
}
