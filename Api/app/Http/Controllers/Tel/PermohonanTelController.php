<?php

namespace App\Http\Controllers\Tel;

use stdClass;
use Exception;
use Illuminate\Support\Facades\Crypt;
use App\Enums\ConstLog;
use Illuminate\Http\Request;
use App\Enums\TypeUnitTeknis;
use App\Enums\TypeLevelJabatan;
use App\Enums\TypeDisposisi;
use App\Enums\TypeIzinJenisTel;
use App\Enums\TypeKirimKelengkapanFile;
use App\Enums\TypeEvaluasiUlo;
use App\Repo\Tel\PermohonanTelDb;
use App\Http\Controllers\Controller;
use App\Repo\Tel\PermohonanKomitDb;
use App\Repo\Tel\PermohonanKomitUloDb;
use App\Repo\Tel\PermohonanDisposisiTelDb;
use App\Repo\PermohonanLogDb;
use App\Repo\PermohonanUloCatatanDb;
use App\Repo\PerusahaanDb;
use App\Repo\UserDb;
use App\Notifications\ApprovalPermohonanMail;

class PermohonanTelController extends Controller
{
    private $pdb_tel;
    private $pkommitdb;
    private $pdispo;
    private $pkulo;
    private $plog;
    private $pulocatatan;
    private $udb;
    private $perdb;

    public function __construct()
    {
        //$this->middleware('auth');
        $this->pdb_tel = new PermohonanTelDb();
        $this->pkommitdb = new PermohonanKomitDb();
        $this->pdispo = new PermohonanDisposisiTelDb();
        $this->pkulo = new PermohonanKomitUloDb();
        $this->plog = new PermohonanLogDb();
        $this->pulocatatan = new PermohonanUloCatatanDb();
        $this->udb = new UserDb();
        $this->perdb = new PerusahaanDb();
    }

    public function Post(Request $re)
    {  
        $a  = json_decode($re->getContent());
        $result = $this->pdb_tel->PostPermohonan($a[0]);
        if($result->result)
        {
            return response()->json(['message' => "OK", 'code' => 200]);
        }
        else
        {
            return response()->json(['message' => "Bad Request", 'code' => 400]);;
        }
    }

