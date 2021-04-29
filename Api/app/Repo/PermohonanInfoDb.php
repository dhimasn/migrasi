<?php

namespace App\Repo;

use stdClass;
use Exception;
use App\Enums\ConstLog;
use Illuminate\Support\Carbon;
use App\Repo\Tel\PermohonanTelDb;
use Illuminate\Support\Facades\DB;

class PermohonanInfoDb
{
    public function __construct()
    {
    }

    public function PostPermohonanInfo($input)
    {
        try
        {
            if($this->IsExistPermohonanInfo($input->id_permohonan))
            {
                $q = sprintf("UPDATE p_permohonan_info set value='%s', tanggal_input='%s' where id_permohonan=%d", $input->value, $input->tanggal_input->format('Y-m-d H:i:s'), $input->id_permohonan);

                $a = DB::update($q);
            }
            else
            {
                $q = sprintf("INSERT into p_permohonan_info(id_permohonan, tanggal_input, value) values(%d, '%s', '%s')", $input->id_permohonan, $input->tanggal_input->format('Y-m-d H:i:s'), $input->value);

                $a = DB::insert($q);
            }
            
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    private function IsExistPermohonanInfo($id_permohonan)
    {
        $q = sprintf("SELECT count(id) as jumlah from p_permohonan_info where id_permohonan = %d", $id_permohonan);
        $result = DB::select($q)[0]->jumlah;
        $return = true;
        if($result == 0)
        {
            $return = false;
        }
        return $return;
    }
}