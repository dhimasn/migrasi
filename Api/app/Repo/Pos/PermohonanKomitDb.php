<?php

namespace App\Repo\Pos;

use stdClass;
use Exception;
use App\Repo\GetNoDb;
use App\Enums\TypeDisposisi;
use App\Helper\GenerateNomor;
use Illuminate\Support\Carbon;
use App\Enums\TypeIzinJenisPos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Enums\TypeKirimKelengkapanFile;
use App\Enums\TypeKomitKelengkapanStatus;
use App\Repo\Pos\PermohonanDisposisiPosDb;
use Psr\Log\NullLogger;

class PermohonanKomitDb
{
    private $pdispoposdb;
    private $gen_nomor;
    private $get_nomor;

    public function __construct()
    {
        $this->pdispoposdb = new PermohonanDisposisiPosDb();
        $this->gen_nomor = new GenerateNomor();
        $this->get_nomor = new GetNoDb();
    }

    public function PostPermohonanKomit($input)
    {
        $model = new stdClass();
        try
        {
            if($this->IsExistPermohonan($input->id_permohonan))
            {
                $model->status = false;
                return $model;
            }
            else
            {
                $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'p_permohonan_komit' ", env('DB_DATABASE'));
                $id_permohonan_komit = DB::select($q)[0]->AUTO_INCREMENT;
                $q = sprintf("SELECT a.*, (SELECT COUNT(b.id_permohonan) FROM p_permohonan_layanan b WHERE b.id_permohonan=a.id) AS permohonan_baru, (SELECT COUNT(c.id_permohonan) FROM p_permohonan_penambahan_layanan_pos c WHERE c.id_permohonan=a.id) AS penambahan_layanan FROM p_permohonan a WHERE a.id=".$input->id_permohonan);
                $getlayanan = DB::select($q);
                $permohonan_baru = 0;
                $penambahan_layanan = 0;
                foreach ($getlayanan as $key => $value) {
                    if($value->permohonan_baru>0){
                        $permohonan_baru = 1;
                    }elseif($value->penambahan_layanan>0){
                        $penambahan_layanan = 1;
                    }else{
                        echo'';
                    }
                }
                $last_no_permohonan = $this->get_nomor->GetLastPermohomonanKomit();
                $no_komitmen = $this->gen_nomor->PenyelenggaraanKomit($last_no_permohonan);
                $tanggal_now  = Carbon::now();
                if($permohonan_baru==1){
                    $q = sprintf("INSERT into p_permohonan_komit(id, no_komitmen, tanggal_input, tanggal_update, 	id_permohonan_komit_kelengkapan_status, id_permohonan) values(%d, '%s', '%s', '%s', %d, %d)", $id_permohonan_komit, $no_komitmen, $tanggal_now->format('Y-m-d H:i:s'), $tanggal_now->format('Y-m-d H:i:s'), TypeKomitKelengkapanStatus::Draft, $input->id_permohonan);
                }elseif($penambahan_layanan==1){
                    $q = sprintf("INSERT into p_permohonan_komit_pos(id, no_komitmen, tanggal_input, tanggal_update, 	id_permohonan_komit_kelengkapan_status, id_permohonan) values(%d, '%s', '%s', '%s', %d, %d)", $id_permohonan_komit, $no_komitmen, $tanggal_now->format('Y-m-d H:i:s'), $tanggal_now->format('Y-m-d H:i:s'), TypeKomitKelengkapanStatus::Draft, $input->id_permohonan);
                }else{
                    echo'';
                }
                $a = DB::insert($q);
                $list_id_permohonan_komit_layanan = array();
                foreach ($input->permohonan_komit_layanan as $pkl) {
                    if($permohonan_baru==1){
                        $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'p_permohonan_komit_layanan' ", env('DB_DATABASE'));
                        $id_permohonan_komit_layanan = DB::select($q)[0]->AUTO_INCREMENT;
                        // $q_get_last_id = DB::select('SELECT a.id FROM p_permohonan_komit_layanan a ORDER BY a.id DESC LIMIT 1');
                        // $id_permohonan_komit_layanan = '';
                        // if($q_get_last_id==NULL){
                        //     $id_permohonan_komit_layanan = 1;
                        // }else{
                        //     foreach ($q_get_last_id as $key => $value) {
                        //         $id_permohonan_komit_layanan = ($value->id)+1;
                        //     }
                        // }
                        $insert_1 = sprintf("INSERT INTO `p_permohonan_komit_layanan` (`id`, `id_permohonan_komit`, `id_permohonan_layanan`) VALUES ('$id_permohonan_komit_layanan', '$id_permohonan_komit', '$pkl->id_permohonan_layanan')");
                        DB::insert($insert_1);

                        $model_permohonan_komit = new stdClass();
                        $model_permohonan_komit->id_permohonan_komit_layanan = $id_permohonan_komit_layanan;
                        $model_permohonan_komit->id_layanan = $pkl->id_layanan;
                        array_push($list_id_permohonan_komit_layanan, $model_permohonan_komit);
                    }elseif($penambahan_layanan==1){
                        $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'p_permohonan_komit_layanan_pos' ", env('DB_DATABASE'));
                        $id_permohonan_komit_layanan = DB::select($q)[0]->AUTO_INCREMENT;
                        $insert_1 = sprintf("INSERT INTO `p_permohonan_komit_layanan_pos` (`id`, `id_permohonan_komit`, `id_permohonan_layanan`) VALUES ('$id_permohonan_komit_layanan', '$id_permohonan_komit', '$pkl->id_permohonan_layanan')");
                        DB::insert($insert_1);

                        $model_permohonan_komit = new stdClass();
                        $model_permohonan_komit->id_permohonan_komit_layanan = $id_permohonan_komit_layanan;
                        $model_permohonan_komit->id_layanan = $pkl->id_layanan;
                        array_push($list_id_permohonan_komit_layanan, $model_permohonan_komit);
                    }else{
                        echo'';
                    }
                }
                $model->list_id_permohonan_komit_layanan = $list_id_permohonan_komit_layanan;
                $model->id_permohonan_komit = $id_permohonan_komit;
                $model->status = true;
                return $model;
            }
        }
        catch(Exception $e)
        {
            $model->status = false;
            return $model;
        }
    }

