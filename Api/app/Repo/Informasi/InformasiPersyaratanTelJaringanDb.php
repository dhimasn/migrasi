<?php

namespace App\Repo\Informasi;

use Illuminate\Support\Facades\DB;

class InformasiPersyaratanTelJaringanDb
{
    public function __construct()
    {

    }

    public function GetRefLayananJaringan()
    {        
        $result = DB::table('ref_layanan_jaringan')->where('aktif', 1)->get();
        return $result;
    }
}