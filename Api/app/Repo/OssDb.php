<?php

namespace App\Repo;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OssDb
{
    public function __construct()
    {

    }

    public function GetCredentialOSS($id_akun_oss)
    {
        $q = sprintf("SELECT * FROM m_akun_oss where id=%d", $id_akun_oss) ;
        $result = DB::select($q);
        return $result;
    } 

    public function PostDataNIB($input)
    {
        try
        {                        
            $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'm_data_nib' ", env('DB_DATABASE'));
            $id = DB::select($q)[0]->AUTO_INCREMENT;   

            $tanggal_input  = Carbon::now();
            $q = sprintf("INSERT INTO m_data_nib(id, nib, data_nib, versi_pia, kunci_header, status_auth, tanggal_input) VALUES (%d, '%s', '%s', '%s', '%s', %d, '%s')", $id, $input->nib, $input->data_nib, $input->versi_pia, $input->kunci_header, $input->status_auth, $tanggal_input->format('Y-m-d H:i:s'));

            $a = DB::insert($q);

            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    
}