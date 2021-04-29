<?php

namespace App\Repo\Tel;

use App\Enums\ConstHelper;
use stdClass;
use Exception;
use App\Repo\GetNoDb;
use App\Enums\TypeDisposisi;
use App\Helper\GenerateNomor;
use Illuminate\Support\Carbon;
use App\Enums\TypeIzinJenisTel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Enums\TypeKirimKelengkapanFile;
use App\Enums\TypeKomitKelengkapanStatus;
use App\Enums\TypeUloStatus;
use App\Repo\Tel\PermohonanDisposisiTelDb;

class PermohonanKomitDb
{
    private $pdispoteldb;
    private $gen_nomor;
    private $get_nomor;

    public function __construct()
    {
        $this->pdispoteldb = new PermohonanDisposisiTelDb();
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

                $last_no_permohonan = $this->get_nomor->GetLastPermohomonanKomit();
                $no_komitmen = $this->gen_nomor->PenyelenggaraanKomit($last_no_permohonan);
                $tanggal_now  = Carbon::now();

                $q = sprintf("INSERT into p_permohonan_komit(id, no_komitmen, tanggal_input, tanggal_update, 	id_permohonan_komit_kelengkapan_status, id_permohonan) values(%d, '%s', '%s', '%s', %d, %d)", $id_permohonan_komit, $no_komitmen, $tanggal_now->format('Y-m-d H:i:s'), $tanggal_now->format('Y-m-d H:i:s'), TypeKomitKelengkapanStatus::Draft, $input->id_permohonan);
                $a = DB::insert($q);
               
                $list_id_permohonan_komit_layanan = array();
                foreach ($input->permohonan_komit_layanan as $pkl) {
                    $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'p_permohonan_komit_layanan' ", env('DB_DATABASE'));
                    $id_permohonan_komit_layanan = DB::select($q)[0]->AUTO_INCREMENT;

                    $q = sprintf("INSERT into p_permohonan_komit_layanan(id, id_permohonan_komit,id_permohonan_layanan) values(%d, %d, %d)", $id_permohonan_komit_layanan,  $id_permohonan_komit, $pkl->id_permohonan_layanan);
                    $a = DB::insert($q);
                    
                    foreach ($pkl->isi_row as $iu) {
                        $q = sprintf("INSERT into p_komitmen_ulo_proses(id_komitmen_ulo, baris, value, id_permohonan_komit_layanan) values(%d, %d, '%s', %d)",  $iu->id_komitmen_ulo, $iu->baris, $iu->value, $id_permohonan_komit_layanan);
                        $a = DB::insert($q);
                    }

                    if($pkl->id_izin_jenis == TypeIzinJenisTel::Jaringan)
                    {
                        foreach ($pkl->isi_kinerja as $ik) {
                            $q = sprintf("INSERT into p_permohonan_kinerja(id_permohonan_komit_layanan, tahun, jenis, value, baris) values(%d, %d, '%s', '%s', %d)",  $id_permohonan_komit_layanan, $ik->tahun, $ik->jenis, $ik->value, $ik->baris);
                            $a = DB::insert($q);
                        }
                    }

                    $model_permohonan_komit = new stdClass();
                    $model_permohonan_komit->id_permohonan_komit_layanan = $id_permohonan_komit_layanan;
                    $model_permohonan_komit->id_layanan = $pkl->id_layanan;
                    array_push($list_id_permohonan_komit_layanan, $model_permohonan_komit);
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
        $q = sprintf("SELECT count(id) as jumlah from p_permohonan_komit where id_permohonan = %d and id_permohonan_komit_kelengkapan_status=%d", $id_permohonan, TypeKomitKelengkapanStatus::Kirim);
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
            $q = sprintf("UPDATE p_permohonan_komit set id_permohonan_komit_kelengkapan_status = %d where id = %d",$input->id_permohonan_komit_kelengkapan_status, $input->id_permohonan_komit);
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
            $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'p_permohonan_komit_kelengkapan' ", env('DB_DATABASE'));
            $id_permohonan_komit_kelengkapan = DB::select($q)[0]->AUTO_INCREMENT;

            $q = sprintf("INSERT into p_permohonan_komit_kelengkapan(id, id_permohonan_komit_layanan, id_jenis_kelengkapan, id_permohonan_komit_kelengkapan_status) values(%d, %d, %d, %d)",$id_permohonan_komit_kelengkapan, $input->id_permohonan_komit_layanan, $input->id_jenis_kelengkapan, TypeKomitKelengkapanStatus::Kirim);
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
            $q = sprintf("INSERT into p_permohonan_komit_file(id_permohonan_komit_kelengkapan, nama, stream) values(%d, '%s', '%s')", $input->id_permohonan_komit_kelengkapan, $input->nama, $input->stream);
            $a = DB::insert($q);
    
            if ($input->type_kirim_file == TypeKirimKelengkapanFile::Akhir) {
                //update status kelengkapan file di table permohonan komit
                $model = new stdClass();
                $model->id_permohonan_komit_kelengkapan_status = TypeKomitKelengkapanStatus::Kirim;
                $model->id_permohonan_komit = $input->id_permohonan_komit;
                $this->UpdateStatus($model);
    
                //setelah itu didisposisi ke kasubdit
                //insert to p_permohonan_disposisi
                $dispo = new stdClass();
                $dispo->type_disposisi = TypeDisposisi::Post;
                $dispo->id_izin_jenis = $input->id_izin_jenis;
                $dispo->id_permohonan = $input->id_permohonan;
                $this->pdispoteldb->Proses($dispo);
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
            $q = sprintf("INSERT into p_permohonan_komit_file(id_permohonan_komit_kelengkapan, nama, stream) values(%d, '%s', '%s')", $input->id_permohonan_komit_kelengkapan, $input->nama, $input->stream);
            $a = DB::insert($q);
    
            if ($input->type_kirim_file == TypeKirimKelengkapanFile::Akhir) {
                //update status kelengkapan file di table permohonan komit
                $model = new stdClass();
                $model->id_permohonan_komit_kelengkapan_status = TypeKomitKelengkapanStatus::Kirim;
                $model->id_permohonan_komit = $input->id_permohonan_komit;
                $this->UpdateStatus($model);
    
                //setelah itu didisposisi ke kasubdit
                //insert to p_permohonan_disposisi
                $dispo = new stdClass();
                $dispo->type_disposisi = TypeDisposisi::Edit;
                $dispo->id_permohonan = $input->id_permohonan;
                $dispo->id_izin_jenis = TypeIzinJenisTel::All;
                $this->pdispoteldb->Proses($dispo);
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
        $q = sprintf("SELECT * from p_permohonan_komit_file where id = %d", $id_permohonan_komit_file);
        $result = DB::select($q);
        return $result;
    }

    public function GetMKomitmenUlo($id_layanan)
    {
        $q = sprintf("SELECT * from m_komitmen_ulo where id_layanan = %d order by urutan asc", $id_layanan);
        $result = DB::select($q);
        return $result;
    }

    public function UpdateStatusKelengkapan($list_input)
    {
        try
        {
            foreach($list_input as $li)
            {
                $q = sprintf("UPDATE p_permohonan_komit_kelengkapan set id_permohonan_komit_kelengkapan_status = %d where id = %d",$li->id_permohonan_komit_kelengkapan_status, $li->id_permohonan_komit_kelengkapan);
                $a = DB::update($q);

                if($li->id_permohonan_komit_kelengkapan_status == TypeKomitKelengkapanStatus::Ditolak)
                {
                    $tanggal_now  = Carbon::now();
                    $q = sprintf("INSERT into p_permohonan_komit_catatan(id_permohonan_komit_kelengkapan, catatan, tanggal_input) values(%d, '%s', '%s')",$li->id_permohonan_komit_kelengkapan, $li->catatan, $tanggal_now->format('Y-m-d H:i:s'));
                    $a = DB::update($q);
                }
            }
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function GetKomitCatatan($id_permohonan_komit_kelengkapan)
    {
        $model = new stdClass();
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
        
        return $model;
    }

    public function IsMekanismeUlo($id_permohonan)
    {
        $q = sprintf("SELECT * from p_permohonan_komit pk left join p_ulo p on pk.id = p.id_permohonan_komit where pk.id_permohonan=%d", $id_permohonan);
        $result = DB::select($q)[0];
        $return = true;
        if($result->id_ulo_status != null || $result->id_permohonan_komit_kelengkapan_status != TypeKomitKelengkapanStatus::Disetujui)
        {
            $return = false;
        }
        return $return;
    }

    public function IsEditTanggalUlo($id_permohonan)
    {
        $q = sprintf("SELECT count(pk.id) as jumlah from p_permohonan_komit pk left join p_ulo p on pk.id = p.id_permohonan_komit where pk.id_permohonan=%d and p.id_ulo_status=%d", $id_permohonan, TypeUloStatus::Ditolak);
        $result = DB::select($q)[0]->jumlah;
        $return = true;
        if($result == 0)
        {
            $return = false;
        }
        return $return;
    }

    public function IsUploadUjiMandiri($id_permohonan)
    {
        $q = sprintf("SELECT COUNT(mup.value) as jumlah FROM p_mekanisme_ulo_proses mup left join p_permohonan_komit_layanan pkl on mup.id_permohonan_komit_layanan = pkl.id left join p_permohonan_komit pk on pkl.id_permohonan_komit = pk.id where pk.id_permohonan=%d and mup.value = '%s' group by pk.id_permohonan, mup.value", $id_permohonan, ConstHelper::uji_mandiri);
        $result = DB::select($q)[0]->jumlah;
        $return = true;
        if($result == 0)
        {
            $return = false;
        }
        return $return;
    }

    public function GetSKIzin($id_sk_izin)
    {
        $q = sprintf("SELECT * from p_sk_izin_file where id = %d", $id_sk_izin);
        $result = DB::select($q);
        return $result;
    }

    public function GetSKPenomoran($id_sk_penomoran)
    {
        $q = sprintf("SELECT * from p_sk_penomoran_file where id = %d", $id_sk_penomoran);
        $result = DB::select($q);
        return $result;
    }

    public function GetSKUlo($id_sk_ulo)
    {
        $q = sprintf("SELECT * from p_sk_ulo_file where id = %d", $id_sk_ulo);
        $result = DB::select($q);
        return $result;
    }

    public function GetSKKomit($id_sk_komit)
    {
        $q = sprintf("SELECT * from p_sk_penetapan_komit_file where id = %d", $id_sk_komit);
        $result = DB::select($q);
        return $result;
    }
}