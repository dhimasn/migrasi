<?php

namespace App\Http\Controllers\Migrasi;

use App\Enums\TypeIzinJenisTel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repo\PermohonanInfoDb;
use App\Repo\Migrasi\SipppdihatiOldDb;
use App\Repo\Migrasi\SipppdihatiNewDb;
use App\Repo\Migrasi\SyaratKomitmenTelsusDb;
use App\Repo\Tel\PermohonanDisposisiTelDb;
use Exception;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;
use stdClass;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class MigrasiTelsusController extends Controller
{
    private $pdbOld;
    private $pdbNew;
    private $berkas;
    private $syarat;

    public function __construct()
    {
        $this->syarat = new SyaratKomitmenTelsusDb();
        $this->pdbOld = new SipppdihatiOldDb();
        $this->pdbNew = new SipppdihatiNewDb();
        $this->berkas = new MigrasiBerkasController();
        $this->perusahaan = new MigrasiPerusahaanController();
    }

    public function MigrasiTelsus(Request $re)
    {   
        ini_set('memory_limit', '4096M');
        ini_set('MAX_EXECUTION_TIME', '-1');
        ini_set('post_max_size', '4096M');
        ini_set('upload_max_filesize', '4096M');
        DB::beginTransaction();
        try {

            $dataLama = $this->pdbOld->GetTablePermohonanByTelsus();

            if(!empty($dataLama)){
                
                foreach($dataLama as $data){   

                    $this->perusahaan->findPerusahaan($data);

                    if(!empty($data->id_perusahaan)){

                        $result = $this->CreatePermohonanNew($data);
                        if($result != null){
                            $this->CreatePemenuhanKomitmen($data, $result['data_perm_new'],$result['data_layanan_new']);
                            $this->berkas->CreatePskFile($result['data_perm_new'], $data);
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

    public function CreatePermohonanNew($data)
    {

        $data_perm_new = array();

        $data->id_jenis_izin =  4;     

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

            $jenis_lyn_old = $this->pdbOld->GetTableSyaratIzinPTelsus($data->id_permohonan);

            if(!$jenis_lyn_old->isEmpty()){

                foreach($jenis_lyn_old as $lyn_old){

                    if($lyn_old->nilai_string == 'Kawat'){
                        $id_lyn_new = 29;
                    }else if($lyn_old->nilai_string == 'Serat Optik'){
                        $id_lyn_new = 30;
                    }else if($lyn_old->nilai_string == 'Sistem Elektromagnetik Lainnya'){
                        $id_lyn_new = null;
                    }else if($lyn_old->nilai_string == 'Spektrum Frekuensi Radio untuk Sistem Komunikasi Radio'){
                        $id_lyn_new = 31;
                    }else if($lyn_old->nilai_string == 'Spektrum Frekuensi Radio untuk Sistem Komunikasi Satelit'){
                        $id_lyn_new = 34;
                    }
                    
                    if($id_lyn_new != null){
                        #p_permohonan_layanan
                        $layanan_new = $this->pdbNew->CreatePermohonanLayanan($data_perm_new->id, $id_lyn_new);
                        array_push($data_layanan_new, $layanan_new);
                    }
                }        
            }
        }

        $result['data_perm_new'] = $data_perm_new;
        $result['data_layanan_new'] = $data_layanan_new;
        return $result;

    }

    public function CreatePemenuhanKomitmen($data, $data_perm_new)
    {
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

                    if($lyn_old->nilai_string == 'Kawat'){
                        $id_lyn_new = 29;
                    }else if($lyn_old->nilai_string == 'Serat Optik'){
                        $id_lyn_new = 30;
                    }else if($lyn_old->nilai_string == 'Sistem Elektromagnetik Lainnya'){
                        $id_lyn_new = null;
                    }else if($lyn_old->nilai_string == 'Spektrum Frekuensi Radio untuk Sistem Komunikasi Radio'){
                        $id_lyn_new = 31;
                    }else if($lyn_old->nilai_string == 'Spektrum Frekuensi Radio untuk Sistem Komunikasi Satelit'){
                        $id_lyn_new = 34;
                    }
                    
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

                $data_komit_new->id_izin_jenis = 4;

                $this->createHistoriPermohonaKomitmen($permohonan_komit_old, $data_komit_new, $data_perm_new); 

                    foreach($data_layanan_new as $layanan_new){

                        $komit_layanan_new = $this->pdbNew->CreatePermohonanKommitlayanan($data_komit_new->id, $layanan_new->id);

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

                                                #find table m_komitmen
                                                $syarat_table_komitmen = $this->pdbOld->GetTableTsyaratIzinPByIdSyarat($permohonan_komit_old->id_permohonan,$komitmen->id_syarat_izin_s);
                                                if($syarat_table_komitmen->isNotEmpty()){
                                                    #migrasi ULO    
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
    
    public function createHistoriPermohonaKomitmen($permohonan_komit_old, $data_komit_new,$data_perm_new)
    {
         
        $t_histori_permohonan_komit = $this->pdbOld->GetTableHistoriPermohonanAktf($permohonan_komit_old->id_permohonan);
        
        if($t_histori_permohonan_komit  != null ){    

            if(in_array($t_histori_permohonan_komit->id_aktivitas_workflow, [3, 15, 19, 24, 31, 36, 41, 59, 65, 72, 80, 94, 122, 137, 154, 174, 185, 191, 197, 211, 233, 259])){
                
                //create sklo
                $this->berkas->CreatePuloSklo($permohonan_komit_old, $data_perm_new->id);

                //create sk penetapan komitmen
                $this->berkas->CreatePskPenetapanKomitmen($permohonan_komit_old, $data_perm_new->id);

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
            $this->CreateHistoriPermohonan($data_histori, $data_perm_new, $data_komit_new->id);
        }
    
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
           
                //create disposisi staf and ulo
                $this->finduser($data_perm_new, $histori);
            
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

        $this->pdbNew->UpdatePermohonan($data_perm_new, $data->aktif);

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
                        $komitmen_ulo_proses =  $this->pdbNew->CreatePermohonanKomitmenUloProcess($syarat_izin_p, $m_komitmen_ulo->id , $komit_layanan_new->id);
                    }
                }else{
                    //m_evaluasi_ulo
                    $m_evaluasi_ulo = $this->pdbNew->FindevaluasiUloByIdlayanan($layanan_new->id_layanan, $m_syarat_izin_p->teks_judul);
                    if(!empty($m_evaluasi_ulo)){                  
                        if(!empty($syarat_izin_p->nilai_string)){
                            #p_evaluasi_ulo_proses
                            $evaluasi_ulo_proses =  $this->pdbNew->createPermohonanEvaluasiUloProses($syarat_izin_p, $m_evaluasi_ulo->id , $komit_layanan_new->id);
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

    public function FindJenisLyn($lyn_old)
    {

        switch($lyn_old->nilai_string) {

            case "Kawat":
                $result = 29;
                break;
            case "Serat Optik":
                $result = 30;
                break;
            case "Sistem Elektromagnetik Lainnya":
                $result = null;
                break;
            case "Spektrum Frekuensi Radio untuk Sistem Komunikasi Radio":
                $result = 31;
                break;    
            case "Spektrum Frekuensi Radio untuk Sistem Komunikasi Satelit":
                $result = 34;
                break;
            default:
              $result = null;
        }

        return $result;

    }

    public function finduser($data_perm_new, $histori)
    {

        if(property_exists($histori,'id_user')){

            $this->pdbNew->DisposisiStaf($data_perm_new, $histori);
            
            $this->pdbNew->DisposisiStafUlo($data_perm_new, $histori);

        }
    }

    public function MigrasiTelsusPrima(Request $re)
    {
        ini_set('memory_limit', '4096M');
        ini_set('MAX_EXECUTION_TIME', '-1');
        ini_set('post_max_size', '4096M');
        ini_set('upload_max_filesize', '4096M');
        DB::beginTransaction();
        try {
            $dataLama = $this->pdbNew->GetPpermohonanTelsusPrima();
            if(!empty($dataLama)){
                foreach($dataLama as $data){ 
                    //if($data->no_penyelenggaraan == "SUSULO002308"){
                        $this->CreatePermohonanKomitmenPrima($data);   
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

    public function CreatePermohonanKomitmenPrima($data){
        
        $old_permohonan = $this->pdbOld->findPermohonanPrima($data->no_penyelenggaraan);
       
        if(!empty($old_permohonan)){

            //find log permohonan
            $t_log_permohonan = $this->pdbOld->findTabelLogPermohonan($old_permohonan);
            
            if(!empty($t_log_permohonan))
            {
               
                if($t_log_permohonan[0]->id_personil == 0){

                    //create p_permohonan_komitmen
                    if($data->id_permohonan_status == 2){
                        $id_kelengkapan_status = 3;
                    }else if($data->id_permohonan_status == 1){
                        $id_kelengkapan_status = 4;
                    }
                   
                    $p_komitmen = $this->pdbNew->CreatePermohonanKomitmen($data, $data->id, $id_kelengkapan_status);
                    
                    if(!empty($p_komitmen)){
                        
                        //get p_permohonan_layanan
                        $p_permohonan_layanan = $this->pdbNew->GetPermohonanLayanan($data->id);
                        if(!empty($p_permohonan_layanan))
                        {
                            //create p_permohonan_komit_layanan
                            $p_permohonan_komit_layanan = $this->pdbNew->CreatePermohonanKommitlayanan($p_komitmen->id, $p_permohonan_layanan->id);
                            if(!empty($p_permohonan_komit_layanan)){

                                //find tbl_t_berkas_persyaratan
                                $berkasPersyaratan = $this->pdbOld->GetTableBerkasPersyaratan($old_permohonan->id_permohonan);
                            
                                //find_k_komit_kelengkapan
                                $k_komit_kelengkapan = $this->pdbNew->GetKomitKelengkapan($p_permohonan_layanan->id_layanan);
                                
                                if(!empty($berkasPersyaratan)){

                                    $this->matchPersyaratan($p_permohonan_komit_layanan, $berkasPersyaratan, $k_komit_kelengkapan, $p_permohonan_layanan);

                                }      
                            }
                        }
                    }
                }

                foreach($t_log_permohonan as $log_permohonan)
                {
                
                   
                    //find_personil
                    $personil = $this->pdbOld->GetTablePersonil($log_permohonan->id_personil);

                    //find_jabataan
                    $jabatan  = $this->pdbOld->GetTableJabatan($log_permohonan->id_jabatan);

                    //find tbl_m_permohonan_log
                    $status_log = $this->pdbOld->findPermohonanLog($log_permohonan->id_status_permohonan);
                   
                    //assign data log permohonan
                    $data_log = (object)array(

                        'id_permohonan' => $data->id,
                        'status' => $status_log->status,
                        'nama' => $personil->nama,
                        'jabatan' => $jabatan->jabatan,
                        'tanggal_input' => $log_permohonan->created,
                        'catatan' => $log_permohonan->keterangan

                    );

                    //create p_permohonan_log
                    $this->pdbNew->createPermohonanLog($data_log);

                    $perm_info = $this->pdbNew->getPermohonanInfo($data->id);
            
                    if($perm_info == null){
                        #p_permohonan_info    
                       
                        $this->pdbNew->CreatePermohonanInfo($data, $log_permohonan->created, $status_log->status);
                    }else{
                        #update p_permohonan_info
                        $this->pdbNew->UpdatePermohonanInfo($data, $log_permohonan->created, $status_log->status);
                    }
                }
            }
        }
    }

    public function matchPersyaratan($p_permohonan_komit_layanan, $berkasPersyaratan, $k_komit_kelengkapan, $p_permohonan_layanan){
        
       foreach($berkasPersyaratan as $persyaratan){
           
            $location = str_replace('https://www.pelayananprimaditjenppi.go.id/components/com_chronoforms5/chronoforms/uploads', 'D:/backup-htdocs/database_sipppdihati/new_sipppdihati', $persyaratan->url_berkas);

            foreach($k_komit_kelengkapan as $k_kelengkapan){
              
                $id_jenis_kelengkapan =  $this->matchMaking($persyaratan);

                if(!empty($id_jenis_kelengkapan)){

                     //create p_permohonan_komit_kelengkapan
                     $p_komit_klgnpn = $this->pdbNew->CreatePermohonanKomitKelengkapan($p_permohonan_komit_layanan->id, $id_jenis_kelengkapan , 2);

                     if($p_komit_klgnpn != null){
                    
                        //create p_permohonan_komit_file
                        $this->berkas->CreatePermohonanKomitKelengkapanTelsus($persyaratan, $p_komit_klgnpn->id , $location);
    
                    }
                }                
            }
        }    
    }

    public function matchMaking($persyaratan){

        switch($persyaratan->nama_berkas) {

            case "Data Dukung Tambahan":
               $result = 460;
               break;
           case "Formulir Permohonan":
               $result = 459;
               break;
           case "Struktur Organisasi":
               $result = 458;
               break;
           case "Izin Prinsip":
               $result = 452;
               break;    
           case "Bukti Kepemilikan Perangkat":
               $result = 461;
               break;
           case "Daftar perangkat telekomunikasi yang digunakan":
               $result = 462;
               break;
           case "Salinan sertifikasi perangkat dari Ditjen SDPPI":
               $result = 456;
               break;
           case "Kelengkapan Administrasi Lain":
               $result = 463;
               break;
           case "BHP dan ISR":
               $result = 464;
               break;
           case "Topologi Jaringan":
               $result = 465;
               break;
           default:
             $result = null;

       }
       return $result;
    }
}
