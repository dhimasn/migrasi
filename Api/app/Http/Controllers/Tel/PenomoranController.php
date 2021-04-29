<?php

namespace App\Http\Controllers\Tel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repo\Tel\PenomoranDb;

class PenomoranController extends Controller
{
    private $pdb;
    public function __construct()
    {
        //$this->middleware('auth');
        $this->pdb = new PenomoranDb();
    }

    public function GetByIdLayanan(Request $re)
    {  
        $result = $this->pdb->GetByIdLayanan($re->id_layanan);
        return response()->json(['message' => "OK", 'code' => 200, 'result' => $result]);
    }

    public function GetListNomorTidakAktif(Request $re)
    {  
        $result = $this->pdb->GetListNomorTidakAktif($re->id_penomoran_tel, $re->nomor);
        return response()->json(['message' => "OK", 'code' => 200, 'result' => $result]);
    }
}
