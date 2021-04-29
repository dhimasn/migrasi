<?php

namespace App\Http\Controllers\Migrasi;

use App\Enums\TypeIzinJenisTel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repo\PermohonanInfoDb;
use App\Repo\Migrasi\SipppdihatiOldDb;
use App\Repo\Migrasi\SipppdihatiNewDb;
use App\Repo\Migrasi\SyaratKomitmenJaringanDb;
use Exception;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;
use stdClass;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class MigrasiBerkasPosController extends Controller
{
    private $pdbOld;
    private $pdbNew;
    private $berkas;
    public function __construct()
    {
        $this->berkas = new MigrasiBerkasController();
        $this->pdbOld = new SipppdihatiOldDb();
        $this->pdbNew = new SipppdihatiNewDb();
    }

    public function MigrasiBerkasPos(Request $re)
    {   
        DB::beginTransaction();
        try {

            ini_set('memory_limit', '4096M');
            ini_set('MAX_EXECUTION_TIME', '-1');
            ini_set('post_max_size', '4096M');
            ini_set('upload_max_filesize', '4096M');

            #p_permohonan
            $data_permohonan = $this->pdbNew->GetPermohonanPosNewByIdJenisIzin([1,6,7]);//1,6,7
            if(!empty($data_permohonan)){

                foreach($data_permohonan as $data_perm_new){

                    $perm_info = $this->pdbNew->getPermohonanInfo($data_perm_new->id);
                    if($perm_info->value != 'Upload Bukti Bayar'){
                        if($perm_info->value != 'Verifikasi SPM'){
                            if($perm_info->value != 'Drop Izin'){
                                $this->pdbNew->UpdateBuktiBayar($data_perm_new->id);
                            }
                        }   
                    }
                    
                    #p_permohonan_komit
                    $p_permohonan_komit = $this->pdbNew->GetPermohonanKomitByIdpermohonan($data_perm_new->id);
                    if(!empty($p_permohonan_komit)){
                            #p_permohonan_komit_old
                            $permohonan_komit_old = $this->pdbOld->GetTablePermohonanByNoizinRef($p_permohonan_komit->no_komitmen);
                            if(!empty($permohonan_komit_old)){

                                #jika p_permohonan_komit_layanan maka create lagi
                                $p_permohonan_komit_layanan = $this->pdbNew->GetPermohonanKomitLayanan($p_permohonan_komit->id);
                                if(empty($p_permohonan_komit_layanan)){

                                    //cek p_permohonan_layanan
                                    $p_permohonan_layanan = $this->pdbNew->GetPermohonanLayanan($data_perm_new->id);
                                    if(!empty($p_permohonan_layanan)){

                                        //create p_permohonan_komit_layanan
                                        $p_permohonan_komit_layanan = $this->pdbNew->CreatePermohonanKommitlayanan($p_permohonan_komit->id, $p_permohonan_layanan->id);
                                        if(!empty($p_permohonan_komit_layanan)){
  
                                                    $m_syarat_izin_grup = $this->pdbOld->GetMSyaratIzinGroup($permohonan_komit_old->id_jenis_izin);
                                                    if(!empty($m_syarat_izin_grup)){
                                            
                                                        foreach($m_syarat_izin_grup as $syarat_grup){

                                                            $syarat_komit = $this->pdbOld->GetMsyaratIzinSbyIdIzinGrup($syarat_grup->id_syarat_izin_grup);
                                                            if(!empty($syarat_komit)){
                                            
                                                                foreach($syarat_komit as $komitmen){
                                                                    
                                                                    //find by id layanan
                                                                    $k_kelengkapans = $this->pdbNew->findSyaratKomitmenPos($komitmen, $p_permohonan_layanan); 
                                                                    
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
                                                                            $permohonan_komit_kelengkapan = $this->pdbNew->CreatePermohonanKomitKelengkapan($p_permohonan_komit_layanan->id, $k_kelengkapan->id, $id_permohonan_k_kelengkapan_status);                                                                   
                                                                            if($syarat_komitmens[$akhir]->date_added == null){
                                                                                $syarat_komitmens[$akhir]->date_added = $permohonan_komit_old->tgl_permohonan;
                                                                            }
                                                                            
                                                                            if($syarat_komitmens[$akhir]->catatan != null){
                                                                                #p_permohonan_komit_kelengkapan_catatan
                                                                                $this->pdbNew->CreatePermohonanKomitkelengkapanCatatan($permohonan_komit_kelengkapan->id, $syarat_komitmens[$akhir]->catatan, $syarat_komitmens[$akhir]->date_added);
                                                                            }                                                                   
                                                                            
                                                                            foreach ($syarat_komitmens as $syarat_komitmen){   
                                                                                #p_permohinan_komit_kelengkapan_file
                                                                                $p_permohonan_komit_kelengkapan_file = $this->berkas->CreatePermohonanKomitKelengkapanFilePos($permohonan_komit_kelengkapan,$syarat_komitmen);                                                                     
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
}