    private function IsExistPermohonan($id_permohonan)
    {
        $q = sprintf("SELECT a.*, (SELECT COUNT(b.id_permohonan) FROM p_permohonan_layanan b WHERE b.id_permohonan=a.id) AS permohonan_baru, (SELECT COUNT(c.id_permohonan) FROM p_permohonan_penambahan_layanan_pos c WHERE c.id_permohonan=a.id) AS penambahan_layanan FROM p_permohonan a WHERE a.id=".$id_permohonan);
        $getlayanan = DB::select($q);
        foreach ($getlayanan as $key => $value) {
            if($value->permohonan_baru>0){
                $q = sprintf("SELECT count(id) as jumlah from p_permohonan_komit where id_permohonan = %d and id_permohonan_komit_kelengkapan_status=%d", $id_permohonan, TypeKomitKelengkapanStatus::Kirim);
            }elseif($value->penambahan_layanan>0){
                $q = sprintf("SELECT count(id) as jumlah from p_permohonan_komit_pos where id_permohonan = %d and id_permohonan_komit_kelengkapan_status=%d", $id_permohonan, TypeKomitKelengkapanStatus::Kirim);
            }else{
                echo'';
            }
        }
        $result = DB::select($q)[0]->jumlah;
        $return = true;
        if($result == 0)
        {
            $return = false;
        }
        return $return;
    }

