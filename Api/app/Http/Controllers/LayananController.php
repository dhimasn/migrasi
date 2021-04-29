<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repo\LayananDb;

class LayananController extends Controller
{
    private $ldb;
    public function __construct()
    {
        //$this->middleware('auth');
        $this->ldb = new LayananDb();
    }

    public function GetByIdIzinJenis(Request $re)
    {  
        $result = $this->ldb->GetByIdIzinJenis($re->id_izin_jenis);
        return response()->json(['message' => "OK", 'code' => 200, 'result' => $result]);
    }
}
