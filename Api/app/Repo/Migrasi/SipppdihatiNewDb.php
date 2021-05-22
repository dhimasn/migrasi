<?php

namespace App\Repo\Migrasi;

use App\Enums\TypeIzinJenisTel;
use App\Repo\Tel\PermohonanDisposisiTelDb;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;

class SipppdihatiNewDb extends Model {
    
    protected $connection = 'mysql';
    private $pdbDisposisiTel;
    public function __construct()
    {
        $this->pdbDisposisiTel = new PermohonanDisposisiTelDb();
    }

    public function FindMUser($id_user){
        $result = DB::table('m_user')
             ->where('id', $id_user)
            ->first();
        return $result;
    }

    public function GetTableMDataNib($id_data_nib)
    {
        $q = sprintf("SELECT * from m_data_nib where id = $id_data_nib");
        $result = DB::select($q);
        return $result;
    }

    public function GetTablePerusahaanIzinJenis($id_data_nib)
    {
        $result = DB::table('m_perusahaan_izin_jenis')
                    ->where('id', $id_data_nib)
                    ->first();
        return $result; 
    }

    public function GetTablePerusahaan($id_perusahaan)
    {
        $result = DB::table('m_perusahaan')
                    ->where('id', $id_perusahaan)
                    ->first();
        return $result; 
    }

    public function GetTablePerusahaanByname($nama_perusahaan){
        
        $perusahaan = DB::table('m_perusahaan')
            ->where('nama', $nama_perusahaan)
            ->first();
        
        if(empty($perusahaan)){
            $nama_tanpa_pt = substr($nama_perusahaan,3);
            if($nama_tanpa_pt){

                $perusahaan = DB::table('m_perusahaan')
                    ->where('nama', $nama_tanpa_pt)
                    ->first();

                if(empty($perusahaan)){
                    $perusahaan = DB::table('m_perusahaan')
                    ->where('nama','like', '%' .$nama_tanpa_pt. '%')
                    ->first();
                }    
            }
        }
        
        return $perusahaan;
    }

    public function GetPpermohonan($data)
    {
        $result = DB::table('p_permohonan')
                    ->where('no_penyelenggaraan',$data->no_permohonan)
                    ->where('no_sk_izin',$data->no_izin)
                    ->where('id_izin_jenis',$data->id_jenis_izin)
                    ->where('id_perusahaan',$data->id_perusahaan)
                    ->where('id_permohonan_status',$data->aktif)
                    ->where('tanggal_input',$data->tgl_permohonan)
                ->first();
        return $result;
    }

    public function GetPpermohonanByNoPenyelanggaraan($no_permohonan)
    {
        $result = DB::table('p_permohonan')
                    ->where('no_penyelenggaraan',$no_permohonan)
                ->first();
        return $result;
    }

    public function GetPermohonanNewByIdjenisIzin($id_jenis_izin)
    {
        $result = DB::table('p_permohonan')
            ->where('id_izin_jenis',$id_jenis_izin)
            //->whereBetween('id', [950,969])
            //->whereBetween('id', [750,800])
            ->get();
        return $result;    
    }

    public function GetPermohonanPosNewByIdJenisIzin($id_jenis_izin)
    {
        $result = DB::table('p_permohonan')
            ->whereIn('id_izin_jenis', $id_jenis_izin)
            //->where('id', 2276)
            //->whereBetween('id', [477,643])
            //->whereBetween('id', [643,363])
            ->get();
        return $result; 
    }

    public function GetIdJenisLyn($id_jenis_izin, $jenis_lyn_old)
    {
        $result = DB::table('k_layanan')
                    ->where('id_izin_jenis',$id_jenis_izin)
                    ->where('layanan',$jenis_lyn_old)
                    ->get();
        return $result;                
    }

    public function GetjenislynLike($id_jenis_izin){
        $result = DB::table('k_layanan')
            ->where('id_izin_jenis',$id_jenis_izin)
            ->get();
        return $result;
    }

    public function findIdLayanan($id_layanan)
    {
        $result = DB::table('k_layanan')
            ->where('id',$id_layanan)
            ->first();
        return $result;
    }

    public function GetPermohonanKomitByNoPermohonan($data_komit_old, $id_permohonan, $id_kelengkapan_status){
        $result = DB::table('p_permohonan_komit')
                ->where('no_komitmen', $data_komit_old->no_permohonan)
                ->where('id_permohonan', $id_permohonan)
                ->where('tanggal_input', $data_komit_old->tgl_permohonan)
                ->where('tanggal_update', $data_komit_old->tgl_permohonan)
                ->where('id_permohonan_komit_kelengkapan_status', $id_kelengkapan_status)
            ->first();
        return $result;
    }

    public function GetPermohonanKomitByIdpermohonan($id_permohonan){
        $result = DB::table('p_permohonan_komit')
                ->where('id_permohonan', $id_permohonan)
            ->first();
        return $result;
    }

    public function GetPermohonanLayananbyIdPermohonan($id_permohonan, $id_layanan){
        
        $result = DB::table('p_permohonan_layanan')
            ->where('id_permohonan', $id_permohonan)
            ->where('id_layanan',$id_layanan)
            ->first();
        return $result;

    }

    public function GetPermohonanLayananbyId($id_permohonan_layanan){
        $result = DB::table('p_permohonan_layanan')
            ->where('id', $id_permohonan_layanan)
            ->first();
        return $result;

    }

    public function GetPermohonanLayanan($id_permohonan){
        $result = DB::table('p_permohonan_layanan')
            ->where('id_permohonan', $id_permohonan)
            ->first();
        return $result;

    }

    public function GetPermohonanLyananPerusahaan($id_permohonan, $id_jenis_layanan, $id_perusahaan){
        $result = DB::table('p_permohonan_layanan_pos_per_perusahaan')
            ->where('id_permohonan', $id_permohonan)
            ->where('id_layanan',$id_jenis_layanan)
            ->where('id_perusahaan',$id_perusahaan)
            ->first();
        return $result;
    }

