<?php

namespace App\Http\Controllers\Tel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repo\Tel\PermohonanKomitDb;
use Illuminate\Support\Facades\Crypt;
use Exception;

class PermohonanKomitController extends Controller
{
    private $pkdb;
    public function __construct()
    {
        //$this->middleware('auth');
        $this->pkdb = new PermohonanKomitDb();
    }

    public function GetKomitFile(Request $re)
    {
        try{
            $re->id_permohonan_komit_file = Crypt::decryptString($re->id_permohonan_komit_file);
            $result = $this->pkdb->GetKomitFile($re->id_permohonan_komit_file)[0];
            return response()->json(['message' => "OK",  'code' => 200, 'result' => $result]);
        }
        catch(Exception $e)
        {
            return response()->json(['message' => "Bad Request", 'code' => 400, 'result' => null]);
        }
        
    }

    public function GetKKomitKelengkapan(Request $re)
    {  
        $result = $this->pkdb->GetKKomitKelengkapan($re->id_layanan);
        return response()->json(['message' => "OK", 'code' => 200, 'result' => $result]);
    }

    public function PostPermohonanKomit(Request $re)
    {  
        try{
        $a  = json_decode($re->getContent());
        $result = $this->pkdb->PostPermohonanKomit($a[0]);
        if($result->status)
        {
            return response()->json(['message' => "OK", 'code' => 200, 'result' => $result]);
        }
        else
        {
            return response()->json(['message' => "Bad Request", 'code' => 400, 'result' => $result]);
        }
        }
        catch(Exception $e)
        {
            return response()->json(['message' => "Bad Request", 'code' => 400, 'result' => $result]);
        }
        
    }

    public function PostPermohonanKomitKelengkapan(Request $re)
    {  
        $a  = json_decode($re->getContent());
        $result = $this->pkdb->PostPermohonanKomitKelengkapan($a[0]);
        if($result->status)
        {
            return response()->json(['message' => "OK", 'code' => 200, 'result' => $result]);
        }
        else
        {
            return response()->json(['message' => "Bad Request", 'code' => 400, 'result' => $result]);;
        }
    }

    public function PostPermohonanKomitKelengkapanFile(Request $re)
    {  
        $a  = json_decode($re->getContent());
        $result = $this->pkdb->PostPermohonanKomitKelengkapanFile($a[0]);
        if($result)
        {
            return response()->json(['message' => "OK", 'code' => 200]);
        }
        else
        {
            return response()->json(['message' => "Bad Request", 'code' => 400]);;
        }
    }
}