    public function UpdateStatus($input)
    {
        try
        {
            $q = sprintf("SELECT a.*, (SELECT COUNT(b.id_permohonan) FROM p_permohonan_layanan b WHERE b.id_permohonan=a.id) AS permohonan_baru, (SELECT COUNT(c.id_permohonan) FROM p_permohonan_penambahan_layanan_pos c WHERE c.id_permohonan=a.id) AS penambahan_layanan FROM p_permohonan a WHERE a.id=".$input->id_permohonan);
            $getlayanan = DB::select($q);
            foreach ($getlayanan as $key => $value) {
                if($value->permohonan_baru>0){
                    $q = sprintf("UPDATE p_permohonan_komit set id_permohonan_komit_kelengkapan_status = %d where id = %d",$input->id_permohonan_komit_kelengkapan_status, $input->id_permohonan_komit);
                }elseif($value->penambahan_layanan>0){
                    $q = sprintf("UPDATE p_permohonan_komit_pos set id_permohonan_komit_kelengkapan_status = %d where id = %d",$input->id_permohonan_komit_kelengkapan_status, $input->id_permohonan_komit);
                }else{
                    echo'';
                }
            }
            $a = DB::update($q);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function PostPermohonanKomitKelengkapan($input)
    {
        $model = new stdClass();
        try
        {
            $q = sprintf("SELECT a.*, (SELECT COUNT(b.id_permohonan) FROM p_permohonan_layanan b WHERE b.id_permohonan=a.id) AS permohonan_baru, (SELECT COUNT(c.id_permohonan) FROM p_permohonan_penambahan_layanan_pos c WHERE c.id_permohonan=a.id) AS penambahan_layanan FROM p_permohonan a WHERE a.id=".$input->id_permohonan);
            $getlayanan = DB::select($q);
            foreach ($getlayanan as $key => $value) {
                if($value->permohonan_baru>0){
                    $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'p_permohonan_komit_kelengkapan' ", env('DB_DATABASE'));
                    $id_permohonan_komit_kelengkapan = DB::select($q)[0]->AUTO_INCREMENT;

                    $q = sprintf("INSERT into p_permohonan_komit_kelengkapan(id, id_permohonan_komit_layanan, id_jenis_kelengkapan, id_permohonan_komit_kelengkapan_status) values(%d, %d, %d, %d)",$id_permohonan_komit_kelengkapan, $input->id_permohonan_komit_layanan, $input->id_jenis_kelengkapan, TypeKomitKelengkapanStatus::Kirim);
                }elseif($value->penambahan_layanan>0){
                    $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'p_permohonan_komit_kelengkapan_pos' ", env('DB_DATABASE'));
                    $id_permohonan_komit_kelengkapan = DB::select($q)[0]->AUTO_INCREMENT;

                    $q = sprintf("INSERT into p_permohonan_komit_kelengkapan_pos(id, id_permohonan_komit_layanan, id_jenis_kelengkapan, id_permohonan_komit_kelengkapan_status) values(%d, %d, %d, %d)",$id_permohonan_komit_kelengkapan, $input->id_permohonan_komit_layanan, $input->id_jenis_kelengkapan, TypeKomitKelengkapanStatus::Kirim);
                }else{
                    echo'';
                }
            }
            $a = DB::insert($q);
            $model->status = true;
            $model->id_permohonan_komit_kelengkapan = $id_permohonan_komit_kelengkapan;
            return $model;
        }
        catch(Exception $e)
        {
            $model->status = false;
            return $model;
        }
    }

    public function PostPermohonanKomitKelengkapanFile($input)
    {
        try
        {
            $q = sprintf("SELECT a.*, (SELECT COUNT(b.id_permohonan) FROM p_permohonan_layanan b WHERE b.id_permohonan=a.id) AS permohonan_baru, (SELECT COUNT(c.id_permohonan) FROM p_permohonan_penambahan_layanan_pos c WHERE c.id_permohonan=a.id) AS penambahan_layanan FROM p_permohonan a WHERE a.id=".$input->id_permohonan);
            $getlayanan = DB::select($q);
            foreach ($getlayanan as $key => $value) {
                if($value->permohonan_baru>0){
                    $q = sprintf("INSERT into p_permohonan_komit_file(id_permohonan_komit_kelengkapan, nama, stream) values(%d, '%s', '%s')", $input->id_permohonan_komit_kelengkapan, $input->nama, $input->stream);
                }elseif($value->penambahan_layanan>0){
                    $q = sprintf("INSERT into p_permohonan_komit_file_pos(id_permohonan_komit_kelengkapan, nama, stream) values(%d, '%s', '%s')", $input->id_permohonan_komit_kelengkapan, $input->nama, $input->stream);
                }else{
                    echo'';
                }
            }
            $a = DB::insert($q);
            if ($input->type_kirim_file == TypeKirimKelengkapanFile::Akhir) {
                // update status kelengkapan file di table permohonan komit
                $model = new stdClass();
                $model->id_permohonan_komit_kelengkapan_status = TypeKomitKelengkapanStatus::Kirim;
                $model->id_permohonan_komit = $input->id_permohonan_komit;
                $model->id_permohonan = $input->id_permohonan;
                $this->UpdateStatus($model);
                // setelah itu didisposisi ke kasubdit
                // insert to p_permohonan_disposisi
                $dispo = new stdClass();
                $dispo->type_disposisi = TypeDisposisi::Post;
                $dispo->id_izin_jenis = $input->id_izin_jenis;
                $dispo->id_permohonan = $input->id_permohonan;
                $this->pdispoposdb->Proses($dispo);
            }
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function DeletePermohonanKomitKelengkapanFile($input)
    {
        try
        {
            $q = sprintf("DELETE from p_permohonan_komit_file where id_permohonan_komit_kelengkapan = %d", $input->id_permohonan_komit_kelengkapan);
            $a = DB::delete($q);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function EditPermohonanKomitKelengkapanFile($input)
    {
        try
        {
            $q = sprintf("SELECT a.*, (SELECT COUNT(b.id_permohonan) FROM p_permohonan_layanan b WHERE b.id_permohonan=a.id) AS permohonan_baru, (SELECT COUNT(c.id_permohonan) FROM p_permohonan_penambahan_layanan_pos c WHERE c.id_permohonan=a.id) AS penambahan_layanan FROM p_permohonan a WHERE a.id=".$input->id_permohonan);
            $getlayanan = DB::select($q);
            foreach ($getlayanan as $key => $value) {
                if($value->permohonan_baru>0){
                    $q = sprintf("INSERT into p_permohonan_komit_file(id_permohonan_komit_kelengkapan, nama, stream) values(%d, '%s', '%s')", $input->id_permohonan_komit_kelengkapan, $input->nama, $input->stream);
                }elseif($value->penambahan_layanan>0){
                    $q = sprintf("INSERT into p_permohonan_komit_file_pos(id_permohonan_komit_kelengkapan, nama, stream) values(%d, '%s', '%s')", $input->id_permohonan_komit_kelengkapan, $input->nama, $input->stream);
                }else{
                    echo'';
                }
            }
            $a = DB::insert($q);
    
            if ($input->type_kirim_file == TypeKirimKelengkapanFile::Akhir) {
                // update status kelengkapan file di table permohonan komit
                $model = new stdClass();
                $model->id_permohonan_komit_kelengkapan_status = TypeKomitKelengkapanStatus::Kirim;
                $model->id_permohonan_komit = $input->id_permohonan_komit;
                $model->id_permohonan = $input->id_permohonan;
                $this->UpdateStatus($model);
                // seteelah itu didisposisi ke kasubdit
                // insert to p_permohonan_disposisi
                $dispo = new stdClass();
                $dispo->type_disposisi = TypeDisposisi::Edit;
                $dispo->id_permohonan = $input->id_permohonan;
                $q_get_permohonan = DB::select("SELECT *  FROM `p_permohonan` WHERE `id` = ".$input->id_permohonan." ORDER BY `id_izin_jenis` ASC");
                $id_izin_jenis = '';
                foreach ($q_get_permohonan as $key => $value) {
                    $id_izin_jenis = $value->id_izin_jenis;
                }
                $dispo->id_izin_jenis = $id_izin_jenis;
                $this->pdispoposdb->Proses($dispo);
            }

            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function GetKKomitKelengkapan($id_layanan)
    {
        $q = sprintf("SELECT * from k_komit_kelengkapan where id_layanan = %d order by urutan asc", $id_layanan);
        $result = DB::select($q);
        return $result;
    }

    public function GetKomitFile($id_permohonan_komit_file)
    {
        $q = sprintf("SELECT a.*, (SELECT COUNT(bb.id_permohonan) FROM p_permohonan_layanan bb WHERE bb.id_permohonan=e.id) AS permohonan_baru from p_permohonan_komit_file a LEFT JOIN p_permohonan_komit_kelengkapan b ON a.id_permohonan_komit_kelengkapan=b.id LEFT JOIN p_permohonan_komit_layanan c ON b.id_permohonan_komit_layanan=c.id LEFT JOIN p_permohonan_komit d ON c.id_permohonan_komit=d.id LEFT JOIN p_permohonan e ON d.id_permohonan=e.id where a.id = %d", $id_permohonan_komit_file);
        $result = DB::select($q);
        foreach ($result as $key => $value) {
            if($value->permohonan_baru>0){
                echo'';
            }else{
                $q = sprintf("SELECT a.*, (SELECT COUNT(bb.id_permohonan) FROM p_permohonan_layanan_pos bb WHERE bb.id_permohonan=e.id) AS penambahan_layanan from p_permohonan_komit_file_pos a LEFT JOIN p_permohonan_komit_kelengkapan_pos b ON a.id_permohonan_komit_kelengkapan=b.id LEFT JOIN p_permohonan_komit_layanan_pos c ON b.id_permohonan_komit_layanan=c.id LEFT JOIN p_permohonan_komit_pos d ON c.id_permohonan_komit=d.id LEFT JOIN p_permohonan e ON d.id_permohonan=e.id where a.id = %d", $id_permohonan_komit_file);
                $result = DB::select($q);
                foreach ($result as $key => $row) {
                    if($row->penambahan_layanan>0){
                        echo'';
                    }else{
                        $result = '';
                    }
                }
            }
        }
        return $result;
    }

    public function GetMKomitmenUlo($id_layanan)
    {
        $q = sprintf("SELECT * from m_komitmen_ulo where id_layanan = %d order by urutan asc", $id_layanan);
        $result = DB::select($q);
        return $result;
    }

    public function UpdateStatusKelengkapan($list_input,$id)
    {
        try
        {
            foreach($list_input as $li)
            {
                $qq = sprintf("SELECT a.*, (SELECT COUNT(b.id_permohonan) FROM p_permohonan_layanan b WHERE b.id_permohonan=a.id) AS permohonan_baru, (SELECT COUNT(c.id_permohonan) FROM p_permohonan_penambahan_layanan_pos c WHERE c.id_permohonan=a.id) AS penambahan_layanan FROM p_permohonan a WHERE a.id=".$id);
                $getlayanan = DB::select($qq);
                foreach ($getlayanan as $key => $value) {
                    if($value->permohonan_baru>0){
                        $q = sprintf("UPDATE p_permohonan_komit_kelengkapan set id_permohonan_komit_kelengkapan_status = %d where id = %d",$li->id_permohonan_komit_kelengkapan_status, $li->id_permohonan_komit_kelengkapan);
                        $a = DB::update($q);
        
                        if($li->id_permohonan_komit_kelengkapan_status == TypeKomitKelengkapanStatus::Ditolak)
                        {
                            $tanggal_now  = Carbon::now();
                            $q = sprintf("INSERT into p_permohonan_komit_catatan(id_permohonan_komit_kelengkapan, catatan, tanggal_input) values(%d, '%s', '%s')",$li->id_permohonan_komit_kelengkapan, $li->catatan, $tanggal_now->format('Y-m-d H:i:s'));
                            $a = DB::update($q);
                        }
                    }elseif($value->penambahan_layanan>0){
                        $q = sprintf("UPDATE p_permohonan_komit_kelengkapan_pos set id_permohonan_komit_kelengkapan_status = %d where id = %d",$li->id_permohonan_komit_kelengkapan_status, $li->id_permohonan_komit_kelengkapan);
                        $a = DB::update($q);
        
                        if($li->id_permohonan_komit_kelengkapan_status == TypeKomitKelengkapanStatus::Ditolak)
                        {
                            $tanggal_now  = Carbon::now();
                            $q = sprintf("INSERT into p_permohonan_komit_catatan_pos(id_permohonan_komit_kelengkapan, catatan, tanggal_input) values(%d, '%s', '%s')",$li->id_permohonan_komit_kelengkapan, $li->catatan, $tanggal_now->format('Y-m-d H:i:s'));
                            $a = DB::update($q);
                        }
                    }else{
                        echo'';
                    }
                }
            }
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function GetKomitCatatan($id_permohonan_komit_kelengkapan,$id_permohonan)
    {
        $model = new stdClass();
        $qq = sprintf("SELECT a.*, (SELECT COUNT(b.id_permohonan) FROM p_permohonan_layanan b WHERE b.id_permohonan=a.id) AS permohonan_baru, (SELECT COUNT(c.id_permohonan) FROM p_permohonan_penambahan_layanan_pos c WHERE c.id_permohonan=a.id) AS penambahan_layanan FROM p_permohonan a WHERE a.id=".$id_permohonan);
        $getlayanan = DB::select($qq);
        foreach ($getlayanan as $key => $value) {
            if($value->permohonan_baru>0){
                $q = sprintf("SELECT nama, id from p_permohonan_komit_file where id_permohonan_komit_kelengkapan = %d", $id_permohonan_komit_kelengkapan);
                $result = DB::select($q);
                $result_array = array();
                foreach($result as $r)
                {
                    $r->id = Crypt::encryptString($r->id);
                    array_push($result_array, $r); 
                }
                $model->files = $result_array; 
                
                $q = sprintf("SELECT * from p_permohonan_komit_catatan where id_permohonan_komit_kelengkapan = %d order by tanggal_input desc", $id_permohonan_komit_kelengkapan);
                $result = DB::select($q);
                $model->catatan = $result;
            }elseif($value->penambahan_layanan>0){
                $q = sprintf("SELECT nama, id from p_permohonan_komit_file_pos where id_permohonan_komit_kelengkapan = %d", $id_permohonan_komit_kelengkapan);
                $result = DB::select($q);
                $result_array = array();
                foreach($result as $r)
                {
                    $r->id = Crypt::encryptString($r->id);
                    array_push($result_array, $r); 
                }
                $model->files = $result_array; 
                
                $q = sprintf("SELECT * from p_permohonan_komit_catatan_pos where id_permohonan_komit_kelengkapan = %d order by tanggal_input desc", $id_permohonan_komit_kelengkapan);
                $result = DB::select($q);
                $model->catatan = $result;
            }else{
                echo'';
            }
        }
        return $model;
    }
}