    public function GetMkomitmenULoByIdlayanan($id_layanan)
    {
        $result = DB::table('m_komitmen_ulo')
            ->where('id_layanan', $id_layanan)
            ->get();
        return $result;
    }

    public function FindKomitmenUloByIdlayanan($id_layanan, $value)
    {
        $result = DB::table('m_komitmen_ulo')
            ->where('id_layanan', $id_layanan)
            ->where('value','like','%'.$value.'%')
            ->first();
        return $result;
    }

    public function GetMevaluasiUloByIdlayanan($id_layanan)
    {
        $result = DB::table('m_evaluasi_ulo')
        ->where('id_layanan', $id_layanan)
        ->get();
        return $result;
    }

    public function FindevaluasiUloByIdlayanan($id_layanan, $value)
    {
        $result = DB::table('m_evaluasi_ulo')
            ->where('id_layanan', $id_layanan)
            ->where('value','like','%'.$value.'%')
            ->first();
        return $result;
    }

    public function GetKomitKelengkapan($id_layanan){
        $result = DB::table('k_komit_kelengkapan')
            ->where('id_layanan', $id_layanan)
            ->get();
        return $result;
    }

    public function GetPermohonaKomitKelengkapanbyIdPermohonanKomitLayanan($id_permohonan_komit_layanan, $id_jenis_kelengkapan, $id_permohonan_k_kelengkapan_status){
        $result = DB::table('p_permohonan_komit_kelengkapan')
            ->where('id_permohonan_komit_layanan', $id_permohonan_komit_layanan)
            ->where('id_jenis_kelengkapan',$id_jenis_kelengkapan)
            ->where('id_permohonan_komit_kelengkapan_status', $id_permohonan_k_kelengkapan_status)
            ->first();
        return $result;
    }

    public function GetPermohonanKomitKelengkapanByIdPermohonanKomitLayanan($id_permohonan_komit_layanan)
    {
        $result = DB::table('p_permohonan_komit_kelengkapan')
            ->where('id_permohonan_komit_layanan', $id_permohonan_komit_layanan)
            ->first();
        return $result;
    }

    public function GetPermohonanKomitKelengkapanPosbyIdPermohonanKomitLayanan($id_permohonan_komit_layanan){
        $result = DB::table('p_permohonan_komit_kelengkapan_pos')
            ->where('id_permohonan_komit_layanan', $id_permohonan_komit_layanan)
            ->first();
        return $result;
    }

    public function GetPermohonanKomitlayanan($Permohonan_kommit){

        $result = DB::table('p_permohonan_komit_layanan')
            ->where('id_permohonan_komit', $Permohonan_kommit)
            ->first();
        return $result;

    }

    public function GetTablePuloSkloByIdPermohonan($id_permohonan){       
        $result = DB::table('p_ulo_sklo')
            ->where('id_permohonan', $id_permohonan)
            ->first();
        return $result;
    }

    public function GetSkloByid($id_ulo_sklo){
        $result = DB::table('p_sk_ulo_file')
            ->where('id_ulo_sklo', $id_ulo_sklo)
            ->first();
        return $result;
    }

    public function GetPermohonanKomitPosbyIdPermohonan($id_permohonan){
        $result = DB::table('p_permohonan_komit_pos')
            ->where('id_permohonan', $id_permohonan)
            ->first();
        return $result;
    }

    public function GetProvinsi($nama_provinsi){
        $result = DB::table('provinsi')
            ->where('nm_provinsi', $nama_provinsi)
            ->first();
        return $result;
    }

    public function GetKabKota($nama_kab_kota){
        $result = DB::table('kabupaten')
            ->where('Kabupaten', $nama_kab_kota)
            ->first();
        return $result;
    }

    public function getLayanan($layanan){

        $result =DB::table('k_layanan')
            ->where('layanan', $layanan->nilai_string)
            ->first();
        return $result;

    }

    public function GetDataPenomoranByNoSkIzinandIdIzinJenis($data){
       
        $result = DB::table('p_permohonan')
            ->where('no_penyelenggaraan', $data->no_permohonan)
            ->first();
        return $result;
        
    }

    public function GetDataPermohonanByNoSkIzinandIdIzinJenis($data){      
        $result = DB::table('p_permohonan')
            ->where('no_sk_izin', $data->no_izin)
            ->where('id_izin_jenis',$data->id_jenis_izin)
            ->first();
        return $result;
    }

    public function getPermohonanInfo($id_permohonan){
        $result = DB::table('p_permohonan_info')
            ->where('id_permohonan',$id_permohonan)
            ->first();
        return $result;
    }

    public function getMPenomoranTel($id_layanan){
        $result = DB::table('m_penomoran_tel')
             ->where('id_layanan', $id_layanan)
            ->first();
        return $result;
    }

    public function findSyaratKomitmenPos($komitmen, $data_layanan_new){

        $result = DB::table('k_komit_kelengkapan')
            ->where('id_layanan', $data_layanan_new->id_layanan)
            ->where('jenis', $komitmen->teks_judul)
            ->get();
        return $result;
               
    }

    public function findPuloFile($t_syarat_izin_f_p, $komit_layanan_new){
        $result = DB::table('p_ulo_file')
            ->where('nama', $t_syarat_izin_f_p->file_name_asli)
            ->where('id_permohonan_komit_layanan', $komit_layanan_new->id)
            ->first();
        return $result;
    }

    public function GetPermohonanEvaluasiUloProses($id_evaluasi_ulo, $syarat_izin_p, $id_komit_layanan){
        $result = DB::table('p_evaluasi_ulo_proses')
            ->where('id_evaluasi_ulo', $id_evaluasi_ulo)
            ->where('baris', $syarat_izin_p->index)
            ->where('value', $syarat_izin_p->nilai_string)
            ->where('id_permohonan_komit_layanan',$id_komit_layanan)
            ->first();
        return $result;
    }

