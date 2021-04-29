<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repo\DeviceDb;
use App\Notifications\PushNotification;
use Exception;

class DeviceController extends Controller
{
    private $device_db;
    private $fcm;
    public function __construct()
    {
        $this->fcm = new PushNotification();
        $this->device_db = new DeviceDb();
    }

    public function Get(Request $re)
    {
        $result = $this->device_db->GetByIdUser($re->id_user, $re->is_active)[0];
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $result]);
    }

    public function PostData(Request $re)
    {  
        try{
            $a  = json_decode($re->getContent());
            $result = $this->device_db->IsUserExists($a->id_user);
            if($result)
            {
                $update = $this->device_db->Update($a);
            }
            else
            {
                $post = $this->device_db->Post($a);
            }
            
            return response()->json(['message' => "OK", 'code' => 200]);
        }
        catch(Exception $e)
        {
            return response()->json(['message' => "Bad Request", 'code' => 400, 'result' => $result]);
        }
        
    }
}
