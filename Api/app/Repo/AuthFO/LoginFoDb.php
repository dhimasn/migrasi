<?php

namespace App\Repo\AuthFO;

use Exception;
use Illuminate\Support\Facades\DB;

class LoginFoDb
{
    public function __construct()
    {
        
    }

    public function CheckLoginFo($input)
    {
        $resultGet = $this->GetUserByUidNumber($input->uid_number, $input->employee_number);
        
        if(empty($resultGet)){
            $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'm_user_fo'", env('DB_DATABASE'));
            $id_user = DB::select($q)[0]->AUTO_INCREMENT;
            $resultInsert = $this->InsertUserFo($input);

            if($resultInsert){
                return $id_user;
            }else{
                return 0;
            }            
        }else{            
            $id_user = $resultGet[0]->id;
            return $id_user;
        }
    }

    public function GetUserByUidNumber($uidnumber, $employee_number)
    {
        $q = "SELECT * FROM m_user_fo WHERE uid_number = '$uidnumber' AND employee_number = '$employee_number'";
        $result = DB::select($q);
        return $result;
    }

    public function InsertUserFo($input)
    {
        try
        {
            $q = sprintf("INSERT INTO m_user_fo(uid_number, employee_number, nm_user, email_user, tanggal_input, tanggal_update) VALUES ('%s','%s','%s','%s','%s','%s')", $input->uid_number, $input->employee_number, $input->nm_user, $input->email_user, $input->tanggal_input, $input->tanggal_update);
            $a = DB::insert($q);
            return $a;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
}