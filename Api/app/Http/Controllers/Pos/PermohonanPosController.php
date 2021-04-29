<?php

namespace App\Http\Controllers\Pos;

use stdClass;
use App\Enums\ConstLog;
use App\Enums\TypeUnitTeknis;
use App\Enums\TypeLevelJabatan;
use App\Enums\TypePermohonanStatus;
use Illuminate\Http\Request;
use App\Repo\UserDb;
use App\Repo\Pos\PermohonanPosDb;
use App\Repo\Pos\PermohonanKomitDb;
use App\Repo\Pos\PermohonanDisposisiPosDb;
use App\Repo\PermohonanLogDb;
use App\Repo\PermohonanInfoDb;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Exception;
use App\GeneratePdf\SKIzinPos;
use App\Notifications\SKPermohonanPosMail;
use Illuminate\Support\Facades\Notification;
use App\Repo\GetPermohonanMailDb;

class PermohonanPosController extends Controller
{
    private $pposdb;
    private $pdispo;
    private $pinfo;
    private $plog;
    private $pkommitdb;
    private $udb;
    private $gpm;
    private $skpos;

    public function __construct()
    {
        $this->middleware('auth');
        $this->pposdb = new PermohonanPosDb();  
        $this->pinfo = new PermohonanInfoDb();  
        $this->pkommitdb = new PermohonanKomitDb();   
        $this->pdispo = new PermohonanDisposisiPosDb(); 
        $this->plog = new PermohonanLogDb();
        $this->udb = new UserDb();
        $this->skpos = new SKIzinPos();
        $this->gpm = new GetPermohonanMailDb();
    }
    
