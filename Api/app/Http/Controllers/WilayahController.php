<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repo\WilayahDb;

class WilayahController extends Controller
{
    private $wdb;
    public function __construct()
    {
        //$this->middleware('auth');
        $this->wdb = new WilayahDb();
    }

    public function GetKabupatenByNama(Request $re)
    {  
        $result = $this->wdb->GetKabupatenByNama($re->nama);
        return response()->json(['message' => "OK", 'code' => 200, 'result' => $result]);
    }
}
