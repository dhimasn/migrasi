<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class TypeIzinJenisTel extends Enum
{
    const All = 0;
    const Jaringan = 2;
    const Jasa = 3;
    const Khusus = 4;
    const Penomoran = 5;
    const Ulo = 99;
}