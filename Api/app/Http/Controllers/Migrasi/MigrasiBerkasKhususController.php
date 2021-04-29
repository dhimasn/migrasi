<?php

namespace App\Http\Controllers\Migrasi;

use App\Enums\TypeIzinJenisTel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repo\PermohonanInfoDb;
use App\Repo\Migrasi\SipppdihatiOldDb;
use App\Repo\Migrasi\SipppdihatiNewDb;
use App\Repo\Migrasi\SyaratKomitmenTelsusDb;
use Exception;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;
use stdClass;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class MigrasiBerkasKhususController extends Controller
{
    private $pdbOld;
    private $pdbNew;
    private $syarat;
    private $berkas;
    public function __construct()
    {
        $this->syarat = new SyaratKomitmenTelsusDb();
        $this->berkas = new MigrasiBerkasController();
        $this->pdbOld = new SipppdihatiOldDb();
        $this->pdbNew = new SipppdihatiNewDb();
    }

    public function MigrasiBerkaskhusus(Request $re)
    {   
        DB::beginTransaction();
        try {

            ini_set('memory_limit', '4096M');
            ini_set('MAX_EXECUTION_TIME', '-1');
            ini_set('post_max_size', '4096M');
            ini_set('upload_max_filesize', '4096M');

            #p_permohonan
            $data_permohonan = $this->pdbNew->GetPermohonanNewByIdjenisIzin(4);
            if(!empty($data_permohonan)){

                foreach($data_permohonan as $data_perm_new){
                    
                    #p_permohonan_komit
                    $p_permohonan_komit = $this->pdbNew->GetPermohonanKomitByIdpermohonan($data_perm_new->id);
                    if(!empty($p_permohonan_komit)){

                        #p_permohonan_komit_old
                        $permohonan_komit_old = $this->pdbOld->GetTablePermohonanByNoizinRef($p_permohonan_komit->no_komitmen); 
                        if(!empty($permohonan_komit_old)){

                            $t_histori_permohonan_komit = $this->pdbOld->GetTableHistoriPermohonanAktf($permohonan_komit_old->id_permohonan);
                            if($t_histori_permohonan_komit != null){  

                                if(in_array($t_histori_permohonan_komit->id_aktivitas_workflow, [3, 15, 19, 24, 31, 36, 41, 59, 65, 72, 80, 94, 122, 137, 154, 174, 185, 191, 197, 211, 233, 259])){ 
                    
                                    //create sklo
                                    $this->berkas->CreatePuloSklo($permohonan_komit_old, $data_perm_new->id);
                    
                                    //create sk penetapan komitmen
                                    //$this->berkas->CreatePskPenetapanKomitmen($permohonan_komit_old, $data_perm_new->id);
                    
                                }

                            }

                            #p_permohonan_komit_layanan
                            // $p_permohonan_komit_layanan = $this->pdbNew->GetPermohonanKomitLayanan($p_permohonan_komit->id);
                            // if(!empty($p_permohonan_komit_layanan)){

                            //     #p_permohonan_komit_kelengkapan
                            //     $p_permohonan_komit_kelengkapan = $this->pdbNew->GetPermohonanKomitKelengkapanByIdPermohonanKomitLayanan($p_permohonan_komit_layanan->id);
                            //     if(empty($p_permohonan_komit_kelengkapan)){

                            //             #p_permohonan_layanan
                            //             $p_permohonan_layanan = $this->pdbNew->GetPermohonanLayananbyId($p_permohonan_komit_layanan->id_permohonan_layanan);
                            //             if(!empty($p_permohonan_layanan))
                            //             {
                                        
                            //                 $m_syarat_izin_grup = $this->pdbOld->GetMSyaratIzinGroup($permohonan_komit_old->id_jenis_izin);
                            //                 if(!empty($m_syarat_izin_grup)){

                            //                 foreach($m_syarat_izin_grup as $syarat_grup){
            
                            //                     $syarat_komit = $this->pdbOld->GetMsyaratIzinSbyIdIzinGrup($syarat_grup->id_syarat_izin_grup);
                                                
                            //                     if(!empty($syarat_komit)){

                            //                         foreach($syarat_komit as $komitmen){                                                
                                                        
                            //                             $syarat_komitmens = $this->pdbOld->GetTableTsyaratIzinFByIdsIdPermohonan($permohonan_komit_old->id_permohonan, $komitmen->id_syarat_izin_s);
                                                        
                            //                             if($syarat_komitmens->isNotEmpty()){

                            //                                 //$k_kelengkapan = $this->syarat->findNewSyaratKomitmen($komitmen, $p_permohonan_layanan->id_layanan);
                                                            
                            //                                 // if($k_kelengkapan != null){

                            //                                 //     $akhir = (count($syarat_komitmens) - 1);
                            //                                 //     if($akhir >= 0){

                            //                                 //         if($syarat_komitmens[$akhir]->catatan == 'Sesuai'){
                            //                                 //             $id_permohonan_k_kelengkapan_status = 4;
                            //                                 //         }else if ($syarat_komitmens[$akhir]->file_name_asli != null) {
                            //                                 //             $id_permohonan_k_kelengkapan_status = 4;
                            //                                 //         }else{
                            //                                 //             $id_permohonan_k_kelengkapan_status = 2;
                            //                                 //         }

                            //                                 //         if($syarat_komitmens[$akhir]->date_added == null){
                            //                                 //             $syarat_komitmens[$akhir]->date_added = $permohonan_komit_old->tgl_permohonan;
                            //                                 //         }

                            //                                 //         $permohonan_komit_kelengkapan = $this->pdbNew->CreatePermohonanKomitKelengkapan($p_permohonan_komit_layanan->id, $k_kelengkapan, $id_permohonan_k_kelengkapan_status);
                            //                                 //         if($permohonan_komit_kelengkapan != null){

                            //                                 //             if($syarat_komitmens[$akhir]->catatan != null){
                            //                                 //                 $this->pdbNew->CreatePermohonanKomitkelengkapanCatatan($permohonan_komit_kelengkapan->id, $syarat_komitmens[$akhir]->catatan, $syarat_komitmens[$akhir]->date_added);
                            //                                 //             }
                                                                    
                            //                                 //             foreach ($syarat_komitmens as $syarat_komitmen){
                            //                                 //                 $p_permohonan_komit_kelengkapan_file = $this->berkas->CreatePermohonanKomitKelengkapanData($permohonan_komit_kelengkapan->id, $syarat_komitmen);
                            //                                 //             }
                            //                                 //         }
                            //                                 //     }  
                            //                                 // }

                            //                                 // #find table BA ULO dan evaluasi
                            //                                 // $syarat_ba_evaluasi = $this->pdbOld->GetTableTsyaratIzinFPByIdSyarat($permohonan_komit_old->id_permohonan, $komitmen->id_syarat_izin_s);
                            //                                 // if($syarat_ba_evaluasi->isNotEmpty()){
                            //                                 //#migrasi BA ULO dan evaluasi   
                            //                                 //     foreach($syarat_ba_evaluasi as $syarat_izin_p){
                            //                                 //         $this->findBaSptUlo($syarat_izin_p, $layanan_new, $komit_layanan_new, $data_komit_new);    
                            //                                 //     }
                            //                                 // }

                            //                                     #find table m_komitmen
                            //                                     $syarat_table_komitmen = $this->pdbOld->GetTableTsyaratIzinPByIdSyarat($permohonan_komit_old->id_permohonan, $komitmen->id_syarat_izin_s);
                            //                                     if($syarat_table_komitmen->isNotEmpty()){
                            //                                         #migrasi ULO    
                            //                                         foreach($syarat_table_komitmen as $syarat_izin_p)
                            //                                         {
                            //                                         $syarat_ulo_new = $this->findNewSyaratUlo($syarat_izin_p, $p_permohonan_layanan, $p_permohonan_komit_layanan);
                            //                                         }
                            //                                     }
                            //                                 }
                            //                             }                                               
                            //                         }
                            //                     }
                            //                 }
                            //             }
                            //         }                                      
                            //     }
                            // }
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

    // public function findNewSyaratUlo($syarat_izin_p, $layanan_new, $komit_layanan_new)
    // {

    //     //m_syarat_izin_p
    //     $m_syarat_izin_p = $this->pdbOld->findKomitmenUloMsyaratIzinP($syarat_izin_p);
    //     if(!empty($m_syarat_izin_p)){
    //         if($m_syarat_izin_p->teks_judul != "Tgl ULO" && $m_syarat_izin_p->teks_judul != "Mekanisme ULO"){

    //             // //m_komitmen_ulo
    //             // $m_komitmen_ulo = $this->pdbNew->FindKomitmenUloByIdlayanan($layanan_new->id_layanan, $m_syarat_izin_p->teks_judul);
    //             // if(!empty($m_komitmen_ulo)){
    //             //     if(!empty($syarat_izin_p->nilai_string)){
    //             //         #p_komitmen_ulo_proses
    //             //         $komitmen_ulo_proses =  $this->pdbNew->CreatePermohonanKomitmenUloProcess($syarat_izin_p, $m_komitmen_ulo->id , $komit_layanan_new->id);
    //             //     }
    //             // }else{
    //                 //m_evaluasi_ulo
    //                 // $m_evaluasi_ulo = $this->pdbNew->FindevaluasiUloByIdlayanan($layanan_new->id_layanan, $m_syarat_izin_p->teks_judul);
    //                 // if(!empty($m_evaluasi_ulo)){                  
    //                 //     if(!empty($syarat_izin_p->nilai_string)){
    //                 //         #p_evaluasi_ulo_proses
    //                 //         $evaluasi_ulo_proses =  $this->pdbNew->createPermohonanEvaluasiUloProses($syarat_izin_p, $m_evaluasi_ulo->id , $komit_layanan_new->id);
    //                 //     }
    //                 // }
    //             //}
                
    //         }
    
    //         if($m_syarat_izin_p->teks_judul == "Tgl ULO"){
    //             if(!empty($syarat_izin_p->nilai_string)){
    //                 #p_mekanisme_ulo_process
    //                 $mekanisme_ulo_proses = $this->pdbNew->CreatePermohonanKomitmenMekanismeUloProcess($syarat_izin_p, 2, $komit_layanan_new->id);
    //             }
    //         }
    
    //         if($m_syarat_izin_p->teks_judul == "Mekanisme ULO"){
    //             if(!empty($syarat_izin_p->nilai_string)){
    //                 #p_mekanisme_ulo_process
    //                 $mekanisme_ulo_proses = $this->pdbNew->CreatePermohonanKomitmenMekanismeUloProcess($syarat_izin_p, 1, $komit_layanan_new->id);
    //             }
    //         }
    //     }
    // }
}
