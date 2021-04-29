<?php

namespace App\Repo;

use stdClass;
use Exception;
use App\Enums\ConstLog;
use App\Enums\ConstHelper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Enums\TypeInputMekanismeUlo;
use App\GeneratePdf\SKIzin;
use Illuminate\Support\Facades\Crypt;
use App\Notifications\ApprovalUloMail;
use App\Notifications\PermohonanKomitMail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ApprovalPermohonanMail;
use App\Notifications\PengembalianPermohonanMail;
use App\Notifications\PengembalianUloMail;

class PermohonanLogDb
{
    private $pinfo;
    private $gpm;
    public function __construct()
    {
        $this->pinfo = new PermohonanInfoDb();
        $this->gpm = new GetPermohonanMailDb();
    }

    public function Get($id_permohonan)
    {
        $q = sprintf("SELECT * from p_permohonan_log where id_permohonan=%d order by tanggal_input asc", $id_permohonan);
        $result = DB::select($q);
        return $result;
    }

    public function Post($input)
    {
        try
        {
            $tanggal_input  = Carbon::now();
            $q = sprintf("INSERT into p_permohonan_log(id_permohonan, status, nama, jabatan, tanggal_input, catatan) values(%d, '%s', '%s', '%s','%s', '%s')", $input->id_permohonan, $input->status, $input->nama, $input->jabatan, $tanggal_input->format('Y-m-d H:i:s'), $input->catatan);
            $result = DB::insert($q);

            //insert to p_permohonan info
            $per_info = new stdClass();
            $per_info->id_permohonan = $input->id_permohonan;
            $per_info->tanggal_input = $tanggal_input;
            if($input->status == ConstLog::pengajuan_komitmen)
            {
                $per_info->value = ConstLog::i_proses_disposisi_jabatan;

                $permohonan_for_mail = $this->gpm->GetForEmailById($input->id_permohonan);
                $model_send_mail = new stdClass();
                $model_send_mail->nama_pt =  $permohonan_for_mail->nama_perusahaan;
                $model_send_mail->no_permohonan = $permohonan_for_mail->no_komitmen;
                $model_send_mail->no_sk = $permohonan_for_mail->no_sk_izin;
                $to = $permohonan_for_mail->email_user;
                Notification::route('mail', $to)->notify(new PermohonanKomitMail($model_send_mail));
            }
            else if($input->status == ConstLog::pengajuan_permohonan)
            {
                $per_info->value = ConstLog::i_proses_disposisi_jabatan;

                $permohonan_for_mail = $this->gpm->GetForEmailById($input->id_permohonan);
                $model_send_mail = new stdClass();
                $model_send_mail->nama_pt =  $permohonan_for_mail->nama_perusahaan;
                $model_send_mail->no_permohonan = $permohonan_for_mail->no_komitmen;
                $model_send_mail->no_sk = $permohonan_for_mail->no_sk_izin;
                $to = $permohonan_for_mail->email_user;
                Notification::route('mail', $to)->notify(new PermohonanKomitMail($model_send_mail));
            }
            else if($input->status == ConstLog::disposisi_jabatan)
            {
                $per_info->value = ConstLog::i_proses_evaluasi;
            }
            else if($input->status == ConstLog::pengembalian_permohonan)
            {
                $per_info->value = ConstLog::i_proses_perbaikan;

                $permohonan_for_mail = $this->gpm->GetForEmailById($input->id_permohonan);
                $model_send_mail = new stdClass();
                $model_send_mail->nama_pt =  $permohonan_for_mail->nama_perusahaan;
                $model_send_mail->no_permohonan = $permohonan_for_mail->no_komitmen;
                $model_send_mail->no_sk = $permohonan_for_mail->no_sk_izin;
                $model_send_mail->catatan = $input->catatan;
                $model_send_mail->jenis_izin = $permohonan_for_mail->jenis;
                $model_send_mail->url_edit_komitmen = sprintf("%s/tel/permohonan/edit_komitmen/%s", env('BASE_URL_APP_FO'), Crypt::encryptString($input->id_permohonan));
                $to = $permohonan_for_mail->email_user;
                Notification::route('mail', $to)->notify(new PengembalianPermohonanMail($model_send_mail));
            }
            else if($input->status == ConstLog::perbaikan)
            {
                $per_info->value = ConstLog::i_proses_evaluasi;
            }
            else if($input->status == ConstLog::evaluasi_jabatan)
            {
                $per_info->value = ConstLog::i_proses_setuju;
            }
            else if($input->status == ConstLog::approval_jabatan)
            {
                $_jabatan = explode(' ', $input->jabatan)[0];
                if($_jabatan == ConstHelper::kasubdit)
                {
                    //kasubdit
                    $per_info->value = ConstLog::i_proses_pemilihan_tgl_ulo;

                    $permohonan_for_mail = $this->gpm->GetForEmailById($input->id_permohonan);
                    $model_send_mail = new stdClass();
                    $model_send_mail->nama_pt =  $permohonan_for_mail->nama_perusahaan;
                    $model_send_mail->no_permohonan = $permohonan_for_mail->no_komitmen;
                    $model_send_mail->no_sk = $permohonan_for_mail->no_sk_izin;
                    $model_send_mail->url_mekanisme_ulo = sprintf("%s/tel/permohonan_komit/mekanisme_ulo/%s/%s", env('BASE_URL_APP_FO'), Crypt::encryptString($input->id_permohonan), TypeInputMekanismeUlo::Post);
                    $to = $permohonan_for_mail->email_user;
                    Notification::route('mail', $to)->notify(new ApprovalPermohonanMail($model_send_mail));
                }
                else
                {
                    //kasi
                    $per_info->value = ConstLog::i_proses_setuju;
                }
                
            }
            else if($input->status == ConstLog::pemilihan_tgl_ulo)
            {
                if($input->type_input_mekanisme_ulo == TypeInputMekanismeUlo::Post)
                {
                    $per_info->value = ConstLog::i_proses_disposisi_jabatan;    
                }
                else
                {
                    $per_info->value = ConstLog::i_proses_evaluasi;
                }
            }
            else if($input->status == ConstLog::pengembalian_ulo)
            {
                $per_info->value = ConstLog::i_proses_ulang_ulo;

                $permohonan_for_mail = $this->gpm->GetForEmailById($input->id_permohonan);
                $model_send_mail = new stdClass();
                $model_send_mail->nama_pt =  $permohonan_for_mail->nama_perusahaan;
                $model_send_mail->no_permohonan = $permohonan_for_mail->no_komitmen;
                $model_send_mail->no_sk = $permohonan_for_mail->no_sk_izin;
                $model_send_mail->catatan = $input->catatan;
                $model_send_mail->jenis_izin = $permohonan_for_mail->jenis;
                $model_send_mail->url_mekanisme_ulo = sprintf("%s/tel/permohonan_komit/mekanisme_ulo/%s/%s", env('BASE_URL_APP_FO'), Crypt::encryptString($input->id_permohonan), TypeInputMekanismeUlo::Edit);
                $to = $permohonan_for_mail->email_user;
                Notification::route('mail', $to)->notify(new PengembalianUloMail($model_send_mail));
            }
            else if($input->status == ConstLog::approval_ulo_jabatan)
            {
                $per_info->value = ConstLog::i_proses_setuju_ulo;
            }
            else if($input->status == ConstLog::izin_efektif)
            {
                $per_info->value = ConstLog::i_izin_efektif;
            }
            else if($input->status == ConstLog::approval_no_komit)
            {
                $per_info->value = ConstLog::i_proses_setuju;
            }
            else if($input->status == 'Komitmen Akhir Pos')
            {
                $q = sprintf("SELECT a.*, (SELECT COUNT(b.id_permohonan) FROM p_permohonan_layanan b WHERE b.id_permohonan=a.id) AS permohonan_baru, (SELECT COUNT(c.id_permohonan) FROM p_permohonan_penambahan_layanan_pos c WHERE c.id_permohonan=a.id) AS penambahan_layanan FROM p_permohonan a WHERE a.id=".$input->id_permohonan);
                $getlayanan = DB::select($q);
                foreach ($getlayanan as $key => $value) {
                    if($value->permohonan_baru>0){
                        $per_info->value = ConstLog::i_izin_efektif;
                    }elseif($value->penambahan_layanan>0){
                        $per_info->value = ConstLog::i_input_permohonan_spm;
                    }else{
                        echo'';
                    }
                }
            }
            
            $this->pinfo->PostPermohonanInfo($per_info);
            return $result;
        }
        catch(Exception $ex)
        {
            return false;
        }
        
    }
}