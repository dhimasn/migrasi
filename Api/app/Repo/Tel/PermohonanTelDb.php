<?php

namespace App\Repo\Tel;

use stdClass;
use Exception;
use App\Repo\GetNoDb;
use App\Enums\TypeIzin;
use App\Enums\ConstHelper;
use App\Enums\ConstLog;
use App\Enums\TypeUnitTeknis;
use App\Helper\GenerateNomor;
use Illuminate\Support\Carbon;
use App\Enums\TypeIzinJenisTel;
use App\Enums\TypeLevelJabatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Enums\TypeKomitKelengkapanStatus;
use App\Enums\TypePermohonanStatus;
use App\Enums\TypeUloStatus;
use App\Repo\PermohonanInfoDb;
use App\Repo\PermohonanLogDb;

class PermohonanTelDb
{
    private $gen_nomor;
    private $get_nomor;
    private $nomordb;
    private $plog;
    private $pinfo;
    public function __construct()
    {
        $this->gen_nomor = new GenerateNomor();
        $this->get_nomor = new GetNoDb();
        $this->nomordb = new PenomoranDb();
        $this->plog = new PermohonanLogDb();
        $this->pinfo = new PermohonanInfoDb();
    }

    public function PostPermohonan($input)
    {
        $model = new stdClass();
        try
        {
            $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'p_permohonan' ", env('DB_DATABASE'));
            $id_permohonan = DB::select($q)[0]->AUTO_INCREMENT;
            $last_no_permohonan = $this->get_nomor->GetLastPermohomonanKomit();
            $no_penyelenggaraan = $this->gen_nomor->PenyelenggaraanKomit($last_no_permohonan);

            $last_no_izin = $this->get_nomor->GetLastIzin(TypeIzin::Telekomunikasi, $input->id_izin_jenis);
            $no_sk_izin = $this->gen_nomor->no_sk_izin($last_no_izin, TypeIzin::Telekomunikasi, $input->id_izin_jenis);
            $tanggal_input  = Carbon::now();

            $q = sprintf("INSERT into p_permohonan(id, no_penyelenggaraan,no_sk_izin,id_izin_jenis,id_perusahaan,id_permohonan_status,tanggal_input) values(%d, '%s', '%s', %d, %d, %d, '%s');", $id_permohonan, $no_penyelenggaraan, $no_sk_izin, $input->id_izin_jenis, $input->id_perusahaan, $input->id_permohonan_status, $tanggal_input->format('Y-m-d H:i:s'));
            $a = DB::insert($q);

            foreach($input->permohonan_layanan as $p)
            {
                $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'p_permohonan_layanan' ", env('DB_DATABASE'));
                $id_permohonan_layanan = DB::select($q)[0]->AUTO_INCREMENT;

                $q = sprintf(" INSERT into p_permohonan_layanan(id, id_permohonan, id_layanan) values(%d, %d, %d)", $id_permohonan_layanan, $id_permohonan, $p->id_layanan);
                $a = DB::insert($q);

                if ($p->dengan_nomor) {
                    if($input->id_izin_jenis == TypeIzinJenisTel::Jasa)
                    {
                        //update penomoran menjadi aktif
                        $last_no_penomoran = $this->get_nomor->GetLastPenomoran(TypeIzin::Telekomunikasi, TypeIzinJenisTel::Penomoran);
                        $no_sk_penomoran = $this->gen_nomor->no_sk_izin($last_no_penomoran, TypeIzin::Telekomunikasi, TypeIzinJenisTel::Penomoran);
                        $nomor_penomoran = new stdClass();
                        $nomor_penomoran->id_penomoran_tel_list = $p->nomor_penomoran;
                        $nomor_penomoran->id_perusahaan = $input->id_perusahaan;
                        $nomor_penomoran->no_sk_penomoran = $no_sk_penomoran;
                        $nomor_penomoran->id_permohonan = $id_permohonan;
                        $this->nomordb->PostPenomoranPakai($nomor_penomoran);
                    }
                }
            }

            //insert to p_permohonan info
            $per_info = new stdClass();
            $per_info->id_permohonan = $id_permohonan;
            $per_info->tanggal_input = $tanggal_input;
            $per_info->value = ConstLog::i_input_permohonan;
            $this->pinfo->PostPermohonanInfo($per_info);
            
            $model->result = true;
            $model->no_penyelenggaraan = $no_penyelenggaraan;
            $model->no_sk_izin = $no_sk_izin;
            $model->id_permohonan = $id_permohonan;
            return $model;
        }
        catch(Exception $e)
        {
            $model->result = false;
            return $model;
        }
    }

    
    public function GetAll($start, $length, $kolom, $order_by, $search)
    {
        $q = sprintf("SELECT p.id, p.no_penyelenggaraan, p.no_sk_izin, i.izin, ij.jenis, per.nama as nama_perusahaan, ps.status, ps.id as id_permohonan_status, pinfo.value as info, p.tanggal_input, ppk.no_komitmen from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id");

        if($search != '')
        {
           
            $q .= sprintf(" where p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or ppk.no_komitmen like '%%%s%%'", $search, $search, $search, $search, $search, $search, $search) ;
            
        }
           
        $nama_kolom = '';
        if($kolom == 0)
        {
            $nama_kolom = "p.no_penyelenggaraan";
        }
        if($kolom == 1)
        {
            $nama_kolom = "ppk.no_komitmen";
        }
        else if($kolom == 2)
        {
            $nama_kolom = "p.no_sk_izin";
        }
        else if($kolom == 3)
        {
            $nama_kolom = "per.nama";
        }
        else if($kolom == 4)
        {
            $nama_kolom = "ij.jenis";
        }
        else if($kolom == 5)
        {
            $nama_kolom = "p.tanggal_input";
        }
        else if($kolom == 6)
        {
            $nama_kolom = "pinfo.value";
        }
        else if($kolom == 7)
        {
            $nama_kolom = "ps.status";
        }

        $q .= sprintf(" order by %s %s LIMIT %d OFFSET %d", $nama_kolom, $order_by, $length, $start);
        $result = DB::select($q);
        return $result;
    }

