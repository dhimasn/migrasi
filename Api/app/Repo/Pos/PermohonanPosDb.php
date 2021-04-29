<?php

namespace App\Repo\Pos;

use stdClass;
use Exception;
use App\Repo\GetNoDb;
use App\Enums\TypeIzin;
use App\Enums\ConstHelper;
use App\Enums\ConstLog;
use App\Enums\TypeUnitTeknis;
use App\Helper\GenerateNomor;
use Illuminate\Support\Carbon;
use App\Enums\TypeIzinJenisPos;
use App\Enums\TypeLevelJabatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Enums\TypeKomitKelengkapanStatus;
use App\Enums\TypePermohonanStatus;
use App\Repo\PermohonanInfoDb;
use App\Repo\PermohonanLogDb;
use App\Repo\GetPermohonanMailDb;
use App\Notifications\PostPermohonanPosMail;
use SebastianBergmann\Environment\Console;
use Illuminate\Support\Facades\Notification;
use App\GeneratePdf\SKIzinPos;

class PermohonanPosDb
{
    private $gen_nomor;
    private $get_nomor;
    private $nomordb;
    private $plog;
    private $pinfo;
    private $gpm;
    private $skpos;
    public function __construct()
    {
        $this->gpm = new GetPermohonanMailDb();
        $this->gen_nomor = new GenerateNomor();
        $this->get_nomor = new GetNoDb();
        $this->plog = new PermohonanLogDb();
        $this->skpos = new SKIzinPos();
        $this->pinfo = new PermohonanInfoDb();
    }