    public function GetPermohonanKomitmenUloProses($id_komitmen_ulo, $syarat_izin_p, $id_komit_layanan)
    {
        $result = DB::table('p_komitmen_ulo_proses')
            ->where('id_komitmen_ulo', $id_komitmen_ulo)
            ->where('baris', $syarat_izin_p->index)
            ->where('value', $syarat_izin_p->nilai_string)
            ->where('id_permohonan_komit_layanan',$id_komit_layanan)
            ->first();
        return $result;
    }

    public function GetpermohonanKomitmenUloProsesByIdKomitmenIdKomitlyn($id_komitmen_ulo,$id_permohonan_komit_layanan)
    {
        $result = DB::table('p_komitmen_ulo_proses')
            ->where('id_komitmen_ulo', $id_komitmen_ulo)
            ->where('id_permohonan_komit_layanan',$id_permohonan_komit_layanan)
            ->get();
        return $result;
    }

    public function GetpermohonanEvaluasiUloProsesByIdKomitmenIdKomitlyn($id_komitmen_ulo,$id_permohonan_komit_layanan)
    {
        $result = DB::table('p_komitmen_ulo_proses')
            ->where('id_komitmen_ulo', $id_komitmen_ulo)
            ->where('id_permohonan_komit_layanan',$id_permohonan_komit_layanan)
            ->get();
        return $result;
    }

    public function IsExistDisposisi($id_permohonan){
        $result = DB::table('p_permohonan_disposisi')
            ->where('id_permohonan', $id_permohonan)
            ->first();
        return $result;
    }

    public function IsExistDisposisiUlo($id_permohonan){
        $result = DB::table('p_permohonan_disposisi_ulo')
            ->where('id_permohonan', $id_permohonan)
            ->first();
        return $result;
    }

    public function UpdatePenomoranTel($nomor)
    {
        $result = array();

        $m_penomoran_tel_list = DB::table('m_penomoran_tel_list')
            ->where('nomor', $nomor)
            ->limit(1)
            ->update(['id_penomoran_status' => 1]);

        if($m_penomoran_tel_list){
           
            $result = $this->GetPenomoranTelList($nomor);
        
        } 

        return $result;
    }

    public function createPenomoranTel($nomor, $id_m_penomoran_tel)
    {
        $result = array();

        $m_penomoran  = DB::table('m_penomoran_tel_list')->insert([
            'id_penomoran_tel'=> $id_m_penomoran_tel,
            'id_penomoran_status' => 1,             
            'nomor'=> $nomor,
        ]);

        if($m_penomoran){
            $result = $this->GetPenomoranTelList($nomor);
        }

        return $result;
    }

    public function CreatePermohonanPenomoranTel($id_permohonan, $id_penomoran_tel)
    {
        $result = array();

        $m_penomoran  = DB::table('p_permohonan_penomoran_tel')->insert([
            'id_permohonan'=> $id_permohonan,
            'id_penomoran_tel' => $id_penomoran_tel,
        ]);

        if($m_penomoran){
            $result = $this->GetPermohonanPenomoranTelandIdpenomoran($id_permohonan, $id_penomoran_tel);
        }

        return $result;

    }

    public function GetPermohonanPenomoranTel($id_permohonan)
    {
        $result = DB::table('p_permohonan_penomoran_tel')
            ->where('id_permohonan', $id_permohonan)
            ->first();
        return $result;
    }

    public function GetPermohonanPenomoranTelandIdpenomoran($id_permohonan, $id_penomoran_tel ){
        $result = DB::table('p_permohonan_penomoran_tel')
            ->where('id_permohonan', $id_permohonan)
            ->where('id_penomoran_tel', $id_penomoran_tel)
            ->first();
        return $result;
    }

    public function FindNewJenisPenomoran($jns_pnmr)
    {
        $result = DB::table('m_penomoran_tel')
            ->where('jenis_penomoran','like', '%'.$jns_pnmr.'%')
            ->first();
        return $result;
    }

    public function UpdateDisposi($id_permohonan, $id_user){
        $result = DB::table('p_permohonan_disposisi')
              ->where('id_permohonan', $id_permohonan)->limit(1)->update(['id_user' => $id_user]);
        return $result;
    }

    public function UpdateDisposiUlo($id_permohonan, $id_user){
        $result = DB::table('p_permohonan_disposisi_ulo')
              ->where('id_permohonan', $id_permohonan)->limit(1)->update(['id_user' => $id_user]);
        return $result;
    }

    public function GetPenomoranTelList($nomor){
        $result = DB::table('m_penomoran_tel_list')
            ->where('nomor', $nomor)
            ->first();
        return $result;
    }

    public function GetPenomoranTelPakai($nomor)
    {
        $result = DB::table('p_penomoran_tel_pakai')
            ->where('no_sk_penomoran', $nomor)
            ->first();
        return $result;
    }

    public function  GetPenomoranTelPakaiById($id)
    {
        $result = DB::table('p_penomoran_tel_pakai')
            ->where('id', $id)
            ->first();
        return $result;
    }

    public function cekLog($data){
        $result = DB::table('p_permohonan_log')
            ->where('id_permohonan', $data->id_permohonan)
            ->where('status', $data->status)
            ->where('nama', $data->nama)
            ->where('jabatan', $data->jabatan)
            ->first();
        return $result;
    } 

    public function getPermohonanKomitmenUloProces($syarat_izin_p, $id_mekanisme_ulo, $id_komit_layanan){
        $result = DB::table('p_mekanisme_ulo_proses')
            ->where('id_mekanisme_ulo', $id_mekanisme_ulo)
            ->where('baris', $syarat_izin_p->index)
            ->where('value', $syarat_izin_p->nilai_string)
            ->where('id_permohonan_komit_layanan', $id_komit_layanan)
            ->first();
         return $result;
    }
   
    //CREATE