    public function GetTotalAll($search)
    {
        $q = sprintf("SELECT COUNT(p.id) as jumlah from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id");

        if($search != '')
        {
           
            $q .= sprintf(" where p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or ppk.no_komitmen like '%%%s%%'", $search, $search, $search, $search, $search, $search, $search) ;
            
        }
        $result = DB::select($q)[0]->jumlah;
        return $result;
    }

    public function GetByIzinJenis($id_izin_jenis, $start, $length, $kolom, $order_by, $search)
    {
        $q = sprintf("SELECT p.id, p.no_penyelenggaraan, p.no_sk_izin, i.izin, ij.jenis, per.nama as nama_perusahaan, ps.status, ps.id as id_permohonan_status, pinfo.value as info, p.tanggal_input, ppk.no_komitmen from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id where p.id_izin_jenis=%d", $id_izin_jenis);

        if($search != '')
        {
           
            $q .= sprintf(" and (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or ppk.no_komitmen like '%%%s%%')", $search, $search, $search, $search, $search, $search, $search) ;
            
        }
           
        $nama_kolom = '';
        if($kolom == 0)
        {
            $nama_kolom = "p.no_penyelenggaraan";
        }
        if($kolom == 1)
        {
            $nama_kolom = "ppk.no_komitmen";
        }
        else if($kolom == 2)
        {
            $nama_kolom = "p.no_sk_izin";
        }
        else if($kolom == 3)
        {
            $nama_kolom = "per.nama";
        }
        else if($kolom == 4)
        {
            $nama_kolom = "ij.jenis";
        }
        else if($kolom == 5)
        {
            $nama_kolom = "p.tanggal_input";
        }
        else if($kolom == 6)
        {
            $nama_kolom = "pinfo.value";
        }
        else if($kolom == 7)
        {
            $nama_kolom = "ps.status";
        }

        $q .= sprintf(" order by %s %s LIMIT %d OFFSET %d", $nama_kolom, $order_by, $length, $start);
        $result = DB::select($q);
        return $result;
    }

    public function GetTotalByIzinJenis($id_izin_jenis, $search)
    {
        $q = sprintf("SELECT COUNT(p.id) as jumlah from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id where p.id_izin_jenis=%d", $id_izin_jenis);

        if($search != '')
        {
           
            $q .= sprintf(" and (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or ppk.no_komitmen like '%%%s%%')", $search, $search, $search, $search, $search, $search, $search) ;
            
        }
        $result = DB::select($q)[0]->jumlah;
        return $result;
    }

    public function GetByDisposisi($id_user, $id_permohonan_komit_kelengkapan_status, $id_izin_jenis, $start, $length, $kolom, $order_by, $search)
    {
        $q = sprintf("SELECT p.id, p.no_penyelenggaraan, p.no_sk_izin, i.izin, ij.jenis, per.nama as nama_perusahaan, ps.status, ps.id as id_permohonan_status, pinfo.value as info, p.tanggal_input, ppk.no_komitmen, pds.id as done_disposisi from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin");

        if($id_izin_jenis == TypeIzinJenisTel::Ulo)
        {
            $q .= sprintf(" left join p_permohonan_disposisi_ulo pd on pd.id_permohonan = p.id left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id left join p_permohonan_disposisi_staf_ulo pds on pds.id_permohonan = p.id LEFT JOIN p_ulo pulo on pulo.id_permohonan_komit = ppk.id");

            $q .= sprintf(" where pd.id_user=%d and ppk.id_permohonan_komit_kelengkapan_status=%d and p.id_permohonan_status=%d and pulo.id_ulo_status!=%d", $id_user, $id_permohonan_komit_kelengkapan_status, TypePermohonanStatus::BelumEfektif, TypeUloStatus::Ditolak);
        }
        else
        {
            $q .= sprintf(" left join p_permohonan_disposisi pd on pd.id_permohonan = p.id left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id left join p_permohonan_disposisi_staf pds on pds.id_permohonan = p.id");

            $q .= sprintf(" where pd.id_user=%d and ppk.id_permohonan_komit_kelengkapan_status=%d and p.id_permohonan_status=%d", $id_user, $id_permohonan_komit_kelengkapan_status, TypePermohonanStatus::BelumEfektif);
        }

        if($search != '')
        {
           
            $q .= sprintf(" and (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or ppk.no_komitmen like '%%%s%%')", $search, $search, $search, $search, $search, $search,  $search) ;
            
        }
           
        $nama_kolom = '';
        if($kolom == 0)
        {
            $nama_kolom = "p.no_penyelenggaraan";
        }
        if($kolom == 1)
        {
            $nama_kolom = "ppk.no_komitmen";
        }
        else if($kolom == 2)
        {
            $nama_kolom = "p.no_sk_izin";
        }
        else if($kolom == 3)
        {
            $nama_kolom = "per.nama";
        }
        else if($kolom == 4)
        {
            $nama_kolom = "ij.jenis";
        }
        else if($kolom == 5)
        {
            $nama_kolom = "p.tanggal_input";
        }
        else if($kolom == 6)
        {
            $nama_kolom = "pinfo.value";
        }
        else if($kolom == 7)
        {
            $nama_kolom = "ps.status";
        }

        $q .= sprintf(" order by %s %s LIMIT %d OFFSET %d", $nama_kolom, $order_by, $length, $start);
        $result = DB::select($q);
        return $result;
    }

