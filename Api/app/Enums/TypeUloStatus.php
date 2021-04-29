<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class TypeUloStatus extends Enum
{
    const Draft = 1;
    const Kirim = 2;
    const Ditolak = 3;
    const Disetujui = 4;
    const Disposisi = 5;
}