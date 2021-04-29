<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Enums\ConstSession;
use App\Enums\TypeLevelJabatan;
use App\Repo\UserDb;

class UserController extends Controller
{
    private $user_db;
    public function __construct()
    {
        $this->user_db = new UserDb();
    }

    public function GetUserStaf(Request $re)
    {
        $result = $this->user_db->GetUserByLevelUnitTeknis(TypeLevelJabatan::Staff, $re->id_unit_teknis);
        return response()->json(['message' => "OK",  'code' => 200, 'result' => $result]);
    }
}
