<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repo\MigrationOldDb;
use App\Repo\OssDb;
use App\Repo\PerusahaanDb;
use Illuminate\Support\Facades\Hash;
use stdClass;

class OssController extends Controller
{
    private $odb;
    private $pdb;
    private $modb;

    public function __construct()
    {
        //$this->middleware('auth');
        $this->odb = new OssDb();
        $this->pdb = new PerusahaanDb();
        $this->modb = new MigrationOldDb();
    }

    public function receiveNIB(Request $re)
    {  
        $username 	= $re->akun;
		$passkey 	= $re->akun.'1000';

		$key 	= $re->header('OSS-HUB-KOMINFO-KEY');
		$dataNIB= json_decode(file_get_contents('php://input'),true)['dataNIB'];
        $valid 	= hash('sha512', $username.$passkey.date('YmdHi'));
        $status = "SUCCESS";

        $model = new stdClass();
        $model->data_nib        = json_encode($dataNIB);
        $model->nib             = $dataNIB['nib'];
        $model->versi_pia       = $dataNIB['versi_pia'];
        $model->kunci_header    = $key;        
		if ($key == $valid) {
            $model->status_auth = 1;
		} else {
            $model->status_auth = 99;
            $status = "FAILED TO AUTHENTICATE";
		}        
        $resultNIB = $this->odb->PostDataNIB($model);

        $model4 = new stdClass();
        $model4->id_user_proses = $dataNIB['jenis_id_user_proses'].'-'.$dataNIB['no_id_user_proses'];
        $model4->nama = $dataNIB['nama_user_proses'];
        $model4->nik = $dataNIB['no_id_user_proses'];
        $model4->email = $dataNIB['email_user_proses'];        
        $model4->telp = $dataNIB['hp_user_proses'];

        $resultPemohon = $this->pdb->PostDataPemohon($model4);
        
        $model2 = new stdClass();
        $model2->id_pemohon             = $resultPemohon;
        $model2->id_penanaman_modal     = intval($dataNIB['status_penanaman_modal']);
        $model2->id_perusahaan_jenis    = $dataNIB['jenis_perseroan'];
        $model2->id_perusahaan_status   = 1;
        $model2->nib                    = $dataNIB['nib'];
        $model2->oss_id                 = $dataNIB['oss_id'];
        $model2->nama                   = $dataNIB['nama_perseroan'];
        $model2->npwp                   = $dataNIB['npwp_perseroan'];
        $model2->email                  = $dataNIB['email_perseroan'];
        $model2->telp                   = $dataNIB['id_penanaman_modal'];
        $model2->alamat                 = $dataNIB['alamat_perseroan'];
        $model2->rt_rw                  = $dataNIB['rt_rw_perseroan'];
        $model2->kelurahan              = $dataNIB['kelurahan_perseroan'];
        $model2->id_wilayah             = $dataNIB['perseroan_daerah_id'];
        $model2->modal_dasar            = $dataNIB['modal_dasar'];
        $model2->total_pma              = $dataNIB['total_pma'];
        $model2->nilai_pma_dominan      = $dataNIB['nilai_pma_dominan'];
        $model2->nilai_pmdn             = $dataNIB['nilai_pmdn'];
        $model2->persen_pma             = $dataNIB['persen_pma'];
        $model2->persen_pmdn            = $dataNIB['persen_pmdn'];
        $model2->data_saham             = $dataNIB['pemegang_saham'];
        
        $resultPerusahaan = $this->pdb->PostDataPerusahaan($model2);

        $model3 = new stdClass();
        $model3->kode_izin  = $dataNIB['kd_izin'];
        $model3->id_izin  = $dataNIB['id_izin'];
        $model3->id_id_nib_status = 1;      
        
        $resultPerusahaanJenis = $this->pdb->PostDataPerusahaanJenis($model3);
        
		if ($resultNIB == false && $resultPemohon == null && $resultPerusahaan == false && $resultPerusahaanJenis == false) {
			$status 	= 'FAIL TO AUTHENTICATE';
            return response()->json(['status' => $status, 'code' => 400]);
		} else{
            return response()->json(['status' => $status, 'code' => 200]);
        }        
    }

