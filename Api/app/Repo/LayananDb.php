<?php

namespace App\Repo;
use Illuminate\Support\Facades\DB;

class LayananDb
{
    public function __construct()
    {
    }

    public function GetByIdIzinJenis($id_izin_jenis)
    {
        $q = sprintf("SELECT * FROM k_layanan where id_izin_jenis=%d", $id_izin_jenis) ;
        $result = DB::select($q);
        return $result;
    }
}