    public function GetAll(Request $re)
    {
        $result = $this->pdb_tel->GetAll($re->start, $re->length, $re->kolom, $re->order_by, $re->search);
        $total = $this->pdb_tel->GetTotalAll($re->search);
        $model = new stdClass();
        $model->data = $result;
        $model->total = $total;
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $model]);
    }

    public function GetByDisposisi(Request $re)
    {
        $result = $this->pdb_tel->GetByDisposisi($re->id_user, $re->id_permohonan_komit_kelengkapan_status, $re->id_izin_jenis, $re->start, $re->length, $re->kolom, $re->order_by, $re->search);
        $total = $this->pdb_tel->GetTotalByDisposisi($re->id_user, $re->id_permohonan_komit_kelengkapan_status, $re->id_izin_jenis, $re->search);
        $model = new stdClass();
        $model->data = $result;
        $model->total = $total;
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $model]);
    }

    public function GetByDisposisiSend(Request $re)
    {
        $result = $this->pdb_tel->GetByDisposisiSend($re->id_user, $re->id_izin_jenis, $re->start, $re->length, $re->kolom, $re->order_by, $re->search);
        $total = $this->pdb_tel->GetTotalByDisposisiSend($re->id_user, $re->id_izin_jenis, $re->search);
        $model = new stdClass();
        $model->data = $result;
        $model->total = $total;
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $model]);
    }
    
    public function GetDisposisiNoKomit(Request $re)
    {
        $permohonan_result = $this->pdb_tel->GetByDisposisiNoKomit($re->id_user, $re->start, $re->length, $re->kolom, $re->order_by, $re->search);
        $total = $this->pdb_tel->GetTotalByDisposisiNoKomit($re->id_user, $re->search);
        $model = new stdClass();
        $model->data = $permohonan_result;
        $model->total = $total;
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $model]);
    }

    public function GetDisposisiSendNoKomit(Request $re)
    {
        $permohonan_result = $this->pdb_tel->GetByDisposisiSendNoKomit($re->id_user, $re->start, $re->length, $re->kolom, $re->order_by, $re->search);
        $total = $this->pdb_tel->GetTotalByDisposisiSendNoKomit($re->id_user, $re->search);
        $model = new stdClass();
        $model->data = $permohonan_result;
        $model->total = $total;
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $model]);
    }

    public function GetById(Request $re)
    {
        $result = $this->pdb_tel->GetById($re->id_permohonan);
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $result]);
    }

    public function GetByNoSKIzin(Request $re)
    {
        $result = $this->pdb_tel->GetByNoSKIzin($re->no_sk_izin, $re->id_izin_jenis, $re->id_pemohon);
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $result]);
    }

    public function GetValidasiPermohonan(Request $re)
    {
        $result = $this->pdb_tel->GetValidasiPermohonan($re->no_sk_izin, $re->id_izin_jenis, $re->id_pemohon);
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $result]);
    }    

    public function UpdateStatusPermohonanKomit(Request $re)
    {  
        $a  = json_decode($re->getContent());
        $result = $this->pkommitdb->UpdateStatus($a);

        $level_jabatan =  $a->level_jabatan;
        if($level_jabatan == TypeLevelJabatan::Kasubdit)
        {
            $id_unit_teknis =  $a->id_unit_teknis;
            $id_izin_jenis = TypeIzinJenisTel::All;
            if ($id_unit_teknis == TypeUnitTeknis::TelsusKPT) {
                $id_izin_jenis = TypeIzinJenisTel::Ulo;
            }

            $permohonan_dispo =  $this->pdispo->GetByIdPermohonan($a->id_permohonan, $id_izin_jenis)[0];
            $user = $this->udb->GetUserById($permohonan_dispo->id_user)[0];

            if (!$this->pdispo->IsExistDisposisiKirim($a->id_permohonan, $id_izin_jenis, $user->id)) {
                $this->pdispo->InsertDisposisiKirim($id_izin_jenis, $a->id_permohonan, $user->id); //masuk ke table kirim
            }

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

    public function UpdateStatus(Request $re)
    {  
        $a  = json_decode($re->getContent());
        $result = $this->pdb_tel->UpdateStatus($a);
        if($result)
        {
            $id_izin_jenis = TypeIzinJenisTel::All;
            $permohonan_dispo =  $this->pdispo->GetByIdPermohonan($a->id_permohonan, $id_izin_jenis)[0];
            $user = $this->udb->GetUserById($permohonan_dispo->id_user)[0];

            if (!$this->pdispo->IsExistDisposisiKirim($a->id_permohonan, $id_izin_jenis, $user->id)) {
                $this->pdispo->InsertDisposisiKirim($id_izin_jenis, $a->id_permohonan, $user->id); //masuk ke table kirim
            }

             // log disposisi
             
             $input_log = new stdClass();
             $input_log->id_permohonan = $a->id_permohonan;
             $input_log->status = sprintf("%s", ConstLog::approval_no_komit);
             $input_log->nama =  $a->nama;
             $input_log->jabatan = $a->jabatan;
             $input_log->catatan = "";
             $this->plog->Post($input_log);

             // log disposisi untuk ubah status menjadi efektif
             $input_log = new stdClass();
             $input_log->id_permohonan = $a->id_permohonan;
             $input_log->status = sprintf("%s", ConstLog::izin_efektif);
             $input_log->nama =  "";
             $input_log->jabatan = "";
             $input_log->catatan = "";
             $this->plog->Post($input_log);
        }
        else
        {
            return response()->json(['message' => "Bad Request", 'code' => 400]);
        }

        return response()->json(['message' => "OK",  'code' => 200]);
    }
    
    public function GetForEvaluasiUlo(Request $re)
    {
        $result = $this->pkulo->GetEvaluasiUlo($re->id_permohonan, TypeEvaluasiUlo::Evaluator);
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $result]);
    }
    
    public function PostEvaluasiUloProses(Request $request)
    {
        $a  = json_decode($request->getContent());
        $return = $this->pkulo->PostEvaluasiUloProses($a);
        if($return)
        {
            return response()->json(['message' => "OK", 'code' => 200]);
        }
        else
        {
            return response()->json(['message' => "Bad Request", 'code' => 400]);
        }
    }

    public function PostEvaluasiUloUpProses(Request $request)
    {
        $a  = json_decode($request->getContent());
        
        //insert to p_permohonan_disposisi
        $dispo = new stdClass();
        $dispo->type_disposisi = TypeDisposisi::Up;
        $dispo->id_izin_jenis = TypeIzinJenisTel::Ulo;
        $dispo->id_permohonan = $a->id_permohonan;
        $dispo->id_permohonan_komit = $a->id_permohonan_komit;
        $return = $this->pdispo->Proses($dispo);

        if($return)
        {
             // log disposisi
             $input = new stdClass();
             $input->id_permohonan = $a->id_permohonan;
             $input->status = sprintf("%s", ConstLog::approval_ulo_jabatan);
             $input->nama =  $a->nama;
             $input->jabatan = $a->jabatan;
             $input->catatan = $a->catatan;
             $this->plog->Post($input);
             
            return response()->json(['message' => "OK", 'code' => 200]);
        }
        else
        {
            return response()->json(['message' => "Bad Request", 'code' => 400]);;
        }
    }

    public function PostEvaluasiUloKembaliProses(Request $request)
    {
        $a  = json_decode($request->getContent());
        
        //insert to p_permohonan_disposisi
        $dispo = new stdClass();
        $dispo->type_disposisi = TypeDisposisi::Kembali;
        $dispo->id_izin_jenis = TypeIzinJenisTel::Ulo;
        $dispo->id_permohonan = $a->id_permohonan;
        $dispo->id_permohonan_komit = $a->id_permohonan_komit;
        $return = $this->pdispo->Proses($dispo);

        if($return)
        {
             $input = new stdClass();
             $input->id_permohonan = $a->id_permohonan;
             $input->status = sprintf("%s", ConstLog::pengembalian_ulo);
             $input->nama =  $a->nama;
             $input->jabatan = $a->jabatan;
             $input->catatan = $a->catatan;
             $this->plog->Post($input);
             
             return response()->json(['message' => "OK", 'code' => 200]);
         }
         else
         {
             return response()->json(['message' => "Bad Request", 'code' => 400]);;
         }
    }
    
    public function PostUloFileProses(Request $request)
    {
        $a  = json_decode($request->getContent());
        $return = $this->pkulo->PostUloFile($a);
        if($return)
        {
            if($a->type_kirim_kelengkapan_file == TypeKirimKelengkapanFile::Akhir)
            {
                 // log disposisi
                $input = new stdClass();
                $input->id_permohonan = $a->id_permohonan;
                $input->status = sprintf("%s", ConstLog::evaluasi_jabatan);
                $input->nama =  $a->nama_user;
                $input->jabatan = $a->jabatan;
                $input->catatan = $a->catatan;
                $this->plog->Post($input);
            }
            
            return response()->json(['message' => "OK", 'code' => 200]);
        }
        else
        {
            return response()->json(['message' => "Bad Request", 'code' => 400]);;
        }
    }

    public function UpdateStatusUlo(Request $re)
    {  
        $a  = json_decode($re->getContent());
        $result = $this->pkulo->UpdateStatusUlo($a);

        $level_jabatan =  $a->level_jabatan;
        if($level_jabatan == TypeLevelJabatan::Direktur)
        {
            // $id_unit_teknis =  $re->session()->get(ConstSession::id_unit_teknis);
            // $id_izin_jenis = TypeIzinJenisTel::All;
            // if ($id_unit_teknis == TypeUnitTeknis::TelsusKPT) {
                $id_izin_jenis = TypeIzinJenisTel::Ulo;
            //}

            $permohonan_dispo =  $this->pdispo->GetByIdPermohonan($a->id_permohonan, $id_izin_jenis)[0];
            $user = $this->udb->GetUserById($permohonan_dispo->id_user)[0];

            if (!$this->pdispo->IsExistDisposisiKirim($a->id_permohonan, $id_izin_jenis, $user->id)) {
                $this->pdispo->InsertDisposisiKirim($id_izin_jenis, $a->id_permohonan, $user->id); //masuk ke table kirim
            }

             // log disposisi
             $input = new stdClass();
             $input->id_permohonan = $a->id_permohonan;
             $input->status = sprintf("%s", ConstLog::approval_ulo_jabatan);
             $input->nama =  $a->nama;
             $input->jabatan = $a->jabatan;
             $input->catatan = "";
             $this->plog->Post($input);

             // log disposisi untuk ubah status menjadi efektif
             $input_log = new stdClass();
             $input_log->id_permohonan = $a->id_permohonan;
             $input_log->status = sprintf("%s", ConstLog::izin_efektif);
             $input_log->nama =  "";
             $input_log->jabatan = "";
             $input_log->catatan = "";
             $this->plog->Post($input_log);
        }
        
        return response()->json(['message' => "OK", 'code' => 200]);
    }

    public function GetUloFile(Request $request)
    {
        $return = $this->pkulo->GetUloFile($request->id_ulo_file)[0];
        return response()->json(['message' => "OK", 'code' => 200, 'result' => $return]);
    }

    public function GetSKIzin(Request $re)
    {
        try{
            $re->id_sk_izin = Crypt::decryptString($re->id_sk_izin);
            $result = $this->pkommitdb->GetSKIzin($re->id_sk_izin)[0];
            return response()->json(['message' => "OK", 'code' => 200, 'result' => $result]);
        }
        catch(Exception $e)
        {
            return response()->json(['message' => "Bad Request", 'code' => 400]);
        }        
    }
    
    public function GetSKKomit(Request $re)
    {
        try{
            $re->id_sk_komit = Crypt::decryptString($re->id_sk_komit);
            $result = $this->pkommitdb->GetSKKomit($re->id_sk_komit)[0];
            return response()->json(['message' => "OK", 'code' => 200, 'result' => $result]);
        }
        catch(Exception $e)
        {
            return response()->json(['message' => "Bad Request", 'code' => 400]);
        }                
    }

    public function GetSKPenomoran(Request $re)
    {
        try{
            $re->id_sk_penomoran = Crypt::decryptString($re->id_sk_penomoran);
            $result = $this->pkommitdb->GetSKPenomoran($re->id_sk_penomoran)[0];
            return response()->json(['message' => "OK", 'code' => 200, 'result' => $result]);
        }
        catch(Exception $e)
        {
            return response()->json(['message' => "Bad Request", 'code' => 400]);
        }                
    }

    public function GetSKUlo(Request $re)
    {
        try{
            $re->id_sk_ulo = Crypt::decryptString($re->id_sk_ulo);
            $result = $this->pkommitdb->GetSKUlo($re->id_sk_ulo)[0];
            return response()->json(['message' => "OK", 'code' => 200, 'result' => $result]);
        }
        catch(Exception $e)
        {
            return response()->json(['message' => "Bad Request", 'code' => 400]);
        }        
        
    }
}