    //t_nib last update 03/10/2020
    //t-nib total row 36179 (36180)
    //last id_nib 55600
    public function nibLama(Request $re)
    {  
        ini_set('memory_limit', '4096M');
        ini_set('MAX_EXECUTION_TIME', '-1');
        //$res = $this->modb->GetOldNIB($re->awal, $re->akhir);
        $res = $this->modb->GetOldNIB();
        
        foreach($res as $data){

            $dataNIB = json_decode($data->data);
           
            if(is_array($dataNIB) || is_object($dataNIB)){
                $model = new stdClass();
                $model->data_nib        = addslashes(json_encode($dataNIB));
               
                if(array_key_exists('nib', $dataNIB)) {
                    $model->nib         = $dataNIB->nib;
                }else{
                    $model->nib         = "-";
                }
                if(array_key_exists('versi_pia', $dataNIB)) {
                    $model->versi_pia   = $dataNIB->versi_pia;
                }else{
                    $model->versi_pia   = "-";
                }
                $model->kunci_header    = $data->test;  
                $model->status_auth     = 1;      
                $resultNIB = $this->odb->PostDataNIB($model);
               
                if(array_key_exists('nib', $dataNIB)) {

                    $OldUidNumber = $this->modb->GetOldUidNumber($dataNIB->no_id_user_proses);
    
                    $tanggal_now  = Carbon::now();
                    $tanggal_string = $tanggal_now->format('Y-m-d H:i:s');
    
                    $model5 = new stdClass();
                    $model5->employee_number = $dataNIB->jenis_id_user_proses.'-'.$dataNIB->no_id_user_proses;
                    $model5->nm_user = addslashes($dataNIB->nama_user_proses);
                    $model5->email_user = $dataNIB->email_user_proses;   
                    $model5->tanggal_input = $tanggal_string;
                    $model5->tanggal_update = $tanggal_string;                 
                    if(!empty($OldUidNumber)){
                        $model5->uid_number = $OldUidNumber[0]->uidnumber;
                    }else{
                        $model5->uid_number = 0; 
                    }

                    $resultUserFO = $this->modb->PostUserFO($model5); 
                    
                    $model4 = new stdClass();
                    $model4->id_user_fo = $resultUserFO;
                    $model4->id_user_proses = $dataNIB->jenis_id_user_proses.'-'.$dataNIB->no_id_user_proses;
                    $model4->nama = addslashes($dataNIB->nama_user_proses);
                    $model4->nik = $dataNIB->no_id_user_proses;
                    $model4->email = $dataNIB->email_user_proses;        
                    $model4->telp = $dataNIB->hp_user_proses;                    
                    $resultPemohon = $this->modb->PostDataPemohon($model4); 
    
                    $model2 = new stdClass();              
                    $model2->id_pemohon             = $resultPemohon;
                   
                    if(array_key_exists('status_penanaman_modal', $dataNIB)) {
                        $model2->status_penanaman_modal  = intval($dataNIB->status_penanaman_modal);
                    }else{
                        $model2->status_penanaman_modal  = "";

                    }
                   
                    if(array_key_exists('jenis_perseroan', $dataNIB)) {
                        $model2->id_perusahaan_jenis    = intval($dataNIB->jenis_perseroan);
                    }else{
                        $model2->id_perusahaan_jenis    = "";

                    }
                    $model2->id_perusahaan_status   = 1;
                    $model2->nib                    = $dataNIB->nib;
                    if(array_key_exists('tgl_perubahan_nib', $dataNIB)) {
                        $model2->tgl_perubahan_nib  = $dataNIB->tgl_perubahan_nib;
                    }else{
                        $model2->tgl_perubahan_nib  = "-";
                    }
                    $model2->oss_id                 = $dataNIB->oss_id;
                    $model2->nama                   = $dataNIB->nama_perseroan;
                    $model2->npwp                   = $dataNIB->npwp_perseroan;
                    if(array_key_exists('email_perusahaan', $dataNIB)) {                    
                        $model2->email              = $dataNIB->email_perusahaan;
                    }else if(array_key_exists('email_perseroan', $dataNIB)){
                        $model2->email              = $dataNIB->email_perseroan;
                    }else{
                        $model2->email              = "-";
                    }
                    if(array_key_exists('nomor_telpon_perseroan', $dataNIB)) {
                        $model2->telp               = $dataNIB->nomor_telpon_perseroan;
                    }else{
                        $model2->telp               = "-";
                    }
                    if(array_key_exists('alamat_perseroan', $dataNIB)) {
                        $model2->alamat               = $dataNIB->alamat_perseroan;
                    }else{
                        $model2->alamat               = "-";
                    }
                    if(array_key_exists('rt_rw_perseroan', $dataNIB)) {
                        $model2->rt_rw               = $dataNIB->rt_rw_perseroan;
                    }else{
                        $model2->rt_rw               = "-";
                    }
                    if(array_key_exists('kelurahan_perseroan', $dataNIB)) {
                        $model2->kelurahan           = $dataNIB->kelurahan_perseroan;
                    }else{
                        $model2->kelurahan           = "-";
                    }
                    if(array_key_exists('id_wilayah', $dataNIB)) {
                        $model2->id_wilayah          = $dataNIB->perseroan_daerah_id;
                    }else{
                        $model2->id_wilayah          = "-";
                    }
                    if(array_key_exists('modal_dasar', $dataNIB)) {
                        $model2->modal_dasar         = $dataNIB->total_modal_dasar;
                    }else{
                        $model2->modal_dasar         = "-";
                    }                              
                    if(array_key_exists('total_pma', $dataNIB)) {
                        $model2->total_pma          = $dataNIB->total_pma;
                    }else{
                        $model2->total_pma          = 0;
                    }
                    if(array_key_exists('nilai_pma_dominan', $dataNIB)) {
                        $model2->nilai_pma_dominan  = $dataNIB->nilai_pma_dominan;
                    }else{
                        $model2->nilai_pma_dominan  = 0;
                    }
                    if(array_key_exists('nilai_pmdn', $dataNIB)) {
                        $model2->nilai_pmdn  = $dataNIB->nilai_pmdn;
                    }else{
                        $model2->nilai_pmdn  = 0;
                    }
                    if(array_key_exists('persen_pma', $dataNIB)) {
                        $model2->persen_pma  = $dataNIB->persen_pma;
                    }else{
                        $model2->persen_pma  = 0;
                    }                
                    if(array_key_exists('persen_pmdn', $dataNIB)) {
                        $model2->persen_pmdn  = $dataNIB->persen_pmdn;
                    }else{
                        $model2->persen_pmdn  = 0;
                    }                                
                    if(array_key_exists('pemegang_saham', $dataNIB)) {
                        $model2->data_saham  = addslashes(json_encode($dataNIB->pemegang_saham));
                    }else{
                        $model2->data_saham  = "-";
                    }                                         
                    
                    $checkLastNIB = $this->pdb->IsExistNibPerusahaan($dataNIB->nib);
                    if($checkLastNIB){     
                        $resultPerusahaan = $this->pdb->UpdatePerusahaan($model2);
                    }else{
                        $resultPerusahaan = $this->pdb->PostDataPerusahaan($model2);
                    }
    
                    $model3 = new stdClass();
                    $model3->id = $data->id_data_nib;
                    $model3->id_perusahaan = $resultPerusahaan;
                    $model3->kode_izin  = $dataNIB->kd_izin;                               
                    if(array_key_exists('id_izin', $dataNIB)) {
                        $model3->id_izin  = $dataNIB->id_izin;
                    }else{
                        $model3->id_izin  = "-";
                    }
                    $model3->id_nib_status = 1;  
                    if($dataNIB->kd_izin == "059000000001"){
                        if($data->id_jenis_izin == 1){
                            $model3->id_izin_jenis = 7;
                        }else if($data->id_jenis_izin == 3){                                                                        
                            $model3->id_izin_jenis = 6;
                        }else if($data->id_jenis_izin == 5){                                                                        
                            $model3->id_izin_jenis = 1;
                        }else{
                            $model3->id_izin_jenis = 7;
                        }
                    }else if($dataNIB->kd_izin == "059000000002"){                    
                        $model3->id_izin_jenis = 2;
                    }else if($dataNIB->kd_izin == "059000000003"){                    
                        $model3->id_izin_jenis = 3;
                    }else if($dataNIB->kd_izin == "059000000005"){                    
                        $model3->id_izin_jenis = 4;
                    }
                    $resultPerusahaanJenis = $this->modb->PostDataPerusahaanJenis($model3);
                }        
            }
                
        }        

        $status 	= 'TRUE';
		if ($resultNIB == false && $resultPemohon == null && $resultPerusahaan == false && $resultPerusahaanJenis == false) {
			$status 	= 'FAIL TO AUTHENTICATE';
            return response()->json(['status' => $status, 'code' => 400]);
		} else{
            return response()->json(['status' => $status, 'code' => 200]);
        }        
    }

    public function UserLama(Request $re){     
        $tanggal_now  = Carbon::now();
        $tanggal_string = $tanggal_now->format('Y-m-d H:i:s');

        $res = $this->modb->GetOldUser($re->awal, $re->akhir);
        
        foreach($res as $data){ 
            $model = new stdClass();
            $model->id = $data->id_jabatan;
            $model->id_parent = 0;
            $model->id_unit_teknis = 1;
            $model->level = 0;
            $model->nama_jabatan = $data->nm_jabatan;
                   
            $result2 = $this->modb->PostJabatan($model);
            
            $model2 = new stdClass();
            $model2->id = $data->id_user;
            $model2->id_jabatan = $data->id_jabatan;
            $model2->email = $data->email;
            $model2->nama = $data->nm_user;
            $model2->sandi = Hash::make("asdf12345");
            $model2->user_name = $data->username;
            $model2->id_user_status = 1;
            $model2->tanggal_input = $tanggal_string;
            $model2->tanggal_update = $tanggal_string;
            $model2->tanggal_update_pass = $tanggal_string;
            $model2->id_user_role = 2;

            $result = $this->modb->PostUserBO($model2);
        }
    }
}
