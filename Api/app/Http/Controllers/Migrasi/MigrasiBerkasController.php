<?php

namespace App\Http\Controllers\Migrasi;

use App\Enums\TypeIzinJenisTel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repo\PermohonanInfoDb;
use App\Repo\Migrasi\SipppdihatiOldDb;
use App\Repo\Migrasi\SipppdihatiNewDb;
use Exception;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;
use stdClass;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class MigrasiBerkasController extends Controller
{
    private $pdbOld;
    private $pdbNew;
    public function __construct()
    {
        $this->pdbOld = new SipppdihatiOldDb();
        $this->pdbNew = new SipppdihatiNewDb();
    }

    /*public function beberkas()
    {
        $result = '/DATAWEB/be-berkas';
        return  $result;
    }

    public function berkas()
    {
        $result = '/DATAWEB/berkas';
        return  $result;
    }*/


    public function beberkas()
    {
        $result = 'D:\be-berkas';
        return  $result;
    }

    public function berkas()
    {
        $result = 'D:\berkas';
        return  $result;
    }

    public function CreatePUloData($komit_layanan_new, $syarat_izin_p)
    {   
        $result = null;

        $file_name = $syarat_izin_p->file_name_hash; 
        
        $url = $this->beberkas().'/berkas_permohonan';

        $path = ''.$url.'/'.$file_name.''; 

        $type = pathinfo($path, PATHINFO_EXTENSION);

        $data = @file_get_contents($path);

        if($data === FALSE){

            $url = $this->berkas().'/berkas_permohonan';

            $path = ''.$url.'/'.$file_name.''; 

            $type = pathinfo($path, PATHINFO_EXTENSION);

            $data = @file_get_contents($path);
                
            if($data === FALSE){
                return $result;
            }else{

                $base64 = base64_encode($data);
                if($base64){
                    $result = $this->pdbNew->CreatePUloFile($syarat_izin_p, $komit_layanan_new, $base64);
                }
            }
            
        }else{
            $base64 = base64_encode($data);
            if($base64){
                $result = $this->pdbNew->CreatePUloFile($syarat_izin_p, $komit_layanan_new, $base64);
            }
        }

        return $result;
    }

    public function CreatePuloSklo($permohonan_komit_old, $id_permohonan)
    {
        $result = null;

        $t_izin_terbit_sklo = $this->pdbOld->GetTableIzinTerbitbyIdPermohonan($permohonan_komit_old->id_permohonan);

        if(!empty($t_izin_terbit_sklo)){

            $p_ulo_sklo = $this->pdbNew->CreateTablePuloSklo($t_izin_terbit_sklo, $id_permohonan);

            if($p_ulo_sklo != null){

                $file_name = $t_izin_terbit_sklo->pdf_generate;
               
                $url = $this->berkas().'/data_izin'; 

                $path = ''.$url.'/'.$file_name.'';

                $type = pathinfo($path, PATHINFO_EXTENSION);
                
                $data = @file_get_contents($path);

                if($data === FALSE){
                    return $result;
                }else{
                    $base64 = base64_encode($data);
                    if($base64){
                        $result = $this->pdbNew->CreateSkUloFile($t_izin_terbit_sklo, $p_ulo_sklo, $base64);
                    }
                }
            }
        }
        return $result;
    }

    public function CreatePskPenetapanKomitmen($permohonan_komit_old, $id_permohonan){

        $result = null;

        $t_izin_terbit_sk_komit = $this->pdbOld->GetTableIzinTerbitbyIdPermohonan($permohonan_komit_old->id_permohonan);

        if(!empty($t_izin_terbit_sk_komit)){


            $file_name = $t_izin_terbit_sk_komit->pdf_generate;
            
            $url = $this->berkas().'/data_sk_pemenuhan'; 

            $path = ''.$url.'/'.$file_name.'';

            $type = pathinfo($path, PATHINFO_EXTENSION);
            
            $data = @file_get_contents($path);

            if($data === FALSE){
                return $result;
            }else{
                $base64 = base64_encode($data);
                if($base64){
                    $result = $this->pdbNew->CreateSkKomitFile($t_izin_terbit_sk_komit->pdf_generate, $id_permohonan, $base64);
                }
            }
            
        }
        return $result;

    }

    public function CreatePermohonanKomitKelengkapanData($id_permohonan_komit_kelengkapan,$syarat_komitmen)
    {
        //find file komit
        $result = null;

        $file_name_hash = $syarat_komitmen->file_name_hash;

        $url = $this->beberkas().'/berkas_permohonan';

        $path = ''.$url.'/'.$file_name_hash.'';

        $type = pathinfo($path, PATHINFO_EXTENSION);

        $data = @file_get_contents($path);

        if($syarat_komitmen->file_name_asli == null){
            return $result;
        }

        if($data === FALSE){

            $url = $this->berkas().'/berkas_permohonan';

            $path = ''.$url.'/'.$file_name_hash.''; 

            $type = pathinfo($path, PATHINFO_EXTENSION);

            $data = @file_get_contents($path);
            
            if($data === FALSE){
                return $result;
            }else{
                $base64 = base64_encode($data);
                
                if($base64){
                    $result = $this->pdbNew->createPermmohonanKomitKelengkapanFile($id_permohonan_komit_kelengkapan,$syarat_komitmen->file_name_asli,$base64);
                }
            }

        }else{
            $base64 = base64_encode($data);

            if($base64){
                $result = $this->pdbNew->createPermmohonanKomitKelengkapanFile($id_permohonan_komit_kelengkapan,$syarat_komitmen->file_name_asli,$base64);
            }
            
        }                
           
        return $result;
               
    }

    public function CreatePskFile($data_perm_new, $data)
    {
        
        $result = null;
        
        if(!empty($data)){

            $file_name = $data->pdf_generate;

            $url = $this->berkas().'/data_izin';

            $path = ''.$url.'/'.$file_name.'';

            $type = pathinfo($path, PATHINFO_EXTENSION);

            $data = @file_get_contents($path);

            if($data === FALSE){
                return $result;
            }else{
                $base64 = base64_encode($data);
                if($base64){
                    $result = $this->pdbNew->createPermohonanSkIzinFile($data_perm_new, $file_name, $base64);
                }
            }
        }

        return $result;
    }

    public function CreateSkPencabutanFile($rekom, $flagging_data)
    {

        //find sk file
        $result = null;

        $file_name = $rekom->filename;   
        $url = $this->berkas().'/data_pencabutan';
        $path = ''.$url.'/'.$file_name.'';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = @file_get_contents($path);
        
        if($data === FALSE){
            return $result;
        }else{
            $base64 = base64_encode($data);
            if($base64){
                $result = $this->pdbNew->createPencabutanFile($rekom,$base64,$flagging_data);
            }
        }
        return $result;
        
    }

    public function CreateSkPencabutanPenomoranFile($rekom, $flagging_data, $id_penomoran_tel_pakai){
        
         
         $result = null;
        
         $file_name = $rekom->filename;  

         $url = $this->berkas().'/data_pencabutan';
         
         $path = ''.$url.'/'.$file_name.'';
         
         $type = pathinfo($path, PATHINFO_EXTENSION);
         
         $data = @file_get_contents($path);
         
         if($data === FALSE){
             return $result;
         }else{
             $base64 = base64_encode($data);
             if($base64){
                 $result = $this->pdbNew->createPskPencabutanPenomoran($rekom, $base64, $flagging_data, $id_penomoran_tel_pakai);
             }
         }
         return $result;
    }
    
    public function buktibayar($data, $data_perm_new)
    {

        //find bukti bayar
        $bukti_bayar = $this->pdbOld->getBuktibayar($data);

        if(!empty($bukti_bayar)){

            foreach($bukti_bayar as $bayar){
                
                $result = null;

                $file_name = $bayar->file_name_hash;

                $url = $this->beberkas().'/data_bukti_bayar';

                $path = ''.$url.'/'.$file_name.'';

                $type = pathinfo($path, PATHINFO_EXTENSION);

                $data = @file_get_contents($path);

                $base64 = base64_encode($data);

                if($data === FALSE){
                    return $result;
                }else{

                    if($bayar->date_added == null){

                        $histori = $this->pdbOld->getThistoribyUploadSpm($bayar);
                        if(!empty($histori)){
                            $bayar->date_added = $histori->waktu_in;                                        
                        }else{
                            $bayar->date_added = date("Y-m-d H:i:s");
                        }

                        if($base64){
                            $this->pdbNew->createBuktiBayar($bayar,$base64,$data_perm_new->id);
                        }

                    }else{
                        
                        if($base64){
                            $this->pdbNew->createBuktiBayar($bayar,$base64,$data_perm_new->id);
                        }
                    }
                }                
            }
        }
    }

    public function CreatePermohonanKomitKelengkapanFilePos($permohonan_komit_kelengkapan, $syarat_komitmen)
    {
        
        //find file komit
        $result = null;

        $file_name_hash = $syarat_komitmen->file_name_hash;

        $url = $this->beberkas().'/berkas_permohonan';

        $path = ''.$url.'/'.$file_name_hash.'';

        $type = pathinfo($path, PATHINFO_EXTENSION);

        $data = @file_get_contents($path);

        $base64 = base64_encode($data);

        if($syarat_komitmen->file_name_asli == null){
            return $result;
        }

        if($data === FALSE){

            $url = $this->berkas().'/berkas_permohonan';

            $path = ''.$url.'/'.$file_name_hash.''; 

            $type = pathinfo($path, PATHINFO_EXTENSION);

            $data = @file_get_contents($path);
            
            if($data === FALSE){
                return $result;
            }else{
                $base64 = base64_encode($data);
                if($base64){ //can change p_permohonan_komit_kelengkapan_file
                    $result = $this->pdbNew->createPermmohonanKomitKelengkapanFile($permohonan_komit_kelengkapan->id, $syarat_komitmen->file_name_asli, $base64);
                }
            }

        }else{
            $base64 = base64_encode($data);
            if($base64){ //can change p_permohonan_komit_kelengkapan_file
                $result = $this->pdbNew->createPermmohonanKomitKelengkapanFile($permohonan_komit_kelengkapan->id, $syarat_komitmen->file_name_asli, $base64);
            }
        }
            
        return $result;        
    }


    public function CreateBerkasSkPenomoran($id_permohonan, $id_penomoran_tel_pakai)
    {
        $result = null;
        
        $berkas_penomoran = $this->pdbOld->findRekomTerbit($id_permohonan);

        if(!empty($berkas_penomoran)){

            $file_name = $berkas_penomoran->filename; 
        
            $url = $this->berkas().'/data_penomoran';
    
            $path = ''.$url.'/'.$file_name.''; 
    
            $type = pathinfo($path, PATHINFO_EXTENSION);
    
            $data = @file_get_contents($path);
    
            if($data === FALSE){   
                return $result;
            }else{
                $base64 = base64_encode($data);
                if($base64){
                    $this->pdbNew->CreatePskPenomoranFile($id_penomoran_tel_pakai, $file_name, $base64);
                }
            }

        }
        return $result;       
    }

    public function createBerkasKelengkapanFile($berkas, $id_permohonan_kelengkapan, $p_penomoran_tel)
    { 
        //create p_Permohonan_penomoran_kelengkapan

        $permohonan_penomoran_klgpn = $this->pdbNew->createPermohonanPenomoranKelengkapan($p_penomoran_tel->id, $id_permohonan_kelengkapan, 4);
        
        if(!empty($permohonan_penomoran_klgpn)){

            $result = null;

            $file_name = $berkas->file_name_hash;
    
            $url = $this->berkas().'/berkas_permohonan';
    
            $path = ''.$url.'/'.$file_name.''; 
    
            $type = pathinfo($path, PATHINFO_EXTENSION);
        
            $data = @file_get_contents($path);
    
            if($data === FALSE){   
                return $result;
            }else{
                $base64 = base64_encode($data);

                if($base64){
                    $this->pdbNew->CreateBerkasKelengkapanPenomoran( $permohonan_penomoran_klgpn->id, $file_name, $base64);
                }
                
            } 
        }
    }
}