    public function GetTotalByDisposisi($id_user, $id_permohonan_komit_kelengkapan_status, $id_izin_jenis, $search)
    {
        $q = sprintf("SELECT COUNT(p.id) as jumlah from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin");

        if($id_izin_jenis == TypeIzinJenisTel::Ulo)
        {
            $q .= sprintf(" left join p_permohonan_disposisi_ulo pd on pd.id_permohonan = p.id left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id left join p_permohonan_disposisi_staf_ulo pds on pds.id_permohonan = p.id LEFT JOIN p_ulo pulo on pulo.id_permohonan_komit = ppk.id");

            $q .= sprintf(" where pd.id_user=%d and ppk.id_permohonan_komit_kelengkapan_status=%d and p.id_permohonan_status=%d and pulo.id_ulo_status!=%d", $id_user, $id_permohonan_komit_kelengkapan_status, TypePermohonanStatus::BelumEfektif, TypeUloStatus::Ditolak);
        }
        else
        {
            $q .= sprintf(" left join p_permohonan_disposisi pd on pd.id_permohonan = p.id left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id left join p_permohonan_disposisi_staf pds on pds.id_permohonan = p.id");

            $q .= sprintf(" where pd.id_user=%d and ppk.id_permohonan_komit_kelengkapan_status=%d and p.id_permohonan_status=%d", $id_user, $id_permohonan_komit_kelengkapan_status, TypePermohonanStatus::BelumEfektif);
        }

        if($search != '')
        {
           
            $q .= sprintf(" and (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or ppk.no_komitmen like '%%%s%%')", $search, $search, $search, $search, $search, $search, $search) ;
            
        }
        $result = DB::select($q)[0]->jumlah;
        return $result;
    }

    public function GetByDisposisiSend($id_user, $id_izin_jenis, $start, $length, $kolom, $order_by, $search)
    {
        $q = sprintf("SELECT p.id, p.no_penyelenggaraan, p.no_sk_izin, i.izin, ij.jenis, per.nama as nama_perusahaan, ps.status, ps.id as id_permohonan_status, pinfo.value as info, p.tanggal_input, ppk.no_komitmen from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin");

        if ($id_izin_jenis == TypeIzinJenisTel::Ulo) 
        {
            $q .= sprintf(" left join p_permohonan_disposisi_ulo_kirim pd on pd.id_permohonan = p.id left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id left join m_user mu on mu.id = pd.id_user left join m_jabatan mj on mj.id = mu.id_jabatan");
        } 
        else 
        {
            $q .= sprintf(" left join p_permohonan_disposisi_kirim pd on pd.id_permohonan = p.id left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id left join m_user mu on mu.id = pd.id_user left join m_jabatan mj on mj.id = mu.id_jabatan");
        }

        $q .= sprintf(" where pd.id_user=%d", $id_user);
        
        if($search != '')
        {
           
            $q .= sprintf(" and (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or ppk.no_komitmen like '%%%s%%')", $search, $search, $search, $search, $search, $search,  $search) ;
            
        }
           
        $nama_kolom = '';
        if($kolom == 0)
        {
            $nama_kolom = "p.no_penyelenggaraan";
        }
        if($kolom == 1)
        {
            $nama_kolom = "ppk.no_komitmen";
        }
        else if($kolom == 2)
        {
            $nama_kolom = "p.no_sk_izin";
        }
        else if($kolom == 3)
        {
            $nama_kolom = "per.nama";
        }
        else if($kolom == 4)
        {
            $nama_kolom = "ij.jenis";
        }
        else if($kolom == 5)
        {
            $nama_kolom = "p.tanggal_input";
        }
        else if($kolom == 6)
        {
            $nama_kolom = "pinfo.value";
        }
        else if($kolom == 7)
        {
            $nama_kolom = "ps.status";
        }

        $q .= sprintf(" order by %s %s LIMIT %d OFFSET %d", $nama_kolom, $order_by, $length, $start);
        $result = DB::select($q);
        return $result;
    }

