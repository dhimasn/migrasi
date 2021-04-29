<?php

namespace App\Repo\Pos;

use Exception;
use App\Enums\TypeNomorPenomoran;
use Illuminate\Support\Facades\DB;

class PenomoranDb
{
    public function __construct()
    {
        
    }

    public function GetByIdLayanan($id_layanan)
    {
        $q = sprintf("SELECT * from m_penomoran_pos where id_layanan = %d", $id_layanan);
        $result = DB::select($q);
        return $result;
    }

    public function GetListNomorTidakAktif($id_penomoran_pos, $nomor)
    {
        $q = sprintf("SELECT id, nomor as text from m_penomoran_pos_list where id_penomoran_pos = %d and id_penomoran_status = %d and nomor like '%%%s%%'", $id_penomoran_pos, TypeNomorPenomoran::TidakAktif, $nomor);
        $result = DB::select($q);
        return $result;
    }

    public function PostPenomoranPakai($input)
    {
        try
        {
            $q = sprintf("INSERT into p_penomoran_pos_pakai(id_penomoran_pos_list, id_perusahaan, id_penomoran_status, no_sk_penomoran) values(%d, %d, %d, '%s')", $input->id_penomoran_pos_list, $input->id_perusahaan,TypeNomorPenomoran::Aktif, $input->no_sk_penomoran);

            $a = DB::insert($q);

            $q = sprintf("UPDATE m_penomoran_pos_list set id_penomoran_status=%d where id=%d", TypeNomorPenomoran::Aktif, $input->id_penomoran_pos_list);
            
            $a = DB::update($q);

            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
}