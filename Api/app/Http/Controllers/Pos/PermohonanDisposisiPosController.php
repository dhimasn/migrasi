<?php

namespace App\Http\Controllers\Pos;

use stdClass;
use App\Enums\ConstLog;
use App\Enums\ConstSession;
use App\Enums\TypeDisposisi;
use Illuminate\Http\Request;
use App\Enums\TypeUnitTeknis;
use App\Repo\PermohonanLogDb;
use App\Enums\TypeIzinJenisPos;
use App\Enums\TypeLevelJabatan;
use App\Repo\Pos\PermohonanKomitDb;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Repo\Pos\PermohonanDisposisiPosDb;
use App\Repo\PerusahaanDb;
use Illuminate\Support\Facades\DB;

class PermohonanDisposisiPosController extends Controller
{
    private $dispo_pos_db;
    private $p_komit;
    private $plog;
    private $perdb;
    public function __construct()
    {
        $this->middleware('auth');
        $this->dispo_pos_db = new PermohonanDisposisiPosDb();
        $this->p_komit = new PermohonanKomitDb();
        $this->plog = new PermohonanLogDb();
        $this->perdb = new PerusahaanDb();
    }

    public function DisposisiToStaff(Request $re)
    { 
        $a  = json_decode($re->getContent());
        $result = $this->dispo_pos_db->ProsesDisposisStaf($a, $a->id_unit_teknis, $a->id_izin_jenis);
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
            return response()->json(['message' => "Bad Request", 'code' => 400]);
        }
    }
    
    public function Disposisi(Request $re)
    {  
        $a  = json_decode($re->getContent());
        $result = $this->dispo_pos_db->Proses($a);
        if($result)
        {
            $result_lengkap = $this->p_komit->UpdateStatusKelengkapan($a->list_kelengkapan_status,$a->id_permohonan);
            if($result_lengkap){
                if($a->type_disposisi == TypeDisposisi::Up)
                {
                    // log disposisi
                    $input = new stdClass();
                    $input->id_permohonan = $a->id_permohonan;
                    if ($a->level_jabatan == TypeLevelJabatan::Staff) {
                        $input->status = sprintf("%s", ConstLog::evaluasi_jabatan);
                    } else if ($a->level_jabatan == TypeLevelJabatan::Kasi) {
                        $input->status = sprintf("%s", ConstLog::disposisi_jabatan);
                    } else if ($a->level_jabatan == TypeLevelJabatan::Kasubdit) {
                        $input->status = sprintf("%s", ConstLog::disposisi_jabatan);
                    } else if ($a->level_jabatan == TypeLevelJabatan::Direktur) {
                        $input->status = sprintf("%s", ConstLog::izin_efektif);
                    }
                    $input->nama =  $a->nama;
                    $input->jabatan = $a->jabatan;
                    $input->catatan = $a->catatan;
                    $this->plog->Post($input);
                }
                else if($a->type_disposisi == TypeDisposisi::Kembali)
                {                
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
        }else{                
            return response()->json(['message' => "Bad Request", 'code' => 400]);
        }
    
    }
}
