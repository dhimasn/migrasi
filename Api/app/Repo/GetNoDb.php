<?php

namespace App\Repo;
use App\Enums\TypeIzin;
use App\Enums\TypeIzinJenisTel;
use App\Helper\GenerateNomor;
use Illuminate\Support\Facades\DB;

class GetNoDb
{
    private $gen_nomor;
    public function __construct()
    {
        $this->gen_nomor = new GenerateNomor();
    }

    public function GetLastPermohomonanKomit()
    {
        $q = "SELECT CONVERT(SUBSTRING(p.no, 10, 5), int) as nomor FROM (SELECT no_penyelenggaraan as no from p_permohonan where YEAR(tanggal_input) = YEAR(NOW()) AND MONTH(tanggal_input) = MONTH(NOW()) UNION ALL SELECT no_komitmen as no from p_permohonan_komit where YEAR(tanggal_input) = YEAR(NOW()) AND MONTH(tanggal_input) = MONTH(NOW())) as p order by CONVERT(SUBSTRING(p.no, 10, 5), int) desc";
        $result = DB::select($q);
        if(count($result) == 0)
        {
            $result = 0;
        }
        else
        {
            $result = $result[0]->nomor;
        }
        return $result;
    }

    public function GetLastIzin($type_izin, $type_izin_jenis)
    {
        $kode = $this->gen_nomor->GenerateKode($type_izin, $type_izin_jenis);
        $q = "SELECT CONVERT(SUBSTRING_INDEX(no_sk_izin, '/', 1), SIGNED) as nomor from p_permohonan where SUBSTRING_INDEX(SUBSTRING_INDEX(no_sk_izin, '/', 2), '/', -1) = '$kode' order by CONVERT(SUBSTRING_INDEX(no_sk_izin, '/', 1), SIGNED) desc";
        $result = DB::select($q);
        if(count($result) == 0)
        {
            $result = 0;
        }
        else
        {
            $result = $result[0]->nomor;
        }
        return $result;
    }

    public function GetLastPenomoran($type_izin, $type_izin_jenis)
    {
        $kode = $this->gen_nomor->GenerateKode($type_izin, $type_izin_jenis);
        $q = "SELECT CONVERT(SUBSTRING_INDEX(no_sk_penomoran, '/', 1), SIGNED) as nomor from p_penomoran_tel_pakai where SUBSTRING_INDEX(SUBSTRING_INDEX(no_sk_penomoran, '/', 2), '/', -1) = '$kode' order by CONVERT(SUBSTRING_INDEX(no_sk_penomoran, '/', 1), SIGNED) desc";
        $result = DB::select($q);
        if(count($result) == 0)
        {
            $result = 0;
        }
        else
        {
            $result = $result[0]->nomor;
        }
        return $result;
    }

    public function GetLastSklo()
    {
        $kode = $this->gen_nomor->GenerateKode(TypeIzin::Telekomunikasi, TypeIzinJenisTel::Ulo);
        $q = "SELECT CONVERT(SUBSTRING_INDEX(no_sklo, '/', 1), SIGNED) as nomor from p_ulo_sklo where SUBSTRING_INDEX(SUBSTRING_INDEX(no_sklo, '/', 2), '/', -1) = '$kode' order by CONVERT(SUBSTRING_INDEX(no_sklo, '/', 1), SIGNED) desc";
        $result = DB::select($q);
        if(count($result) == 0)
        {
            $result = 0;
        }
        else
        {
            $result = $result[0]->nomor;
        }
        return $result;
    }
}