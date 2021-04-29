<?php

namespace App\Task;

use App\Enums\TypeIzinJenisTel;
use stdClass;
use Exception;
use App\GeneratePdf\SKIzin;
use Illuminate\Support\Carbon;
use App\Repo\GetPermohonanMailDb;
use Illuminate\Support\Facades\DB;
use App\Enums\TypePermohonanStatus;
use Illuminate\Support\Facades\Crypt;
use App\Notifications\ApprovalUloMail;
use Illuminate\Support\Facades\Notification;


class SignSendMailSkLoPenetapan
{
    private $sk_izin;
    private $gpm;
    public function __construct()
    {
        $this->sk_izin = new SKIzin();
        $this->gpm = new GetPermohonanMailDb();
    }
  
    public function Process()
    {
        try
        {
            $q = sprintf("SELECT p.* FROM p_permohonan p LEFT JOIN p_ulo_sklo ulo_sklo on p.id = ulo_sklo.id_permohonan LEFT JOIN p_sk_ulo_file sk_ulo on sk_ulo.id_ulo_sklo = ulo_sklo.id WHERE p.id_permohonan_status = %d AND sk_ulo.id is NULL AND p.id_izin_jenis != %d", TypePermohonanStatus::Efektif, TypeIzinJenisTel::Penomoran);
            $permohonan = DB::select($q);
            foreach ($permohonan as $p) {
                //generate sk dan sign
                $model_sk = new stdClass();
                $model_sk->id_permohonan = $p->id;
                $model_sk->tanggal = Carbon::now();

                $result_sk = $this->sk_izin->Sklo($model_sk);
                $result_sk_penetapan_komit = $this->sk_izin->SkPenetapan($model_sk);
                if ($result_sk->result && $result_sk_penetapan_komit->result) {
                    $permohonan_for_mail = $this->gpm->GetForEmailById($p->id);
                    $model_send_mail = new stdClass();
                    $model_send_mail->nama_pt =  $permohonan_for_mail->nama_perusahaan;
                    $model_send_mail->no_permohonan = $permohonan_for_mail->no_komitmen;
                    $model_send_mail->no_sk = $permohonan_for_mail->no_sk_izin;
                    $model_send_mail->jenis_izin = $permohonan_for_mail->jenis;
                    $model_send_mail->url_download_sk = sprintf("%s/download_sk/%s", env('BASE_URL_APP_FO'), Crypt::encryptString($p->id));
                    $model_send_mail->list_path = $result_sk->list_path;

                    array_push($model_send_mail->list_path, $result_sk_penetapan_komit);

                    $to = $permohonan_for_mail->email_user;
                    Notification::route('mail', $to)->notify(new ApprovalUloMail($model_send_mail));

                    foreach ($model_send_mail->list_path as $lp) {
                        unlink($lp->file_pdf_path);

                        if($lp->is_sklo)
                        {
                            $this->sk_izin->PostSkloFile($lp);
                        }
                        else
                        {
                            $this->sk_izin->PostSKPenetapanKomit($lp);
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