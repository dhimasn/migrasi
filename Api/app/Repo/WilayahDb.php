<?php

namespace App\Repo;
use Illuminate\Support\Facades\DB;

class WilayahDb
{
    public function __construct()
    {
    }

    public function GetProvinsi()
    {
        $q = sprintf("SELECT a.* FROM provinsi a") ;
        $result = DB::select($q);
        return $result;
    }
    public function GetKabupatenByIdProvinsi($id)
    {
        $q = sprintf("SELECT a.* FROM kabupaten a where a.Id_provinsi like '%%%s%%'", $id) ;
        $result = DB::select($q);
        return $result;
    }
    public function GetKabupatenByNama($nama)
    {
        $q = sprintf("SELECT Kabupaten as id, Kabupaten as text FROM kabupaten where Kabupaten like '%%%s%%'", $nama) ;
        $result = DB::select($q);
        return $result;
    }
}