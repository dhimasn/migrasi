<?php

namespace App\Repo;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DeviceDb
{
    public function __construct()
    {
        
    }

    public function GetByIdUser($id_user,$is_active)
    {
        $q = sprintf("SELECT * FROM m_device WHERE is_active = %d AND id_user = %d", $is_active, $id_user);
        $result = DB::select($q);
        return $result;
    }         

    public function IsUserExists($id_user)
    {
        $q = sprintf("SELECT count(id) as jumlah from m_device where id_user = '%d'", $id_user);
        $result = DB::select($q)[0]->jumlah;
        $return = true;
        if($result == 0)
        {
            $return = false;
        }
        return $return;
    }

    public function Post($input)
    {
        try
        {               
            $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'm_device' ", env('DB_DATABASE'));
            $id = DB::select($q)[0]->AUTO_INCREMENT;   

            $q = sprintf("INSERT INTO m_device(id, id_user, fcm_token, is_active) VALUES ('%d','%d','%s','%d')", $id, $input->id_user, $input->fcm_token, $input->is_active);
            $result = DB::insert($q);            
            
            return $result;
        }
        catch(Exception $e)
        {
            return false;
        }
    }    
       
    public function Update($input)
    {
        try
        {            
            $q = sprintf("UPDATE m_device set  fcm_token = '%s', is_active = '%d' where id_user = '%d'", $input->fcm_token, $input->is_active,$input->id_user);
            
            $a = DB::update($q);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
}