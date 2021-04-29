<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class TypeEvaluasiUloValue extends Enum
{
    const TglUjiPetik = 'Tgl Evaluasi Uji Petik';
    const NoSpt = 'No SPT';
    const TglSpt = 'Tgl SPT';
    const AlamatPusat = 'Alamat Pusat Layanan Pelanggan';
    const AlamatUlo = 'Alamat Pelaksanaan ULO';
    const Hasil = 'Hasil Evaluasi';
    const Mekanisme = 'Mekanisme ULO';
}