    public function CreateBuktibayar($data,$base64, $id_permohonan){

        $result = DB::table('p_permohonan_pos_bukti_bayar_file')->insert([
            'id_permohonan'=> $id_permohonan,
            'nama'=> $data->file_name_asli,
            'stream'=> $base64,
            'tanggal_input'=> $data->date_added,
            'status'=> $data->aktif
        ]);

        return $result;
    }

    public function CreatePermohonanWilayah($id_permohonan,$id_wilayah){
        $result = DB::table('p_permohonan_pos_cakupan_wilayah')->insert([
            'id_permohonan' => $id_permohonan,
            'id_wilayah' => $id_wilayah,
        ]);
        return $result;
    }

    public function createPermohonan($data){

        $result = array();

        $p_permohonan = DB::table('p_permohonan')->insert([
            'no_penyelenggaraan' => $data->no_permohonan,
            'no_sk_izin' => $data->no_izin,
            'id_izin_jenis' => $data->id_jenis_izin,
            'id_perusahaan' => $data->id_perusahaan,
            'id_permohonan_status' => $data->aktif,
            'tanggal_input' => $data->tgl_permohonan
        ]);
       
        if($p_permohonan){
            $result = $this->GetPpermohonan($data);
        }
        
        return $result;
    }

    public function CreatePermohonanLayanan($id_permohonan,$id_layanan){

        $result = $this->GetPermohonanLayananbyIdPermohonan($id_permohonan, $id_layanan);

        if($result == null){

            $permohonan_lyn = DB::table('p_permohonan_layanan')->insert([
                'id_permohonan' => $id_permohonan,
                'id_layanan' => $id_layanan,
            ]);

            if(!empty($permohonan_lyn)){
                $result = $this->GetPermohonanLayananbyIdPermohonan($id_permohonan, $id_layanan);
            }
        }

        return $result;

    }

    public function createPermohonanLog($data){

        if(empty($data->tanggal_input)){
            $data->tanggal_input = date("Y-m-d H:i:s");  
        }
        
        $result = $this->cekLog($data);

        if(empty($result)){
            $result = DB::table('p_permohonan_log')->insert([
                'id_permohonan' => $data->id_permohonan,
                'status' => $data->status,
                'nama' => $data->nama,
                'jabatan' => $data->jabatan,
                'tanggal_input' => $data->tanggal_input,
                'catatan' => $data->catatan
            ]);
        }
         
        return $result;

    }

    public function CreatePermohonanKomit($data_komit_old, $id_permohonan, $id_kelengkapan_status){

        $result = array();
        
        $p_permohonan_komit = DB::table('p_permohonan_komit')->insert([
            'no_komitmen' => $data_komit_old->no_permohonan,
            'tanggal_input' => $data_komit_old->tgl_permohonan,
            'tanggal_update' => $data_komit_old->tgl_permohonan,
            'id_permohonan_komit_kelengkapan_status' => $id_kelengkapan_status,
            'id_permohonan' => $id_permohonan
        ]);

        if(!empty($p_permohonan_komit)){
            $result = $this->GetPermohonanKomitByNoPermohonan($data_komit_old, $id_permohonan, $id_kelengkapan_status);
        }

        return $result; 

    }

    public function CreatePermohonanKomitPos($dataKomitOld, $id_permohonan, $id_kelengkapan_status){
        $result = array();
        $permohonan_komit_pos = DB::table('p_permohonan_komit_pos')->insert([
            'no_komitmen' => $dataKomitOld->no_permohonan,
            'tanggal_input' => $dataKomitOld->tgl_permohonan,
            'tanggal_update' => $dataKomitOld->tgl_permohonan,
            'id_permohonan_komit_kelengkapan_status' => $id_kelengkapan_status,
            'id_permohonan' => $id_permohonan
        ]);


        if($permohonan_komit_pos){
            $result = $this->GetPermohonanKomitPosbyIdPermohonan($id_permohonan);
        }

        return $result;
    }

    public function CreatePermohonanKommitlayanan($permohonan_komit, $permohonan_layanan){
        $result = array();
        
        $permohonan_komit_lyn = DB::table('p_permohonan_komit_layanan')->insert([
            'id_permohonan_komit'=>$permohonan_komit,
            'id_permohonan_layanan'=>$permohonan_layanan,
        ]);

        if($permohonan_komit_lyn){
            $result = $this->GetPermohonanKomitlayanan($permohonan_komit);
        }
        return $result;
    }

    public function createPermohonanKomitStatus($id_komit_layanan,$id_kelengkapan_status){
       
        $result = DB::table('p_permohonan_komit_status')->insert([
            'id_permohonan_komit_layanan'=> $id_komit_layanan,
            'id_permohonan_komit_kelengkapan_status'=>$id_kelengkapan_status,
        ]);
        return $result;
        
    }

    public function CreatePermohonanKomitlayananPos($permohonan_kommit,$permohonan_layanan){
        $result = array();
        $permohonan_komit_lyn_pos = DB::table('p_permohonan_komit_layanan_pos')->insert([
            'id_permohonan_komit'=> $permohonan_kommit,
            'id_permohonan_layanan'=> $permohonan_layanan,
        ]);
        if($permohonan_komit_lyn_pos){
            $result = $this->GetPermohonanKomitlayanan($permohonan_kommit);
        }
        return $result;
    }

    public function CreatePermohonanKomitmenUloProcess($syarat_izin_p, $id_komitmen_ulo, $id_komit_layanan){

        $result = $this->GetPermohonanKomitmenUloProses($id_komitmen_ulo, $syarat_izin_p, $id_komit_layanan);
        if(empty($result)){
            $result = DB::table('p_komitmen_ulo_proses')->insert([
                'id_komitmen_ulo' => $id_komitmen_ulo,
                'baris' => $syarat_izin_p->index,
                'value' => $syarat_izin_p->nilai_string,
                'id_permohonan_komit_layanan' => $id_komit_layanan,
            ]);
        }
        return $result;
    
    }