    public function GetTotalByDisposisiSend($id_user, $id_izin_jenis, $search)
    {
        $q = sprintf("SELECT COUNT(p.id) as jumlah from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin");

        if ($id_izin_jenis == TypeIzinJenisTel::Ulo) 
        {
            $q .= sprintf(" left join p_permohonan_disposisi_ulo_kirim pd on pd.id_permohonan = p.id left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id left join m_user mu on mu.id = pd.id_user left join m_jabatan mj on mj.id = mu.id_jabatan");
        } 
        else 
        {
            $q .= sprintf(" left join p_permohonan_disposisi_kirim pd on pd.id_permohonan = p.id left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id left join m_user mu on mu.id = pd.id_user left join m_jabatan mj on mj.id = mu.id_jabatan");
        }

        $q .= sprintf(" where pd.id_user=%d", $id_user);
      
        if($search != '')
        {
           
            $q .= sprintf(" and (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or ppk.no_komitmen like '%%%s%%')", $search, $search, $search, $search, $search, $search, $search) ;
            
        }
        $result = DB::select($q)[0]->jumlah;
        return $result;
    }

    public function GetById($id_permohonan)
    {
        $model = new stdClass();
        try
        {
            $q = sprintf("SELECT p.id, p.no_penyelenggaraan, p.no_sk_izin, i.izin, ij.jenis, per.nama as nama_perusahaan, per.nib, per.npwp, per.email as email_perusahaan, per.id as id_perusahaan, per.telp as telp_perusahaan, kpm.penanaman_modal, kpj.jenis as jenis_perusahaan, uf.nm_user as nama_pemohon, pe.nik, uf.email_user as email_pemohon, pe.telp as telp_pemohon, ps.status, pinfo.value as info, p.tanggal_input, pds.id as done_disposisi, sk_izin.id as id_sk_izin, sk_ulo.id as id_sk_ulo, ppk.no_komitmen, pkomit.id as id_sk_komit, p.id_izin_jenis, per.alamat as alamat_perusahaan from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join m_pemohon pe on pe.id = per.id_pemohon left join p_permohonan_disposisi_staf as pds on pds.id_permohonan = p.id left join m_user_fo as uf on pe.id_user_fo = uf.id left join k_penanaman_modal as kpm on kpm.id = per.id_penanaman_modal left join k_perusahaan_jenis kpj on kpj.id = per.id_perusahaan_jenis left join p_sk_izin_file as sk_izin on sk_izin.id_permohonan = p.id left join p_ulo_sklo ulo on ulo.id_permohonan = p.id left join p_sk_ulo_file sk_ulo on sk_ulo.id_ulo_sklo = ulo.id left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id left join p_sk_penetapan_komit_file pkomit on pkomit.id_permohonan = p.id where p.id=%d", $id_permohonan);
            $result_header = DB::select($q)[0];
            if($result_header->id_sk_izin !== null)
            {
                $result_header->id_sk_izin = Crypt::encryptString($result_header->id_sk_izin); 
            }
            if($result_header->id_sk_ulo !== null)
            {
                $result_header->id_sk_ulo = Crypt::encryptString($result_header->id_sk_ulo); 
            }
            if($result_header->id_sk_komit !== null)
            {
                $result_header->id_sk_komit = Crypt::encryptString($result_header->id_sk_komit); 
            }

            $model->data_header = $result_header;
            $model->data_layanan = array();

            if($result_header->id_izin_jenis == TypeIzinJenisTel::Penomoran)
            {
                $q = sprintf("SELECT *, pl.id as id_permohonan_penomoran_tel FROM p_permohonan_penomoran_tel pl left join m_penomoran_tel l on pl.id_penomoran_tel = l.id left join k_layanan kl on kl.id = l.id_layanan where pl.id_permohonan = %d", $id_permohonan);
                $result_layanan = DB::select($q);

                foreach ($result_layanan as $rl) {
                    $q = sprintf("SELECT *, kl.id as id_permohonan_penomoran_kelengkapan FROM p_permohonan_penomoran_kelengkapan kl left join k_penomoran_kelengkapan kkk on kkk.id = kl.id_penomoran_kelengkapan where kl.id_permohonan_penomoran_tel = %d order by kkk.urutan asc",  $rl->id_permohonan_penomoran_tel);
                    $result_layanan_kelengkapan = DB::select($q);
                    $model_layanan = new stdClass();
                    $model_layanan->layanan = $rl;

                    $model_layanan->kelengkapan = array();
                    foreach ($result_layanan_kelengkapan as $lk) {
                        $model_kelengkapan = $lk;

                        $q = sprintf("SELECT id, nama from p_permohonan_penomoran_kelengkapan_file where id_permohonan_penomoran_kelengkapan = %d", $lk->id_permohonan_penomoran_kelengkapan);
                        $result_kelengkapan_file = DB::select($q);
                        $file_array = array();
                        foreach ($result_kelengkapan_file as $rkf) {
                            $rkf->id = Crypt::encryptString($rkf->id);
                            array_push($file_array, $rkf);
                        }
                        $model_kelengkapan->file = $file_array;
                        array_push($model_layanan->kelengkapan, $model_kelengkapan);
                    }

                    $q = sprintf("SELECT ptp.*, ptl.*, pt.*, kl.*, sk_nomor.id as id_sk_nomor, ptp.id as id_penomoran_tel_pakai from p_penomoran_tel_pakai ptp left join m_penomoran_tel_list ptl on ptp.id_penomoran_tel_list = ptl.id left join m_penomoran_tel pt on ptl.id_penomoran_tel = pt.id left join k_layanan kl on kl.id = pt.id_layanan left join p_sk_penomoran_file sk_nomor on sk_nomor.id_penomoran_tel_pakai = ptp.id where ptp.id_perusahaan = %d and pt.id_layanan = %d and ptp.id_permohonan = %d", $result_header->id_perusahaan, $rl->id_layanan, $id_permohonan);
                    $penomoran = DB::select($q);
                    $model_layanan->with_nomor = false;
                    if (count($penomoran) != 0) {
                        $model_layanan->with_nomor = true;
                        if ($penomoran[0]->id_sk_nomor !== null) {
                            $penomoran[0]->id_sk_nomor = Crypt::encryptString($penomoran[0]->id_sk_nomor);
                        }
                        $model_layanan->penomoran = $penomoran[0];
                    }

                    array_push($model->data_layanan, $model_layanan);
                }
            }
            else
            {
                $q = sprintf("SELECT *, pkl.id as id_permohonan_komit_layanan FROM p_permohonan_layanan pl left join k_layanan l on pl.id_layanan = l.id LEFT join p_permohonan_komit_layanan pkl on pkl.id_permohonan_layanan = pl.id where pl.id_permohonan = %d", $id_permohonan);
                $result_layanan = DB::select($q);

                foreach ($result_layanan as $rl) {
                    $q = sprintf("SELECT *, kl.id as id_permohonan_komit_kelengkapan FROM p_permohonan_komit_kelengkapan kl left join k_komit_kelengkapan kkk on kkk.id = kl.id_jenis_kelengkapan where kl.id_permohonan_komit_layanan = %d order by kkk.urutan asc",  $rl->id_permohonan_komit_layanan);
                    $result_layanan_kelengkapan = DB::select($q);
                    $model_layanan = new stdClass();
                    $model_layanan->layanan = $rl;

                    $model_layanan->kelengkapan = array();
                    foreach ($result_layanan_kelengkapan as $lk) {
                        $model_kelengkapan = $lk;

                        $q = sprintf("SELECT id, nama from p_permohonan_komit_file where id_permohonan_komit_kelengkapan = %d", $lk->id_permohonan_komit_kelengkapan);
                        $result_kelengkapan_file = DB::select($q);
                        $file_array = array();
                        foreach ($result_kelengkapan_file as $rkf) {
                            $rkf->id = Crypt::encryptString($rkf->id);
                            array_push($file_array, $rkf);
                        }
                        $model_kelengkapan->file = $file_array;
                        array_push($model_layanan->kelengkapan, $model_kelengkapan);
                    }

                    $q = sprintf("SELECT ulo.*, mulo.value as value_header, mulo.urutan FROM p_komitmen_ulo_proses ulo left join m_komitmen_ulo mulo on ulo.id_komitmen_ulo = mulo.id WHERE ulo.id_permohonan_komit_layanan = %d order by ulo.baris, mulo.urutan asc", $rl->id_permohonan_komit_layanan);
                    $model_layanan->komitmen = DB::select($q);

                    $q = sprintf("SELECT ptp.*, ptl.*, pt.*, kl.*, sk_nomor.id as id_sk_nomor from p_penomoran_tel_pakai ptp left join m_penomoran_tel_list ptl on ptp.id_penomoran_tel_list = ptl.id left join m_penomoran_tel pt on ptl.id_penomoran_tel = pt.id left join k_layanan kl on kl.id = pt.id_layanan left join p_sk_penomoran_file sk_nomor on sk_nomor.id_penomoran_tel_pakai = ptp.id where ptp.id_perusahaan = %d and pt.id_layanan = %d and ptp.id_permohonan = %d", $result_header->id_perusahaan, $rl->id_layanan, $id_permohonan);
                    $penomoran = DB::select($q);
                    $model_layanan->with_nomor = false;
                    if (count($penomoran) != 0) {
                        $model_layanan->with_nomor = true;
                        if ($penomoran[0]->id_sk_nomor !== null) {
                            $penomoran[0]->id_sk_nomor = Crypt::encryptString($penomoran[0]->id_sk_nomor);
                        }
                        $model_layanan->penomoran = $penomoran[0];
                    }

                    //get kinerja untuk jaringan
                    $q = sprintf("SELECT * FROM p_permohonan_kinerja where id_permohonan_komit_layanan=%d order by baris asc", $rl->id_permohonan_komit_layanan);
                    $model_layanan->kinerja = DB::select($q);

                    array_push($model->data_layanan, $model_layanan);
                }
            }

            $model->log = $this->plog->Get($id_permohonan);
    
            return $model;
        }
        catch(Exception $e)
        {
            return $model;
        }     
    }

