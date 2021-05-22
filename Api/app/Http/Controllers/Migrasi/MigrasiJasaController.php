<?php

namespace App\Http\Controllers\Migrasi;

use App\Enums\TypeIzinJenisTel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repo\PermohonanInfoDb;
use App\Repo\Migrasi\SipppdihatiOldDb;
use App\Repo\Migrasi\SipppdihatiNewDb;
use App\Repo\Migrasi\SyaratKomitmenJasaDb;
use App\Repo\Tel\PermohonanDisposisiTelDb;
use Exception;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;
use stdClass;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class MigrasiJasaController extends Controller
{
    private $pdbOld;
    private $pdbNew;
    private $syarat;
    private $berkas;
    private $perusahaan;

    public function __construct()
    {
        $this->pdbOld = new SipppdihatiOldDb();
        $this->pdbNew = new SipppdihatiNewDb();
        $this->pdbDisposisiTel = new PermohonanDisposisiTelDb();
        $this->syarat = new SyaratKomitmenJasaDb();
        $this->perusahaan = new MigrasiPerusahaanController();
        $this->berkas = new MigrasiBerkasController();
    }

    public function MigrasiJasa(Request $re)
    {   
        
        DB::beginTransaction();
        try {
            ini_set('memory_limit', '4096M');
            ini_set('MAX_EXECUTION_TIME', '-1');
            ini_set('post_max_size', '4096M');
            ini_set('upload_max_filesize', '4096M');
            $dataLama = $this->pdbOld->GetTablePermohonanByJasa();
            if(!empty($dataLama)){
                foreach($dataLama as $data){
                    $this->perusahaan->findPerusahaan($data);
                    if(empty($data->id_perusahaan)){
                       break;
                    }
                    $result = $this->CreatePermohonanNew($data);
                    if($result != null){
                        $this->CreatePemenuhanKomitmen($data, $result['data_perm_new'], $result['data_layanan_new']);
                        $this->berkas->CreatePskFile($result['data_perm_new'], $data);
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
   
        $data->id_jenis_izin = 3;
        
        $isExist = $this->pdbNew->GetDataPermohonanByNoSkIzinandIdIzinJenis($data);
        if($isExist != null){
           return null;
        }

        #p_permohonan
        $data_perm_new = $this->pdbNew->createPermohonan($data);
        
        $data_histori = $this->pdbOld->GetTableHistoriPermohonan($data->id_permohonan);                                                
        if(!empty($data_histori)){

            #update status permohonan
            $this->GetstatusPermohonan($data_perm_new, $data);

            #data histori
            $this->CreateHistoriPermohonan($data_histori, $data_perm_new, false);

        }

        if(!empty($data_perm_new)){
            
            //find data layanan new
            $data_layanan_new = array();

            $jenis_lyn_old = $this->pdbOld->GetTableSyaratIzinPJasa($data->id_permohonan);  
            if(!$jenis_lyn_old->isEmpty()){
    
                foreach($jenis_lyn_old as $lyn_old){
    
                    $id_lyn_new = $this->FindJenisLyn($lyn_old);
                    
                    if($id_lyn_new != null){

                        #p_permohonan_layanan
                        $layanan_new = $this->pdbNew->CreatePermohonanLayanan($data_perm_new->id, $id_lyn_new);
                        $this->CreateSkPenomoran($layanan_new, $data);
                        array_push($data_layanan_new, $layanan_new);
                        
                    }
    
                } 
                
            }
            
        }

        $result['data_perm_new'] = $data_perm_new;
        $result['data_layanan_new'] = $data_layanan_new;
        return $result;

    }

    public function CreatePemenuhanKomitmen($data, $data_perm_new, $data_layanan_new)
    {
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

            if(empty($data_layanan_new))
            {
                //find data layanan new
                $data_layanan_new = array();

                $jenis_lyn_old = $this->pdbOld->GetTableSyaratIzinPTelsus($permohonan_komit_old->id_permohonan);

                if(!$jenis_lyn_old->isEmpty()){

                    foreach($jenis_lyn_old as $lyn_old){

                        $id_lyn_new = $this->FindJenisLyn($lyn_old);
                        
                        if($id_lyn_new != null){
                            #p_permohonan_layanan
                            $layanan_new = $this->pdbNew->CreatePermohonanLayanan($data_perm_new->id, $id_lyn_new);
                            array_push($data_layanan_new, $layanan_new);
                        }
                    }        
                }
            }

            if(!empty($data_layanan_new)){ 

            $data_komit_new = $this->pdbNew->CreatePermohonanKomit($permohonan_komit_old, $data_perm_new->id, $id_kelengkapan_status);
            if(!empty($data_komit_new)){

                $data_komit_new->id_izin_jenis = 3;

                $this->createHistoriPermohonaKomitmen($permohonan_komit_old, $data_komit_new, $data_perm_new);

                    foreach($data_layanan_new as $layanan_new){

                        $komit_layanan_new= $this->pdbNew->CreatePermohonanKommitlayanan($data_komit_new->id, $layanan_new->id);
                        if(!empty($komit_layanan_new)){

                            #p_permohonan_komit_status
                            $this->pdbNew->createPermohonanKomitStatus($komit_layanan_new->id, $id_kelengkapan_status);

                            $m_syarat_izin_grup = $this->pdbOld->GetMSyaratIzinGroup($permohonan_komit_old->id_jenis_izin);
        
                            if(!empty($m_syarat_izin_grup)){

                                foreach($m_syarat_izin_grup as $syarat_grup){

                                    $id_syarat_izin_grup = $syarat_grup->id_syarat_izin_grup;

                                    if(!empty($id_syarat_izin_grup)){

                                        $syarat_komit = $this->pdbOld->GetMsyaratIzinSbyIdIzinGrup($id_syarat_izin_grup);
                                        
                                        if(!empty($syarat_komit)){

                                            foreach($syarat_komit as $komitmen){   
                                                
                                                $syarat_komitmens = $this->pdbOld->GetTableTsyaratIzinFByIdsIdPermohonan($permohonan_komit_old->id_permohonan, $komitmen->id_syarat_izin_s);
                                                
                                                $k_kelengkapan = $this->syarat->findNewSyaratKomitmen($komitmen, $layanan_new->id_layanan);

                                                if($syarat_komitmens->isNotEmpty() && $k_kelengkapan != null){
                                                                    
                                                    $akhir = (count($syarat_komitmens) - 1);
                                                    if($akhir >= 0){

                                                        if($syarat_komitmens[$akhir]->catatan == 'Sesuai'){
                                                            $id_permohonan_k_kelengkapan_status = 4;
                                                        }else if ($syarat_komitmens[$akhir]->file_name_asli != null) {
                                                            $id_permohonan_k_kelengkapan_status = 4;
                                                        }else{
                                                            $id_permohonan_k_kelengkapan_status = 2;
                                                        }
                                                    
                                                        if($syarat_komitmens[$akhir]->date_added == null){
                                                            $syarat_komitmens[$akhir]->date_added = $permohonan_komit_old->tgl_permohonan;
                                                        }

                                                        $permohonan_komit_kelengkapan = $this->pdbNew->CreatePermohonanKomitKelengkapan($komit_layanan_new->id, $k_kelengkapan, $id_permohonan_k_kelengkapan_status);
                                                        if($permohonan_komit_kelengkapan != null){

                                                            if($syarat_komitmens[$akhir]->catatan != null){
                                                                $this->pdbNew->CreatePermohonanKomitkelengkapanCatatan($permohonan_komit_kelengkapan->id, $syarat_komitmens[$akhir]->catatan, $syarat_komitmens[$akhir]->date_added);
                                                            }
                                                        
                                                            foreach ($syarat_komitmens as $syarat_komitmen){
                                                                $this->berkas->CreatePermohonanKomitKelengkapanData($permohonan_komit_kelengkapan->id, $syarat_komitmen);
                                                            }
                                                        }
                                                    }
                                                }
                                                
                                                #find table BA ULO dan evaluasi
                                                $syarat_ba_evaluasi = $this->pdbOld->GetTableTsyaratIzinFPByIdSyarat($permohonan_komit_old->id_permohonan, $komitmen->id_syarat_izin_s);
                                                if($syarat_ba_evaluasi->isNotEmpty()){
                                                    foreach($syarat_ba_evaluasi as $syarat_izin_p){
                                                        $this->findBaSptUlo($syarat_izin_p, $layanan_new, $komit_layanan_new, $data_komit_new);
                                                    }
                                                }

                                                #find table komitmen 
                                                $syarat_table_komitmen = $this->pdbOld->GetTableTsyaratIzinPByIdSyarat($permohonan_komit_old->id_permohonan, $komitmen->id_syarat_izin_s);
                                                if($syarat_table_komitmen->isNotEmpty()){
                                                    #migrasi komitmen ulo dan evaluasi Ulo
                                                    foreach($syarat_table_komitmen as $syarat_izin_p){
                                                        $this->findNewSyaratUlo($syarat_izin_p, $layanan_new, $komit_layanan_new);
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

    public function createHistoriPermohonaKomitmen($permohonan_komit_old, $data_komit_new, $data_perm_new)
    {

        $t_histori_permohonan_komit = $this->pdbOld->GetTableHistoriPermohonanAktf($permohonan_komit_old->id_permohonan);
    
        if($t_histori_permohonan_komit != null){  

            if(in_array($t_histori_permohonan_komit->id_aktivitas_workflow, [3, 15, 19, 24, 31, 36, 41, 59, 65, 72, 80, 94, 122, 137, 154, 174, 185, 191, 197, 211, 233, 259])){ 

                //create sklo
                $this->berkas->CreatePuloSklo($permohonan_komit_old, $data_perm_new->id);

                //create sk penetapan komitmen
                //$this->berkas->CreatePskPenetapanKomitmen($permohonan_komit_old, $data_perm_new->id);

            }

            if(in_array($t_histori_permohonan_komit->id_aktivitas_workflow, [33, 38, 43, 52, 55, 61, 67, 75, 83, 98, 103, 104, 112, 126, 159, 176, 187, 215, 237, 263])){
                $aktif = 2;
                $id_ulo_status = 3;
            }else if(in_array($t_histori_permohonan_komit->id_aktivitas_workflow, [3, 15, 19, 24, 31, 36, 41, 59, 65, 72, 80, 94, 122, 137, 154, 174, 185, 191, 197, 211, 233, 259])){
                $aktif = 1;
                $id_ulo_status = 4;
            }else if(in_array($t_histori_permohonan_komit->id_aktivitas_workflow, [11, 90, 118, 133, 150])){
                $id_ulo_status = 5;
            }else{
                $id_ulo_status = 2;
                $aktif = 0;
            }

            $this->pdbNew->UpdatePermohonan($data_perm_new, $aktif);
            
            $this->pdbNew->CreateTablePulo($id_ulo_status, $data_komit_new->id, $t_histori_permohonan_komit->waktu_in);

        }

        $data_histori = $this->pdbOld->GetTableHistoriPermohonan($permohonan_komit_old->id_permohonan);
        if(!empty($data_histori)){   
            $this->CreateHistoriPermohonan($data_histori, $data_perm_new, true);
        }
        
        return $id_ulo_status;

    }

    public function CreateHistoriPermohonan($data_histori, $data_perm_new, $permohonan_komitmen)
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

            $this->pdbNew->createPermohonanLog($data_log);

            if($permohonan_komitmen == true){

                //create disposisi staf
                $this->finduser($data_perm_new, $histori);
                
                //create disposisi staf ulo
                $this->finduserUlo($data_perm_new, $histori);

            }
           
            $perm_info = $this->pdbNew->getPermohonanInfo($data_perm_new->id);
            
            if($perm_info == null){
                #p_permohonan_info    
                $this->pdbNew->CreatePermohonanInfo($data_perm_new, $histori->waktu_in, $data_log->status);
            }else{
                #update p_permohonan_info
                $this->pdbNew->UpdatePermohonanInfo($data_perm_new, $histori->waktu_in, $data_log->status);
            }
                            
            #p_sk_pencabutan
            if(in_array($histori->id_aktivitas_workflow, [33, 38, 43, 52, 55, 61, 67, 75, 83, 98, 103, 104, 112, 126, 142, 159, 176, 187, 215, 237, 263])){

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
                            
    }

    public function findBaSptUlo($syarat_izin_p, $layanan_new, $komit_layanan_new, $data_komit_new)
    {
        //m_syarat_izin_p
        $m_syarat_izin_p = $this->pdbOld->findKomitmenUloMsyaratIzinP($syarat_izin_p);
        if(!empty($m_syarat_izin_p)){

            //m_evaluasi_ulo
            $m_evaluasi_ulo = $this->pdbNew->FindevaluasiUloByIdlayanan($layanan_new->id_layanan, $m_syarat_izin_p->teks_judul);
            if(!empty($m_evaluasi_ulo)){

                if($m_evaluasi_ulo->value == 'Hasil Pengujian Lapangan'){
                            
                    $ulo_file = $this->berkas->CreatePUloData($komit_layanan_new, $syarat_izin_p);

                    if($syarat_izin_p->catatan != null){
                        #p_ulo_catatan
                        $this->pdbNew->createUloCatatan($syarat_izin_p->catatan, $syarat_izin_p->date_added, $data_komit_new->id);
                    }
                    if($ulo_file != null){
                        #p_evaluasi_ulo_proses
                        $syarat_izin_p->nilai_string = json_encode(array("id" => $ulo_file->id, "nama" => $ulo_file->nama));
                        $this->pdbNew->createPermohonanEvaluasiUloProses($syarat_izin_p, $m_evaluasi_ulo->id , $komit_layanan_new->id);
                    }
                    
                }else if($m_evaluasi_ulo->value == 'SPT'){
                               
                    $ulo_file = $this->berkas->CreatePUloData($komit_layanan_new, $syarat_izin_p);
                    if($syarat_izin_p->catatan != null){
                        #p_ulo_catatan
                        $this->pdbNew->createUloCatatan($syarat_izin_p->catatan, $syarat_izin_p->date_added, $data_komit_new->id);
                    }
                    if($ulo_file != null){
                        #p_evaluasi_ulo_proses
                        $syarat_izin_p->nilai_string = json_encode(array("id" => $ulo_file->id, "nama" => $ulo_file->nama));
                        $this->pdbNew->createPermohonanEvaluasiUloProses($syarat_izin_p, $m_evaluasi_ulo->id , $komit_layanan_new->id);
                    }
                }else{
                    $ulo_file = $this->berkas->CreatePUloData($komit_layanan_new, $syarat_izin_p);
                    if($syarat_izin_p->catatan != null){
                        #p_ulo_catatan
                        $this->pdbNew->createUloCatatan($syarat_izin_p->catatan, $syarat_izin_p->date_added, $data_komit_new->id);
                    }
                    if($ulo_file != null){
                        #p_evaluasi_ulo_proses
                        $syarat_izin_p->nilai_string = json_encode(array("id" => $ulo_file->id, "nama" => $ulo_file->nama));
                        $this->pdbNew->createPermohonanEvaluasiUloProses($syarat_izin_p, $m_evaluasi_ulo->id , $komit_layanan_new->id);
                    }
                }
            }
        } 
    }
    
    public function findNewSyaratUlo($syarat_izin_p, $layanan_new, $komit_layanan_new)
    {
        //m_syarat_izin_p
        $m_syarat_izin_p = $this->pdbOld->findKomitmenUloMsyaratIzinP($syarat_izin_p);
        if(!empty($m_syarat_izin_p)){
            if($m_syarat_izin_p->teks_judul != "Tgl ULO" && $m_syarat_izin_p->teks_judul != "Mekanisme ULO"){
 
                //m_komitmen_ulo
                $m_komitmen_ulo = $this->pdbNew->FindKomitmenUloByIdlayanan($layanan_new->id_layanan, $m_syarat_izin_p->teks_judul);
                if(!empty($m_komitmen_ulo)){
                    if(!empty($syarat_izin_p->nilai_string)){
                        #p_komitmen_ulo_proses
                        $this->pdbNew->CreatePermohonanKomitmenUloProcess($syarat_izin_p, $m_komitmen_ulo->id , $komit_layanan_new->id);
                    }
                }else{
                    //m_evaluasi_ulo
                    $m_evaluasi_ulo = $this->pdbNew->FindevaluasiUloByIdlayanan($layanan_new->id_layanan, $m_syarat_izin_p->teks_judul);
                    if(!empty($m_evaluasi_ulo)){                  
                        if(!empty($syarat_izin_p->nilai_string)){
                            #p_evaluasi_ulo_proses
                           $this->pdbNew->createPermohonanEvaluasiUloProses($syarat_izin_p, $m_evaluasi_ulo->id , $komit_layanan_new->id);
                        }
                    }
                }
                
            }
     
            if($m_syarat_izin_p->teks_judul == "Tgl ULO"){
                if(!empty($syarat_izin_p->nilai_string)){
                    #p_mekanisme_ulo_process
                    $this->pdbNew->CreatePermohonanKomitmenMekanismeUloProcess($syarat_izin_p, 2, $komit_layanan_new->id);
                }
            }
     
            if($m_syarat_izin_p->teks_judul == "Mekanisme ULO"){
                if(!empty($syarat_izin_p->nilai_string)){
                    #p_mekanisme_ulo_process
                    $this->pdbNew->CreatePermohonanKomitmenMekanismeUloProcess($syarat_izin_p, 1, $komit_layanan_new->id);
                }
            }
        }
    }

    public function FindJenisLyn($lyn_old)
    {
        switch($lyn_old->nilai_string) {
             case "Teleponi Dasar melalui Jaringan Telekomunikasi":
                $result = 42;
                break;
            case "Pusat Panggilan Informasi (Call Center)":
                $result = 1;
                break;
            case "Panggilan Terkelola (Caling Card)":
                $result = 2;
                break;
            case "Internet Teleponi untuk Keperluan Publik (ITKP)":
                $result = 41;
                break;    
            case "Konten Panggilan Premium (Premium Call)":
                $result = 46;
                break;
            case "Konten SMS Premium (Content Provider)":
                $result = 44;
                break;
            case "Akses Internet (Internet Service Provider/ISP)":
                $result = 40;
                break;
            case "Gerbang Akses Internet (Network Access Provider/NAP)":
                $result = 45;
                break;
            case "Sistem Komunikasi Data (Siskomdat)":
                $result = 47;
                break;
            case "Televisi Protokol Internet (Internet Protocol Television/IPTV)":
                $result = 39;
                break;
            case "Teleponi Dasar melalui Satelit yang telah memperoleh Hak Labuh (Landing Right)":
                $result = 43;
                break;
            default:
              $result = null;
        }
        return $result;
    }

    public function finduser($data_perm_new,$histori)
    {
        if(property_exists($histori,'id_user')){
            $this->pdbNew->DisposisiStaf($data_perm_new, $histori);
        }
    }

    public function finduserUlo($data_perm_new, $histori)
    {
         if(property_exists($histori,'id_user')){
            $this->pdbNew->DisposisiStafUlo($data_perm_new, $histori);  
        }
    }

    public function CreateSkPenomoran($layanan_new, $data)
    {
        //find jenis penomoran 
       $m_penomoran_tel = $this->pdbNew->FindMPenomoranTel($layanan_new->id_layanan);
       if(!empty($m_penomoran_tel)){
            //find jenis refensi penomoran
            $tbl_m_pertel = $this->pdbOld->FindBlokPenomoran($data->id_permohonan);
            if(!empty($tbl_m_pertel)){
                if($data->aktif == 1){
                    $status_pnmr = 1;
                }else{
                    $status_pnmr = 2;
                }
                //create m_penomoran_tel_list
                $m_penomoran_tel_list = $this->pdbNew->CreateMpenomoranTellist($m_penomoran_tel->id, $tbl_m_pertel->nilai_string, $status_pnmr);
                if(!empty($m_penomoran_tel_list)){
                    //find no_sk_penetapan
                    $nomor = $this->pdbOld->FindTableMPenomoran($tbl_m_pertel->nilai_string);
                    if(!empty($nomor)){
                        //create p_penomoran_tel_pakai
                        $m_tel_pakai = $this->pdbNew->CreatePenomoranTelPakai($m_penomoran_tel_list, $data, $nomor->no_penetapan, $status_pnmr);
                        //create sk penomoran
                        $this->berkas->CreateBerkasSkPenomoran($m_tel_pakai->id, $data->id_permohonan);
                    }
                }
            }
        }
    }
    
}
