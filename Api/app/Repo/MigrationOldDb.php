<?php

namespace App\Repo;
use Exception;
use Illuminate\Support\Facades\DB;

class MigrationOldDb
{
    public function __construct()
    {

    }

    //public function GetOldNIB($awal, $akhir)
    public function GetOldNIB()
    {
        $q = sprintf("SELECT id_data_nib, id_jenis_izin, data, test FROM t_data_nib WHERE id_data_nib BETWEEN 61309 AND 62073 ORDER BY id_data_nib ASC");
        //$q = sprintf("SELECT id_data_nib, id_jenis_izin, data, test FROM t_data_nib LIMIT 1, 10000");//,$awal, $akhir
        $result = DB::select($q);
        return $result;
    }

    public function GetOldUser()
    {
        $q = sprintf("SELECT * FROM t_user_toing");//LIMIT %d, %d", $awal, $akhir");
        $result = DB::select($q);
        return $result;
    }

    public function GetOldPemohon($id)
    {
        $q = sprintf("SELECT * FROM t_pemohon WHERE no_kk = '%s'", $id);
        $result = DB::select($q);
        return $result;
    }
    
    public function GetOldUidNumber($id)
    {
        $q = sprintf("SELECT DISTINCT(uidnumber),id_user_fe FROM (SELECT a.*,b.uidnumber FROM t_pemohon a LEFT JOIN m_user_fe b ON a.id_user_fe = b.id_user_fe) AS tp WHERE tp.no_kk = '%s'", $id);
        //$q = sprintf("SELECT a.*, b.uidnumber FROM t_pemohon a LEFT JOIN m_user_fe b ON a.id_user_fe = b.id_user_fe"); //,$id);
        $result = DB::select($q);
        return $result;
    }    

    public function PostUserFO($input)
    {
        $q ="SELECT * FROM m_user_fo WHERE uid_number = '$input->uid_number' AND employee_number = '$input->employee_number'";
        $resultGet = DB::select($q);
        
        if(empty($resultGet)){  
            $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'm_user_fo' ", env('DB_DATABASE'));
            $id = DB::select($q)[0]->AUTO_INCREMENT;   
            $q = sprintf("INSERT INTO m_user_fo(id, uid_number, employee_number, nm_user, email_user, tanggal_input, tanggal_update) VALUES (%d, '%s','%s','%s','%s','%s','%s')", $id, $input->uid_number, $input->employee_number, $input->nm_user, $input->email_user, $input->tanggal_input, $input->tanggal_update);
            $resultInsert = DB::insert($q);
            if($resultInsert){
                return $id;
            }else{
                return 0;
            }                       
        }else{            
            $id_user = $resultGet[0]->id;
            return $id_user;
        }
    }   

    public function PostDataPemohon($input)
    {
        try
        {
            $qs = sprintf("SELECT COUNT(nik) as jumlah FROM m_pemohon WHERE nik = '%s'", $input->nik);
            $result = DB::select($qs)[0]->jumlah;
            if($result == 0){
                $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'm_pemohon' ", env('DB_DATABASE'));
                $id_pemohon = DB::select($q)[0]->AUTO_INCREMENT;   

                $qss = sprintf("INSERT INTO m_pemohon(id, id_user_proses, nama, nik, email, telp, id_user_fo) VALUES (%d, '%s','%s','%s','%s','%s', %d)", $id_pemohon, $input->id_user_proses, $input->nama, $input->nik, $input->email, $input->telp, $input->id_user_fo);
                $a = DB::insert($qss); 
            }else{                
                $qsd = sprintf("SELECT id FROM m_pemohon WHERE nik = '%s'", $input->nik);
                $id_pemohon = DB::select($qsd)[0]->id;
            }                 
            
            return $id_pemohon;
        }
        catch(Exception $e)
        {
            return false;
        }
    }      

    public function PostDataPerusahaanJenis($input)
    {
        try
        {                
            $qs = sprintf("SELECT COUNT(id) as jumlah FROM m_perusahaan_izin_jenis WHERE id_perusahaan = %d AND id_izin = '%s'", $input->id_perusahaan, $input->id_izin);
            $result = DB::select($qs)[0]->jumlah;
            if($result == 0){
                $q = sprintf("INSERT INTO m_perusahaan_izin_jenis(id, id_perusahaan, id_izin_jenis, id_izin, kode_izin, id_nib_status) VALUES (%d, %d, %d, '%s', '%s', %d)", $input->id, $input->id_perusahaan, $input->id_izin_jenis, $input->id_izin, $input->kode_izin, $input->id_nib_status);
                $a = DB::insert($q);
            }

            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function PostUserBO($input)
    {
        try
        {                
            $qs = sprintf("SELECT COUNT(id) as jumlah FROM m_user WHERE id = %d", $input->id);
            $result = DB::select($qs)[0]->jumlah;
            if($result == 0){
                $q = sprintf("INSERT INTO m_user(id, id_jabatan, email, nama, sandi, user_name, id_user_status, tanggal_input, tanggal_update, tanggal_update_pass, id_user_role) VALUES (%d, %d, '%s', '%s','%s','%s', %d,'%s','%s','%s',%d)", $input->id, $input->id_jabatan, $input->email, $input->nama, $input->sandi, $input->user_name, $input->id_user_status, $input->tanggal_input, $input->tanggal_update, $input->tanggal_update_pass, $input->id_user_role);
                $a = DB::insert($q);
            }

            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function PostJabatan($input)
    {
        try
        {                
            $qs = sprintf("SELECT COUNT(id) as jumlah FROM m_jabatan WHERE id = %d", $input->id);
            $result = DB::select($qs)[0]->jumlah;
            if($result == 0){
                $q = sprintf("INSERT INTO m_jabatan(id, id_parent, id_unit_teknis, level, nama_jabatan) VALUES (%d, %d, %d, %d,'%s')", $input->id, $input->id_parent, $input->id_unit_teknis, $input->level, $input->nama_jabatan);
                $a = DB::insert($q);
            }

            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
}