    public function GetByNoSKIzin($no_sk_izin, $id_izin_jenis, $id_pemohon)
    {
        $model = new stdClass();
        $list_permohonan = $this->GetValidasiPermohonan($no_sk_izin, $id_izin_jenis, $id_pemohon);
        if(count($list_permohonan) != 0)
        {
            $permohonan = $list_permohonan[0];
            $model->permohonan = $permohonan;
            $q = sprintf("SELECT *, pl.id as id_permohonan_layanan from p_permohonan_layanan pl left join k_layanan kl on pl.id_layanan = kl.id where pl.id_permohonan = %d", $permohonan->id_permohonan);
            $layanan = DB::select($q);
            $model->data_layanan = array();
            foreach ($layanan as $l) {
                $model_layanan = new stdClass();
                $model_layanan->layanan = $l;
                $q = sprintf("SELECT *, ptp.id as id_penomoran_tel_pakai from p_penomoran_tel_pakai ptp left join m_penomoran_tel_list ptl on ptp.id_penomoran_tel_list = ptl.id left join m_penomoran_tel pt on ptl.id_penomoran_tel = pt.id left join k_layanan kl on kl.id = pt.id_layanan where ptp.id_perusahaan = %d and pt.id_layanan = %d and ptp.id_permohonan = %d", $permohonan->id_perusahaan, $l->id_layanan, $permohonan->id_permohonan);
                $penomoran = DB::select($q);
                $model_layanan->with_nomor = false;
                if (count($penomoran) != 0) {
                    $model_layanan->with_nomor = true;
                    $model_layanan->penomoran = $penomoran[0];
                }
                array_push($model->data_layanan, $model_layanan);
            }   
        }        
        return $model;
    }

