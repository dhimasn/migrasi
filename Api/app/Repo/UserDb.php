<?php

namespace App\Repo;
use Exception;
use App\Helper\DoubleApo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use stdClass;

class UserDb
{
    private $double;
    public function __construct()
    {
        $this->double = new DoubleApo();
    }

    public function GetUserByLevelUnitTeknis($level, $id_unit_teknis)
    {
        $q = "SELECT u.id, u.email, u.nama, u.user_name, u.tanggal_input, u.tanggal_update, u.tanggal_update_pass, j.id_parent, j.id_unit_teknis, j.level, j.nama_jabatan from m_user as u left join m_jabatan as j on u.id_jabatan = j.id where j.level={$level} and j.id_unit_teknis={$id_unit_teknis}";
        $result = DB::select($q);
        return $result;
    }

    public function GetUserById($id_user)
    {
        $q = "SELECT u.id, u.email, u.nama, u.user_name, u.tanggal_input, u.tanggal_update, u.tanggal_update_pass, j.id_parent as id_parent_jabatan, j.id_unit_teknis, j.level, j.nama_jabatan from m_user as u left join m_jabatan as j on u.id_jabatan = j.id where u.id={$id_user}";
        $result = DB::select($q);
        return $result;
    }

    public function GetUserByIdJabatan($id_jabatan)
    {
        $q = "SELECT mu.*, mj.nama_jabatan from m_user mu left join m_jabatan mj on mu.id_jabatan = mj.id where id_jabatan ={$id_jabatan}";
        $result = DB::select($q);
        return $result;
    }

    public function Login($user_name, $password)
    {
        $model = new stdClass();
        $user_name = $this->double->Process($user_name);
        $q = sprintf("SELECT u.*, j.id as id_jabatan, j.nama_jabatan as jabatan, j.level as level_jabatan, j.id_unit_teknis from m_user u left join m_jabatan j on u.id_jabatan = j.id where u.user_name='%s'", $user_name);
        $users = DB::select($q);
        if($users == null)
        {
            $model->status = false;
            return $model;
        }
        else
        {            
            if (Hash::check($password, $users[0]->sandi))
            {
                $model->status = true;
                $model->id_user = $users[0]->id;
                $model->role = $users[0]->id_user_role;
                $model->email = $users[0]->email;
                $model->nama = $users[0]->nama;
                $model->jabatan = $users[0]->jabatan;
                $model->id_jabatan = $users[0]->id_jabatan;
                $model->level_jabatan = $users[0]->level_jabatan;
                $model->id_unit_teknis = $users[0]->id_unit_teknis;
                return $model;
            }
            else
            {
                $model->status = false;
                return $model;
            }
        }
    }
    
    public function Daftar($input)
    {
        try
        {
            //insert ke user datas
            $q = sprintf("INSERT into m_user(user_name, password, role, nama, no_wa) values('%s', '%s', %d, '%s', '%s')", $input->user_name, $input->password, $input->role, $input->nama, $input->no_wa);
            $a = DB::insert($q);
            return $a;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
}