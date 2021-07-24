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

use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class MigrasiPenomoranPrimaController extends Controller
{
    private $pdbOld;
    private $pdbNew;
    private $perusahaan;
    private $berkas; 
    private $Migrasi;
    public function __construct()
    {
        #base_url
        $this->pdbOld = new SipppdihatiOldDb();
        $this->pdbNew = new SipppdihatiNewDb();
        $this->perusahaan = new MigrasiPerusahaanController();
        $this->pdbDisposisiTel = new PermohonanDisposisiTelDb();
        $this->berkas = new MigrasiBerkasController();
        $this->Migrasi = new MigrasiPenomoranController();
    }

    public function MigrasiPenomoranPrima(){

        DB::beginTransaction();
        try {

            ini_set('memory_limit', '4096M');
            ini_set('MAX_EXECUTION_TIME', '-1');
            ini_set('post_max_size', '4096M');
            ini_set('upload_max_filesize', '4096M');

            $data_lama = $this->pdbOld->GetTablePermohonanSipppdb1();
        
            if(!empty($data_lama)){

                foreach($data_lama as $data){

                    if(substr($data->no_penyelenggaraan, 0, 1) == 'N'){ 
                        $this->CreatePermohonanNewSipppDb($data);
                    }
                    
                    if(substr($data->no_penyelenggaraan, 0, 1) == 'P'){
                        $this->CreatePermohonanNewSipppdihati($data);
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

    public function CreatePermohonanNewSipppDb($data)
    {

        $this->perusahaan->normalizeDataPerusahaanPrima($data);

        // $isExist = $this->pdbNew->GetDataPenomoranByNomor($data);

        // if(!empty($isExist)){

        //     return null;
        
        // }else{

        
            if($data->tanggal_input == date('0000-00-00 00:00:00')){                                             
                $data->tanggal_input = null;
            }

            //create p_permohonan_penomoran
            $p_permohonan_penomoran = $this->pdbNew->CreatePermohonanPenomoranPrima($data);
            if(!empty($p_permohonan_penomoran)){

                //find tbl_t_permohonan
                $tbl_t_permohonan = $this->pdbOld->TableTtpermohonan($data);
                
                if(!empty($tbl_t_permohonan)){
                             
                    //find tbl_t_izin_penomoran
                    $tbl_t_izin_penomoran = $this->pdbOld->Table_t_izin_penomoran($tbl_t_permohonan);
                    if(!empty($tbl_t_izin_penomoran)){

                        //find m penomoran tel list
                        $m_nomor_tel_list = $this->pdbNew->GetPenomoranTelList($tbl_t_izin_penomoran->nomor);

                        // if(empty($m_penomoran_tel_list)){
                        //     //m penomoran tel list 
                        //     $m_penomoran_tel_list = $this->pdbNew->createPenomoranTel($tbl_t_izin_penomoran->nomor, $m_pertel->id_jns_penomoran);
                        // }

                        if(!empty($m_nomor_tel_list))
                        {
                            
                            //create p_penomoran_tel_pakai
                            $this->pdbNew->CreatePenomoranTelPakai($p_permohonan_penomoran, $m_nomor_tel_list, $tbl_t_izin_penomoran->no_penetapan);
                           
                            //create p_permohonan_penomoran_tel
                            $this->pdbNew->CreatePermohonanPenomoranTel($p_permohonan_penomoran->id, $m_nomor_tel_list->id_penomoran_tel);
                           
                            #histori and log permohonan
                            $data_histori = $this->pdbOld->get_t_log_permohonan($tbl_t_permohonan->id_permohonan);   
                            
                            if(!empty($data_histori)){
                                
                                foreach($data_histori as $his){
                                    
                                    $data  = (object) array('jabatan'=> '',
                                                'nama'=> '',
                                                'status' => '',
                                                'id_permohonan' => '',
                                                'tanggal_input' => '',
                                                'catatan' => '');
                                            
                                    //status permohonan
                                    $status = $this->pdbOld->GetTableStatusPermohonan($his->id_status_permohonan);
                                    if(!empty($status)){

                                        //personil permohonan
                                        $personil = $this->pdbOld->GetTablePersonil($his->id_personil);
                                        if(!empty($personil)){

                                            $jabatan = $this->pdbOld->GetTableJabatan($his->id_jabatan);
                                            $data->nama  = $personil->nama;
                                            $data->jabatan  = $jabatan->jabatan;

                                            if($status->status == 'Selesai' || $status->status == 'Upload Pemenuhan Komitmen'){
                                                $status->status = 'Izin Berlaku Efektif';
                                            }
    
                                            if($his->created == date('0000-00-00 00:00:00')){                                             
                                                $his->created = null;                                        
                                            }

                                            $data->id_permohonan = $p_permohonan_penomoran->id;
                                            $data->tanggal_input = $his->created;
                                            $data->catatan = '';
                                            $data->status = $status->status; 
 
                                            #create p permohonan log
                                            $this->pdbNew->createPermohonanLog($data);

                                            $perm_info = $this->pdbNew->getPermohonanInfo($p_permohonan_penomoran->id);
                                
                                            if($perm_info == null){
            
                                            //p_permohonan_info    
                                            $this->pdbNew->CreatePermohonanInfo($p_permohonan_penomoran, $his->created, $status->status);
            
                                            }else{
            
                                                //update p_permohonan_info
                                                $this->pdbNew->UpdatePermohonanInfo($p_permohonan_penomoran, $his->created, $status->status);
                                            
                                            }
                                        }
                                    }   
                                }
                            }
                        }                      
                    }
                }
            }
        
        //}
   
    }


    public function CreatePermohonanNewSipppdihati($old_data)
    {
        //find t_permohonan by no_permohonan
        $re = $this->pdbOld->getTablePermohonan($old_data);
       
        if(!empty($re)){
            $re->no_izin_ref = $old_data->no_sk_izin;
            $objetoRequest = new \Illuminate\Http\Request();
            $objetoRequest->setMethod('GET');
            $objetoRequest->request->add([$re]);
            $this->Migrasi->MigrasiPenomoran($objetoRequest);

        } 

    }
    

}
