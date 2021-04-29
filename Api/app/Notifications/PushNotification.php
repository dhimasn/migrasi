<?php

namespace App\Notifications;

use App\Repo\DeviceDb;

class PushNotification
{
    private $devicedb;
    public function __construct()
    {        
        $this->devicedb = new DeviceDb();
    }

    public function sendPushNotification($id_user, $title, $message) {  
        $push_notification_key = "AAAA5IcmnVQ:APA91bGmQLwAbUNEq4FSlxj_Plxi4ag5RUuj3zEUeg4Z85jut-h_k_rb4LPj8kPZg_FTGy917S3RfBovvbasasFaPzgHX2fplcmQU3-oAJOrbfBB57j-dz_YMIOAB0G4X5rbnPqedwNN";    
        $sender_id = "981519998292";

        $url = "https://fcm.googleapis.com/fcm/send";            
        $header = array("Authorization: key=" . $push_notification_key . "",
            "content-type: application/json"
        );    
        
        $result = $this->devicedb->GetByIdUser($id_user, 1);
        if(empty($result)){
            return false;
        }

        $postdata = '{
            "to" : "' . $result[0]->fcm_token . '",
            "priority" : 10,
            "content_available" : true,
            "notification" : {
                "title":"' . $title . '",
                "body" : "' . $message . '",
                "text" : "' . $message . '"
            },
            "data" : {
                "id" : "'.$sender_id.'",
                "title":"' . $title . '",
                "description" : "' . $message . '",
                "text" : "' . $message . '",
                "is_read": 0
              }
        }';

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $result = curl_exec($ch);    
        curl_close($ch);

        return $result;
    }
}
