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
use Exception;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;
use stdClass;

use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class MigrasiPenomoranController extends Controller
{
    private $pdbOld;
    private $pdbNew;
    private $perusahaan;
    private $berkas;
    public function __construct()
    {
        #base_url
        $this->pdbOld = new SipppdihatiOldDb();
        $this->pdbNew = new SipppdihatiNewDb();
        $this->perusahaan = new MigrasiPerusahaanController();
        $this->pdbDisposisiTel = new PermohonanDisposisiTelDb();
        $this->berkas = new MigrasiBerkasController();
    }

    public function MigrasiPenomoran(){

        DB::beginTransaction();
        try {
            ini_set('memory_limit', '4096M');
            ini_set('MAX_EXECUTION_TIME', '-1');
            ini_set('post_max_size', '4096M');
            ini_set('upload_max_filesize', '4096M');

            $data_lama = $this->pdbOld->GetTablePermohonanPenomoranSipppdihati();
            
            if(!empty($data_lama)){
                foreach($data_lama as $data){
                    //if($data->id_permohonan == 1556){
                        $this->perusahaan->findPerusahaan($data);
                        if(!empty($data->id_perusahaan)){
                            $this->CreatePermohonanNew($data);
                        }
                    //}
                }
            }

        }
            catch(Exception $ex)
        {
            DB::rollBack();
            throw $ex;
        }
        DB::commit();
        return response()->json(['message' => "OK", 'code' => 200]);
    }

    public function CreatePermohonanNew($data){

        
        $isExist = $this->pdbNew->GetDataPenomoranByNoSkIzinandIdIzinJenis($data);
        if(!empty($isExist)){
            return null;
        }

        if($data->id_jenis_izin == 7){
            //penetapan baru
            $this->penetapanBaru($data); 
        }
        
        if($data->id_jenis_izin == 51){
            //penyesuain kode akses
            $this->penyesuaianKode($data);
        }        
    }

    public function penetapanBaru($data){

        
        #data id jenis izin
        $data->id_jenis_izin = 5;

        //p_permohonan
        $data_perm_new = $this->pdbNew->createPermohonanPenomoran($data);
      
        //update status permohonan
        $status = $this->GetstatusPermohonan($data);
       
        #find penomoran sipppdihati
        $kode_akses = $this->pdbOld->FindTablePenomoranSipppdihati($data->id_permohonan);
        
        if(!empty($kode_akses)){

            foreach($kode_akses as $kde){

                #find nomor sk no penetapan
                $m_pertel = $this->pdbOld->FindTableMPenomoranPrima($kde->nilai_string);
                if(!empty($m_pertel)){

                    if($m_pertel->no_penetapan != "520 Tahun 2014"){

                        $m_penomoran_tel_list = $this->pdbNew->UpdatePenomoranTel($kde->nilai_string);                   
                        if(empty($m_penomoran_tel_list)){
                            //m penomoran tel list 
                            $m_penomoran_tel_list = $this->pdbNew->createPenomoranTel($kde->nilai_string, $m_pertel->id_jns_penomoran);
                        }

                        //p_permohonan_penomoran_tel
                        $p_penomoran_tel = $this->pdbNew->CreatePermohonanPenomoranTel($data_perm_new->id, $m_penomoran_tel_list->id_penomoran_tel);
                        if(!empty($p_penomoran_tel)){

                            if(!empty($status)){

                                //Find No penetapan
                                if($status['data_aktif'] == 2){
                                    
                                    //flagging data pencabutan
                                    $flagging_data = $this->pdbOld->findFlaggingdataCabutPenomoran($data->id_permohonan);

                                }else{
                                    
                                    //flagging data penomran
                                    $flagging_data = $this->pdbOld->getFlaggingdata($data->id_permohonan);

                                }

                            }

                            
                            
                            if(!empty($flagging_data)){

                                //P PENOMORAN TEL PAKAI
                                $p_penomoran_tel_pakai = $this->pdbNew->CreatePenomoranTelPakai($data_perm_new, $m_penomoran_tel_list, $flagging_data->text);
                                if(!empty($p_penomoran_tel_pakai)){

                                    if(!empty($status['histori_aktif'])){

                                        $this->GetSkPenomoranAndPencabutan($status['histori_aktif'], $p_penomoran_tel_pakai, $data_perm_new, $status['data_aktif'], $data->id_permohonan);

                                    }

                                }

                            }
                            

                            #histori and log permohonan
                            $data_histori = $this->pdbOld->GetTableHistoriPermohonan($data->id_permohonan);   

                            if(!empty($data_histori)){

                                #data histori
                                $this->CreateHistoriPermohonan($data_histori, $data_perm_new);

                            }
                        }
                        
                    }else{
                       
                        $this->pdbNew->UpdatePenomoranTel($kde->nilai_string);
                    
                    } 
                }
            }
        }
    }

    public function penyesuaianKode($data){

        //data id jenis izin
        $data->id_jenis_izin = 5;

        //p_permohonan
        $data_perm_new = $this->pdbNew->createPermohonanPenomoran($data);

        //update status permohonan
        $status = $this->GetstatusPermohonan($data);
        
        $kde = $this->pdbOld->FindTablePenomoranSipppdihati1($data->id_permohonan);

        if(!empty($kde)){
            
            #find nomor sk no penetapan
            $m_pertel = $this->pdbOld->FindTableMPenomoranPrima($kde->nilai_string);

            if(!empty($m_pertel)){

                if($m_pertel->no_penetapan != "520 Tahun 2014"){

                    $m_penomoran_tel_list = $this->pdbNew->UpdatePenomoranTel($kde->nilai_string);
                    if(empty($m_penomoran_tel_list)){

                        //m penomoran tel list 
                        $m_penomoran_tel_list = $this->pdbNew->createPenomoranTel($m_pertel->nomor, 9);
                    
                    }
    
                    //p_permohonan_penomoran_tel
                    $p_penomoran_tel = $this->pdbNew->CreatePermohonanPenomoranTel($data_perm_new->id, $m_pertel->id_jns_penomoran);
                    if(!empty($p_penomoran_tel)){

                        if(!empty($status)){

                            //Find No penetapan
                            if($status['data_aktif'] == 2){
                                    
                                //flagging data pencabutan
                                $flagging_data = $this->pdbOld->findFlaggingdataCabutPenomoran($data->id_permohonan);

                            }else{
                                
                                //flagging data penomran
                                $flagging_data = $this->pdbOld->getFlaggingdata($data->id_permohonan);

                            }
                        }
                      
                        if(!empty($flagging_data)){
                        
                            //P PENOMORAN TEL PAKAI
                            $p_penomoran_tel_pakai = $this->pdbNew->CreatePenomoranTelPakai($data_perm_new, $m_penomoran_tel_list, $flagging_data->text);
                        
                            if(!empty($p_penomoran_tel_pakai)){

                                if(!empty($status['histori_aktif'])){

                                    $this->GetSkPenomoranAndPencabutan($status['histori_aktif'], $p_penomoran_tel_pakai, $data_perm_new, $status['data_aktif'], $data->id_permohonan);

                                }
                            }
                        }

                        //upload file berkas penomoran
                        $berkas_penomoran = $this->pdbOld->getFilePenomoran($data->id_permohonan);
                        if(!empty($berkas_penomoran)){                         
                           
                            foreach($berkas_penomoran as $bks_pnmrn)
                            {
                                //find kelengkapan file
                                $klngpn_file = $this->findMsyaratS($bks_pnmrn);
                                
                                if(!empty($klngpn_file)){
                                    $this->berkas->createBerkasKelengkapanFile($bks_pnmrn, $klngpn_file, $p_penomoran_tel);
                                }

                            }

                        }
                        
                        #histori and log permohonan
                        $data_histori = $this->pdbOld->GetTableHistoriPermohonan($data->id_permohonan);   
                        if(!empty($data_histori)){
                            
                            #data histori
                            $this->CreateHistoriPermohonan($data_histori, $data_perm_new);
                        
                        } 

                    }

                }else{

                    $this->pdbNew->UpdatePenomoranTel($kde->nilai_string);
               
                }
            }
        }
    }

    public function GetstatusPermohonan($data)
    {
        //define 
        $result = array();

        $histori_aktif = $this->pdbOld->GetTableHistoriPermohonanAktf($data->id_permohonan);

        if(!empty($histori_aktif)){

            if(in_array($histori_aktif->id_aktivitas_workflow, [33, 38, 43, 52, 55, 61, 67, 75, 83, 98, 103, 104, 112, 126, 159, 176, 187, 215, 237, 263])){
                $data->aktif = 2;
            }else if(in_array($histori_aktif->id_aktivitas_workflow, [28, 84, 111, 3, 15, 19, 24, 31, 36, 41, 59, 65, 72, 80, 94, 122, 137, 154, 174, 185, 191, 197, 211, 233, 259])){
                $data->aktif = 1;
            }else{
                $data->aktif = 0;
            }

            $result['histori_aktif'] = $histori_aktif->id_aktivitas_workflow;
            $result['data_aktif'] = $data->aktif;
             
        }

        return $result; 
    }


    public function GetSkPenomoranAndPencabutan($histori_aktif, $p_penomoran_tel_pakai, $data_perm_new, $data_aktif, $id_permohonan){

        if(in_array($histori_aktif, [33, 38, 43, 52, 55, 61, 67, 75, 83, 98, 103, 104, 112, 126, 142, 159, 176, 187, 215, 237, 263])){
    
            $flagging_data = $this->pdbOld->findFlaggingdataCabutPenomoran($id_permohonan);  
            if(!empty($flagging_data)){

                $rekom = $this->pdbOld->findRekomTerbit($id_permohonan);
                if(!empty($rekom)){

                    //p_sk_penomoran_pencabutan
                    $this->berkas->CreateSkPencabutanPenomoranFile($rekom, $flagging_data, $p_penomoran_tel_pakai->id);

                }
            }        
        }

       
        if(in_array($histori_aktif,[28, 84, 111, 3, 15, 19, 24, 31, 36, 41, 59, 65, 72, 80, 94, 122, 137, 154, 174, 185, 191, 211, 233, 259])){

            $flagging_data = $this->pdbOld->getFlaggingdata($id_permohonan);  
            if(!empty($flagging_data)){

                //p_sk_penomoran_file
                $this->berkas->CreateBerkasSkPenomoran($id_permohonan, $p_penomoran_tel_pakai->id);
            
            }
        
        }

        $this->pdbNew->UpdatePermohonan($data_perm_new, $data_aktif);

    }




    public function CreateHistoriPermohonan($data_histori, $data_perm_new){

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

            //create p permohonan log
            $this->pdbNew->createPermohonanLog($data_log);

            #create disposisi staf
            $this->finduser($data_perm_new, $histori);
                         
            $perm_info = $this->pdbNew->getPermohonanInfo($data_perm_new->id);
            
            if($perm_info == null){

                //p_permohonan_info    
                $this->pdbNew->CreatePermohonanInfo($data_perm_new, $histori->waktu_in, $data_log->status);

            }else{

                //update p_permohonan_info
                $this->pdbNew->UpdatePermohonanInfo($data_perm_new, $histori->waktu_in, $data_log->status);
            }
        }
    }

    public function finduser( $data_perm_new,$histori)
    {
        if(property_exists($histori,'id_user')){
            $this->pdbNew->DisposisiStaf($data_perm_new, $histori);
        }
    }
    
    public function findMsyaratS($bks_pnmrn)
    {
        switch($bks_pnmrn->id_syarat_izin_s) {
           case 294:
               $result = 4;
               break;
           case 296:
               $result = 38;
               break;
           case 298:
               $result = 39;
               break;    
           case 299:
               $result = 40;
               break;
           default:
             $result = null;
       }
       return $result;
    }
}
