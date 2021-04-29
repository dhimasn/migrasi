<?php

namespace App\Repo;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PermohonanUloCatatanDb
{
    public function __construct()
    {
    }

    public function Post($input)
    {
        try
        {
            $tanggal_input  = Carbon::now();
            $q = sprintf("INSERT into p_ulo_catatan(id_permohonan_komit, catatan, tanggal_input) values(%d, '%s', '%s')", $input->id_permohonan_komit, $input->catatan, $tanggal_input->format('Y-m-d H:i:s'));
            $a = DB::insert($q);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
}