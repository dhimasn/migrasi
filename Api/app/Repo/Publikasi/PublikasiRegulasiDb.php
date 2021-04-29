<?php

namespace App\Repo\Publikasi;

use Illuminate\Support\Facades\DB;

class PublikasiRegulasiDb
{
    public function __construct()
    {

    }

    public function GetRegulasiByJenisRegulasi($jenis_regulasi)
    {        
        $result = DB::table('m_regulasi')->where('jenis_regulasi', $jenis_regulasi)->get();
        return $result;
    }
}