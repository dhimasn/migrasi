<?php

namespace App\Repo;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PerusahaanDb
{
    public function __construct()
    {
        
    }

    public function GetByIdPemohon($id_pemohon)
    {
        $q = sprintf("SELECT per.nib, pm.penanaman_modal, pj.jenis, per.id as id_perusahaan, per.nama as nama_perusahaan, per.telp as telp_perusahaan, per.npwp, uf.nm_user as nama, pem.nik, uf.email_user as email, pem.telp, per.alamat from m_perusahaan per left join m_pemohon pem on per.id_pemohon = pem.id left join k_perusahaan_jenis pj on per.id_perusahaan_jenis = pj.id left join k_perusahaan_status ps on per.id_perusahaan_status = ps.id left join k_penanaman_modal pm on per.id_penanaman_modal = pm.id left join m_user_fo uf on pem.id_user_fo = uf.id where per.id_pemohon = %d", $id_pemohon);
        $result = DB::select($q);
        return $result;
    }

    public function GetPemohonByIdUserFo($id_user_fo)
    {
        $q = sprintf("SELECT mp.*, per.nama as nama_perusahaan, per.id as id_perusahaan from m_pemohon mp left join m_perusahaan per on mp.id = per.id_pemohon where mp.id_user_fo = %d", $id_user_fo);
        $result = DB::select($q);
        return $result;
    }   

    public function PostDataPemohon($input)
    {
        try
        {               
            $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'm_pemohon' ", env('DB_DATABASE'));
            $id_pemohon = DB::select($q)[0]->AUTO_INCREMENT;   

            $q = sprintf("INSERT INTO m_pemohon(id, id_user_proses, nama, nik, email, telp, id_user_fo) VALUES (%d, '%s','%s','%s','%s','%s', %d)", $id_pemohon, $input->id_user_proses, $input->nama, $input->nik, $input->email, $input->telp, $input->id_user_fo);
            $a = DB::insert($q);            
            
            return $id_pemohon;
        }
        catch(Exception $e)
        {
            return false;
        }
    }    
       