    public function createPermohonanEvaluasiUloProses($syarat_izin_p, $id_evaluasi_ulo, $id_komit_layanan){
        
        $result = $this->GetPermohonanEvaluasiUloProses($id_evaluasi_ulo, $syarat_izin_p, $id_komit_layanan);
            if(empty($result)){
                $result = DB::table('p_evaluasi_ulo_proses')->insert([
                    'id_evaluasi_ulo' => $id_evaluasi_ulo,
                    'baris' => $syarat_izin_p->index,
                    'value' => $syarat_izin_p->nilai_string,
                    'id_permohonan_komit_layanan' => $id_komit_layanan,
                ]);
            }
        return $result;
        }

    public function CreatePermohonanKomitmenMekanismeUloProcess($syarat_izin_p, $id_mekanisme_ulo, $id_komit_layanan){
        $result = $this->getPermohonanKomitmenUloProces($syarat_izin_p, $id_mekanisme_ulo, $id_komit_layanan);
        if(empty($result)){
            $result = DB::table('p_mekanisme_ulo_proses')->insert([
                'id_mekanisme_ulo' => $id_mekanisme_ulo,
                'baris' => $syarat_izin_p->index,
                'value' => $syarat_izin_p->nilai_string,
                'id_permohonan_komit_layanan' => $id_komit_layanan,
            ]);
        }
        return $result;
    }

    public function CreateTablePulo($id_status, $data_komit_new_id, $tanggal_input)
    {
        $result = DB::table('p_ulo')->insert([
            'tanggal_input' => $tanggal_input,
            'id_ulo_status' => $id_status,
            'id_permohonan_komit' => $data_komit_new_id,
        ]);
        return $result;
    }
    
    public function createUloCatatan($catatan,$tanggal_input,$id_permohonan_komit){
        $result = DB::table('p_ulo_catatan')->insert([
            'id_permohonan_komit' => $id_permohonan_komit,
            'catatan' => $catatan,
            'tanggal_input' => $tanggal_input,
        ]);
        return $result;

    }

    public function CreateTablePuloSklo($t_izin_terbit_sklo,$id_permohonan){
        
        $result = $this->GetTablePuloSkloByIdPermohonan($id_permohonan);

        if(empty($result)){

            $result = DB::table('p_ulo_sklo')->insert([
                'id_permohonan' => $id_permohonan,
                'no_sklo' => $t_izin_terbit_sklo->no_izin,
                'tanggal_input' => $t_izin_terbit_sklo->tgl_terbit,
            ]);

            if($result == true){
                $result = $this->GetTablePuloSkloByIdPermohonan($id_permohonan);
            }   

        }
      
        return $result;
    }

    public function CreateSkUloFile($t_izin_terbit_sklo, $p_ulo_sklo, $base64){

        $result = $this->GetSkloByid($p_ulo_sklo->id);

        if(empty($result)){

            $result = DB::table('p_sk_ulo_file')->insert([
                'id_ulo_sklo' => $p_ulo_sklo->id,
                'nama' => $t_izin_terbit_sklo->pdf_generate,
                'stream' => $base64,
            ]);
            
            if($result == true){
                $result = $this->GetSkloByid($p_ulo_sklo->id);
            } 
        }

        return $result;
    
    }

    public function CreateSkKomitFile($pdf_generate, $id_permohonan_komit, $base64){

        $result = DB::table('p_sk_penetapan_komit_file')->insert([
            'id_permohonan' => $id_permohonan_komit,
            'nama' => $pdf_generate,
            'stream' => $base64,
        ]);
        return $result;

    }

    public function CreatePskPenomoranFile($id_penomoran_tel_pakai, $file_name, $base64){
        $result = DB::table('p_sk_penomoran_file')->insert([
            'id_penomoran_tel_pakai' => $id_penomoran_tel_pakai,
            'nama' => $file_name,
            'stream' => $base64,
        ]);
        return $result;
    }

    public function CreateBerkasKelengkapanPenomoran($id, $file_name, $base64)
    {
        $result = DB::table('p_permohonan_penomoran_kelengkapan_file')->insert([
            'id_permohonan_penomoran_kelengkapan' => $id,
            'nama' => $file_name,
            'stream' => $base64,
        ]);
        return $result;
    }

    public function createPencabutanFile($rekom, $base64, $flagging_data){
        $result = DB::table('p_sk_pencabutan')->insert([
            'id_permohonan' => $rekom->id_permohonan,
            'no_sk_cabut' => $flagging_data->text,
            'nama' => $rekom->filename,
            'stream' => $base64,
            'tanggal_input' => $rekom->tgl_terbit,
        ]);
        return $result;
    }

    public function createPskPencabutanPenomoran($rekom, $base64, $flagging_data, $id_penomoran_tel_pakai){
        $result = DB::table('p_sk_penomoran_pencabutan')->insert([
            'id_penomoran_tel_pakai' => $id_penomoran_tel_pakai,
            'no_sk_cabut' => $flagging_data->text,
            'nama' => $rekom->filename,
            'stream' => $base64,
            'tanggal_input' => $rekom->tgl_terbit,
        ]);
        return $result;
    }

    public function createPermohonanPenomoranKelengkapan($id_penomoran_tel,$id_permohnohonan_kelengkapan,$status)
    {
        $result = null;

        $nomor_klgkpn = DB::table('p_permohonan_penomoran_kelengkapan')->insert([
            'id_permohonan_penomoran_tel' => $id_penomoran_tel,
            'id_penomoran_kelengkapan' => $id_permohnohonan_kelengkapan,
            'id_permohonan_komit_kelengkapan_status' => $status,
        ]);

        
        if($nomor_klgkpn){
            $result = $this->GetPermohonanPenomoranKelengkapan($id_penomoran_tel, $id_permohnohonan_kelengkapan, $status);
        }

        return $result;
    }

    public function GetPermohonanPenomoranKelengkapan($id_penomoran_tel, $id_permohnohonan_kelengkapan, $status)
    {
        $result = DB::table('p_permohonan_penomoran_kelengkapan')
            ->where('id_permohonan_penomoran_tel', $id_penomoran_tel)
            ->where('id_penomoran_kelengkapan',$id_permohnohonan_kelengkapan)
            ->where('id_permohonan_komit_kelengkapan_status', $status)
            ->first();
        return $result;
    }

