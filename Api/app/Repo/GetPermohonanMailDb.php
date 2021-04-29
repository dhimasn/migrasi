<?php

namespace App\Repo;

use stdClass;
use Exception;
use App\Enums\ConstLog;
use Illuminate\Support\Carbon;
use App\Repo\Tel\PermohonanTelDb;
use Illuminate\Support\Facades\DB;

class GetPermohonanMailDb
{
    public function __construct()
    {
    }

    public function GetForEmailById($id_permohonan)
    {
        $q = sprintf("SELECT *, mp.telp AS no_telp_perusahaan, mp.email AS email_perusahaan, mp.nama as nama_perusahaan, p.tanggal_input as tgl_permohonan from p_permohonan p left join p_permohonan_komit pk on p.id = pk.id_permohonan left join m_perusahaan mp on mp.id = p.id_perusahaan left join m_pemohon mpe on mpe.id = mp.id_pemohon LEFT JOIN m_user_fo muf on muf.id = mpe.id_user_fo LEFT JOIN k_izin_jenis kij on kij.id = p.id_izin_jenis where p.id = %d", $id_permohonan);
        $result = DB::select($q)[0];
        return $result;
    }
}