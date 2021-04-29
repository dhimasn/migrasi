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
    public function __construct()
    {
        #base_url
        $this->pdbOld = new SipppdihatiOldDb();
        $this->pdbNew = new SipppdihatiNewDb();
        $this->perusahaan = new MigrasiPerusahaanController();
        $this->pdbDisposisiTel = new PermohonanDisposisiTelDb();
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
                    $this->perusahaan->findPerusahaan($data);
                    if(!empty($data->id_perusahaan)){
                        $data = $this->CreatePermohonanNew($data);    
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

    public function CreatePermohonanNew($data){

    
        //data id jenis izin
        $data->id_jenis_izin = 5;

        $isExist = $this->pdbNew->GetDataPenomoranByNoSkIzinandIdIzinJenis($data);
        if(!empty($isExist)){
            return null;
        }

        #p_permohonan
        $data_perm_new = $this->pdbNew->createPermohonanPenomoran($data);
        
        $data_histori = $this->pdbOld->GetTableHistoriPermohonan($data->id_permohonan);   
       
        if(!empty($data_histori)){

            #update status permohonan
            $status = $this->GetstatusPermohonan($data_perm_new, $data);
            
            #data histori
            $this->CreateHistoriPermohonan($data_histori, $data_perm_new, false);

        }

        $sub_jenis_izin = $this->pdbOld->FindNewSubJenisIzin($data);
        if(!empty($sub_jenis_izin)){

            $jenis_izin = $this->pdbNew->FindMPenomoranTel($sub_jenis_izin->nm_sub_jenis_izin);
            if(empty($jenis_izin)){
                
                $jenis_izin_penomoran = $this->FindMpenomoran($sub_jenis_izin->nm_sub_jenis_izin);

            }

           $penomoran = $this->pdbOld->FindTableMPenomoran($data);

            if(empty($penomoran)){
                $this->pdbNew->CrateMpenomoranTel($data, $jenis_izin, $penomoran);
            }

        }

        #p_penomoran_tel_pakai
        $this->pdbNew->CreatePenomoranTelPakai($data);

        return $data_perm_new;  
    
    }

    public function FindMPenomoran($nm_sub_jenis_izin)
    {
        switch($nm_sub_jenis_izin) {
            case "Kode Wilayah + Blok Nomor":
                $result = null;
                break;
           case "NDC (National Destination code)":
                $result = null;
                break;
           case "SPC (Signalling Point Code)":
                $result = null;
                break;
           case "ISPC (International Signalling Point Code)":
                $result = null;
                break;    
           case "PLMNID (Public Land Mobile Network Identity)":
                $result = null;
                break;
           case "Intelligent Network":
                $result = null;
                break;
           case "Kode Akses SLJJ":
                $result = null;
                break;
           case "Kode Akses SLI":
                $result = null;
                break;
           case "Kode Akses Call Center":
                $result = null;
                break;
           case "Kode Akses Calling Card":
                $result = null;
                break;
           case "Kode Akses ITKP":
                $result = null;
                break;
           case "Kode Akses Konten":
                $result = null;
                break;
           case "Kode Akses Layanan Masyarakat (Layanan Suara)":
                $result = null;
                break;
           case "Kode Akses Layanan Masyarakat (Pesan Singkat)":
                $result = null;
                break;
           default:
                $result = null;
       }
       return $result;
    }

    public function GetstatusPermohonan($data_perm_new, $data)
    {

        $histori_aktif = $this->pdbOld->GetTableHistoriPermohonanAktf($data->id_permohonan);
       
        if(in_array($histori_aktif->id_aktivitas_workflow, [33, 38, 43, 52, 55, 61, 67, 75, 83, 98, 103, 104, 112, 126, 159, 176, 187, 215, 237, 263])){
            $data->aktif = 2;
        }else if(in_array($histori_aktif->id_aktivitas_workflow, [3, 15, 19, 24, 31, 36, 41, 59, 65, 72, 80, 94, 122, 137, 154, 174, 185, 191, 197, 211, 233, 259])){
            $data->aktif = 1;
        }else{
            $data->aktif = 0;
        }

        $this->pdbNew->UpdatePermohonan($data_perm_new, $data->aktif);
        return  $data->aktif;
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

            if(!empty($histori->id_status_permohonan)){
                $status_permohonan = $this->pdbOld->GetTableStatusPermohonan($histori->id_status_permohonan);
                if($status_permohonan != null){
                    $data_log->status = $status_permohonan->status;
                } 
            }

            
            if($histori->id_personil){
                $m_personil = $this->pdbOld->GetTablePersonil($histori->id_personil); 
                if(!empty($m_personil))
                {
                    $data_log->nama = $m_personil->nama;
                    $jabatan = $this->pdbOld->GetTableJabatan($m_personil->id_jabatan);
                    if(!empty($m_role))
                    {
                        $data_log->jabatan = $jabatan->jabatan;
                    }
                }
            }
            
            
            if(!empty($data_perm_new)){
                $data_log->id_permohonan = $data_perm_new->id;
            }else{
                #temporary
                $data_log->id_permohonan = 3;
            }

            
            if(!empty($histori->created)){
                $data_log->tanggal_input = $histori->created;
            }

            
            if(!empty($histori->keterangan)){
                $data_log->catatan = $histori->keterangan;
            }
            print_r($data_log);exit;
            $this->pdbNew->createPermohonanLog($data_log);

            $user = $this->pdbNew->FindMUser($histori->id_personil);
            if($user == null){
                $histori->id_user = 10;
            }
  
            $this->pdbNew->DisposisiStaf($data_perm_new, $histori);    
            
            $status_akt = $this->pdbOld->GetTableStatusPermohonan($histori->id_status_permohonan);
            
            if(!empty($status_akt->nm_aktivitas_workflow)){
                $perm_info = $this->pdbNew->getPermohonanInfo($data_perm_new->id);
                if(empty($perm_info)){
                    $this->pdbNew->CreatePermohonanInfo($data_perm_new, $histori->waktu_in, $status_akt->status); 
                }
            }
                                        
            #p_sk_penomoran_pencabutan
            if(in_array($histori->id_status_permohonan,[29,31,32,33,35,36,37,38,39,42,44,48])){
                        
            }
            
            #p_sk_penomoran_file
            if(in_array($histori->id_status_permohonan,[])){

            }
        }
    }
}
