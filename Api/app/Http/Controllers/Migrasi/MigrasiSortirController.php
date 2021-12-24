<?php

namespace App\Http\Controllers\Migrasi;

use App\Enums\TypeIzinJenisPos;
use App\Enums\TypeIzinJenisTel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repo\PermohonanInfoDb;
use App\Repo\Migrasi\SipppdihatiOldDb;
use App\Repo\Migrasi\SipppdihatiNewDb;
use App\Repo\Tel\PermohonanDisposisiTelDb;
use DateTime;
use Exception;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;
use stdClass;
use File;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class MigrasiSortirController extends Controller
{
    private $pdbOld;
    private $pdbNew;
    private $berkas;
    private $perusahaan;
    
    public function __construct()
    {
        $this->pdbOld = new SipppdihatiOldDb();
        $this->pdbNew = new SipppdihatiNewDb();
        $this->perusahaan = new MigrasiPerusahaanController();
        $this->berkas = new MigrasiBerkasController();
    }
    
    public function Sortirfile(Request $re){
        
        ini_set('memory_limit', '4096M');
        ini_set('MAX_EXECUTION_TIME', '-1');
        ini_set('post_max_size', '4096M');
        ini_set('upload_max_filesize', '4096M');
    
        //sk_izin
        //$data_sk = $this->pdbNew->getTableSk();
        // if(!empty($data_sk)){
        //     foreach($data_sk as $dt_sk){
        //         //data izin
        //         $data = @file_get_contents('D:/berkas/data_izin/'.$dt_sk->Izin_PDF.'');
        //         if($data != FALSE){   
        //             //copy file
        //             copy('D:/berkas/data_izin/'.$dt_sk->Izin_PDF.'', 'D:/berkas/sk_izin/'.$dt_sk->Izin_PDF.'');         
        //         } 
        //         //data sk pemenuhan komitmen
        //         $data = @file_get_contents('D:/berkas/data_sk_pemenuhan/'.$dt_sk->Penetapan_Komitmen.'');
        //         if($data != FALSE){   
        //             //copy file
        //             copy('D:/berkas/data_sk_pemenuhan/'.$dt_sk->Penetapan_Komitmen.'', 'D:/berkas/sk_komitmen/'.$dt_sk->Penetapan_Komitmen.'');                  
        //         } 
        //         //data sklo
        //         $data = @file_get_contents('D:/berkas/data_uji/'.$dt_sk->SKLO.'');
        //         if($data != FALSE){   
        //             //copy file
        //             copy('D:/berkas/data_uji/'.$dt_sk->SKLO.'', 'D:/berkas/sklo/'.$dt_sk->SKLO.'');          
        //         } 
        //         $data = @file_get_contents('D:/berkas/data_izin/'.$dt_sk->SKLO.'');
        //         if($data != FALSE){   
        //             //copy file
        //             copy('D:/berkas/data_izin/'.$dt_sk->SKLO.'', 'D:/berkas/sklo/'.$dt_sk->SKLO.'');           
        //         } 
        //     }
        // } 

        //get data sk 2021
        //$data_sk2021 = $this->pdbNew->getTableSk2021();
        //if(!empty($data_sk2021)){
            //foreach($data_sk2021 as $dt_sk){
                //$get_sk_izin = $this->pdbNew->getTableSkizinFile($dt_sk->Izin_PDF);
                // Convert blob (base64 string) back to PDF
                //if (!empty($get_sk_izin->stream)) {
                    //$base64data = base64_decode($get_sk_izin->stream, true);
                    
                    //Return the number of bytes saved, or false on failure
                    //$result = file_put_contents("D:/berkas/sk_izin_21/$get_sk_izin->nama", $base64data);
                //}
                //$get_sk_izin = $this->pdbNew->getTableSPenetapanFile($dt_sk->Penetapan_Komitmen);
                // Convert blob (base64 string) back to PDF
                //if (!empty($get_sk_izin->stream)) {
                    //$base64data = base64_decode($get_sk_izin->stream, true);
                    
                    //Return the number of bytes saved, or false on failure
                    //$result = file_put_contents("D:/berkas/sk_komitmen_21/$get_sk_izin->nama", $base64data);
                //}
                //$get_sk_izin = $this->pdbNew->getTableSkloFile($dt_sk->SKLO);
                // Convert blob (base64 string) back to PDF
                //if (!empty($get_sk_izin->stream)) {
                    //$base64data = base64_decode($get_sk_izin->stream, true);
                    
                    //Return the number of bytes saved, or false on failure
                    //$result = file_put_contents("D:/berkas/sklo_21/$get_sk_izin->nama", $base64data);
                //}
            //}
        //}

        //get data file komitmen file
        $data_komit_file = $this->pdbNew->getTableKomitmenfile();
        if(!empty($data_komit_file)){
            foreach($data_komit_file as $dt){
                if (!empty($dt->stream)) {
                    
                    $base64data = base64_decode($dt->stream, true);
                    
                    //Return the number of bytes saved, or false on failure
                    $result = file_put_contents("D:/berkas/komitmen_file/$dt->nama", $base64data);
                }   
            }
        }


        
    } 

    public function MigrasiPos(Request $re)
    {   
        ini_set('memory_limit', '4096M');
        ini_set('MAX_EXECUTION_TIME', '-1');
        ini_set('post_max_size', '4096M');
        ini_set('upload_max_filesize', '4096M');
        DB::beginTransaction();
        try {
            $dataLama = $this->pdbOld->GetTablePermohonanbyPos();
            
            if(!empty($dataLama)){
                foreach($dataLama as $data){
                    
                    $this->perusahaan->findPerusahaan($data);
                    if(!empty($data->id_perusahaan)){
                        
                        $result = $this->CreatePermohonanNew($data);
                        if($result != null){
                            #sk_pemenuhan_komitmen
                            $this->CreatePemenuhanKomitmen($data, $result['data_perm_new'], $result['data_layanan_new']);
                            #bukti bayar
                            $this->berkas->buktibayar($data, $result['data_perm_new']);
                            #sk file
                            $this->berkas->CreatePskFile($result['data_perm_new'], $data);
                        }
                        
                    }
                }       
            }

        }catch(Exception $ex)
        {
            DB::rollBack();
            throw $ex;
        }
        DB::commit();    
        return response()->json(['message' => "OK", 'code' => 200]);
    }

    public function CreatePermohonanNew($data)
    {

        $data_perm_new = array();

        $jenis_izin_new = $this->GetIdjenisNew($data->id_jenis_izin);
        
        if($jenis_izin_new != null){
            $data->id_jenis_izin =  $jenis_izin_new;
            $isExist = $this->pdbNew->GetDataPermohonanByNoSkIzinandIdIzinJenis($data);
            if($isExist != null){
                return null;
            }
        }else{
           return null; 
        }

        #p_permohonan
        $data_perm_new = $this->pdbNew->createPermohonan($data);

        $data_histori = $this->pdbOld->GetTableHistoriPermohonan($data->id_permohonan);

        if(!empty($data_histori) && !empty($data_perm_new)){
            
            #update status permohonan
            $this->GetstatusPermohonan($data_perm_new, $data);

            #data histori
            $this->CreateHistoriPermohonan($data_histori, $data_perm_new);

        }

        $data_layanan_new = array();

        $jenis_lyn_olds = $this->pdbOld->GetTableSyaratIzinPpos($data->id_permohonan); 

        if(!$jenis_lyn_olds->isEmpty()){
           
            foreach($jenis_lyn_olds as $layanan){
            
                $jenis_layanan = $this->pdbNew->GetIdJenisLyn($data->id_jenis_izin, $layanan->nilai_string);

                if(!empty($jenis_layanan)){

                    foreach($jenis_layanan as $jenis_lyn_new){

                        #p_permohonan_layanan_pos_perusahaan
                        $this->pdbNew->CreatePermohonanLynPosPerusahaan($data ,$jenis_lyn_new->id, $data_perm_new);

                        #p_permohonan_layanan
                        $layanan_new = $this->pdbNew->CreatePermohonanLayanan($data_perm_new->id, $jenis_lyn_new->id);

                    }
                }
            } 
        }

        $result['data_perm_new'] = $data_perm_new;
        $result['data_layanan_new'][] = $layanan_new;
        return $result;

    }

    public function CreatePemenuhanKomitmen($data, $data_perm_new, $data_layanan_new)
    {
        #check if data izin efektif
        //cek udah mengajukan permohonan komitmen apa belum
        $permohonan_komit_old = $this->pdbOld->GetTablePermohonanByNoizin($data->no_izin, $data->id_data_nib);
        if(!empty($permohonan_komit_old)){

            if($data->aktif == 2){
                $id_kelengkapan_status = 3;
            }else if($data->aktif == 1){
                $id_kelengkapan_status = 4;
            }else if ($data->aktif == 0){
                $id_kelengkapan_status = 4;
            }else{
                $id_kelengkapan_status = 1;
            }

            if(empty($data_layanan_new[0])){

                $data_layanan_new = array();

                $jenis_lyn_olds = $this->pdbOld->GetTableSyaratIzinPpos($permohonan_komit_old->id_permohonan); 

                if(!$jenis_lyn_olds->isEmpty()){
                
                    foreach($jenis_lyn_olds as $layanan){
                    
                        $jenis_layanan = $this->pdbNew->GetIdJenisLyn($permohonan_komit_old->id_jenis_izin, $layanan->nilai_string);

                        if(!empty($jenis_layanan)){

                            foreach($jenis_layanan as $jenis_lyn_new){

                                #p_permohonan_layanan_pos_perusahaan
                                $this->pdbNew->CreatePermohonanLynPosPerusahaan($permohonan_komit_old ,$jenis_lyn_new->id, $data_perm_new);

                                #p_permohonan_layanan
                                $layanan_new = $this->pdbNew->CreatePermohonanLayanan($data_perm_new->id, $jenis_lyn_new->id);

                                if(!empty($layanan_new)){
                                    $data_layanan_new[0] = $layanan_new;
                                }
                            }
                        }
                    } 
                }
            }
            
            #data_layanan_new
            if(!empty($data_layanan_new[0])){

                #p_permohonan_komit
                $data_komit_new = $this->pdbNew->CreatePermohonanKomit($permohonan_komit_old, $data_perm_new->id, $id_kelengkapan_status);     
                if(!empty($data_komit_new)){

                    #cakupan wilayah
                    $this->cakupanWilayah($data, $data_perm_new);

                    $data_histori = $this->pdbOld->GetTableHistoriPermohonan($permohonan_komit_old->id_permohonan);
                    
                    if(!empty($data_histori) && !empty($data_perm_new)){
                        #update status permohonan
                        $this->GetstatusPermohonan($data_perm_new, $permohonan_komit_old);
                        
                        #create data histori
                        $this->CreateHistoriPermohonan($data_histori, $data_perm_new);

                    } 

                    foreach($data_layanan_new as $jenis_layanan){

                        #p_permohonan_komit_layanan
                        $komit_layanan_new= $this->pdbNew->CreatePermohonanKommitlayanan($data_komit_new->id, $jenis_layanan->id);               
                        if(!empty($komit_layanan_new)){

                            $m_syarat_izin_grup = $this->pdbOld->GetMSyaratIzinGroup($permohonan_komit_old->id_jenis_izin);

                            if(!empty($m_syarat_izin_grup)){

                                foreach($m_syarat_izin_grup as $syarat_grup){

                                    $syarat_komit = $this->pdbOld->GetMsyaratIzinSbyIdIzinGrup($syarat_grup->id_syarat_izin_grup);

                                    if(!empty($syarat_komit)){

                                        foreach($syarat_komit as $komitmen){

                                            $k_kelengkapans = $this->pdbNew->findSyaratKomitmenPos($komitmen, $jenis_layanan);
                                            
                                            if($k_kelengkapans->isNotEmpty()){

                                                foreach($k_kelengkapans as $k_kelengkapan){                        

                                                    $syarat_komitmens = $this->pdbOld->GetTableTsyaratIzinFByIdsIdPermohonan($permohonan_komit_old->id_permohonan, $komitmen->id_syarat_izin_s);
                                                
                                                    $akhir =(count($syarat_komitmens) - 1);

                                                    if($syarat_komitmens[$akhir]->catatan == 'Sesuai'){
                                                        $id_permohonan_k_kelengkapan_status = 4;
                                                    }else{
                                                        $id_permohonan_k_kelengkapan_status = 2;
                                                    }

                                                    #p_permohonan_komit_kelengkapan
                                                    $permohonan_komit_kelengkapan = $this->pdbNew->CreatePermohonanKomitKelengkapan($komit_layanan_new->id, $k_kelengkapan->id, $id_permohonan_k_kelengkapan_status);
                                                    
                                                    if($syarat_komitmens[$akhir]->date_added == null){
                                                        $syarat_komitmens[$akhir]->date_added = $permohonan_komit_old->tgl_permohonan;
                                                    }

                                                    if($syarat_komitmens[$akhir]->catatan != null){
                                                        #p_permohonan_komit_kelengkapan_catatan
                                                        $this->pdbNew->CreatePermohonanKomitkelengkapanCatatan($permohonan_komit_kelengkapan->id, $syarat_komitmens[$akhir]->catatan, $syarat_komitmens[$akhir]->date_added);
                                                    }
                                                    
                                                    foreach ($syarat_komitmens as $syarat_komitmen){   

                                                        #p_permohinan_komit_kelengkapan_file
                                                        $this->berkas->CreatePermohonanKomitKelengkapanFilePos($permohonan_komit_kelengkapan,$syarat_komitmen);
                                                        
                                                    }
                                                }
                                            } 
                                        }
                                    }
                                }
                            }                                                                                                           
                        }
                    }
                }
            }
        } 
    }

    public function CreateHistoriPermohonan($data_histori, $data_perm_new)
    {
        foreach($data_histori as $histori){    
            
            $data_log =  new stdClass();
            $data_log->status = '';
            $data_log->nama = '';
            $data_log->jabatan = '';
            $data_log->id_permohonan = '';
            $data_log->tanggal_input = '';
            $data_log->catatan = '';
            $data_log->id_user = '';

            if(!empty($histori->id_aktivitas_workflow)){
                $t_aktivitas_workflow = $this->pdbOld->GetTableAktivitasWorkFlow($histori->id_aktivitas_workflow);
                if(!empty($t_aktivitas_workflow)){                                   
                    $data_log->status = $t_aktivitas_workflow[0]->nm_aktivitas_workflow;
                }
            }

            
            if(!empty($histori->id_user)){
               
                $m_user = $this->pdbOld->GetTableMUser($histori->id_user);                                
                if(!empty($m_user))
                {
                    $data_log->nama = $m_user->username;
                    $m_role = $this->pdbOld->GetTableMRole($m_user->id_role);
                    if(!empty($m_role))
                    {
                        $data_log->jabatan = $m_role->nm_role;
                    }
                }
                
            }
                        
            if(!empty($data_perm_new)){
                $data_log->id_permohonan = $data_perm_new->id;
            }
       
            if(!empty($histori->waktu_in)){
                $data_log->tanggal_input = $histori->waktu_in;
            }

            if(!empty($histori->catatan)){
                $data_log->catatan = $histori->catatan;
            }

            #p_permohonan_log
            $this->pdbNew->createPermohonanLog($data_log);  
    
            $this->finduser($data_perm_new, $histori);
            
            $perm_info = $this->pdbNew->getPermohonanInfo($data_perm_new->id);
            
            if($perm_info == null){
                #p_permohonan_info    
                $this->pdbNew->CreatePermohonanInfo($data_perm_new, $histori->waktu_in, $data_log->status);
            }else{
                #update p_permohonan_info
                $this->pdbNew->UpdatePermohonanInfo($data_perm_new, $histori->waktu_in, $data_log->status);
            }
 
            #p_sk_pencabutan
            if(in_array($histori->id_aktivitas_workflow, [33, 38, 43, 52, 61, 67, 75, 83, 98, 103, 104, 112, 126, 159, 176, 187, 215, 237, 263])){

                $rekom_terbit = $this->pdbOld->getTableRekomTerbit($histori);

                if(!empty($rekom_terbit)){

                    foreach($rekom_terbit as $rekom){

                        $flagging_data = $this->pdbOld->findFlaggingdata($rekom);
                        
                        if(!empty($flagging_data)){

                            $this->berkas->CreateSkPencabutanFile($rekom, $flagging_data);

                        }
                    }
                }           
            }
        }

        #update status bukti bayar
        $perm_info = $this->pdbNew->getPermohonanInfo($data_perm_new->id);
        if($perm_info->value != 'Upload Bukti Bayar'){
            if($perm_info->value != 'Verifikasi SPM'){
                if($perm_info->value != 'Drop Izin'){
                $this->pdbNew->UpdateBuktiBayar($data_perm_new->id);
                }
            }   
        }
    }

    public function GetstatusPermohonan($data_perm_new, $data)
    {

        $histori_aktif = $this->pdbOld->GetTableHistoriPermohonanAktf($data->id_permohonan);

        if(in_array($histori_aktif->id_aktivitas_workflow, [33, 38, 43, 52, 61, 67, 75, 83, 98, 103, 104, 112, 126, 159, 176, 187, 215, 237, 263])){
            $data->aktif = 2;
        }else if(in_array($histori_aktif->id_aktivitas_workflow, [3, 15, 19, 24, 31, 36, 41, 59, 65, 72, 80, 94, 122, 137, 154, 174, 185, 191, 197, 211, 233, 259])){
            $data->aktif = 1;
        }else{           
            $data->aktif = 0;
        }
        #update p_permohonan
        $this->pdbNew->UpdatePermohonan($data_perm_new, $data->aktif);

    }
    
    public function GetIdjenisNew($id_jenis)
    {
        
        switch($id_jenis) {
            case 1:
               $id_izin_jenis = TypeIzinJenisPos::Nasional;
               break;
            case 3:
               $id_izin_jenis = TypeIzinJenisPos::KabupatenKota;
               break;
            case 5:
               $id_izin_jenis = TypeIzinJenisPos::Provinsi;            
               break;
            case 19:
               $id_izin_jenis = TypeIzinJenisPos::Nasional;            
               break;
            case 20:
                $id_izin_jenis = TypeIzinJenisPos::KabupatenKota;            
                break;
            case 21:
                $id_izin_jenis = TypeIzinJenisPos::Provinsi;            
                break;         
            default:
              $id_izin_jenis = null;
        }
        return $id_izin_jenis;

    }

    public function cakupanWilayah($data, $data_perm_new)
    {

        $cakupan_wilayah = $this->pdbOld->GetTableSyaratIzinPposCakWilayah($data->id_permohonan);

        if(!empty($cakupan_wilayah)){

            foreach($cakupan_wilayah as $wilayah){

                $nilai_string = strtoupper($wilayah->nilai_string);

                if($nilai_string){

                    $provinsi = $this->pdbNew->GetProvinsi($nilai_string);

                    $kabupaten = $this->pdbNew->GetKabKota($nilai_string);

                    if(!empty($provinsi)){

                        #p_provinsi
                        $this->pdbNew->CreatePermohonanWilayah($data_perm_new->id, $provinsi->id_provinsi);

                    }else if(!empty($kabupaten)){

                        #p_kabupaten
                        $this->pdbNew->CreatePermohonanWilayah($data_perm_new->id, $kabupaten->id);

                    }
                }
            }
        }
    }

    public function finduser($data_perm_new, $histori)
    {
        if(property_exists($histori,'id_user')){
        
            if($histori->id_user == 30){
                $histori->id_user = 2;
            }

            #p_disposisi_staff
            $this->pdbNew->DisposisiStaf($data_perm_new, $histori);
            
        }
    }

}
