<?php

namespace App\Repo;
use Exception;
use Illuminate\Support\Facades\DB;

class IotentikDb
{
    public function __construct()
    {
    }
  
    public function GetLoginIotentik($id)
    {
        $q = "SELECT * FROM m_akun_iotentik WHERE id={$id}";
        $result = DB::select($q);
        return $result;
    }

    public function UpdateTokenIotentik($input)
    {
        try
        {            	
            $q = sprintf("UPDATE m_akun_iotentik set token = %s, timestamp = %s where id = 1",$input->token, $input->timestamp->format('Y-m-d H:i:s'));
            $a = DB::update($q);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
  
    public function GetUserIotentikByUser($id)
    {
        $q = "SELECT username, password, id_cert FROM m_user_iot WHERE id_user={$id}";
        $result = DB::select($q);
        return $result;
    }
}