<?php

namespace App\Repo\Pos;

use stdClass;
use Exception;
use App\Repo\UserDb;
use App\Enums\TypeDisposisi;
use App\Enums\TypeUloStatus;
use App\Enums\TypeUnitTeknis;
use Illuminate\Support\Carbon;
use App\Enums\TypeIzinJenisPos;
use App\Enums\TypeLevelJabatan;
use Illuminate\Support\Facades\DB;
use App\Repo\GetPermohonanMailDb;
use App\Notifications\DisposisiMail;
use App\Notifications\PerbaikanMail;
use App\Notifications\UpProcessMail;
use App\Notifications\PushNotification;
use Illuminate\Support\Facades\Notification;

class PermohonanDisposisiPosDb
{
    private $udb;
    private $gpm;
    private $fcm;
    public function __construct()
    {
        $this->udb = new UserDb();
        $this->fcm = new PushNotification();
        $this->gpm = new GetPermohonanMailDb();
        Carbon::setLocale('id');
    }

    public function Proses($input)
    {
        try
        {
            // jika posting komitmen pertama kali oleh pemohon
            if($input->type_disposisi == TypeDisposisi::Post)
            {
                $catatan = 'Penyampaian Pemenuhan Komitmen ';
                $user = new stdClass();
                $table_disposisi = "p_permohonan_disposisi";
                if ($input->id_izin_jenis == TypeIzinJenisPos::Provinsi OR $input->id_izin_jenis == TypeIzinJenisPos::KabupatenKota OR $input->id_izin_jenis == TypeIzinJenisPos::Nasional) 
                {
                    // get Kasubdit
                    $user = $this->udb->GetUserByLevelUnitTeknis(TypeLevelJabatan::Kasubdit, TypeUnitTeknis::Pos)[0];    
                }

                if(!$this->IsExistDisposisi($input->id_permohonan, $input->id_izin_jenis))
                {
                    $q = sprintf("INSERT into %s(id_permohonan, id_user) values(%d, %d)", $table_disposisi, $input->id_permohonan, $user->id);
                    $a = DB::insert($q);
                }

                // send mail
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
                
                // Send Notif Android
                $this->fcm->sendPushNotification($user->id, "Pemberitahuan Status Permohonan Pos (Disposisi)", $catatan.$permohonan_for_mail->jenis);
            }
            else if($input->type_disposisi == TypeDisposisi::Up)
            {
                $permohonan_dispo =  $this->GetByIdPermohonan($input->id_permohonan, $input->id_izin_jenis)[0];
                $user = $this->udb->GetUserById($permohonan_dispo->id_user)[0];
                $user_parent = $this->udb->GetUserByIdJabatan($user->id_parent_jabatan)[0];
                
                if(!$this->IsExistDisposisiKirim($input->id_permohonan, $input->id_izin_jenis, $user->id))
                {
                    $this->InsertDisposisiKirim($input->id_izin_jenis, $input->id_permohonan, $user->id);// masuk ke table kirim
                }

                $catatan = 'Penyampaian Pemenuhan Komitmen ';

                $table_disposisi = "p_permohonan_disposisi";
                $q = sprintf("UPDATE %s set id_user=%d where id_permohonan=%d", $table_disposisi, $user_parent->id, $input->id_permohonan);
                $a = DB::update($q);

                $this->DeleteDisposisiKirim($input->id_izin_jenis, $input->id_permohonan, $user_parent->id);// delete disposisi dari table kirim untuk tujuannya
                // send mail
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
                
                // send notif android
                $this->fcm->sendPushNotification($user->id, "Pemberitahuan Status Permohonan Pos (Evaluasi)", $catatan.$permohonan_for_mail->jenis);
            }
            else if($input->type_disposisi == TypeDisposisi::Edit)
            {
                $catatan = 'Penyampaian Pemenuhan Komitmen ';
                // disposisi dari edit permohonan oleh pemohon
                $permohonan_dispo_staf =  $this->GetStafByIdPermohonan($input->id_permohonan, $input->id_izin_jenis)[0];
                
                $table_disposisi = "p_permohonan_disposisi";
                $q = sprintf("UPDATE %s set id_user=%d where id_permohonan=%d", $table_disposisi, $permohonan_dispo_staf->id_user, $input->id_permohonan);
                $a = DB::update($q);

                $this->DeleteDisposisiKirim($input->id_izin_jenis, $input->id_permohonan, $permohonan_dispo_staf->id_user);//delete disposisi dari table kirim untuk tujuannya
                // send mail
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
                
                // send notif android
                $this->fcm->sendPushNotification($user->id, "Pemberitahuan Status Permohonan Pos (Perbaikan)", $catatan.$permohonan_for_mail->jenis);
            }
            else
            {
                $q = sprintf("UPDATE p_permohonan_komit set id_permohonan_komit_kelengkapan_status = %d where id = %d",$input->id_permohonan_komit_kelengkapan_status, $input->id_permohonan_komit);
                $a = DB::update($q);

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
        $q = sprintf("INSERT into %s(id_permohonan, id_user) values(%d, %d)",$table_disposisi, $id_permohonan, $id_user);
        $result = DB::insert($q);
    }

    public function DeleteDisposisiKirim($id_izin_jenis, $id_permohonan, $id_user)
    {
        $table_disposisi = "p_permohonan_disposisi_kirim";
        $q = sprintf("DELETE from %s where id_permohonan = %d and id_user = %d",$table_disposisi, $id_permohonan, $id_user);
        $result = DB::delete($q);
    }

    public function ProsesDisposisStaf($input, $id_unit_teknis, $id_izin_jenis)
    {
        try
        {
            $catatan = 'Penyampaian Pemenuhan Komitmen ';
            $table_disposisi_staf = "p_permohonan_disposisi_staf";
            $table_disposisi = "p_permohonan_disposisi";
            
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
                // insert ke tabel kirim     
                $this->InsertDisposisiKirim($id_izin_jenis, $input->id_permohonan, $user->id);// masuk ke table kirim
            }
            
            // update ke table permohonan disposisi 
            $q = sprintf("UPDATE %s set id_user=%d where id_permohonan=%d", $table_disposisi, $input->id_user, $input->id_permohonan);
            $a = DB::update($q);

            // send mail
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
            
            // send notif android
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

        $q = sprintf("SELECT * from %s where id_permohonan = %d", $table_disposisi, $id_permohonan);
        $result = DB::select($q);
        return $result;
    }

    public function GetStafByIdPermohonan($id_permohonan, $id_izin_jenis)
    {
        $table_disposisi = "p_permohonan_disposisi_staf";
        
        $q = sprintf("SELECT * from %s where id_permohonan = %d", $table_disposisi, $id_permohonan);
        $result = DB::select($q);
        return $result;
    }

    public function IsUserDisposisi($id_permohonan, $id_user, $id_izin_jenis)
    {
        $table_disposisi = "p_permohonan_disposisi";
        
        $q = sprintf("SELECT count(id) as jumlah from %s where id_permohonan = %d and id_user = %d", $table_disposisi, $id_permohonan, $id_user);
        $result = DB::select($q)[0]->jumlah;
        $return = true;
        if($result == 0)
        {
            $return = false;
        }
        return $return;
    }

    public function GenerateSK($id_permohonan)
    {
        try
        {
            // $permohonan_for_mail = $this->gpm->GetForEmailById($id_permohonan);
            // $expired_date = date('Y-m-d', strtotime('+10 day', strtotime($permohonan_for_mail->tgl_permohonan)));
            // $model_send_mail = new stdClass();
            // $model_send_mail->no_sk =  $permohonan_for_mail->no_sk_izin;
            // $model_send_mail->nib =  $permohonan_for_mail->nib;
            $date_now = date('Y-m-d H:i:s');
            $convert_date_now = Carbon::parse($date_now)->translatedFormat('d F Y');
            // $model_send_mail->url_upload_spm =  '';
            // $model_send_mail->id_permohonan =  $id_permohonan;
            // $model_send_mail->tanggal_approved =  $convert_date_now;
            // $model_send_mail->id_izin_jenis =  $input->id_izin_jenis;
            // $model_send_mail->nama_pt =  $permohonan_for_mail->nama_perusahaan;
            // $model_send_mail->npwp =  $permohonan_for_mail->npwp;
            // $model_send_mail->no_penyelenggaraan = $permohonan_for_mail->no_penyelenggaraan;
            // $model_send_mail->expired_date = Carbon::parse($expired_date)->translatedFormat('d F Y');
            // $model_send_mail->tanggal_input = Carbon::parse($permohonan_for_mail->tgl_permohonan)->translatedFormat('d F Y');
            // $model_send_mail->jenis_izin = $permohonan_for_mail->jenis;
            // $model_send_mail->no_telp_perusahaan = $permohonan_for_mail->no_telp_perusahaan;
            // $model_send_mail->alamat_perusahaan = $permohonan_for_mail->alamat;
            $q_get_layanan = DB::select('SELECT a.*,b.layanan FROM p_permohonan_layanan a LEFT JOIN k_layanan b ON a.id_layanan=b.id WHERE a.id_permohonan="'.$id_permohonan.'" ORDER BY a.id DESC');
            $jenis_layanan = array();
            foreach ($q_get_layanan as $key => $value) {
                array_push($jenis_layanan,$value->layanan);
            }
            // $model_send_mail->layanan = implode('; ',$jenis_layanan);
            // $to = $permohonan_for_mail->email_perusahaan;

            // $get_att2 = $this->skpos->GenerateSK($model_send_mail);
            // $tampung_list_path = array();
            // $tampung_list_path[] = $get_att2->list_path;
            // $model_send_mail->list_path = $tampung_list_path;
            // Notification::route('mail', $to)->notify(new PostPermohonanPosMail($model_send_mail));
            // unlink($get_att2->list_path);

            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
}