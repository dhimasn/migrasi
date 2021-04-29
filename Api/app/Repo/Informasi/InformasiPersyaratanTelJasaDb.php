<?php

namespace App\Repo\Informasi;

use Illuminate\Support\Facades\DB;

class InformasiPersyaratanTelJasaDb
{
    public function __construct()
    {

    }

    public function GetRefLayananJasa()
    {        
        $result = DB::table('ref_layanan_jasa')->where('aktif', 1)->get();
        return $result;
    }
}