    public function CreatePermohonanKomitKelengkapan($id_permohonan_komit_layanan, $id_jenis_kelengkapan, $id_permohonan_k_kelengkapan_status)
    {
        $result = null;

        $permohonanKomitKelengkapan = DB::table('p_permohonan_komit_kelengkapan')->insert([
            'id_permohonan_komit_layanan' => $id_permohonan_komit_layanan,
            'id_jenis_kelengkapan' => $id_jenis_kelengkapan,
            'id_permohonan_komit_kelengkapan_status' => $id_permohonan_k_kelengkapan_status,
        ]);

        
        if($permohonanKomitKelengkapan){
            $result = $this->GetPermohonaKomitKelengkapanbyIdPermohonanKomitLayanan($id_permohonan_komit_layanan, $id_jenis_kelengkapan, $id_permohonan_k_kelengkapan_status);
        }

        return $result;
    
    }

    public function CreatePermohonanKomitKelengkapanPos($id_permohonan_komit_layanan, $id_jenis_kelengkapan, $id_permohonan_k_kelengkapan_status){
       
        $result = array();
        $permohonanKomitKelengkapanPos = DB::table('p_permohonan_komit_kelengkapan_pos')->insert([
            'id_permohonan_komit_layanan'=>$id_permohonan_komit_layanan,
            'id_jenis_kelengkapan'=>$id_jenis_kelengkapan,
            'id_permohonan_komit_kelengkapan_status'=>$id_permohonan_k_kelengkapan_status,
        ]);
        if($permohonanKomitKelengkapanPos){
            $result = $this->GetPermohonanKomitKelengkapanPosbyIdPermohonanKomitLayanan($id_permohonan_komit_layanan);
        }
        return $result;

        
    }

    public function CreatePermohonanKomitkelengkapanCatatan($id_permohnohonan_kelengkapan, $catatan, $tanggal_input){
    
        $result = DB::table('p_permohonan_komit_catatan')->insert([
            'id_permohonan_komit_kelengkapan' => $id_permohnohonan_kelengkapan,
            'catatan' => $catatan,
            'tanggal_input' => $tanggal_input,
        ]);

        return $result;
    }


    public function CreatePermohonanKomitkelengkapanCatatanPos($id_permohnohonan_kelengkapan, $catatan, $tanggal_input){
      
        $result = DB::table('p_permohonan_komit_catatan_pos')->insert([
            'id_permohonan_komit_kelengkapan' => $id_permohnohonan_kelengkapan,
            'catatan' => $catatan,
            'tanggal_input' => $tanggal_input,
        ]);
        return $result;

    }

    public function createPermmohonanKomitKelengkapanFile($id_permohonan_komit_kelengkapan, $file_name_asli, $base64){

        $result = DB::table('p_permohonan_komit_file')->insert([
            'id_Permohonan_komit_kelengkapan' => $id_permohonan_komit_kelengkapan,
            'nama'=> $file_name_asli,
            'stream' => $base64,
        ]);
        
        return $result;
    }

    public function createPermmohonanKomitKelengkapanFilePos($id_permohonan_komit_kelengkapan, $file_name_asli, $base64){

        $result = DB::table('p_permohonan_komit_file_pos')->insert([
            'id_Permohonan_komit_kelengkapan' => $id_permohonan_komit_kelengkapan,
            'nama'=> $file_name_asli,
            'stream' => $base64,
        ]);
        return $result;
        
    }

    public function createPermohonanSkIzinFile($data_perm_new, $name, $base64){

        $result = DB::table('p_sk_izin_file')->insert([
            'id_permohonan'=> $data_perm_new->id,
            'nama'=> $name,
            'stream'=> $base64,
        ]);
        return $result;
    }

    public function CreatePUloFile($t_syarat_izin_f_p, $komit_layanan_new, $base64){

        $result = $this->findPuloFile($t_syarat_izin_f_p, $komit_layanan_new);

        if(empty($result)){
            $result = DB::table('p_ulo_file')->insert([ 
                'id'=> strtoupper(bin2hex(openssl_random_pseudo_bytes(16))),
                'nama'=> $t_syarat_izin_f_p->file_name_asli,
                'stream'=> $base64,
                'id_permohonan_komit_layanan' => $komit_layanan_new->id
            ]);
        }

        if(!empty($result)){
           $result = $this->findPuloFile($t_syarat_izin_f_p, $komit_layanan_new);
        }

        return $result;
    }

    public function CreatePermohonanDisposisiStafUlo($id_permohonan,$id_user){
        $result = DB::table('p_permohonan_disposisi_staf_ulo')->insert([
            'id_permohonan'=> $id_permohonan,             
            'id_user'=> $id_user,
        ]);
        return $result;
    }

    public function CreatePermohonanDisposisiStaf($id_permohonan,$id_user){
        $result = DB::table('p_permohonan_disposisi_staf')->insert([
            'id_permohonan'=> $id_permohonan,             
            'id_user'=> $id_user,
        ]);
        return $result;
    }

    public function CreatePermohonanDisposisiKirimUlo($id_permohonan,$id_user){
        $result = DB::table('p_permohonan_disposisi_ulo_kirim')->insert([
            'id_permohonan'=> $id_permohonan,             
            'id_user'=> $id_user,
        ]);
        return $result;
    }

    public function CreatePermohonanDisposisiKirim($id_permohonan, $id_user){
        $result = DB::table('p_permohonan_disposisi_kirim')->insert([
            'id_permohonan'=> $id_permohonan,             
            'id_user'=> $id_user,
        ]);
        return $result;
    }

    public function CreatePermohonanDisposisiUlo($id_permohonan,$id_user){
        $result = DB::table('p_permohonan_disposisi_ulo')->insert([
            'id_permohonan'=> $id_permohonan,             
            'id_user'=> $id_user,
        ]);
        return $result;
    }