    public function GetAll(Request $re)
    {
        $result = $this->pposdb->GetAll($re->start, $re->length, $re->kolom, $re->order_by, $re->search);
        $total = $this->pposdb->GetTotalAll($re->search);
        $model = new stdClass();
        $model->data = $result;
        $model->total = $total;
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $model]);
    }

    public function GetAllPembayaran(Request $re)
    {
        $result = $this->pposdb->GetAllPembayaran($re->start, $re->length, $re->kolom, $re->order_by, $re->search);
        $total = $this->pposdb->GetTotalAllPembayaran($re->search);
        $model = new stdClass();
        $model->data = $result;
        $model->total = $total;
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $model]);
    }

    public function GetPembayaranById(Request $re)
    {  
        $result = $this->pposdb->GetDetailPermohonan($re->id_permohonan)[0];
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $result]);
    }

    public function GetBuktiBayarFile(Request $re)
    {  
        $result = $this->pposdb->GetBuktiBayarFile($re->id_permohonan)[0];
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $result]);
    }

    public function GetById(Request $re)
    {  
        $result = $this->pposdb->GetById($re->id_permohonan);
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $result]);
    }

    public function ValidasiBuktiBayar(Request $re)
    {
        $a  = json_decode($re->getContent());
        $result = $this->pposdb->ValidasiBuktiBayar($a->status_bayar, $a->id_permohonan);

        if($result){
            $per_info = new stdClass();
            $per_info->id_permohonan = $a->id_permohonan;
            $per_info->tanggal_input = Carbon::now();
            $status_info = '';
            if($a->status_bayar == '1'){
                $status_info = ConstLog::i_input_spm_diterima;
            }elseif($a->status_bayar== '2'){
                $status_info = ConstLog::i_input_spm_ditolak;
            }else{
                echo '';
            }
            $per_info->value = $status_info;
            $this->pinfo->PostPermohonanInfo($per_info);
        }
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $result]);
    }

    public function GetDisposisi(Request $re)
    {
        $result = $this->pposdb->GetByDisposisi($re->id_user, $re->id_permohonan_komit_kelengkapan_status, $re->id_izin_jenis, $re->start, $re->length, $re->kolom, $re->order_by, $re->search);
        $total = $this->pposdb->GetTotalByDisposisi($re->id_user, $re->id_permohonan_komit_kelengkapan_status, $re->id_izin_jenis, $re->search);
        $model = new stdClass();
        $model->data = $result;
        $model->total = $total;
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $model]);        
    }

    public function GetDisposisiSend(Request $re)
    {
        $result = $this->pposdb->GetByDisposisiSend($re->id_user, $re->id_izin_jenis, $re->start, $re->length, $re->kolom, $re->order_by, $re->search);
        $total = $this->pposdb->GetTotalByDisposisiSend($re->id_user, $re->id_izin_jenis, $re->search);
        $model = new stdClass();
        $model->data = $result;
        $model->total = $total;
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $model]);        
    }

    public function UpdateStatusPermohonanKomit(Request $re)
    {  
        $a  = json_decode($re->getContent());
        $result = $this->pkommitdb->UpdateStatus($a);

        $level_jabatan =  $a->level_jabatan;
        $id_izin_jenis = $a->id_izin_jenis;
        if($level_jabatan == TypeLevelJabatan::Direktur)
        {            
            $permohonan_dispo =  $this->pdispo->GetByIdPermohonan($a->id_permohonan, $id_izin_jenis)[0];
            $user = $this->udb->GetUserById($permohonan_dispo->id_user)[0];

            if (!$this->pdispo->IsExistDisposisiKirim($a->id_permohonan, $id_izin_jenis, $user->id)) {
                $this->pdispo->InsertDisposisiKirim($id_izin_jenis, $a->id_permohonan, $user->id); //masuk ke table kirim
            }
            
            $data_update_status = new stdClass();
            $data_update_status->id_permohonan = $a->id_permohonan;
            $data_update_status->id_permohonan_status = TypePermohonanStatus::Efektif;
            $hasil_update_status = $this->pposdb->UpdateStatus($data_update_status);
            // log disposisi
            $input_log = new stdClass();
            $input_log->id_permohonan = $a->id_permohonan;
            $input_log->status = sprintf("%s", ConstLog::approval_jabatan);
            $input_log->nama =  $a->nama;
            $input_log->jabatan = $a->jabatan;
            $input_log->catatan = "";
            $this->plog->Post($input_log);
            
            // log disposisi untuk ubah status menjadi efektif
            $input_log = new stdClass();
            $input_log->id_permohonan = $a->id_permohonan;
            $input_log->status = 'Komitmen Akhir Pos';
            $input_log->nama =  "";
            $input_log->jabatan = "";
            $input_log->catatan = "";
            $this->plog->Post($input_log);

            $permohonan_for_mail = $this->gpm->GetForEmailById($a->id_permohonan);
            $expired_date = date('Y-m-d', strtotime('+10 day', strtotime($permohonan_for_mail->tgl_permohonan)));
            $model_send_mail = new stdClass();
            $model_send_mail->no_sk =  $permohonan_for_mail->no_sk_izin;
            $model_send_mail->nib =  $permohonan_for_mail->nib;
            $model_send_mail->id_permohonan =  $a->id_permohonan;
            $model_send_mail->id_izin_jenis =  $id_izin_jenis;
            $model_send_mail->nama_pt =  $permohonan_for_mail->nama_perusahaan;
            $model_send_mail->npwp =  $permohonan_for_mail->npwp;
            $model_send_mail->no_penyelenggaraan = $permohonan_for_mail->no_penyelenggaraan;
            $model_send_mail->expired_date = Carbon::parse($expired_date)->translatedFormat('d F Y');
            $model_send_mail->tanggal_input = Carbon::parse($permohonan_for_mail->tgl_permohonan)->translatedFormat('d F Y');
            $model_send_mail->jenis_izin = $permohonan_for_mail->jenis;
            $model_send_mail->no_telp_perusahaan = $permohonan_for_mail->no_telp_perusahaan;
            $model_send_mail->alamat_perusahaan = $permohonan_for_mail->alamat;
            $to = $permohonan_for_mail->email_perusahaan;

            $get_att1 = $this->skpos->GenerateSK($model_send_mail);
            $tampung_list_path = array();
            $tampung_list_path[] = $get_att1->list_path;
            $model_send_mail->list_path = $tampung_list_path;
            Notification::route('mail', $to)->notify(new SKPermohonanPosMail($model_send_mail));
            unlink($get_att1->list_path);
             // log disposisi
             $input_log = new stdClass();
             $input_log->id_permohonan = $a->id_permohonan;
             $input_log->status = sprintf("%s", ConstLog::approval_jabatan);
             $input_log->nama =  $a->nama;
             $input_log->jabatan = $a->jabatan;
             $input_log->catatan = "";
             $this->plog->Post($input_log);
        }
        
        return response()->json(['message' => "OK",  'code' => 200]);
    }

    public function GetKomitFile(Request $re)
    {
        try{
            $re->id_permohonan_komit_file = Crypt::decryptString($re->id_permohonan_komit_file);
            $result = $this->pkommitdb->GetKomitFile($re->id_permohonan_komit_file)[0];
            return response()->json(['message' => "OK",  'code' => 200, 'result' => $result]);
        }
        catch(Exception $e)
        {
            return response()->json(['message' => "Bad Request", 'code' => 400, 'result' => null]);
        }
        
    }

}