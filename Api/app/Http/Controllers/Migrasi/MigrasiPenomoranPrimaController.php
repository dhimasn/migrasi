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

class MigrasiPenomoranPrimaController extends Controller
{
    private $pdbOld;
    private $pdbNew;
    private $pdbDisposisiTel;
    public function __construct()
    {
        #base_url
        $this->pdbOld = new SipppdihatiOldDb();
        $this->pdbNew = new SipppdihatiNewDb();
        $this->pdbDisposisiTel = new PermohonanDisposisiTelDb();
    }

    /*public function MigrasiPenomoranPrima(){

        DB::beginTransaction();
        try {
            $data_lama = $this->pdbOld->GetTablePermohonanPenomoran();
            if(!empty($data_lama)){
                foreach($data_lama as $data){

                    if($data->id_permohonan == 2103){

                        find data perusahaan
                        $m_perusahaan_izin_jenis = $this->pdbNew->GetTablePerusahaanIzinJenis($data->id_data_nib);
                        if(!empty($m_perusahaan_izin_jenis)){
                                
                            $m_perusahaan = $this->pdbNew->GetTablePerusahaan($m_perusahaan_izin_jenis->id_perusahaan); 
                            if(!empty($m_perusahaan)){
                                $data->id_perusahaan = $m_perusahaan->id;
                            }else{
                                #temporary
                                $data->id_perusahaan = 1;
                            }

                        }else{
                            #temporary
                            $data->id_perusahaan = 1;
                        }

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

        $data_perm_new = array();

        //data id jenis izin
        $data->id_jenis_izin = 5;

        $isExist = $this->pdbNew->GetDataPenomoranByNoSkIzinandIdIzinJenis($data);

        if(!empty($isExist)){
            return $data_perm_new;
        }

        $this->GetstatusPermohonan($data);
       
        #p_permohonan
        $data_perm_new = $this->pdbNew->createPermohonanPenomoran($data);
        
        $t_histori_permohonan = $this->pdbOld->GetTableHistoriLogPenomoran($data->id_permohonan);                                          
        if(!empty($t_histori_permohonan)){
            #p_histori_permohonan
            $this->CreateHistoriPermohonan($t_histori_permohonan, $data_perm_new);
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

    public function GetstatusPermohonan($data)
    {

        $histori_aktif = $this->pdbOld->GetTableLogPenomoran($data);
       
        if(in_array($histori_aktif[0]->id_status_permohonan, [25])){
            $data->aktif = 1;
        }else{
            $data->aktif = 0;
        }

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
    }*/
}
