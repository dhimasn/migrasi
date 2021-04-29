<?php

namespace App\Repo\Pos;

use stdClass;
use Exception;
use App\Enums\ConstHelper;
use App\Enums\TypeDisposisi;
use App\Enums\TypeEvaluasiUlo;
use Illuminate\Support\Carbon;
use App\Enums\TypeIzinJenisPos;
use App\Enums\TypeJenisFormUlo;
use App\Enums\TypeKirimKelengkapanFile;
use App\Enums\TypeUloStatus;
use Illuminate\Support\Facades\DB;

class PermohonanKomitUloDb
{
    /*
    private $pdispo;
    private $pdb;
    public function __construct()
    {
        $this->pdispo = new PermohonanDisposisiPosDb();
        $this->pdb = new PermohonanPosDb();
    }

    public function GetMekanismeUlo($id_permohonan)
    {
        $model = new stdClass();
        try
        {
            $q = sprintf("SELECT p.id, p.no_penyelenggaraan, p.no_sk_izin, i.izin, ij.jenis, per.nama as nama_perusahaan, per.nib, per.kode_izin, per.email as email_perusahaan, per.id as id_perusahaan, uf.nm_user as nama_pemohon, pe.nik, uf.email_user as email_pemohon, pe.telp as telp_pemohon, ps.status, pinfo.value as info, p.tanggal_input, pds.id as done_disposisi from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join m_pemohon pe on pe.id = per.id_pemohon left join p_permohonan_disposisi_staf as pds on pds.id_permohonan = p.id left join m_user_fo as uf on pe.id_user_fo = uf.id where p.id=%d", $id_permohonan);
            $result_header = DB::select($q)[0];
            $model->data_header = $result_header;
    
            $q = sprintf("SELECT *, pkl.id as id_permohonan_komit_layanan FROM p_permohonan_layanan pl left join k_layanan l on pl.id_layanan = l.id LEFT join p_permohonan_komit_layanan pkl on pkl.id_permohonan_layanan = pl.id where pl.id_permohonan = %d", $id_permohonan);
            $result_layanan = DB::select($q);
    
            $model->data_layanan = array();
            
            foreach($result_layanan as $rl)
            {
                $model_layanan = new stdClass();
                $model_layanan->layanan = $rl;

                $q = sprintf("SELECT * from (SELECT ulo.baris, ulo.value, ulo.id_permohonan_komit_layanan, mulo.value as value_header, mulo.urutan FROM p_komitmen_ulo_proses ulo left join m_komitmen_ulo mulo on ulo.id_komitmen_ulo = mulo.id WHERE ulo.id_permohonan_komit_layanan =%d
                UNION ALL
                SELECT 0 as baris, '' as value, id as id_permohonan_komit_layanan, value as value_header, urutan FROM m_mekanisme_ulo) temp order by temp.baris, temp.urutan asc", $rl->id_permohonan_komit_layanan);
                $model_layanan->komitmen = DB::select($q);

                $q = sprintf("SELECT * from p_penomoran_pos_pakai ptp left join m_penomoran_pos_list ptl on ptp.id_penomoran_pos_list = ptl.id left join m_penomoran_pos pt on ptl.id_penomoran_pos = pt.id left join k_layanan kl on kl.id = pt.id_layanan where ptp.id_perusahaan = %d and pt.id_layanan = %d", $result_header->id_perusahaan, $rl->id_layanan);
                $penomoran = DB::select($q);
                $model_layanan->with_nomor = false;
                if (count($penomoran) != 0) {
                    $model_layanan->with_nomor = true;
                    $model_layanan->penomoran = $penomoran[0];
                }

                array_push($model->data_layanan, $model_layanan);
            }

            return $model;
        }
        catch(Exception $e)
        {
            return $model;
        }  
    }

    public function PostMekanismeUloProses($input)
    {
        try
        {
            foreach($input->mekanisme_ulo_proses as $mup)
            {
                $q = sprintf("INSERT into p_mekanisme_ulo_proses(id_mekanisme_ulo, baris, value, id_permohonan_komit_layanan) values(%d, %d, '%s', %d)", $mup->id_mekanisme_ulo, $mup->baris, $mup->value, $mup->id_permohonan_komit_layanan);
                $a = DB::insert($q);
            }

            //insert to p_ulo
            $tanggal_input  = Carbon::now();
            $q = sprintf("INSERT into p_ulo(tanggal_input, id_ulo_status, id_permohonan_komit) values('%s', %d, %d)", $tanggal_input->format('Y-m-d H:i:s'), TypeUloStatus::Kirim, $input->id_permohonan_komit);
            $a = DB::insert($q);
            
            //insert to p_permohonan_disposisi
            $dispo = new stdClass();
            $dispo->type_disposisi = TypeDisposisi::Post;
            $dispo->id_izin_jenis = TypeIzinJenisPos::Ulo;
            $dispo->id_permohonan = $input->id_permohonan;
            $this->pdispo->Proses($dispo);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function GetEvaluasiUlo($id_permohonan, $type_evaluasi_ulo)
    {
        $model = new stdClass();
        try
        {
            $q = sprintf("SELECT p.id, p.no_penyelenggaraan, p.no_sk_izin, i.izin, ij.jenis, per.nama as nama_perusahaan, per.nib, per.kode_izin, per.email as email_perusahaan, per.id as id_perusahaan, uf.nm_user as nama_pemohon, pe.nik, uf.email_user as email_pemohon, pe.telp as telp_pemohon, ps.status, pinfo.value as info, p.tanggal_input, pds.id as done_disposisi from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join m_pemohon pe on pe.id = per.id_pemohon left join p_permohonan_disposisi_staf as pds on pds.id_permohonan = p.id left join m_user_fo as uf on pe.id_user_fo = uf.id where p.id=%d", $id_permohonan);
            $result_header = DB::select($q)[0];
            $model->data_header = $result_header;
    
            $q = sprintf("SELECT *, pkl.id as id_permohonan_komit_layanan FROM p_permohonan_layanan pl left join k_layanan l on pl.id_layanan = l.id LEFT join p_permohonan_komit_layanan pkl on pkl.id_permohonan_layanan = pl.id where pl.id_permohonan = %d", $id_permohonan);
            $result_layanan = DB::select($q);
    
            $model->data_layanan = array();
            
            foreach($result_layanan as $rl)
            {
                $model_layanan = new stdClass();
                $model_layanan->layanan = $rl;

                $q = sprintf("SELECT * from (SELECT ulo.baris, ulo.value, ulo.id_permohonan_komit_layanan, mulo.value as value_header, mulo.urutan, 1 as urutan_union, 0 as is_both FROM p_komitmen_ulo_proses ulo left join m_komitmen_ulo mulo on ulo.id_komitmen_ulo = mulo.id WHERE ulo.id_permohonan_komit_layanan =%d
                UNION ALL
                SELECT mup.baris, mup.value, mup.id_permohonan_komit_layanan, mmu.value as value_header, mmu.urutan, 2 as urutan_union, 0 as is_both from p_mekanisme_ulo_proses mup LEFT join m_mekanisme_ulo mmu on mmu.id = mup.id_mekanisme_ulo where mup.id_permohonan_komit_layanan = %d
                UNION ALL
                SELECT 0 as baris, u.id as value, k.id as id_permohonan_komit_layanan, u.value as value_header, u.urutan, 3 as urutan_union, u.is_both FROM m_evaluasi_ulo u left join k_jenis_form_ulo k on u.id_jenis_form_ulo = k.id where u.id_layanan = %d", $rl->id_permohonan_komit_layanan,$rl->id_permohonan_komit_layanan, $rl->id_layanan);
                if($type_evaluasi_ulo == TypeEvaluasiUlo::Pemohon)
                {
                    $q .= sprintf(" and u.is_both = 1");
                }
                
                $q .= sprintf(" UNION ALL SELECT up.baris, up.value, up.id_permohonan_komit_layanan, mu.value as value_header, mu.urutan, 4 as urutan_union, mu.is_both FROM p_evaluasi_ulo_proses up left join m_evaluasi_ulo mu on up.id_evaluasi_ulo = mu.id where up.id_permohonan_komit_layanan = %d", $rl->id_permohonan_komit_layanan);

                if($type_evaluasi_ulo == TypeEvaluasiUlo::Pemohon)
                {
                    $q .= sprintf(" and mu.is_both = 1");
                }

                $q .= sprintf(" ) temp order by temp.baris, temp.urutan_union, temp.urutan asc");
                $model_layanan->komitmen_evaluasi = DB::select($q);

                $q = sprintf("SELECT * from p_penomoran_pos_pakai ptp left join m_penomoran_pos_list ptl on ptp.id_penomoran_pos_list = ptl.id left join m_penomoran_pos pt on ptl.id_penomoran_pos = pt.id left join k_layanan kl on kl.id = pt.id_layanan where ptp.id_perusahaan = %d and pt.id_layanan = %d", $result_header->id_perusahaan, $rl->id_layanan);
                $penomoran = DB::select($q);
                $model_layanan->with_nomor = false;
                if (count($penomoran) != 0) {
                    $model_layanan->with_nomor = true;
                    $model_layanan->penomoran = $penomoran[0];
                }

                array_push($model->data_layanan, $model_layanan);
            }

            return $model;
        }
        catch(Exception $e)
        {
            return $model;
        }  
    }

    public function IsAnyUjiMandiri($id_permohonan)
    {
        $q = sprintf("SELECT COUNT(mup.value) as jumlah FROM p_mekanisme_ulo_proses mup left join p_permohonan_komit_layanan pkl on mup.id_permohonan_komit_layanan = pkl.id left join p_permohonan_komit pk on pkl.id_permohonan_komit = pk.id where pk.id_permohonan = 27 AND mup.value = '%s'", $id_permohonan, ConstHelper::uji_mandiri);
        $result = DB::select($q)[0]->jumlah;
        $return = true;
        if($result == 0)
        {
            $return = false;
        }
        return $return;
    }

    public function PostEvaluasiUloProses($input)
    {
        try
        {
            foreach($input->evaluasi_ulo_proses as $uup)
            {
                $q = sprintf("INSERT into p_evaluasi_ulo_proses(id_evaluasi_ulo, baris, value, id_permohonan_komit_layanan) values(%d, %d, '%s', %d)", $uup->id_evaluasi_ulo, $uup->baris, $uup->value, $uup->id_permohonan_komit_layanan);
                $a = DB::insert($q);
            }
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function PostUloFile($file)
    {
        try
        {
            $q = sprintf("INSERT into p_ulo_file(id, nama, stream, id_permohonan_komit_layanan) values('%s', '%s','%s', %d)",$file->id, $file->nama, $file->stream, $file->id_permohonan_komit_layanan);
            $a = DB::insert($q);

            if($file->type_kirim_kelengkapan_file == TypeKirimKelengkapanFile::Akhir)
            {
            //insert to p_permohonan_disposisi
            $dispo = new stdClass();
            $dispo->type_disposisi = TypeDisposisi::Up;
            $dispo->id_izin_jenis = TypeIzinJenisPos::Ulo;
            $dispo->id_permohonan = $file->id_permohonan;
            $dispo->id_permohonan_komit = $file->id_permohonan_komit;
            $this->pdispo->Proses($dispo);
            }
        return true;
        }
        catch(Exception $ex)
        {
            return false;
        }
        
    }

    public function GetUloFile($id_ulo_file)
    {
        $q = sprintf("SELECT * from p_ulo_file where id = '%s'", $id_ulo_file);
        $result = DB::select($q);
        return $result;
    }

    public function UpdateStatusUlo($input)
    {
        try
        {
            //update status permohonan
            $this->pdb->UpdateStatus($input);

            //update statu p_ulo
            $q = sprintf("UPDATE p_ulo set id_ulo_status = %d where id_permohonan_komit = %d",$input->id_ulo_status, $input->id_permohonan_komit);
            $a = DB::update($q);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    */
}