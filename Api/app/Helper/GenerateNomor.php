<?php

namespace App\Helper;

use App\Enums\TypeIzin;
use App\Enums\TypeIzinJenisTel;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class GenerateNomor
{
    public function PenyelenggaraanKomit($last_no)
    {
        $now_no = $last_no + 1;
        $now_no_get = sprintf('%05d', $now_no);
        $utc_now = Carbon::now();
        $year = Str::substr((string)$utc_now->year, 2, 2);
        $month = sprintf('%02d', $utc_now->month);
        $day = sprintf('%02d', $utc_now->day);
        $no = "PHN{$year}{$month}{$day}{$now_no_get}";
        return $no;
    }

    public function no_sk_izin($last_no, $type_izin, $type_izin_jenis)
    {
        $now_no = $last_no + 1;
        $kode = $this->GenerateKode($type_izin, $type_izin_jenis);
        $utc_now = Carbon::now();
        $no = "{$now_no}/{$kode}/{$utc_now->year}";
        return $no;
    }

    public function GenerateKode($type_izin, $type_izin_jenis)
    {
        $kode = '';
        if($type_izin == TypeIzin::Pos)
        {
            $kode = "POS.01.01";
        }
        else if($type_izin == TypeIzin::Telekomunikasi)
        {
            $kode = "TEL.";
            if($type_izin_jenis == TypeIzinJenisTel::Jasa)
            {
                $kode .= "02.02";
            }
            else if($type_izin_jenis == TypeIzinJenisTel::Jaringan)
            {
                $kode .= "01.02";
            }
            else if($type_izin_jenis == TypeIzinJenisTel::Khusus)
            {
                $kode .= "03.02";
            }
            else if($type_izin_jenis == TypeIzinJenisTel::Penomoran)
            {
                $kode .= "05.05";
            }
            else
            {
                //ulo
                $kode .= "04.02";
            }
        }
        else
        {
            //cabut sk
            $kode = "TEL.";
            if($type_izin_jenis == TypeIzinJenisTel::Jasa)
            {
                $kode .= "02.04";
            }
            else if($type_izin_jenis == TypeIzinJenisTel::Jaringan)
            {
                $kode .= "01.04";
            }
            else if($type_izin_jenis == TypeIzinJenisTel::Khusus)
            {
                $kode .= "03.04";
            }
        }
        return $kode;
    }
}