    public function PostPermohonan($input)
    {
        try
        {
            $last_no_permohonan = $this->get_nomor->GetLastPermohomonanKomit();
            $no_penyelenggaraan = $this->gen_nomor->PenyelenggaraanKomit($last_no_permohonan);
            $last_no_izin = $this->get_nomor->GetLastIzin(TypeIzin::Pos, $input->id_izin_jenis);
            $no_sk_izin = $this->gen_nomor->no_sk_izin($last_no_izin, TypeIzin::Pos, $input->id_izin_jenis);
            $tanggal_input  = Carbon::now();
            $q_get_last_id = DB::select('SELECT a.id FROM p_permohonan a ORDER BY a.id DESC LIMIT 1');
            $id_permohonan = '';
            if($q_get_last_id==NULL){
                $id_permohonan = 1;
            }else{
                foreach ($q_get_last_id as $key => $value) {
                    $id_permohonan = ($value->id)+1;
                }
            }
            $insert_1 = sprintf("INSERT INTO `p_permohonan` (`id`, `no_penyelenggaraan`, `no_sk_izin`, `id_izin_jenis`, `id_perusahaan`, `id_permohonan_status`, `tanggal_input`) VALUES ('$id_permohonan', '$no_penyelenggaraan', '$no_sk_izin', '$input->id_izin_jenis', '$input->id_perusahaan', '0', '$tanggal_input')");
            DB::insert($insert_1);

            foreach($input->permohonan_layanan as $p)
            {
                $id_layanan = '';
                $q_id_layanan = DB::select('SELECT a.id FROM k_layanan a WHERE a.id_izin_jenis="'.$input->id_izin_jenis.'" AND a.layanan="'.$p->layanan.'" ORDER BY a.id DESC LIMIT 1');
                foreach ($q_id_layanan as $key => $value) {
                    $id_layanan = $value->id;
                }
                $insert_2 = sprintf("INSERT INTO `p_permohonan_layanan` (`id`, `id_permohonan`, `id_layanan`) VALUES (NULL, '$id_permohonan', '$id_layanan')");
                DB::insert($insert_2);
            }
            foreach($input->permohonan_wilayah as $p)
            {
                $insert_4 = sprintf("INSERT INTO `p_permohonan_pos_cakupan_wilayah` (`id`, `id_permohonan`, `id_wilayah`) VALUES (NULL, '$id_permohonan', '$p->wilayah')");
                DB::insert($insert_4);
            }
            $per_info = new stdClass();
            $per_info->id_permohonan = $id_permohonan;
            $per_info->tanggal_input = $tanggal_input;
            $per_info->value = ConstLog::i_input_permohonan;
            $this->pinfo->PostPermohonanInfo($per_info);

            // $id_permohonan = 88;
            $permohonan_for_mail = $this->gpm->GetForEmailById($id_permohonan);
            $expired_date = date('Y-m-d', strtotime('+10 day', strtotime($permohonan_for_mail->tgl_permohonan)));
            $model_send_mail = new stdClass();
            $model_send_mail->no_sk =  $permohonan_for_mail->no_sk_izin;
            $model_send_mail->nib =  $permohonan_for_mail->nib;
            $model_send_mail->url_upload_spm =  '';
            $model_send_mail->id_permohonan =  $id_permohonan;
            $model_send_mail->id_izin_jenis =  $input->id_izin_jenis;
            $model_send_mail->nama_pt =  $permohonan_for_mail->nama_perusahaan;
            $model_send_mail->npwp =  $permohonan_for_mail->npwp;
            $model_send_mail->no_penyelenggaraan = $permohonan_for_mail->no_penyelenggaraan;
            $model_send_mail->expired_date = Carbon::parse($expired_date)->translatedFormat('d F Y');
            $model_send_mail->tanggal_input = Carbon::parse($permohonan_for_mail->tgl_permohonan)->translatedFormat('d F Y');
            $model_send_mail->jenis_izin = $permohonan_for_mail->jenis;
            $model_send_mail->no_telp_perusahaan = $permohonan_for_mail->no_telp_perusahaan;
            $model_send_mail->alamat_perusahaan = $permohonan_for_mail->alamat;
            $to = $permohonan_for_mail->email_perusahaan;

            // $get_att1 = $this->skpos->Spm($model_send_mail);
            // $get_att2 = $this->skpos->Kesanggupan($model_send_mail);
            // $get_att3 = $this->skpos->GenerateSK($model_send_mail);
            // $tampung_list_path = array();
            // $tampung_list_path[] = $get_att1->list_path;
            // $tampung_list_path[] = $get_att2->list_path;
            // $tampung_list_path[] = $get_att3->list_path;
            // $model_send_mail->list_path = $tampung_list_path;
            // Notification::route('mail', $to)->notify(new PostPermohonanPosMail($model_send_mail));
            // unlink($get_att1->list_path);
            // unlink($get_att2->list_path);
            // unlink($get_att3->list_path);
            // $get_att = $this->skpos->Spm($model_send_mail);
            // $model_send_mail->list_path = $get_att->list_path;
            // Notification::route('mail', $to)->notify(new PostPermohonanPosMail($model_send_mail));
            // unlink($get_att->list_path);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function PostPermohonanPenambahanLayanan($input)
    {
        try
        {
            $last_no_permohonan = $this->get_nomor->GetLastPermohomonanKomit();
            $no_penyelenggaraan = $this->gen_nomor->PenyelenggaraanKomit($last_no_permohonan);
            $last_no_izin = $this->get_nomor->GetLastIzin(TypeIzin::Pos, $input->id_izin_jenis);
            $no_sk_izin = $this->gen_nomor->no_sk_izin($last_no_izin, TypeIzin::Pos, $input->id_izin_jenis);
            $tanggal_input  = Carbon::now();
            $q_get_last_id = DB::select('SELECT a.id FROM p_permohonan a ORDER BY a.id DESC LIMIT 1');
            $id_permohonan = '';
            if($q_get_last_id==NULL){
                $id_permohonan = 1;
            }else{
                foreach ($q_get_last_id as $key => $value) {
                    $id_permohonan = ($value->id)+1;
                }
            }
            $insert_1 = sprintf("INSERT INTO `p_permohonan` (`id`, `no_penyelenggaraan`, `no_sk_izin`, `id_izin_jenis`, `id_perusahaan`, `id_permohonan_status`, `tanggal_input`) VALUES ('$id_permohonan', '$no_penyelenggaraan', '$no_sk_izin', '$input->id_izin_jenis', '$input->id_perusahaan', '0', '$tanggal_input')");
            DB::insert($insert_1);

            foreach($input->permohonan_layanan as $p)
            {
                $id_layanan = '';
                $q_id_layanan = DB::select('SELECT a.id FROM k_layanan a WHERE a.id_izin_jenis="'.$input->id_izin_jenis.'" AND a.layanan="'.$p->layanan.'" ORDER BY a.id DESC LIMIT 1');
                foreach ($q_id_layanan as $key => $value) {
                    $id_layanan = $value->id;
                }
                $insert_2 = sprintf("INSERT INTO `p_permohonan_penambahan_layanan_pos` (`id`, `id_permohonan`, `id_layanan`) VALUES (NULL, '$id_permohonan', '$id_layanan')");
                DB::insert($insert_2);
            }
            $per_info = new stdClass();
            $per_info->id_permohonan = $id_permohonan;
            $per_info->tanggal_input = $tanggal_input;
            $per_info->value = ConstLog::i_input_permohonan;
            $this->pinfo->PostPermohonanInfo($per_info);

            // $id_permohonan = 88;
            $permohonan_for_mail = $this->gpm->GetForEmailById($id_permohonan);
            $expired_date = date('Y-m-d', strtotime('+10 day', strtotime($permohonan_for_mail->tgl_permohonan)));
            $model_send_mail = new stdClass();
            $model_send_mail->no_sk =  $permohonan_for_mail->no_sk_izin;
            $model_send_mail->url_upload_spm =  '';
            $model_send_mail->id_permohonan =  $id_permohonan;
            $model_send_mail->id_izin_jenis =  $input->id_izin_jenis;
            $model_send_mail->nama_pt =  $permohonan_for_mail->nama_perusahaan;
            $model_send_mail->npwp =  $permohonan_for_mail->npwp;
            $model_send_mail->no_penyelenggaraan = $permohonan_for_mail->no_penyelenggaraan;
            $model_send_mail->expired_date = Carbon::parse($expired_date)->translatedFormat('d F Y');
            $model_send_mail->tanggal_input = Carbon::parse($permohonan_for_mail->tgl_permohonan)->translatedFormat('d F Y');
            $model_send_mail->jenis_izin = $permohonan_for_mail->jenis;
            $model_send_mail->no_telp_perusahaan = $permohonan_for_mail->no_telp_perusahaan;
            $model_send_mail->alamat_perusahaan = $permohonan_for_mail->alamat;
            $to = $permohonan_for_mail->email_perusahaan;

            $get_att2 = $this->skpos->Kesanggupan($model_send_mail);
            $tampung_list_path = array();
            $tampung_list_path[] = $get_att2->list_path;
            $model_send_mail->list_path = $tampung_list_path;
            Notification::route('mail', $to)->notify(new PostPermohonanPosMail($model_send_mail));
            unlink($get_att2->list_path);

            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function PostBuktiPembayaran($input)
    {
        try
        {
            $input->id_permohonan = Crypt::decryptString($input->id_permohonan);
            $tanggal_input = Carbon::now();
            $insert_ = sprintf("INSERT INTO `p_permohonan_pos_bukti_bayar_file` (`id`, `id_permohonan`, `nama`, `stream`, `tanggal_input`, `status`) VALUES (NULL, '$input->id_permohonan', '$input->nama', '$input->stream', '$tanggal_input', '0')");
            DB::insert($insert_);
            $per_info = new stdClass();
            $per_info->id_permohonan = $input->id_permohonan;
            $per_info->tanggal_input = $tanggal_input;
            $per_info->value = ConstLog::i_input_spm;
            $this->pinfo->PostPermohonanInfo($per_info);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function GetDetailPermohonan($id_permohonan)
    {
        $q = sprintf("SELECT a.id_izin_jenis,a.no_penyelenggaraan,aa.jenis AS izin_jenis,a.no_sk_izin,a.tanggal_input,per.nib, pm.penanaman_modal, pj.jenis, per.id as id_perusahaan, per.nama as nama_perusahaan, per.telp as telp_perusahaan, per.npwp, uf.nm_user as nama, pem.nik, uf.email_user as email, pem.telp,bb.nama AS nama_file,bb.stream from p_permohonan a LEFT JOIN m_perusahaan per ON a.id_perusahaan=per.id left join m_pemohon pem on per.id_pemohon = pem.id left join k_perusahaan_jenis pj on per.id_perusahaan_jenis = pj.id left join k_perusahaan_status ps on per.id_perusahaan_status = ps.id left join k_penanaman_modal pm on per.id_penanaman_modal = pm.id left join m_user_fo uf on pem.id_user_fo = uf.id LEFT JOIN k_izin_jenis aa ON a.id_izin_jenis=aa.id LEFT JOIN p_permohonan_pos_bukti_bayar_file bb ON a.id=bb.id_permohonan where a.id = %d ORDER BY bb.id DESC", $id_permohonan);
        $result = DB::select($q);
        return $result;
    }

    public function GetLogPembayaranByIdPermohonan($id_permohonan)
    {
        $id_permohonan = Crypt::decryptString($id_permohonan);
        $q = sprintf("SELECT a.* FROM p_permohonan_pos_bukti_bayar_file a where a.id_permohonan = %d", $id_permohonan);
        $result = DB::select($q);
        return $result;
    }

    public function GetFileSpm($id)
    {
        $q = sprintf("SELECT * from p_permohonan_pos_bukti_bayar_file where md5(id) = '".$id."'");
        $result = DB::select($q);
        return $result;
    }

    public function GetIzinJenisPos()
    {
        $q = sprintf("SELECT * from k_izin_jenis where id_izin = '1'");
        $result = DB::select($q);
        return $result;
    }
    
    public function GetAll($start, $length, $kolom, $order_by, $search)
    {
        $q = sprintf("SELECT p.id, p.no_penyelenggaraan, p.no_sk_izin, i.izin, ij.jenis, per.nama as nama_perusahaan, ps.status, ps.id as id_permohonan_status, pinfo.value as info, p.tanggal_input, ppk.no_komitmen from p_permohonan as p LEFT JOIN p_permohonan_pos_bukti_bayar_file aaa ON aaa.id_permohonan=p.id left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id where ij.id_izin='1' AND aaa.status='1'");
        if($search != '')
        {
            $q .= sprintf(" AND (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or ppk.no_komitmen like '%%%s%%')", $search, $search, $search, $search, $search, $search, $search) ;
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
        $q = sprintf("SELECT COUNT(p.id) as jumlah from p_permohonan as p LEFT JOIN p_permohonan_pos_bukti_bayar_file aaa ON aaa.id_permohonan=p.id left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id where ij.id_izin='1' AND aaa.status='1'");
        if($search != '')
        {
            $q .= sprintf(" AND (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or ppk.no_komitmen like '%%%s%%')", $search, $search, $search, $search, $search, $search, $search) ;
        }
        $result = DB::select($q)[0]->jumlah;
        return $result;
    }

    public function GetAllPembayaran($start, $length, $kolom, $order_by, $search)
    {
        $q = sprintf("SELECT p.id, (SELECT COUNT(bx.id_permohonan) FROM p_permohonan_layanan bx WHERE bx.id_permohonan=p.id) AS permohonan_baru, (SELECT COUNT(cx.id_permohonan) FROM p_permohonan_penambahan_layanan_pos cx WHERE cx.id_permohonan=p.id) AS penambahan_layanan, aaa.status AS status_bayar, p.no_penyelenggaraan, p.no_sk_izin, i.izin, ij.jenis, per.nama as nama_perusahaan, ps.status, ps.id as id_permohonan_status, pinfo.value as info, p.tanggal_input, ppk.no_komitmen from p_permohonan as p LEFT JOIN p_permohonan_pos_bukti_bayar_file aaa ON p.id=aaa.id_permohonan left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join p_permohonan_komit_pos as ppk on ppk.id_permohonan = p.id where ij.id_izin='1' AND i.id='1' AND aaa.status='0'");
        if($search != '')
        {
            $q .= sprintf(" AND (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or ppk.no_komitmen like '%%%s%%')", $search, $search, $search, $search, $search, $search, $search) ;
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

    public function GetTotalAllPembayaran($search)
    {
        $q = sprintf("SELECT COUNT(p.id) as jumlah from p_permohonan as p LEFT JOIN p_permohonan_pos_bukti_bayar_file aaa ON p.id=aaa.id_permohonan left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join p_permohonan_komit_pos as ppk on ppk.id_permohonan = p.id where ij.id_izin='1' AND i.id='1' AND aaa.status='0'");
        if($search != '')
        {
            $q .= sprintf(" AND (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or ppk.no_komitmen like '%%%s%%')", $search, $search, $search, $search, $search, $search, $search) ;
        }
        $result = DB::select($q)[0]->jumlah;
        return $result;
    }

    public function GetAllHistoryPembayaran($start, $length, $kolom, $order_by, $search)
    {
        $q = sprintf("SELECT p.id, (SELECT ppk.no_komitmen FROM p_permohonan_komit ppk WHERE ppk.id_permohonan=p.id LIMIT 1) AS no_komit_baru, (SELECT ppk.no_komitmen FROM p_permohonan_komit_pos ppk WHERE ppk.id_permohonan=p.id LIMIT 1) AS no_komit_tambah_layanan, aaa.status AS status_bayar, p.no_penyelenggaraan, p.no_sk_izin, i.izin, ij.jenis, per.nama as nama_perusahaan, ps.status, ps.id as id_permohonan_status, pinfo.value as info, p.tanggal_input from p_permohonan as p LEFT JOIN p_permohonan_pos_bukti_bayar_file aaa ON p.id=aaa.id_permohonan left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin where ij.id_izin='1' AND i.id='1' AND aaa.status='1'");
        if($search != '')
        {
            $q .= sprintf(" AND (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%')", $search, $search, $search, $search, $search, $search) ;
        }
        $nama_kolom = '';
        if($kolom == 0)
        {
            $nama_kolom = "p.no_penyelenggaraan";
        }
        if($kolom == 2)
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

    public function GetTotalAllHistoryPembayaran($search)
    {
        $q = sprintf("SELECT COUNT(p.id) as jumlah from p_permohonan as p LEFT JOIN p_permohonan_pos_bukti_bayar_file aaa ON p.id=aaa.id_permohonan left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin where ij.id_izin='1' AND i.id='1' AND aaa.status='1'");
        if($search != '')
        {
            $q .= sprintf(" AND (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%')", $search, $search, $search, $search, $search, $search) ;
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
        $q = sprintf("SELECT p.id, (SELECT dsd.no_komitmen FROM p_permohonan_komit dsd WHERE dsd.id_permohonan=p.id LIMIT 1) AS no_komit_baru, (SELECT dsdc.no_komitmen FROM p_permohonan_komit_pos dsdc WHERE dsdc.id_permohonan=p.id LIMIT 1) AS no_komit_layanan_baru, p.no_penyelenggaraan, p.no_sk_izin, i.izin, ij.jenis, per.nama as nama_perusahaan, ps.status, ps.id as id_permohonan_status, pinfo.value as info, p.tanggal_input, pds.id as done_disposisi from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin");
        $q .= sprintf(" left join p_permohonan_disposisi pd on pd.id_permohonan = p.id left join p_permohonan_disposisi_staf pds on pds.id_permohonan = p.id");
        $q .= sprintf(" where pd.id_user=%d and p.id_permohonan_status=%d", $id_user, TypePermohonanStatus::BelumEfektif);
        if($search != '')
        {
            $q .= sprintf(" and (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%')", $search, $search, $search, $search, $search, $search) ;
        }
        $nama_kolom = '';
        if($kolom == 0)
        {
            $nama_kolom = "p.no_penyelenggaraan";
        }
        if($kolom == 2)
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
        $q .= sprintf(" left join p_permohonan_disposisi pd on pd.id_permohonan = p.id left join p_permohonan_disposisi_staf pds on pds.id_permohonan = p.id");
        $q .= sprintf(" where pd.id_user=%d", $id_user);
        if($search != '')
        {
            $q .= sprintf(" and (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%')", $search, $search, $search, $search, $search, $search) ;
        }
        $result = DB::select($q)[0]->jumlah;
        return $result;
    }

    public function GetByDisposisiSend($id_user, $id_izin_jenis, $start, $length, $kolom, $order_by, $search)
    {
        $q = sprintf("SELECT p.id, p.no_penyelenggaraan, p.no_sk_izin, i.izin, ij.jenis, per.nama as nama_perusahaan, ps.status, ps.id as id_permohonan_status, pinfo.value as info, p.tanggal_input, ppk.no_komitmen from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin");
        $q .= sprintf(" left join p_permohonan_disposisi_kirim pd on pd.id_permohonan = p.id left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id left join m_user mu on mu.id = pd.id_user left join m_jabatan mj on mj.id = mu.id_jabatan");
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
        $q .= sprintf(" left join p_permohonan_disposisi_kirim pd on pd.id_permohonan = p.id left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id left join m_user mu on mu.id = pd.id_user left join m_jabatan mj on mj.id = mu.id_jabatan");
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
            $q = sprintf("SELECT p.id, p.no_penyelenggaraan, p.no_sk_izin, i.izin, ij.jenis, per.nama as nama_perusahaan, per.nib, per.npwp, per.email as email_perusahaan, per.id as id_perusahaan, per.telp as telp_perusahaan, kpm.penanaman_modal, kpj.jenis as jenis_perusahaan, uf.nm_user as nama_pemohon, pe.nik, uf.email_user as email_pemohon, pe.telp as telp_pemohon, ps.status, pinfo.value as info, p.tanggal_input, pds.id as done_disposisi from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join m_pemohon pe on pe.id = per.id_pemohon left join p_permohonan_disposisi_staf as pds on pds.id_permohonan = p.id left join m_user_fo as uf on pe.id_user_fo = uf.id left join k_penanaman_modal as kpm on kpm.id = per.id_penanaman_modal left join k_perusahaan_jenis kpj on kpj.id = per.id_perusahaan_jenis where p.id=%d", $id_permohonan);
            $result_header = DB::select($q)[0];
            $model->data_header = $result_header;
            $q = sprintf("SELECT *, pkl.id as id_permohonan_komit_layanan FROM p_permohonan_layanan pl left join k_layanan l on pl.id_layanan = l.id LEFT join p_permohonan_komit_layanan pkl on pkl.id_permohonan_layanan = pl.id where pl.id_permohonan = %d", $id_permohonan);
            $result_layanan = DB::select($q);
            $model->data_layanan = array();
            foreach($result_layanan as $rl)
            {
                $q = sprintf("SELECT *, kl.id as id_permohonan_komit_kelengkapan FROM p_permohonan_komit_kelengkapan kl left join k_komit_kelengkapan kkk on kkk.id = kl.id_jenis_kelengkapan where kl.id_permohonan_komit_layanan = %d order by kkk.urutan asc",  $rl->id_permohonan_komit_layanan);
                $result_layanan_kelengkapan = DB::select($q);
                $model_layanan = new stdClass();
                $model_layanan->layanan = $rl;
                $model_layanan->kelengkapan = array();
                foreach($result_layanan_kelengkapan as $lk)
                {
                    $model_kelengkapan = $lk;
                    $q = sprintf("SELECT id, nama from p_permohonan_komit_file where id_permohonan_komit_kelengkapan = %d", $lk->id_permohonan_komit_kelengkapan);
                    $result_kelengkapan_file = DB::select($q);
                    $file_array = array();
                    foreach($result_kelengkapan_file as $rkf)
                    {
                        $rkf->id = Crypt::encryptString($rkf->id);
                        array_push($file_array, $rkf); 
                    }
                    $model_kelengkapan->file = $file_array;
                    array_push($model_layanan->kelengkapan, $model_kelengkapan);
                }
                array_push($model->data_layanan, $model_layanan);
            }
            $query_get_wilayah = sprintf("SELECT a.* FROM p_permohonan_pos_cakupan_wilayah a WHERE a.id_permohonan=".$id_permohonan);
            $getdata_wilayah = DB::select($query_get_wilayah);
            $data_wilayah = array();
            foreach ($getdata_wilayah as $key => $value) {
                $id_wilayah = '';
                if(strlen($value->id_wilayah)==2){
                    $data_provinsi = DB::select("SELECT a.* FROM provinsi a WHERE a.id_provinsi=".$value->id_wilayah)[0];
                    $id_wilayah = $data_provinsi->nm_provinsi;
                }elseif(strlen($value->id_wilayah)==4){
                    $data_kabupaten = DB::select("SELECT a.* FROM kabupaten a WHERE a.Id=".$value->id_wilayah)[0];
                    $id_wilayah = $data_kabupaten->Kabupaten;
                }else{
                    echo'';
                }
                $data_wilayah[] = $id_wilayah;
            }
            $model->data_wilayah = $data_wilayah;
            $model->log = $this->plog->Get($id_permohonan);
            return $model;
        }
        catch(Exception $e)
        {
            return $model;
        }     
    }

    public function GetByNoSKIzin($no_sk_izin, $id_izin_jenis)
    {
        $model = new stdClass();
        $list_permohonan = $this->GetValidasiPermohonan($no_sk_izin, $id_izin_jenis);
        if(count($list_permohonan) != 0)
        {
            $permohonan = $list_permohonan[0];
            $model->permohonan = $permohonan;
            $q = sprintf("SELECT *, pl.id as id_permohonan_layanan from p_permohonan_layanan pl left join k_layanan kl on pl.id_layanan = kl.id where pl.id_permohonan = %d", $permohonan->id_permohonan);
            $layanan = DB::select($q);
            $model->data_layanan = array();
            if($layanan==NULL){
                $q = sprintf("SELECT *, pl.id as id_permohonan_layanan from p_permohonan_penambahan_layanan_pos pl left join k_layanan kl on pl.id_layanan = kl.id where pl.id_permohonan = %d", $permohonan->id_permohonan);
                $getlayanan = DB::select($q);
                foreach ($getlayanan as $l) {
                    $model_layanan = new stdClass();
                    $model_layanan->layanan = $l;
                    array_push($model->data_layanan, $model_layanan);
                }
            }else{
                foreach ($layanan as $l) {
                    $model_layanan = new stdClass();
                    $model_layanan->layanan = $l;
                    array_push($model->data_layanan, $model_layanan);
                }
            }
        }        
        return $model;
    }

    public function GetValidasiPermohonan($no_sk_izin, $id_izin_jenis)
    {
        $q = sprintf("SELECT *, p.id as id_permohonan from p_permohonan p left join m_perusahaan mp on p.id_perusahaan = mp.id where p.no_sk_izin = '%s' and p.id_izin_jenis=%d", $no_sk_izin, $id_izin_jenis);
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
            $qx = sprintf("SELECT a.*, (SELECT COUNT(b.id_permohonan) FROM p_permohonan_layanan b WHERE b.id_permohonan=a.id) AS permohonan_baru, (SELECT COUNT(c.id_permohonan) FROM p_permohonan_penambahan_layanan_pos c WHERE c.id_permohonan=a.id) AS penambahan_layanan FROM p_permohonan a WHERE a.id=".$input->id_permohonan);
            $getlayanan = DB::select($qx);
            foreach ($getlayanan as $key => $value) {
                if($value->permohonan_baru>0){
                    $q = sprintf("UPDATE p_permohonan set id_permohonan_status = %d where id = %d",$input->id_permohonan_status, $input->id_permohonan);
                    $a = DB::update($q);
                    if($input->id_permohonan_status=='1'){
                        $qxx = sprintf("SELECT v.*,a.id_perusahaan FROM p_permohonan a LEFT JOIN p_permohonan_layanan v ON v.id_permohonan=a.id WHERE a.id=".$input->id_permohonan);
                        $get_layanan = DB::select($qxx);
                        foreach ($get_layanan as $key => $row) {
                            $cq = sprintf("INSERT INTO `p_permohonan_layanan_pos_per_perusahaan` (`id`, `id_permohonan`, `id_layanan`, `id_perusahaan`, `status`) VALUES (NULL, '".$input->id_permohonan."', '".$row->id_layanan."', '".$row->id_perusahaan."', '0');");
                            $ac = DB::insert($cq);
                        }
                    }else{
                        echo'';
                    }
                    return true;
                }elseif($value->penambahan_layanan>0){
                    return true;
                }else{
                    return false;
                }
            }
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function GetByPemohon($id_pemohon, $start, $length, $kolom, $order_by, $search)
    {
        $q = sprintf("SELECT p.id, p.id_permohonan_status, (SELECT COUNT(xy.id_permohonan) FROM p_permohonan_penambahan_layanan_pos xy WHERE xy.id_permohonan=p.id) AS layanan_baru, p.no_penyelenggaraan,ppl.id_layanan,ij.id AS id_izin_jenis, (SELECT xxx.status FROM p_permohonan_pos_bukti_bayar_file xxx WHERE xxx.id_permohonan=p.id ORDER BY xxx.id DESC LIMIT 1) AS status_bayar, i.izin, ij.jenis, ps.status, ps.id as id_permohonan_status, pinfo.value as info, p.tanggal_input, sdfs.id_permohonan_komit_kelengkapan_status, sdfsp.id_permohonan_komit_kelengkapan_status AS id_permohonan_komit_kelengkapan_status_pos, pulo.id_ulo_status, temp.jumlah as uji_mandiri from p_permohonan as p LEFT JOIN p_permohonan_layanan ppl ON p.id=ppl.id_permohonan left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join p_permohonan_komit as sdfs on sdfs.id_permohonan = p.id left join p_permohonan_komit_pos as sdfsp on sdfsp.id_permohonan = p.id LEFT JOIN (SELECT COUNT(mup.value) as jumlah, pk.id_permohonan FROM p_mekanisme_ulo_proses mup left join p_permohonan_komit_layanan gdr on mup.id_permohonan_komit_layanan = gdr.id left join p_permohonan_komit pk on gdr.id_permohonan_komit = pk.id left join p_permohonan_komit pkp on gdr.id_permohonan_komit = pkp.id where mup.value = '%s' group by pk.id_permohonan, mup.value) as temp on temp.id_permohonan = p.id LEFT JOIN p_ulo pulo on pulo.id_permohonan_komit = sdfs.id where i.id='1' AND per.id_pemohon=%d", ConstHelper::uji_mandiri, $id_pemohon);
        if($search != '')
        {
            $q .= sprintf(" and (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or sdfs.no_komitmen like '%%%s%%' or sdfsp.no_komitmen like '%%%s%%')", $search, $search, $search, $search, $search, $search, $search) ;
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
        $q = sprintf("SELECT COUNT(p.id) as jumlah from p_permohonan as p left join k_izin_jenis as ij on p.id_izin_jenis = ij.id left join m_perusahaan as per on p.id_perusahaan = per.id left join k_permohonan_status as ps on p.id_permohonan_status = ps.id left join p_permohonan_info as pinfo on p.id = pinfo.id_permohonan left join k_izin as i on i.id = ij.id_izin left join p_permohonan_komit as ppk on ppk.id_permohonan = p.id where i.id='1' AND per.id_pemohon=%d", $id_pemohon);
        if($search != '')
        {
            $q .= sprintf(" and (p.no_penyelenggaraan like '%%%s%%' or p.no_sk_izin like '%%%s%%' or ij.jenis like '%%%s%%' or per.nama like '%%%s%%' or ps.status like '%%%s%%' or pinfo.value like '%%%s%%' or ppk.no_komitmen like '%%%s%%')", $search, $search, $search, $search, $search, $search, $search) ;
        }
        $result = DB::select($q)[0]->jumlah;
        return $result;
    }
    
	public function GetBuktiBayarFile($id_permohonan)
    {
        $q = sprintf("SELECT bb.nama AS nama, bb.stream from p_permohonan a LEFT JOIN p_permohonan_pos_bukti_bayar_file bb ON a.id=bb.id_permohonan where a.id = %d", $id_permohonan);
        $result = DB::select($q);
        return $result;
    }

    public function ValidasiBuktiBayar($status_bayar,$id_permohonan)
    {
        try
        {
            $qx = sprintf("SELECT a.*, (SELECT COUNT(b.id_permohonan) FROM p_permohonan_layanan b WHERE b.id_permohonan=a.id) AS permohonan_baru, (SELECT COUNT(c.id_permohonan) FROM p_permohonan_penambahan_layanan_pos c WHERE c.id_permohonan=a.id) AS penambahan_layanan FROM p_permohonan a WHERE a.id=".$id_permohonan);
            $getlayanan = DB::select($qx);
            foreach ($getlayanan as $key => $value) {
                if($value->permohonan_baru>0){
                    $q = sprintf("UPDATE `p_permohonan_pos_bukti_bayar_file` SET `status` = '".$status_bayar."' WHERE `id_permohonan` = ".$id_permohonan);
                }elseif($value->penambahan_layanan>0){
                    $q = sprintf("UPDATE `p_permohonan_pos_bukti_bayar_file` SET `status` = '".$status_bayar."' WHERE `id_permohonan` = ".$id_permohonan);
                    if($status_bayar=='1'){
                        // memasukkan gabungan layanan
                        $qc = sprintf("SELECT d.id FROM p_permohonan_layanan_pos_per_perusahaan cc LEFT JOIN p_permohonan a ON cc.id_permohonan=a.id LEFT JOIN k_izin_jenis b ON a.id_izin_jenis=b.id LEFT JOIN k_layanan d ON cc.id_layanan=d.id WHERE a.id_perusahaan='".$value->id_perusahaan."' AND a.id_permohonan_status='1' AND b.id_izin='1' GROUP BY d.id");
                        $balikan = DB::select($qc);
                        foreach ($balikan as $key => $row) {
                            $cq = sprintf("INSERT INTO `p_permohonan_layanan_pos_per_perusahaan` (`id`, `id_permohonan`, `id_layanan`, `id_perusahaan`, `status`) VALUES (NULL, '".$value->id_permohonan."', '".$row->id."', '".$value->id_perusahaan."', '1');");
                            $ac = DB::insert($cq);
                        }
                        $qc = sprintf("SELECT cc.id_layanan FROM p_permohonan_penambahan_layanan_pos cc LEFT JOIN p_permohonan a ON cc.id_permohonan=a.id WHERE a.id_permohonan='".$value->id_permohonan."' GROUP BY cc.id_layanan");
                        $balikan = DB::select($qc);
                        foreach ($balikan as $key => $row) {
                            $cq = sprintf("INSERT INTO `p_permohonan_layanan_pos_per_perusahaan` (`id`, `id_permohonan`, `id_layanan`, `id_perusahaan`, `status`) VALUES (NULL, '".$value->id_permohonan."', '".$row->id_layanan."', '".$value->id_perusahaan."', '0');");
                            $ac = DB::insert($cq);
                        }

                        $qcc = sprintf("UPDATE `p_permohonan` SET `id_permohonan_status` = '2' WHERE `id_perusahaan` = ".$value->id_perusahaan." AND `id_perusahaan` = '1'");
                        DB::update($qcc);
                        $qc = sprintf("UPDATE `p_permohonan` SET `id_permohonan_status` = '".$status_bayar."' WHERE `id_permohonan` = ".$id_permohonan);
                        DB::update($qc);
                        $input_log = new stdClass();
                        $input_log->id_permohonan = $id_permohonan;
                        $input_log->status = sprintf("%s", ConstLog::izin_efektif);
                        $input_log->nama =  "";
                        $input_log->jabatan = "";
                        $input_log->catatan = "";
                        $this->plog->Post($input_log);
                    }else{
                        echo'';
                    }
                }else{
                    echo'';
                }
            }
            DB::update($q);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function CekSK($sk,$id_izin_jenis)
    {
        try
        {
            $q = sprintf("SELECT *  FROM `p_permohonan` WHERE `no_sk_izin` LIKE '".$sk."' AND `id_izin_jenis` = ".$id_izin_jenis." ORDER BY `id`  DESC");
            $balikan = DB::select($q);
            return $balikan;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function CekPengajuanPermohonan($id_perusahaan){
        try
        {
            $q = sprintf("SELECT a.* FROM p_permohonan a LEFT JOIN k_izin_jenis j ON a.id_izin_jenis=j.id WHERE a.id_perusahaan = '".$id_perusahaan."' AND (a.id_permohonan_status = '0' OR a.id_permohonan_status = '1') AND j.id_izin = '1'");
            $balikan = DB::select($q);
            return $balikan;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function GetLayanan()
    {
        try
        {
            $q = sprintf("SELECT d.layanan FROM k_layanan d LEFT JOIN k_izin_jenis k ON d.id_izin_jenis=k.id WHERE k.id_izin='1' GROUP BY d.layanan");
            $balikan = DB::select($q);
            return $balikan;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function CekLayananAktif($id_perusahaan)
    {
        try
        {
            // $q = sprintf("SELECT d.layanan FROM p_permohonan a LEFT JOIN k_izin_jenis b ON a.id_izin_jenis=b.id LEFT JOIN p_permohonan_layanan c ON a.id=c.id_permohonan LEFT JOIN k_layanan d ON c.id_layanan=d.id WHERE a.id_perusahaan='".$id_perusahaan."' AND a.id_permohonan_status='1' AND b.id_izin='1' GROUP BY d.layanan");
            // $balikan = DB::select($q);
            // if($balikan==NULL){
            //     $q = sprintf("SELECT d.layanan FROM p_permohonan a LEFT JOIN k_izin_jenis b ON a.id_izin_jenis=b.id LEFT JOIN p_permohonan_penambahan_layanan_pos c ON a.id=c.id_permohonan LEFT JOIN k_layanan d ON c.id_layanan=d.id WHERE a.id_perusahaan='".$id_perusahaan."' AND a.id_permohonan_status='1' AND b.id_izin='1' GROUP BY d.layanan");
            //     $balikan_lagi = DB::select($q);
            //     return $balikan_lagi;
            // }else{
            //     return $balikan;
            // }
            $q = sprintf("SELECT d.layanan FROM p_permohonan_layanan_pos_per_perusahaan cc LEFT JOIN p_permohonan a ON cc.id_permohonan=a.id LEFT JOIN k_izin_jenis b ON a.id_izin_jenis=b.id LEFT JOIN k_layanan d ON cc.id_layanan=d.id WHERE a.id_perusahaan='".$id_perusahaan."' AND a.id_permohonan_status='1' AND b.id_izin='1' GROUP BY d.layanan");
            $balikan = DB::select($q);
            return $balikan;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function CekPenambahanLayananAktif($id_perusahaan)
    {
        try
        {
            $q = sprintf("SELECT d.layanan FROM p_permohonan a LEFT JOIN k_izin_jenis b ON a.id_izin_jenis=b.id LEFT JOIN p_permohonan_penambahan_layanan_pos c ON a.id=c.id_permohonan LEFT JOIN k_layanan d ON c.id_layanan=d.id WHERE a.id_perusahaan='".$id_perusahaan."' AND a.id_permohonan_status='1' AND b.id_izin='1' GROUP BY d.layanan");
            $balikan = DB::select($q);
            return $balikan;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
}