<?php

namespace App\Http\Controllers\Tel;

use stdClass;
use App\Enums\ConstLog;
use App\Enums\ConstSession;
use App\Enums\TypeDisposisi;
use Illuminate\Http\Request;
use App\Enums\TypeUnitTeknis;
use App\Repo\PermohonanLogDb;
use App\Enums\TypeIzinJenisTel;
use App\Enums\TypeLevelJabatan;
use App\Repo\Tel\PermohonanKomitDb;
use App\Http\Controllers\Controller;
use App\Repo\Tel\PermohonanDisposisiTelDb;
use App\Repo\PerusahaanDb;
use App\Repo\Tel\PenomoranDb;

class PermohonanDisposisiTelController extends Controller
{
    private $dispo_tel_db;
    private $p_komit;
    private $plog;
    private $perdb;
    private $penomoran;
    public function __construct()
    {
        //$this->middleware('auth');
        $this->dispo_tel_db = new PermohonanDisposisiTelDb();
        $this->p_komit = new PermohonanKomitDb();
        $this->plog = new PermohonanLogDb();
        $this->perdb = new PerusahaanDb();
        $this->penomoran = new PenomoranDb();
    }

    public function DisposisiToStaff(Request $re)
    {  
        $a  = json_decode($re->getContent());
        $result = $this->dispo_tel_db->ProsesDisposisStaf($a);
        if($result)
        {
            // log disposisi
            $input = new stdClass();
            $input->id_permohonan = $a->id_permohonan;
            $input->status = sprintf("%s", ConstLog::disposisi_jabatan);
            $input->nama =  $a->nama;
            $input->jabatan = $a->jabatan;
            $input->catatan = "";
            $this->plog->Post($input);
            
            return response()->json(['message' => "OK", 'code' => 200]);
        }
        else
        {
            return response()->json(['message' => "Bad Request", 'code' => 400]);;
        }
    }

    public function DisposisiToUp(Request $re)
    {        
        $a  = json_decode($re->getContent());        
        $id_unit_teknis =  $a->id_unit_teknis; 
        $id_izin_jenis = TypeIzinJenisTel::All;  
        if($id_unit_teknis == TypeUnitTeknis::TelsusKPT)
        {
            $id_izin_jenis = TypeIzinJenisTel::Ulo;
        }      
        $a->id_izin_jenis = $id_izin_jenis;
        $result = $this->dispo_tel_db->Proses($re);
        if($result)
        {
            $resultLengkap = $this->p_komit->UpdateStatusKelengkapan($a->list_kelengkapan_status);
            if($resultLengkap){
                return response()->json(['message' => "OK", 'code' => 200]);
            }else{                
                return response()->json(['message' => "Bad Request", 'code' => 400]);
            }
        }
        else
        {
            return response()->json(['message' => "Bad Request", 'code' => 400]);
        }
    }

    public function Disposisi(Request $re)
    {  
        $a  = json_decode($re->getContent());   
        $id_unit_teknis =  $a->id_unit_teknis; 
        $id_izin_jenis = TypeIzinJenisTel::All;
        if($id_unit_teknis == TypeUnitTeknis::Penomoran)
        {
            $id_izin_jenis = TypeIzinJenisTel::Penomoran;
        }
        $a->id_izin_jenis = $id_izin_jenis;
        $result = $this->dispo_tel_db->Proses($a);
        if($result)
        {
            if($id_izin_jenis == TypeIzinJenisTel::Penomoran)
            {
                $resultLengkap = $this->penomoran->UpdateStatusKelengkapanPenomoran($a->list_kelengkapan_status);
            }
            else
            {
                $resultLengkap = $this->p_komit->UpdateStatusKelengkapan($a->list_kelengkapan_status);
            }
            if($resultLengkap){
                if($a->type_disposisi == TypeDisposisi::Up)
                {
                    // log disposisi
                    $input = new stdClass();
                    $input->id_permohonan = $a->id_permohonan;
                    if ($a->level_jabatan == TypeLevelJabatan::Staff) {
                        $input->status = sprintf("%s", ConstLog::evaluasi_jabatan);
                    } else if ($a->level_jabatan == TypeLevelJabatan::Kasi) {
                        if ($id_izin_jenis == TypeIzinJenisTel::Ulo) {
                            $input->status = sprintf("%s", ConstLog::approval_ulo_jabatan);
                        } else {
                            $input->status = sprintf("%s", ConstLog::approval_jabatan);
                        }
                    }
                    $input->nama =  $a->nama;
                    $input->jabatan = $a->jabatan;
                    $input->catatan = $a->catatan;
                    $this->plog->Post($input);
                }
                else if($a->type_disposisi == TypeDisposisi::Kembali)
                {
                    // log disposisi
                    $input = new stdClass();
                    $input->id_permohonan = $a->id_permohonan;
                    $input->status = sprintf("%s", ConstLog::pengembalian_permohonan);
                    $input->nama =  $a->nama;
                    $input->jabatan = $a->jabatan;
                    $input->catatan = $a->catatan;
                    $this->plog->Post($input);
                }

                return response()->json(['message' => "OK", 'code' => 200]);

            }else{                
                return response()->json(['message' => "Bad Request", 'code' => 400]);
            }
        }
        else
        {
            return response()->json(['message' => "Bad Request", 'code' => 400]);
        }
    }

}