    public function GetValidasiPermohonan($no_sk_izin, $id_izin_jenis, $id_pemohon)
    {
        $q = sprintf("SELECT p.*, p.id as id_permohonan, pk.id as id_permohonan_komit, kij.jenis as izin_jenis from p_permohonan p left join m_perusahaan mp on p.id_perusahaan = mp.id left join p_permohonan_komit pk on pk.id_permohonan = p.id left join k_izin_jenis kij on kij.id = p.id_izin_jenis where p.no_sk_izin = '%s' and p.id_izin_jenis=%d and p.id_permohonan_status!=%d and mp.id_pemohon=%d", $no_sk_izin, $id_izin_jenis, TypePermohonanStatus::Dicabut, $id_pemohon);
        $list_permohonan = DB::select($q);
        return $list_permohonan;
    }

    public function IsDoneDisposisi($id_permohonan)
    {
        $q = sprintf("SELECT count(id) as jumlah from p_permohonan_disposisi_staf where id_permohonan = %d", $id_permohonan);
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
            $q = sprintf("UPDATE p_permohonan set id_permohonan_status = %d where id = %d",$input->id_permohonan_status, $input->id_permohonan);
            $a = DB::update($q);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function GetByPemohon($id_pemohon, $start, $length, $kolom, $order_by, $search)
    {
        $q = sprintf("SELECT p.id, p.no_penyelenggaraan, i.izin, ij.jenis, ps.status, ps.id as id_permohonan_status, pinfo.value as info, p.tanggal_input, ppk.id_permohonan_komit_kelengkapan_status, pulo.id_ulo_status, temp.jumlah as uji_mandiri, p.id_izin_jenis from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id LEFT JOIN (SELECT COUNT(mup.value) as jumlah, pk.id_permohonan FROM p_mekanisme_ulo_proses mup left join p_permohonan_komit_layanan pkl on mup.id_permohonan_komit_layanan = pkl.id left join p_permohonan_komit pk on pkl.id_permohonan_komit = pk.id where mup.value = '%s' group by pk.id_permohonan, mup.value) as temp on temp.id_permohonan = p.id LEFT JOIN p_ulo pulo on pulo.id_permohonan_komit = ppk.id where per.id_pemohon=%d", ConstHelper::uji_mandiri, $id_pemohon);

        if($search != '')
        {
           
            $q .= sprintf(" and (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or ppk.no_komitmen like '%%%s%%')", $search, $search, $search, $search, $search, $search, $search) ;
            
        }
           
        $nama_kolom = '';
        if($kolom == 0)
        {
            $nama_kolom = "p.no_penyelenggaraan";
        }
        else if($kolom == 1)
        {
            $nama_kolom = "ij.jenis";
        }
        else if($kolom == 2)
        {
            $nama_kolom = "p.tanggal_input";
        }
        else if($kolom == 3)
        {
            $nama_kolom = "pinfo.value";
        }
        else if($kolom == 4)
        {
            $nama_kolom = "ps.status";
        }

        $q .= sprintf(" order by %s %s LIMIT %d OFFSET %d", $nama_kolom, $order_by, $length, $start);
        $result = DB::select($q);
        return $result;
    }

    public function GetTotalByPemohon($id_pemohon, $search)
    {
        $q = sprintf("SELECT COUNT(p.id) as jumlah from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id where per.id_pemohon=%d", $id_pemohon);

        if($search != '')
        {
           
            $q .= sprintf(" and (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or ppk.no_komitmen like '%%%s%%')", $search, $search, $search, $search, $search, $search, $search) ;
            
        }
        $result = DB::select($q)[0]->jumlah;
        return $result;
    }

    public function IsDonePermohonan($id_pemohon)
    {
        $q = sprintf("SELECT count(p.id) as jumlah from p_permohonan p left join m_perusahaan mp on p.id_perusahaan = mp.id where mp.id_pemohon = %d and p.id_permohonan_status = %d", $id_pemohon, TypePermohonanStatus::BelumEfektif);
        $result = DB::select($q)[0]->jumlah;
        $return = true;
        if($result > 0)
        {
            $return = false;
        }
        return $return;
    }

    public function GetByNoSKIzinWithoutPemohon($id_permohonan)
    {
        $model = new stdClass();
        $list_permohonan = $this->GetValidasiPermohonanWithoutPemohon($id_permohonan);
        if(count($list_permohonan) != 0)
        {
            $permohonan = $list_permohonan[0];
            $model->permohonan = $permohonan;
            $q = sprintf("SELECT *, pl.id as id_permohonan_layanan, pkl.id as id_permohonan_komit_layanan from p_permohonan_layanan pl left join k_layanan kl on pl.id_layanan = kl.id left join p_permohonan_komit_layanan pkl on pl.id = pkl.id_permohonan_layanan where pl.id_permohonan = %d", $permohonan->id_permohonan);
            $layanan = DB::select($q);
            $model->data_layanan = array();
            foreach ($layanan as $l) {
                $model_layanan = new stdClass();
                $model_layanan->layanan = $l;
                $q = sprintf("SELECT * from (SELECT meu.value as jenis, ulop.value as value, ulop.baris, meu.urutan, 2 as urutan_union from p_evaluasi_ulo_proses ulop left join m_evaluasi_ulo meu on meu.id = ulop.id_evaluasi_ulo where ulop.id_permohonan_komit_layanan=%d UNION ALL SELECT mu.value as jenis, mup.value as value, mup.baris, mu.urutan, 1 as urutan_union from p_mekanisme_ulo_proses mup left join m_mekanisme_ulo mu on mup.id_mekanisme_ulo = mu.id where mup.id_permohonan_komit_layanan=%d) temp ORDER BY temp.baris, temp.urutan_union, temp.urutan
                ", $l->id_permohonan_komit_layanan, $l->id_permohonan_komit_layanan);
                $evaluasi_ulo = DB::select($q);
                $model_layanan->evaluasi_ulo = $evaluasi_ulo;
                array_push($model->data_layanan, $model_layanan);
            }   
        }        
        return $model;
    }

    public function GetValidasiPermohonanWithoutPemohon($id_permohonan)
    {
        $q = sprintf("SELECT p.*, p.id as id_permohonan, pk.id as id_permohonan_komit, kij.jenis as izin_jenis, mp.nama as nama_perusahaan, mp.alamat from p_permohonan p left join m_perusahaan mp on p.id_perusahaan = mp.id left join p_permohonan_komit pk on pk.id_permohonan = p.id left join k_izin_jenis kij on kij.id = p.id_izin_jenis where p.id = %d and p.id_permohonan_status!=%d", $id_permohonan, TypePermohonanStatus::Dicabut);
        $list_permohonan = DB::select($q);
        return $list_permohonan;
    }

    public function GetForSk($start, $length, $kolom, $order_by, $search)
    {
        $q = sprintf("SELECT DISTINCT p.id, p.no_penyelenggaraan, p.no_sk_izin, i.izin, ij.jenis, per.nama as nama_perusahaan, ps.status, ps.id as id_permohonan_status, pinfo.value as info, p.tanggal_input, ppk.no_komitmen, sk_izin.id as id_sk_izin, sk_nomor.id as id_sk_nomor, sk_ulo.id as id_sk_ulo, pkomit.id as id_sk_komit from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id LEFT JOIN p_ulo pulo on pulo.id_permohonan_komit = ppk.id left join p_sk_izin_file as sk_izin on sk_izin.id_permohonan = p.id left join p_penomoran_tel_pakai nomor_pakai on nomor_pakai.id_permohonan = p.id left join p_sk_penomoran_file sk_nomor on sk_nomor.id_penomoran_tel_pakai = nomor_pakai.id left join p_ulo_sklo ulo on ulo.id_permohonan = p.id left join p_sk_ulo_file sk_ulo on sk_ulo.id_ulo_sklo = ulo.id left join p_sk_penetapan_komit_file pkomit on pkomit.id_permohonan = p.id");

        if($search != '')
        {
           
            $q .= sprintf(" where p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or ppk.no_komitmen like '%%%s%%'", $search, $search, $search, $search, $search, $search, $search) ;
            
        }
           
        $nama_kolom = '';
        if($kolom == 0)
        {
            $nama_kolom = "p.no_penyelenggaraan";
        }
        if($kolom == 1)
        {
            $nama_kolom = "ppk.no_komitmen";
        }
        else if($kolom == 2)
        {
            $nama_kolom = "p.no_sk_izin";
        }
        else if($kolom == 3)
        {
            $nama_kolom = "per.nama";
        }
        else if($kolom == 4)
        {
            $nama_kolom = "ij.jenis";
        }
        else if($kolom == 5)
        {
            $nama_kolom = "p.tanggal_input";
        }
        else if($kolom == 6)
        {
            $nama_kolom = "pinfo.value";
        }
        else if($kolom == 7)
        {
            $nama_kolom = "ps.status";
        }

        $q .= sprintf(" order by %s %s LIMIT %d OFFSET %d", $nama_kolom, $order_by, $length, $start);
        $result = DB::select($q);
        return $result;
    }

    public function GetTotalForSk($search)
    {
        $q = sprintf("SELECT COUNT(DISTINCT p.id) as jumlah from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id LEFT JOIN p_ulo pulo on pulo.id_permohonan_komit = ppk.id left join p_sk_izin_file as sk_izin on sk_izin.id_permohonan = p.id left join p_penomoran_tel_pakai nomor_pakai on nomor_pakai.id_permohonan = p.id left join p_sk_penomoran_file sk_nomor on sk_nomor.id_penomoran_tel_pakai = nomor_pakai.id left join p_ulo_sklo ulo on ulo.id_permohonan = p.id left join p_sk_ulo_file sk_ulo on sk_ulo.id_ulo_sklo = ulo.id left join p_sk_penetapan_komit_file pkomit on pkomit.id_permohonan = p.id");

        if($search != '')
        {
           
            $q .= sprintf(" where p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or ppk.no_komitmen like '%%%s%%'", $search, $search, $search, $search, $search, $search, $search) ;
            
        }
        $result = DB::select($q)[0]->jumlah;
        return $result;
    }

    public function GetByDisposisiNoKomit($id_user, $start, $length, $kolom, $order_by, $search)
    {
        $q = sprintf("SELECT p.id, p.no_penyelenggaraan, i.izin, ij.jenis, per.nama as nama_perusahaan, ps.status, ps.id as id_permohonan_status, pinfo.value as info, p.tanggal_input, pds.id as done_disposisi from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin");

        $q .= sprintf(" left join p_permohonan_disposisi pd on pd.id_permohonan = p.id left join p_permohonan_disposisi_staf pds on pds.id_permohonan = p.id");

        $q .= sprintf(" where pd.id_user=%d and p.id_permohonan_status=%d", $id_user, TypePermohonanStatus::BelumEfektif);
        
        if($search != '')
        {
           
            $q .= sprintf(" and (p.no_penyelenggaraan like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%')", $search, $search, $search, $search, $search) ;
            
        }
           
        $nama_kolom = '';
        if($kolom == 0)
        {
            $nama_kolom = "p.no_penyelenggaraan";
        }
        else if($kolom == 1)
        {
            $nama_kolom = "per.nama";
        }
        else if($kolom == 2)
        {
            $nama_kolom = "ij.jenis";
        }
        else if($kolom == 3)
        {
            $nama_kolom = "p.tanggal_input";
        }
        else if($kolom == 4)
        {
            $nama_kolom = "pinfo.value";
        }
        else if($kolom == 5)
        {
            $nama_kolom = "ps.status";
        }

        $q .= sprintf(" order by %s %s LIMIT %d OFFSET %d", $nama_kolom, $order_by, $length, $start);
        $result = DB::select($q);
        return $result;
    }

    public function GetTotalByDisposisiNoKomit($id_user, $search)
    {
        $q = sprintf("SELECT COUNT(p.id) as jumlah from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin");

        $q .= sprintf(" left join p_permohonan_disposisi pd on pd.id_permohonan = p.id left join p_permohonan_disposisi_staf pds on pds.id_permohonan = p.id");

        $q .= sprintf(" where pd.id_user=%d and p.id_permohonan_status=%d", $id_user, TypePermohonanStatus::BelumEfektif);
        
        if($search != '')
        {
           
            $q .= sprintf(" and (p.no_penyelenggaraan like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%')", $search, $search, $search, $search, $search) ;
            
        }
        $result = DB::select($q)[0]->jumlah;
        return $result;
    }

    public function GetByDisposisiSendNoKomit($id_user, $start, $length, $kolom, $order_by, $search)
    {
        $q = sprintf("SELECT p.id, p.no_penyelenggaraan, i.izin, ij.jenis, per.nama as nama_perusahaan, ps.status, ps.id as id_permohonan_status, pinfo.value as info, p.tanggal_input from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin");

        $q .= sprintf(" left join p_permohonan_disposisi_kirim pd on pd.id_permohonan = p.id left join m_user mu on mu.id = pd.id_user left join m_jabatan mj on mj.id = mu.id_jabatan");
        
        $q .= sprintf(" where pd.id_user=%d", $id_user);
        
        if($search != '')
        {
           
            $q .= sprintf(" and (p.no_penyelenggaraan like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' )", $search, $search, $search, $search, $search) ;
            
        }
           
        $nama_kolom = '';
        if($kolom == 0)
        {
            $nama_kolom = "p.no_penyelenggaraan";
        }
        else if($kolom == 1)
        {
            $nama_kolom = "per.nama";
        }
        else if($kolom == 2)
        {
            $nama_kolom = "ij.jenis";
        }
        else if($kolom == 3)
        {
            $nama_kolom = "p.tanggal_input";
        }
        else if($kolom == 4)
        {
            $nama_kolom = "pinfo.value";
        }
        else if($kolom == 5)
        {
            $nama_kolom = "ps.status";
        }

        $q .= sprintf(" order by %s %s LIMIT %d OFFSET %d", $nama_kolom, $order_by, $length, $start);
        $result = DB::select($q);
        return $result;
    }

    public function GetTotalByDisposisiSendNoKomit($id_user, $search)
    {
        $q = sprintf("SELECT COUNT(p.id) as jumlah from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin");

        $q .= sprintf(" left join p_permohonan_disposisi_kirim pd on pd.id_permohonan = p.id left join m_user mu on mu.id = pd.id_user left join m_jabatan mj on mj.id = mu.id_jabatan");
        
        $q .= sprintf(" where pd.id_user=%d", $id_user);
        
        if($search != '')
        {
           
            $q .= sprintf(" and (p.no_penyelenggaraan like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' )", $search, $search, $search, $search, $search) ;
            
        }
        $result = DB::select($q)[0]->jumlah;
        return $result;
    }
}