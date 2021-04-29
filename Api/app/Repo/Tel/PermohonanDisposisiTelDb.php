<?php

namespace App\Repo\Tel;

use stdClass;
use Exception;
use App\Repo\UserDb;
use App\Enums\TypeDisposisi;
use App\Enums\TypeIzinJenisPos;
use App\Enums\TypeUloStatus;
use App\Enums\TypeUnitTeknis;
use Illuminate\Support\Carbon;
use App\Enums\TypeIzinJenisTel;
use App\Enums\TypeLevelJabatan;
use App\Enums\TypePermohonanStatus;
use App\Repo\GetPermohonanMailDb;
use Illuminate\Support\Facades\DB;
use App\Notifications\DisposisiMail;
use App\Notifications\PerbaikanMail;
use App\Notifications\UpProcessMail;
use App\Notifications\PushNotification;
use Illuminate\Support\Facades\Notification;

class PermohonanDisposisiTelDb
{
    private $udb;
    private $gpm;
    private $fcm;
    public function __construct()
    {
        $this->fcm = new PushNotification();
        $this->udb = new UserDb();
        $this->gpm = new GetPermohonanMailDb();
        Carbon::setLocale('id');
    }

    public function Proses($input)
    {
        try
        {
            //jika posting komitmen pertama kali oleh pemohon
            if($input->type_disposisi == TypeDisposisi::Post)
            {
                $catatan = 'Penyampaian Pemenuhan Komitmen ';

                $user = new stdClass();
                $table_disposisi = "p_permohonan_disposisi";
                if ($input->id_izin_jenis == TypeIzinJenisTel::Jasa) 
                {
                    //get Kasubdit
                    $user = $this->udb->GetUserByLevelUnitTeknis(TypeLevelJabatan::Kasubdit, TypeUnitTeknis::Jasa)[0];    
                }
                else if($input->id_izin_jenis == TypeIzinJenisTel::Ulo)
                {
                    $catatan = 'Penyampaian Pemenuhan Komitmen Ulo ';

                    //get Kasubdit
                    $user = $this->udb->GetUserByLevelUnitTeknis(TypeLevelJabatan::Kasubdit, TypeUnitTeknis::TelsusKPT)[0];
                    $table_disposisi = "p_permohonan_disposisi_ulo";
                }
                else if($input->id_izin_jenis == TypeIzinJenisTel::Jaringan)
                {
                    //get Kasubdit
                    $user = $this->udb->GetUserByLevelUnitTeknis(TypeLevelJabatan::Kasubdit, TypeUnitTeknis::Jaringan)[0];
                }
                else if($input->id_izin_jenis == TypeIzinJenisTel::Penomoran)
                {
                    //get Kasubdit
                    $user = $this->udb->GetUserByLevelUnitTeknis(TypeLevelJabatan::Kasubdit, TypeUnitTeknis::Penomoran)[0];
                }
                else if($input->id_izin_jenis == TypeIzinJenisTel::Khusus)
                {
                    //get Kasubdit
                    $user = $this->udb->GetUserByLevelUnitTeknis(TypeLevelJabatan::Kasubdit, TypeUnitTeknis::TelsusKPT)[0];
                }

                if(!$this->IsExistDisposisi($input->id_permohonan, $input->id_izin_jenis))
                {
                    $q = sprintf("INSERT into %s(id_permohonan, id_user) values(%d, %d)", $table_disposisi, $input->id_permohonan, $user->id);
                    $a = DB::insert($q);
                }

                //send mail
                $permohonan_for_mail = $this->gpm->GetForEmailById($input->id_permohonan);
                $model_send_mail = new stdClass();
                $model_send_mail->nama =  $user->nama;
                $model_send_mail->jabatan =  $user->nama_jabatan;
                $model_send_mail->nama_pt =  $permohonan_for_mail->nama_perusahaan;
                $model_send_mail->no_permohonan = $permohonan_for_mail->no_komitmen;
                $model_send_mail->tanggal_input = Carbon::parse($permohonan_for_mail->tgl_permohonan)->translatedFormat('d F Y');
                $model_send_mail->jenis_permohonan = $catatan.$permohonan_for_mail->jenis;
                $to = $user->email;
                Notification::route('mail', $to)->notify(new DisposisiMail($model_send_mail));
                
                //Send Notif Android
                $this->fcm->sendPushNotification($user->id, "Pemberitahuan Status Permohonan (Disposisi)", $catatan.$permohonan_for_mail->jenis);
            }
            else if($input->type_disposisi == TypeDisposisi::Up)
            {
                $permohonan_dispo =  $this->GetByIdPermohonan($input->id_permohonan, $input->id_izin_jenis)[0];
                $user = $this->udb->GetUserById($permohonan_dispo->id_user)[0];
                $user_parent = $this->udb->GetUserByIdJabatan($user->id_parent_jabatan)[0];
                
                if(!$this->IsExistDisposisiKirim($input->id_permohonan, $input->id_izin_jenis, $user->id))
                {
                    $this->InsertDisposisiKirim($input->id_izin_jenis, $input->id_permohonan, $user->id);//masuk ke table kirim
                }

                $catatan = 'Penyampaian Pemenuhan Komitmen ';
                
                $table_disposisi = "p_permohonan_disposisi";
                if($input->id_izin_jenis == TypeIzinJenisTel::Ulo)
                {
                    $catatan = 'Penyampaian Pemenuhan Komitmen Ulo ';

                    $table_disposisi = "p_permohonan_disposisi_ulo";
                    $this->UpdateStatusPUlo($input->id_permohonan_komit, TypeUloStatus::Kirim);
                }
                $q = sprintf("UPDATE %s set id_user=%d where id_permohonan=%d", $table_disposisi, $user_parent->id, $input->id_permohonan);
                $a = DB::update($q);

                $this->DeleteDisposisiKirim($input->id_izin_jenis, $input->id_permohonan, $user_parent->id);//delete disposisi dari table kirim untuk tujuannya

                //send mail
                $permohonan_for_mail = $this->gpm->GetForEmailById($input->id_permohonan);
                $model_send_mail = new stdClass();
                $model_send_mail->nama =  $user_parent->nama;
                $model_send_mail->jabatan =  $user_parent->nama_jabatan;
                $model_send_mail->nama_pt =  $permohonan_for_mail->nama_perusahaan;
                $model_send_mail->no_permohonan = $permohonan_for_mail->no_komitmen;
                $model_send_mail->tanggal_input = Carbon::parse($permohonan_for_mail->tgl_permohonan)->translatedFormat('d F Y');
                $model_send_mail->jenis_permohonan = $catatan.$permohonan_for_mail->jenis;
                $to = $user_parent->email;
                Notification::route('mail', $to)->notify(new UpProcessMail($model_send_mail));
                
                //send notif android
                $this->fcm->sendPushNotification($user->id, "Pemberitahuan Status Permohonan (Evaluasi)", $catatan.$permohonan_for_mail->jenis);
            }
            else if($input->type_disposisi == TypeDisposisi::Edit)
            {
                //disposisi dari edit permohonan oleh pemohon
                $permohonan_dispo_staf =  $this->GetStafByIdPermohonan($input->id_permohonan, $input->id_izin_jenis)[0];
                
                $catatan = 'Penyampaian Pemenuhan Komitmen ';

                $table_disposisi = "p_permohonan_disposisi";
                if($input->id_izin_jenis == TypeIzinJenisTel::Ulo)
                {
                    $catatan = 'Penyampaian Pemenuhan Komitmen Ulo ';

                    $table_disposisi = "p_permohonan_disposisi_ulo";
                }
                
                $q = sprintf("UPDATE %s set id_user=%d where id_permohonan=%d", $table_disposisi, $permohonan_dispo_staf->id_user, $input->id_permohonan);
                $a = DB::update($q);

                $this->DeleteDisposisiKirim($input->id_izin_jenis, $input->id_permohonan, $permohonan_dispo_staf->id_user);//delete disposisi dari table kirim untuk tujuannya

                //send mail
                $user = $this->udb->GetUserById($permohonan_dispo_staf->id_user)[0];
                $permohonan_for_mail = $this->gpm->GetForEmailById($input->id_permohonan);
                $model_send_mail = new stdClass();
                $model_send_mail->nama =  $user->nama;
                $model_send_mail->jabatan =  $user->nama_jabatan;
                $model_send_mail->nama_pt =  $permohonan_for_mail->nama_perusahaan;
                $model_send_mail->no_permohonan = $permohonan_for_mail->no_komitmen;
                $model_send_mail->tanggal_input = Carbon::parse($permohonan_for_mail->tgl_permohonan)->translatedFormat('d F Y');
                $model_send_mail->jenis_permohonan = $catatan.$permohonan_for_mail->jenis;
                $to = $user->email;
                Notification::route('mail', $to)->notify(new PerbaikanMail($model_send_mail));
                
                //send notif android
                $this->fcm->sendPushNotification($user->id, "Pemberitahuan Status Permohonan (Perbaikan)", $catatan.$permohonan_for_mail->jenis);
            }
            else
            {
                //dikembalikan ke pemohon
                if($input->id_izin_jenis == TypeIzinJenisTel::Ulo)
                {
                    $this->UpdateStatusPUlo($input->id_permohonan_komit, TypeUloStatus::Ditolak);
                }
                else if($input->id_izin_jenis == TypeIzinJenisTel::Penomoran)
                {
                    $q = sprintf("UPDATE p_permohonan set id_permohonan_status = %d where id = %d", TypePermohonanStatus::Dicabut, $input->id_permohonan);
                    $a = DB::update($q);
                }
                else
                {
                    $q = sprintf("UPDATE p_permohonan_komit set id_permohonan_komit_kelengkapan_status = %d where id = %d",$input->id_permohonan_komit_kelengkapan_status, $input->id_permohonan_komit);
                    $a = DB::update($q);
                }

                $permohonan_dispo =  $this->GetByIdPermohonan($input->id_permohonan, $input->id_izin_jenis)[0];
                $user = $this->udb->GetUserById($permohonan_dispo->id_user)[0];
                if(!$this->IsExistDisposisiKirim($input->id_permohonan, $input->id_izin_jenis,  $user->id))
                {
                    $this->InsertDisposisiKirim($input->id_izin_jenis, $input->id_permohonan, $user->id);//masuk ke table kirim
                }
            }
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function UpdateStatusPUlo($id_permohonan_komit, $id_ulo_status)
    {
        $q = sprintf("UPDATE p_ulo set id_ulo_status = %d where id_permohonan_komit = %d",$id_ulo_status, $id_permohonan_komit);
        $a = DB::update($q);
        return true;
    }

    public function InsertDisposisiKirim($id_izin_jenis, $id_permohonan, $id_user)
    {
        $table_disposisi = "p_permohonan_disposisi_kirim";
        if($id_izin_jenis == TypeIzinJenisTel::Ulo)
        {
            $table_disposisi = "p_permohonan_disposisi_ulo_kirim";
        }
        $q = sprintf("INSERT into %s(id_permohonan, id_user) values(%d, %d)",$table_disposisi, $id_permohonan, $id_user);
        $result = DB::insert($q);
    }

    public function DeleteDisposisiKirim($id_izin_jenis, $id_permohonan, $id_user)
    {
        $table_disposisi = "p_permohonan_disposisi_kirim";
        if($id_izin_jenis == TypeIzinJenisTel::Ulo)
        {
            $table_disposisi = "p_permohonan_disposisi_ulo_kirim";
        }
        $q = sprintf("DELETE from %s where id_permohonan = %d and id_user = %d",$table_disposisi, $id_permohonan, $id_user);
        $result = DB::delete($q);
    }

    public function ProsesDisposisStaf($input)
    {
        try
        {
            $catatan = 'Penyampaian Pemenuhan Komitmen ';

            $table_disposisi_staf = "p_permohonan_disposisi_staf";
            $table_disposisi = "p_permohonan_disposisi";
            $id_izin_jenis = $input->id_izin_jenis;
            if($id_izin_jenis == TypeIzinJenisTel::Ulo)
            {
                $catatan = 'Penyampaian Pemenuhan Komitmen ULO ';

                $table_disposisi_staf = "p_permohonan_disposisi_staf_ulo";
                $table_disposisi = "p_permohonan_disposisi_ulo";
                //$id_izin_jenis = TypeIzinJenisTel::Ulo;
                // if($this->pkomitulodb->IsAnyUjiMandiri($input->id_permohonan))
                // {
                //     //if ada uji mandiri
                // }

                //udpate status to p_ulo
                
                $q = sprintf("SELECT * from p_permohonan_komit where id_permohonan=%d", $input->id_permohonan);
                $permohonan_komit = DB::select($q)[0];

                $q = sprintf("UPDATE p_ulo SET id_ulo_status = %d where id_permohonan_komit=%d", TypeUloStatus::Disposisi, $permohonan_komit->id);
                $a = DB::update($q);
                
            }
            if(!$this->IsExistDisposisiStaf($input->id_permohonan, $id_izin_jenis))
            {
                // insert ke table permohonan disposisi staf
                $q = sprintf("INSERT into %s(id_permohonan, id_user) values(%d, %d)", $table_disposisi_staf, $input->id_permohonan, $input->id_user);
                $a = DB::insert($q);
            }
            
            $permohonan_dispo =  $this->GetByIdPermohonan($input->id_permohonan, $id_izin_jenis)[0];
            $user = $this->udb->GetUserById($permohonan_dispo->id_user)[0];
            if(!$this->IsExistDisposisiKirim($input->id_permohonan, $id_izin_jenis, $user->id))
            {
                //insert ke tabel kirim     
                $this->InsertDisposisiKirim($id_izin_jenis, $input->id_permohonan, $user->id);//masuk ke table kirim
            }
            
            //update ke table permohonan disposisi 
            $q = sprintf("UPDATE %s set id_user=%d where id_permohonan=%d", $table_disposisi, $input->id_user, $input->id_permohonan);
            $a = DB::update($q);

            //send mail
            $user_staf = $this->udb->GetUserById($input->id_user)[0];
            $permohonan_for_mail = $this->gpm->GetForEmailById($input->id_permohonan);
            $model_send_mail = new stdClass();
            $model_send_mail->nama =  $user_staf->nama;
            $model_send_mail->jabatan =  $user_staf->nama_jabatan;
            $model_send_mail->nama_pt =  $permohonan_for_mail->nama_perusahaan;
            $model_send_mail->no_permohonan = $permohonan_for_mail->no_komitmen;
            $model_send_mail->tanggal_input = Carbon::parse($permohonan_for_mail->tgl_permohonan)->translatedFormat('d F Y');
            $model_send_mail->jenis_permohonan = $catatan. $permohonan_for_mail->jenis;
            $to = $user_staf->email;
            Notification::route('mail', $to)->notify(new UpProcessMail($model_send_mail));
            
            //send notif android
            $this->fcm->sendPushNotification($user->id, "Pemberitahuan Status Permohonan", $catatan.$permohonan_for_mail->jenis);

            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    public function IsExistDisposisi($id_permohonan, $id_izin_jenis)
    {
        $table_disposisi = "p_permohonan_disposisi";
        if($id_izin_jenis == TypeIzinJenisTel::Ulo)
        {
            $table_disposisi = "p_permohonan_disposisi_ulo";
        }

        $q = sprintf("SELECT count(id) as jumlah from %s where id_permohonan = %d", $table_disposisi, $id_permohonan);
        $result = DB::select($q)[0]->jumlah;
        $return = true;
        if($result == 0)
        {
            $return = false;
        }
        
        return $return;
    }

    public function IsExistDisposisiStaf($id_permohonan, $id_izin_jenis)
    {
        $table_disposisi = "p_permohonan_disposisi_staf";
        if($id_izin_jenis == TypeIzinJenisTel::Ulo)
        {
            $table_disposisi = "p_permohonan_disposisi_staf_ulo";
        }

        $q = sprintf("SELECT count(id) as jumlah from %s where id_permohonan = %d", $table_disposisi, $id_permohonan);
        $result = DB::select($q)[0]->jumlah;
        $return = true;
        if($result == 0)
        {
            $return = false;
        }
        return $return;
    }

    public function IsExistDisposisiKirim($id_permohonan, $id_izin_jenis, $id_user)
    {
        $table_disposisi = "p_permohonan_disposisi_kirim";
        if($id_izin_jenis == TypeIzinJenisTel::Ulo)
        {
            $table_disposisi = "p_permohonan_disposisi_ulo_kirim";
        }

        $q = sprintf("SELECT count(id) as jumlah from %s where id_permohonan = %d AND id_user=%d", $table_disposisi, $id_permohonan, $id_user);
        $result = DB::select($q)[0]->jumlah;
        $return = true;
        if($result == 0)
        {
            $return = false;
        }
        return $return;
    }

    public function GetByIdPermohonan($id_permohonan, $id_izin_jenis)
    {
        $table_disposisi = "p_permohonan_disposisi";
        if($id_izin_jenis == TypeIzinJenisTel::Ulo)
        {
            $table_disposisi = "p_permohonan_disposisi_ulo";
        }

        $q = sprintf("SELECT * from %s where id_permohonan = %d", $table_disposisi, $id_permohonan);
        $result = DB::select($q);
        return $result;
    }

    public function GetStafByIdPermohonan($id_permohonan, $id_izin_jenis)
    {
        $table_disposisi = "p_permohonan_disposisi_staf";
        if($id_izin_jenis == TypeIzinJenisTel::Ulo)
        {
            $table_disposisi = "p_permohonan_disposisi_staf_ulo";
        }

        $q = sprintf("SELECT * from %s where id_permohonan = %d", $table_disposisi, $id_permohonan);
        $result = DB::select($q);
        return $result;
    }

    public function IsUserDisposisi($id_permohonan, $id_user, $id_izin_jenis)
    {
        $table_disposisi = "p_permohonan_disposisi";
        if($id_izin_jenis == TypeIzinJenisTel::Ulo)
        {
            $table_disposisi = "p_permohonan_disposisi_ulo";
        }

        $q = sprintf("SELECT count(id) as jumlah from %s where id_permohonan = %d and id_user = %d", $table_disposisi, $id_permohonan, $id_user);
        $result = DB::select($q)[0]->jumlah;
        $return = true;
        if($result == 0)
        {
            $return = false;
        }
        return $return;
    }
}