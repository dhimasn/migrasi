<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repo\PermohonanLogDb;

class PermohonanLogController extends Controller
{
    private $ldb;
    public function __construct()
    {
        //$this->middleware('auth');
        $this->ldb = new PermohonanLogDb();
    }

    public function GetByIdPermohonan(Request $re)
    {  
        $result = $this->ldb->Get($re->id_permohonan);
        return response()->json(['message' => "OK", 'code' => 200, 'result' => $result]);
    }
}