    public function CreatePermohonanDisposisi ($id_permohonan, $id_user){
        $result = DB::table('p_permohonan_disposisi')->insert([
            'id_permohonan'=> $id_permohonan,             
            'id_user'=> $id_user,
        ]);
        return $result;
    }

    public function CreatePermohonanInfo($permohonan, $waktu_in, $status_akt){
        $result = DB::table('p_permohonan_info')->insert([
            'id_permohonan'=> $permohonan->id,             
            'tanggal_input'=> $waktu_in,
            'value'=> $status_akt
        ]);
        return $result;
    }

    public function UpdatePermohonanInfo($permohonan,$waktu_in,$status_akt){
        $result = DB::table('p_permohonan_info')
              ->where('id_permohonan', $permohonan->id)->limit(1)
              ->update(['tanggal_input' => $waktu_in, 'value' => $status_akt]);
        return $result;
    }

    public function updatePenomoranTelPakai($p_penomoran_tel_pakai, $flagging_data){
        
        $result = array();
        
        $result = DB::table('p_penomoran_tel_pakai')
              ->where('id', $p_penomoran_tel_pakai->id)
              ->update(['no_sk_penomoran' => $flagging_data->text]);
        
        if(!empty($result)){
            $result = $this->GetPenomoranTelPakaiById($p_penomoran_tel_pakai->id);
        }
     
        return $result;
    }

    public function UpdateBuktiBayar($id_permohonan){

        // $result = DB::table('p_permohonan_pos_bukti_bayar_file')
        //       ->where('id_permohonan', $id_permohonan)
        //       ->update(['status' => 1]);
        // return $result;

        $q = sprintf("UPDATE p_permohonan_pos_bukti_bayar_file set status='%d' where id_permohonan=%d", 1, $id_permohonan);
        $a = DB::update($q);

    }

    public function UpdatePermohonan($data_perm_new, $aktif){
        $result = DB::table('p_permohonan')
              ->where('id', $data_perm_new->id)->limit(1)
              ->update(['id_permohonan_status' => $aktif]);
        return $result;
    }

    /*public function UpdatePermohonanInfo($permohonan, $waktu_in, $status_akt)
    {
        $q = sprintf("UPDATE p_permohonan_info set tanggal_input='%s', value='%s'  where id_permohonan=%d", $waktu_in, $status_akt, $permohonan->id);
        $a = DB::update($q);
    }

    public function UpdatePermohonan($data_perm_new, $aktif)
    {
        $q = sprintf("UPDATE p_permohonan set id_permohonan_status='%d' where id=%d", $aktif, $data_perm_new->id);
        $a = DB::update($q);
    }

    public function UpdateDisposiUlo($id_permohonan, $id_user){ 
        $q = sprintf("UPDATE p_permohonan_disposisi_ulo set id_user=%d where id_permohonan=%d", $id_user, $id_permohonan);
        $a = DB::update($q);
        return $a;
    }

    public function UpdateDisposi($id_permohonan, $id_user){
        $q = sprintf("UPDATE p_permohonan_disposisi set id_user=%d where id_permohonan=%d", $id_user, $id_permohonan);
        $a = DB::update($q);
        return $a;
    }*/
    
 
    public function CreatePermohonanLynPosPerusahaan($data, $id_jenis_layanan, $data_perm_new){

        $result = $this->GetPermohonanLyananPerusahaan($data_perm_new->id, $id_jenis_layanan,$data->id_perusahaan);
        
        if($result == null){
        $result = DB::table('p_permohonan_layanan_pos_per_perusahaan')->insert([
                'id_permohonan'=> $data_perm_new->id,
                'id_layanan'=> $id_jenis_layanan,
                'id_perusahaan'=> $data->id_perusahaan,
            ]);
        }

        return $result;
    }

    public function CreatePermohonanPenambahanLayanan($id_permohonan, $id_layanan){
        $result = DB::table('p_permohonan_penambahan_layanan_pos')->insert([
            'id_permohonan'=> $id_permohonan,
            'id_layanan'=> $id_layanan,
        ]);
    }

    public function CreateMediaJaringan($id_permohonan, $id_layanan){
       $result = array();
        $media = $this->getMPenomoranTel($id_layanan);
        if($media){
            $result = DB::table('p_media_jaringan')->insert([
                'id_permohonan_layanan'=> $id_permohonan,
                'id_media'=> $media->id,
            ]);
        }
        return $result;
    }
    
    public function DisposisiStaf($data_perm_new, $histori){
        
        /*if($data_perm_new->id_izin_jenis == 4){
            $temp = 8;
        }else if($data_perm_new->id_izin_jenis == 2){
            $temp = 108;
        }else if($data_perm_new->id_izin_jenis == 3){
            $temp = 4;
        }else{
            $temp = 17;
        }*/
            
        //create permohonan disposisi
        if(!$this->pdbDisposisiTel->IsExistDisposisiStaf($data_perm_new->id, $data_perm_new->id_izin_jenis)){
            if($histori->aktif == 1){
                #p_permohonan_disposisi_staf
                if(in_array($histori->id_user, [1,9,12,15,20,21,24,26,27,28,29,33,35,36,37,38,39,40,41,44,45,46,47,48,49,50,53,54,56,57])){
                    $this->CreatePermohonanDisposisiStaf($data_perm_new->id, $histori->id_user);
                }
            }  
        }

        if(!$this->pdbDisposisiTel->IsExistDisposisiKirim($data_perm_new->id, $data_perm_new->id_izin_jenis, $histori->id_user)){
            
            #p_permohonan_disposisi_kirim
            if($histori->id_user != null){
                $this->CreatePermohonanDisposisiKirim($data_perm_new->id, $histori->id_user);
            }
            
        }
        
        $disposisi = $this->IsExistDisposisi($data_perm_new->id);
        if(empty($disposisi)){
            if($histori->aktif == 1){
                if($histori->id_user != null){
                    #p_permohonan_disposisi
                    $this->CreatePermohonanDisposisi($data_perm_new->id, $histori->id_user);
                }
            }
        } else {
            if($histori->aktif == 1){
                if($histori->id_user != null){
                     #update p_permohonan_disposisi
                    $this->UpdateDisposi($data_perm_new->id, $histori->id_user);
                }    
            }
        }
    }