    public function UpdateDataPemohon($input)
    {
        try
        {            
            $q = sprintf("UPDATE m_pemohon set id_user_proses = '%s', nama = '%s', nik = '%s', email = '%s', telp = '%s', id_user_fo = %d where id = %d",$input->id_user_proses, $input->nama, $input->id_perusahaan_jenis, $input->nik, $input->email, $input->telp, $input->id_user_fo, $input->id);
            
            $a = DB::update($q);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function PostDataPerusahaan($input)
    {
        try
        {            
            $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'm_perusahaan' ", env('DB_DATABASE'));
            $id = DB::select($q)[0]->AUTO_INCREMENT;   

            $tanggal_input  = Carbon::now();
            $q = sprintf("INSERT INTO m_perusahaan(id, id_penanaman_modal, id_pemohon, id_perusahaan_jenis, id_perusahaan_status, nib, oss_id, nama, npwp, email, telp, alamat, rt_rw, kelurahan, id_wilayah, modal_dasar, total_pma, nilai_pma_dominan, nilai_pmdn, persen_pma, persen_pmdn, data_saham, last_update, tgl_perubahan_nib) VALUES (%d, '%s', %d, '%s', %d, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, %d, %d, %d, %d, %d, '%s', '%s', '%s')", $id, $input->id_penanaman_modal, $input->id_pemohon, $input->id_perusahaan_jenis, $input->id_perusahaan_status, $input->nib, $input->oss_id, $input->nama, $input->npwp, $input->email, $input->telp, $input->alamat, $input->rt_rw, $input->kelurahan, $input->id_wilayah, $input->modal_dasar, $input->total_pma, $input->nilai_pma_dominan, $input->nilai_pmdn, $input->persen_pma, $input->persen_pmdn, $input->data_saham, $tanggal_input->format('Y-m-d H:i:s'), $input->tgl_perubahan_nib);

            $a = DB::insert($q);

            return $id;
        }
        catch(Exception $e)
        {
            return false;
        }
    }    

    public function IsExistNibPerusahaan($nib)
    {
        $q = sprintf("SELECT count(nib) as jumlah from m_perusahaan where nib = '%s'", $nib);
        $result = DB::select($q)[0]->jumlah;
        $return = true;
        if($result == 0)
        {
            $return = false;
        }
        return $return;
    }    

    public function IsLastNibPerusahaan($nib, $tgl_ubah)
    {
        $q = sprintf("SELECT tgl_perubahan_nib from m_perusahaan where nib = '%s'", $nib);
        $result = DB::select($q);
        if(empty($result)){
            $return = 0;
        }else{
            $return = 1;
        }
        return $return;
    }

    public function UpdatePerusahaan($input)
    {
        try
        {            
            $p = sprintf("SELECT id from m_perusahaan where nib = '%s'", $input->nib);
            $result = DB::select($p);
            
            if(empty($result)){
                $id = $this->PostDataPerusahaan($input);
                return $id;
            }else{ 
                $id_perusahaan = $result[0]->id;

                $tanggal_input  = Carbon::now();
                $q = sprintf("UPDATE m_perusahaan set id_penanaman_modal = '%s', id_pemohon = %d, id_perusahaan_jenis = '%s', id_perusahaan_status = %d, nib = '%s', oss_id = '%s', nama = '%s', npwp = '%s', email = '%s', telp = '%s', alamat = '%s', rt_rw = '%s', kelurahan = '%s', id_wilayah = '%s', modal_dasar = %d, total_pma = %d, nilai_pma_dominan = %d, nilai_pmdn = %d, persen_pma = %d, persen_pmdn = %d, data_saham = '%s', last_update = '%s', tgl_perubahan_nib = '%s' where id = %d",$input->id_penanaman_modal, $input->id_pemohon, $input->id_perusahaan_jenis, $input->id_perusahaan_status, $input->nib, $input->oss_id, $input->nama, $input->npwp, $input->email, $input->telp, $input->alamat, $input->rt_rw, $input->kelurahan, $input->id_wilayah, $input->modal_dasar, $input->total_pma, $input->nilai_pma_dominan, $input->nilai_pmdn, $input->persen_pma, $input->persen_pmdn, $input->data_saham, $tanggal_input->format('Y-m-d H:i:s'), $input->tgl_perubahan_nib, $id_perusahaan);
                $a = DB::update($q);
                return $id_perusahaan;
            }
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function GetIdPerusahaan($input)
    {
        try
        {            
            $p = sprintf("SELECT id from m_perusahaan where nib = '%s'", $input);
            $id_perusahaan = DB::select($p)[0]->id;
            
            return $id_perusahaan;
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
            $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'm_perusahaan_izin_jenis' ", env('DB_DATABASE'));
            $id = DB::select($q)[0]->AUTO_INCREMENT;  
            
            $q = sprintf("INSERT INTO m_perusahaan_izin_jenis(id, id_perusahaan, id_izin_jenis, id_izin, kode_izin, id_nib_status) VALUES (%d, %d, %d, '%s', '%s', %d)", $id, $input->id_perusahaan, $input->id_izin_jenis, $input->id_izin, $input->kode_izin, $input->id_nib_status);

            $a = DB::insert($q);

            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }    

    public function UpdatePerusahaanJenis($input)
    {
        try
        {            
            $q = sprintf("UPDATE m_perusahaan_izin_jenis set id_perusahaan = %d, id_izin_jenis = %d, kode_izin = '%s', id_nib_status = %d where id = %d",$input->id_perusahaan, $input->id_izin_jenis, $input->kode_izin, $input->id_nib_status, $input->id);
            
            $a = DB::update($q);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function GetByIdPermohonan($id_permohonan)
    {
        $q = sprintf("SELECT p.id as id_permohonan, per.id as id_perusahaan, pem.id as id_pemohon, ufo.id as id_user_fo, ufo.email_user, per.* from p_permohonan p left join m_perusahaan per on p.id_perusahaan = per.id left join m_pemohon pem on per.id_pemohon = pem.id left join m_user_fo ufo on pem.id_user_fo = ufo.id where p.id = %d", $id_permohonan);
        $result = DB::select($q);
        return $result;
    }

}