<?php

namespace App\Repo\Tel;

use stdClass;
use Exception;
use App\Repo\GetNoDb;
use App\Enums\TypeIzin;
use App\Enums\TypeDisposisi;
use App\Helper\GenerateNomor;
use Illuminate\Support\Carbon;
use App\Enums\TypeIzinJenisTel;
use App\Enums\TypeNomorPenomoran;
use Illuminate\Support\Facades\DB;
use App\Enums\TypePermohonanStatus;
use Illuminate\Support\Facades\Crypt;
use App\Enums\TypeKirimKelengkapanFile;
use App\Enums\TypeKomitKelengkapanStatus;

class PenomoranDb
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

    public function GetByIdLayanan($id_layanan)
    {
        $q = sprintf("SELECT * from m_penomoran_tel where id_layanan = %d", $id_layanan);
        $result = DB::select($q);
        return $result;
    }

    public function GetListNomorTidakAktif($id_penomoran_tel, $nomor)
    {
        $q = sprintf("SELECT id, nomor as text from m_penomoran_tel_list where id_penomoran_tel = %d and id_penomoran_status = %d and nomor like '%%%s%%'", $id_penomoran_tel, TypeNomorPenomoran::TidakAktif, $nomor);
        $result = DB::select($q);
        return $result;
    }

    public function PostPenomoranPakai($input)
    {
        try
        {
            $q = sprintf("INSERT into p_penomoran_tel_pakai(id_penomoran_tel_list, id_perusahaan, id_penomoran_status, no_sk_penomoran, id_permohonan) values(%d, %d, %d, '%s', %d)", $input->id_penomoran_tel_list, $input->id_perusahaan,TypeNomorPenomoran::Aktif, $input->no_sk_penomoran, $input->id_permohonan);

            $a = DB::insert($q);

            $q = sprintf("UPDATE m_penomoran_tel_list set id_penomoran_status=%d where id=%d", TypeNomorPenomoran::Aktif, $input->id_penomoran_tel_list);
            
            $a = DB::update($q);

            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function GetValidasiPenomoran($no_sk_penomoran, $id_pemohon, $jenis_layanan)
    {
        $q = sprintf("SELECT DISTINCT nomor.*, l.* FROM p_penomoran_tel_pakai nomor LEFT JOIN m_perusahaan per on nomor.id_perusahaan = per.id LEFT JOIN p_permohonan p on nomor.id_permohonan = p.id LEFT JOIN m_penomoran_tel_list nomor_list on nomor.id_penomoran_tel_list = nomor_list.id LEFT JOIN m_penomoran_tel nomor_tel on nomor_tel.id = nomor_list.id_penomoran_tel LEFT JOIN k_layanan l ON l.id = nomor_tel.id_layanan WHERE nomor.no_sk_penomoran = '%s' AND per.id_pemohon = %d AND p.id_permohonan_status = %d AND l.id in (%s)", $no_sk_penomoran, $id_pemohon, TypePermohonanStatus::Efektif, $jenis_layanan);
        $list_permohonan = DB::select($q);
        return $list_permohonan;
    }

    public function GetPenomoranKelengkapan($id_layanan)
    {
        $q = sprintf("SELECT pk.*, ptel.id_layanan, ptel.jenis_penomoran FROM k_penomoran_kelengkapan pk LEFT JOIN m_penomoran_tel ptel on pk.id_penomoran_tel = ptel.id where ptel.id_layanan = %d ORDER BY pk.urutan asc", $id_layanan);
        $result = DB::select($q);
        return $result;
    }

    public function PostPermohonanPenomoran($input)
    {
        $model = new stdClass();
        try
        {
            $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'p_permohonan' ", env('DB_DATABASE'));
            $id_permohonan = DB::select($q)[0]->AUTO_INCREMENT;

            $last_no_permohonan = $this->get_nomor->GetLastPermohomonanKomit();
            $no_penyelenggaraan = $this->gen_nomor->PenyelenggaraanKomit($last_no_permohonan);
            $tanggal_input  = Carbon::now();

            $q = sprintf("INSERT into p_permohonan(id, no_penyelenggaraan,no_sk_izin,id_izin_jenis,id_perusahaan,id_permohonan_status,tanggal_input) values(%d, '%s', '%s', %d, %d, %d, '%s');", $id_permohonan, $no_penyelenggaraan, '', TypeIzinJenisTel::Penomoran, $input->id_perusahaan, TypePermohonanStatus::BelumEfektif, $tanggal_input->format('Y-m-d H:i:s'));
            $a = DB::insert($q);

            $list_id_permohonan_penomoran_tel = array();
            foreach ($input->permohonan_penomoran_tel as $pkl) {
                $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'p_permohonan_penomoran_tel' ", env('DB_DATABASE'));
                $id_permohonan_penomoran_tel = DB::select($q)[0]->AUTO_INCREMENT;

                $q = sprintf("INSERT into p_permohonan_penomoran_tel(id, id_permohonan, id_penomoran_tel) values(%d, %d, %d)", $id_permohonan_penomoran_tel,  $id_permohonan, $pkl->id_penomoran_tel);
                $a = DB::insert($q);

                $model_penomoran_tel = new stdClass();
                $model_penomoran_tel->id_permohonan_penomoran_tel = $id_permohonan_penomoran_tel;
                $model_penomoran_tel->id_penomoran_tel = $pkl->id_penomoran_tel;
                array_push($list_id_permohonan_penomoran_tel, $model_penomoran_tel);
            }

            $model->list_id_permohonan_penomoran_tel = $list_id_permohonan_penomoran_tel;
            $model->id_permohonan = $id_permohonan;
            $model->id_penomoran_tel = 
            $model->status = true;
            return $model;
        }
        catch(Exception $e)
        {
            $model->status = false;
            return $model;
        }
    }

    public function PostPermohonanPenomoranKelengkapan($input)
    {
        $model = new stdClass();
        try
        {
            $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'p_permohonan_penomoran_kelengkapan' ", env('DB_DATABASE'));
            $id_permohonan_penomoran_kelengkapan = DB::select($q)[0]->AUTO_INCREMENT;

            $q = sprintf("INSERT into p_permohonan_penomoran_kelengkapan(id, id_permohonan_penomoran_tel, id_penomoran_kelengkapan, id_permohonan_komit_kelengkapan_status) values(%d, %d, %d, %d)",$id_permohonan_penomoran_kelengkapan, $input->id_permohonan_penomoran_tel, $input->id_penomoran_kelengkapan, TypeKomitKelengkapanStatus::Kirim);
            $a = DB::insert($q);

            $model->status = true;
            $model->id_permohonan_penomoran_kelengkapan = $id_permohonan_penomoran_kelengkapan;
            return $model;
        }
        catch(Exception $e)
        {
            $model->status = false;
            return $model;
        }
    }

    public function PostPermohonanPenomoranKelengkapanFile($input)
    {
        try
        {
            $q = sprintf("INSERT into p_permohonan_penomoran_kelengkapan_file(id_permohonan_penomoran_kelengkapan, nama, stream) values(%d, '%s', '%s')", $input->id_permohonan_penomoran_kelengkapan, $input->nama, $input->stream);
            $a = DB::insert($q);
    
            if ($input->type_kirim_file == TypeKirimKelengkapanFile::Akhir) {

                //update penomoran menjadi aktif
                $last_no_penomoran = $this->get_nomor->GetLastPenomoran(TypeIzin::Telekomunikasi, TypeIzinJenisTel::Penomoran);
                $no_sk_penomoran = $this->gen_nomor->no_sk_izin($last_no_penomoran, TypeIzin::Telekomunikasi, TypeIzinJenisTel::Penomoran);
                $nomor_penomoran = new stdClass();
                $nomor_penomoran->id_penomoran_tel_list = $input->nomor_penomoran;
                $nomor_penomoran->id_perusahaan = $input->id_perusahaan;
                $nomor_penomoran->no_sk_penomoran = $no_sk_penomoran;
                $nomor_penomoran->id_permohonan = $input->id_permohonan;
                $this->PostPenomoranPakai($nomor_penomoran);

                //setelah itu didisposisi ke kasubdit penomoran
                //insert to p_permohonan_disposisi
                $dispo = new stdClass();
                $dispo->type_disposisi = TypeDisposisi::Post;
                $dispo->id_izin_jenis = TypeIzinJenisTel::Penomoran;
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

    public function GetPenomoranKelengkapanFile($id_permohonan_penomoran_kelengkapan_file)
    {
        $q = sprintf("SELECT * from p_permohonan_penomoran_kelengkapan_file where id = %d", $id_permohonan_penomoran_kelengkapan_file);
        $result = DB::select($q);
        return $result;
    }

    public function UpdateStatusKelengkapanPenomoran($list_input)
    {
        try
        {
            foreach($list_input as $li)
            {
                $q = sprintf("UPDATE p_permohonan_penomoran_kelengkapan set id_permohonan_komit_kelengkapan_status = %d where id = %d",$li->id_permohonan_komit_kelengkapan_status, $li->id_permohonan_penomoran_kelengkapan);
                $a = DB::update($q);

                if($li->id_permohonan_komit_kelengkapan_status == TypeKomitKelengkapanStatus::Ditolak)
                {
                    $tanggal_now  = Carbon::now();
                    $q = sprintf("INSERT into p_permohonan_penomoran_catatan(id_permohonan_penomoran_kelengkapan, catatan, tanggal_input) values(%d, '%s', '%s')",$li->id_permohonan_penomoran_kelengkapan, $li->catatan, $tanggal_now->format('Y-m-d H:i:s'));
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

    public function GetPenomoranCatatan($id_permohonan_penomoran_kelengkapan)
    {
        $model = new stdClass();
        $q = sprintf("SELECT nama, id from p_permohonan_penomoran_kelengkapan_file where id_permohonan_penomoran_kelengkapan = %d", $id_permohonan_penomoran_kelengkapan);
        $result = DB::select($q);
        $result_array = array();
        foreach($result as $r)
        {
            $r->id = Crypt::encryptString($r->id);
            array_push($result_array, $r); 
        }
        $model->files = $result_array; 
        
        $q = sprintf("SELECT * from p_permohonan_penomoran_catatan where id_permohonan_penomoran_kelengkapan = %d order by tanggal_input desc", $id_permohonan_penomoran_kelengkapan);
        $result = DB::select($q);
        $model->catatan = $result;
        
        return $model;
    }

    public function DeletePenomoranKelengkapanFile($input)
    {
        try
        {
            $q = sprintf("DELETE from p_permohonan_penomoran_kelengkapan_file where id_permohonan_penomoran_kelengkapan = %d", $input->id_permohonan_penomoran_kelengkapan);
            $a = DB::delete($q);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function EditPenomoranKelengkapanFile($input)
    {
        try
        {
            $q = sprintf("INSERT into p_permohonan_penomoran_kelengkapan_file(id_permohonan_penomoran_kelengkapan, nama, stream) values(%d, '%s', '%s')", $input->id_permohonan_penomoran_kelengkapan, $input->nama, $input->stream);
            $a = DB::insert($q);
    
            if ($input->type_kirim_file == TypeKirimKelengkapanFile::Akhir) {
                //update status kelengkapan file di table permohonan komit
                $q = sprintf("UPDATE p_permohonan set id_permohonan_status = %d where id = %d",TypePermohonanStatus::BelumEfektif, $input->id_permohonan);
                $a = DB::update($q);
    
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
}