    public function DisposisiStafUlo($data_perm_new, $histori){

        if(!$this->pdbDisposisiTel->IsExistDisposisiStaf($data_perm_new->id,TypeIzinJenisTel::Ulo)){                        
            if($histori->aktif == 1){
                #p_permohonan_disposisi_staf_ulo
                if(in_array($histori->id_user,[24,48,49,56,57])){
                    $this->CreatePermohonanDisposisiStafUlo($data_perm_new->id, $histori->id_user);
                }
            }
        }

        if(!$this->pdbDisposisiTel->IsExistDisposisiKirim($data_perm_new->id, TypeIzinJenisTel::Ulo, $histori->id_user)){                        
            #p_permohonan_disposisi_kirim
            if($histori->id_user != null){
                $this->CreatePermohonanDisposisiKirimUlo($data_perm_new->id, $histori->id_user);
            }
        }

        $disposisi = $this->IsExistDisposisiUlo($data_perm_new->id);
        if(empty($disposisi)){
            if($histori->aktif == 1){
                if($histori->id_user != null){
                #p_permohonan_disposisi
                $this->CreatePermohonanDisposisiUlo($data_perm_new->id,$histori->id_user);
                }
            }
        }else{
            if($histori->aktif == 1){
                if($histori->id_user != null){
                #update p_permohonan_disposisi
                $this->UpdateDisposiUlo($data_perm_new->id,$histori->id_user);
                }
            }
        }
    }

    public function createPermohonanPenomoran($data){

        $result = array();

        if(!empty($data->no_izin)){
            $no_izin = $data->no_izin;
        }else{
            $no_izin = $data->no_izin_ref;
        }
       
        $p_permohonan = DB::table('p_permohonan')->insert([
            'no_penyelenggaraan' => $data->no_permohonan,
            'no_sk_izin' => $no_izin,
            'id_izin_jenis' => $data->id_jenis_izin,
            'id_perusahaan' => $data->id_perusahaan,
            'id_permohonan_status' => $data->aktif,
            'tanggal_input' => $data->tgl_permohonan
        ]);
        
        if($p_permohonan){
            $result = $this->GetPpermohonanPenomoran($data);
        }
        
        return $result;
    }

    public function GetPpermohonanPenomoran($data)
    {
        $result = DB::table('p_permohonan')
                ->where('no_penyelenggaraan',$data->no_permohonan)
                //->where('no_sk_izin',$data->no_izin_ref)
                ->where('id_izin_jenis',$data->id_jenis_izin)
                ->where('id_perusahaan',$data->id_perusahaan)
                ->where('id_permohonan_status',$data->aktif)
                ->where('tanggal_input',$data->tgl_permohonan)
            ->first();
        return $result;
    }

    public function CreateMpenomoranTellist($id_penomoran_tel, $nomor, $status_penomoran){

        $result = DB::table('m_penomoran_tel_list')->insert([
            'id_penomoran_tel' => $id_penomoran_tel,
            'nomor' => $nomor,
            'id_penomoran_status' => $status_penomoran,
        ]);
        return $result;
    }

    public function CreatePenomoranTelPakai($data, $m_penomoran_tel_list, $no_penetapan){
       
        $result = array();

        $tel_pakai = DB::table('p_penomoran_tel_pakai')->insert([
            'id_penomoran_tel_list' => $m_penomoran_tel_list->id,
            'id_perusahaan' => $data->id_perusahaan,
            'id_penomoran_status' => $m_penomoran_tel_list->id_penomoran_status,
             'no_sk_penomoran' => $no_penetapan,
            'id_permohonan' => $data->id,
        ]);

        if($tel_pakai){
            $result = $this->GetPenomoranTelPakai($no_penetapan);
        }
        
        return $result;
    }

    public function updateNomorSkPermohonan($id_permohonan, $no_penetapan)
    {
        $result = DB::table('p_permohonan')
            ->where('id', $id_permohonan)
            ->limit(1)->update(['no_sk_izin' => $no_penetapan]);

        return $result;
    }

    public function CreatePermohonanKinerja($id_komit_layanan, $t_syarat_izin_p, $tahun){

        if($t_syarat_izin_p->index == 1){
            $t_syarat_izin_p->jenis = "Network Availability (%)";
        }else if($t_syarat_izin_p->index == 2){
            $t_syarat_izin_p->jenis = "Pencapaian Mean Time To Restore (jam)";
        }else{
            $t_syarat_izin_p->jenis = "";
        }

        if($tahun == "I"){
            $tahun = 1;   
        }else if($tahun == "II"){
            $tahun = 2;
        }else if($tahun == "III"){
            $tahun = 3;
        }else if($tahun == "IV"){
            $tahun = 4;
        }else if($tahun == "V"){
            $tahun = 5;
        }

        $result = DB::table('p_permohonan_kinerja')->insert([
            'id_permohonan_komit_layanan'=> $id_komit_layanan,
            'tahun'=> $tahun,
            'jenis'=> $t_syarat_izin_p->jenis,
            'value'=> $t_syarat_izin_p->nilai_string,
            'baris'=> $t_syarat_izin_p->index,
        ]);

        return $result;
    }

    public function CreatePermohonanKinerjaStatus($id_komit_layanan, $id_permohonan_k_kelengkapan_status){

        $result = DB::table('p_permohonan_kinerja_status')->insert([
            'id_permohonan_komit_layanan'=> $id_komit_layanan,
            'id_permohonan_komit_kelengkapan_status'=> $id_permohonan_k_kelengkapan_status,
        ]);

        return $result;

    }

    
}
?>