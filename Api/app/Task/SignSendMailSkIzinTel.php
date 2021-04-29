<?php

namespace App\Task;

use stdClass;
use Exception;
use App\Enums\TypeIzin;
use App\GeneratePdf\SKIzin;
use Illuminate\Support\Carbon;
use App\Enums\TypeIzinJenisTel;
use Illuminate\Support\Facades\DB;
use App\Enums\TypePermohonanStatus;
use App\Notifications\PermohonanMail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;

class SignSendMailSkIzinTel
{
    private $sk_izin;
    public function __construct()
    {
        $this->sk_izin = new SKIzin();
    }
  
    public function Process()
    {
        try
        {
            $q = sprintf("SELECT p.*, pemohon.id as id_pemohon, per.nama as nama_perusahaan, user_fo.email_user from p_permohonan p LEFT JOIN p_sk_izin_file sk_izin on p.id = sk_izin.id_permohonan LEFT JOIN k_izin_jenis kij on p.id_izin_jenis = kij.id LEFT JOIN m_perusahaan per on p.id_perusahaan = per.id LEFT JOIN m_pemohon pemohon on per.id_pemohon = pemohon.id LEFT JOIN m_user_fo user_fo on pemohon.id_user_fo = user_fo.id where p.id_permohonan_status = %d and sk_izin.id_permohonan is NULL AND kij.id_izin = %d AND p.id_izin_jenis != %d", TypePermohonanStatus::BelumEfektif, TypeIzin::Telekomunikasi, TypeIzinJenisTel::Penomoran);
            $permohonan = DB::select($q);
            foreach ($permohonan as $p) {
                $jenis_izin = "";
                if ($p->id_izin_jenis == TypeIzinJenisTel::Jaringan) {
                    $jenis_izin = "Jaringan Telekomunikasi";
                } else if ($p->id_izin_jenis == TypeIzinJenisTel::Jasa) {
                    $jenis_izin = "Jasa Telekomunikasi";
                } else if ($p->id_izin_jenis == TypeIzinJenisTel::Khusus) {
                    $jenis_izin = "Telekomunikasi Khusus";
                }

                //generate sk dan sign
                $model_sk = new stdClass();
                $model_sk->url = sprintf("%s/check_sk/%s", env('BASE_URL_APP_FO'), Crypt::encryptString($p->id));
                $model_sk->no_sk = $p->no_sk_izin;
                $model_sk->tanggal = Carbon::now();
                $model_sk->id_pemohon = $p->id_pemohon;
                $model_sk->id_permohonan = $p->id;

                $result_sk = $this->sk_izin->Tel($model_sk, $p->id_izin_jenis);
                if ($result_sk->result) {
                    $model_send_mail = new stdClass();
                    $model_send_mail->nama_pt =  $p->nama_perusahaan;
                    $model_send_mail->jenis_izin = $jenis_izin;
                    $model_send_mail->no_permohonan = $p->no_penyelenggaraan;
                    $model_send_mail->no_sk = $p->no_sk_izin;
                    $model_send_mail->url_komitmen = sprintf("%s/tel/permohonan_komit/validasi/%s", env('BASE_URL_APP_FO'), $p->id_izin_jenis);
                    $model_send_mail->list_path = $result_sk->list_path;
                    $to = $p->email_user;
                    Notification::route('mail', $to)->notify(new PermohonanMail($model_send_mail));

                    foreach ($result_sk->list_path as $lp) {
                        unlink($lp->file_pdf_path);

                        if ($lp->type_izin_jenis_tel == TypeIzinJenisTel::Penomoran) {
                            $this->sk_izin->PostSKPenomoranFile($lp);
                        } else {
                            $this->sk_izin->PostSKFile($lp);
                        }
                    }
                }
            }
            return true;
        }
        catch(Exception $ex)
        {
            return false;
        }
    }
}