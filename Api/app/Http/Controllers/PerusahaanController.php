<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repo\PerusahaanDb;

class PerusahaanController extends Controller
{
    private $pdb;
    public function __construct()
    {
        //$this->middleware('auth');
        $this->pdb = new PerusahaanDb();
    }

    public function GetByIdPemohon(Request $re)
    {  
        $result = $this->pdb->GetByIdPemohon($re->id_pemohon);
        return response()->json(['message' => "OK", 'code' => 200, 'result' => $result]);
    }
}
