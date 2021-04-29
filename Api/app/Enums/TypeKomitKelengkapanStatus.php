<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class TypeKomitKelengkapanStatus extends Enum
{
    const Draft = 1;
    const Kirim = 2;
    const Ditolak = 3;
    const Disetujui = 4;
}