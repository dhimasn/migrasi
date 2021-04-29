<?php

namespace App\Http\Controllers\Migrasi;

use App\Enums\TypeIzinJenisTel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repo\PermohonanInfoDb;
use App\Repo\Migrasi\SipppdihatiOldDb;
use App\Repo\Migrasi\SipppdihatiNewDb;
use App\Repo\SyaratKomitmenJasaDb;
use App\Repo\Tel\PermohonanDisposisiTelDb;
use Exception;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;
use stdClass;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class MigrasiPerusahaanController extends Controller
{
    private $pdbOld;
    private $pdbNew;

    public function __construct()
    {
        #base_url
        $this->pdbOld = new SipppdihatiOldDb();
        $this->pdbNew = new SipppdihatiNewDb();
    }

    public function findPerusahaan($data)
    {   
        $m_perusahaan_izin_jenis = $this->pdbNew->GetTablePerusahaanIzinJenis($data->id_data_nib);
        if(!empty($m_perusahaan_izin_jenis)){   
            $m_perusahaan = $this->pdbNew->GetTablePerusahaan($m_perusahaan_izin_jenis->id_perusahaan); 
            if(!empty($m_perusahaan)){
                $data->id_perusahaan = $m_perusahaan->id;
            }else{
                $t_pemohon = $this->pdbOld->GetTablePemohon($data->id_pemohon);
                if(!empty($t_pemohon)){
                    $m_perusahaan = $this->pdbNew->GetTablePerusahaanByname($t_pemohon->nama_perusahaan);
                    if(!empty($m_perusahaan)){
                        $data->id_perusahaan = $m_perusahaan->id;
                    }
                }
            }
        }else{
            $t_pemohon = $this->pdbOld->GetTablePemohon($data->id_pemohon);
            if(!empty($t_pemohon)){
                $m_perusahaan = $this->pdbNew->GetTablePerusahaanByname($t_pemohon->nama_perusahaan);
                if(!empty($m_perusahaan)){
                    $data->id_perusahaan = $m_perusahaan->id;
                }
            }
        }
    }    
}
