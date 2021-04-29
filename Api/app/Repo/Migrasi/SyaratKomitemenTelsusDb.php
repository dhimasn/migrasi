<?php

namespace App\Repo\Migrasi;


use Illuminate\Database\Eloquent\Model;

class SyaratKomitmenTelsusDb extends Model {

    public function findNewSyaratKomitmen($komitmen, $id_layanan){

        switch($id_layanan){
            case 35:
                $result = $this->TelsusPemerintahKawat($komitmen);
                break;
            case 36:
                $result = $this->TelsusPemerintahSeratOptik($komitmen);
                break;
            case 37:
                $result = $this->TelsusPemerintahFrekuensiRadio($komitmen);
                break;
            case 29:
                $result = $this->TelsusBadanHukumKawat($komitmen);
                break;
            case 30:
                $result = $this->TelsusBadanHukumSeratoptik($komitmen);
                break;
            case 31:
                $result = $this->TelsusBadanHukumKawatSpektrumRadio($komitmen);
                break; 
            case 32:
                $result = $this->TelsusBadanHukumSpektrumRadioTrunking($komitmen);
                break;  
            case 33:
                $result = $this->TelsusBadanHukumSpektrumRadionSiskomdat($komitmen);
                break;
            case 34:
                $result = $this->TelsusBadanHukumSistemKomunikasiSatelit($komitmen);
                break;  
            default:
              $result = null;
        }

        return $result;
    }

    public function TelsusPemerintahKawat($komitmen){

    }

    public function TelsusPemerintahSeratOptik($komitmen){

    }

    public function TelsusPemerintahFrekuensiRadio($komitmen){

    }

    public function TelsusBadanHukumKawatSpektrumRadio($komitmen){
        switch($komitmen->teks_judul) {

            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 145;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 149;
                break;
            case "Susunan Kepemilikan Saham":
                $result = null;
                break;
            case "Hak Labuh (Jika Menggunakan Satelit Asing)":
                $result = 151;
                break;
            case "Salinan Landing Right (Satelit Asing)":
                $result = 208;
                break;
            case "Salinan Izin Stasiun Radio":
                $result = 148;
                break;
            case "Maksud, tujuan, dan alasan membangun telekomunikasi khusus":
                $result = 144;
                break;
            case "Konfigurasi sistem dan teknologi jaringan yang telah dibangun":
                $result = 145;
                break;
            case "Diagram dan rute serta peta jaringan":
                $result = 146;
                break;
            case "Surat bukti ketidaksanggupan dari penyelenggara jaringan telekomunikasi dan/atau penyelenggara jasa telekomunikasi":
                $result = 147;
                break;
            case "Izin Stasiun Radio (ISR) yang masih berlaku, jika menggunakan spektrum frekuensi radio":
                $result = 148;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = null;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = null;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;   
            case "Kawat":
                $result = null;
                break;
            case "Serat Optik":
                $result = null;
                break;
            case "Spektrum Frekuensi Radio untuk Sistem Komunikasi Radio":
                $result = null;
                break;
            case "Spektrum Frekuensi Radio untuk Sistem Komunikasi Satelit":
                $result = null;
                break;
            case "Sistem Elektromagnetik Lainnya":
                $result = null;
                break;
            case "Nama Satelit":
                $result = null;
                break;
            case "Slot Orbit":
                $result = null;
                break;
            case "Bukti Kepemilikan Perangkat":
                $result = null;
                break;
            case "Salinan Hak Labuh (Landing Right) jika menggunakan satelit asing":
                $result = null;
                break;
            case "Salinan Izin Galian dari Pemda/Kementerian terkait dalam hal membangun FO dengan jalur yang melintasi jalan umum":
                $result = 144;
                break;        
            default:
              $result = null;
        }
        return $result;   
    }

    public function TelsusBadanHukumKawat($komitmen){
        switch($komitmen->teks_judul) {

            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 127;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 131;
                break;
            case "Susunan Kepemilikan Saham":
                $result = null;
                break;
            case "Hak Labuh (Jika Menggunakan Satelit Asing)":
                $result = null;
                break;
            case "Salinan Landing Right (Satelit Asing)":
                $result = null;
                break;
            case "Salinan Izin Stasiun Radio":
                $result = null;
                break;
            case "Maksud, tujuan, dan alasan membangun telekomunikasi khusus":
                $result = 126;
                break;
            case "Konfigurasi sistem dan teknologi jaringan yang telah dibangun":
                $result = null;
                break;
            case "Diagram dan rute serta peta jaringan":
                $result = null;
                break;
            case "Surat bukti ketidaksanggupan dari penyelenggara jaringan telekomunikasi dan/atau penyelenggara jasa telekomunikasi":
                $result = 129;
                break;
            case "Izin Stasiun Radio (ISR) yang masih berlaku, jika menggunakan spektrum frekuensi radio":
                $result = 130;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = null;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = null;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;   
            case "Kawat":
                $result = null;
                break;
            case "Serat Optik":
                $result = null;
                break;
            case "Spektrum Frekuensi Radio untuk Sistem Komunikasi Radio":
                $result = null;
                break;
            case "Spektrum Frekuensi Radio untuk Sistem Komunikasi Satelit":
                $result = null;
                break;
            case "Sistem Elektromagnetik Lainnya":
                $result = null;
                break;
            case "Nama Satelit":
                $result = null;
                break;
            case "Slot Orbit":
                $result = null;
                break;
            case "Bukti Kepemilikan Perangkat":
                $result = 132;
                break;
            case "Salinan Hak Labuh (Landing Right) jika menggunakan satelit asing":
                $result = 133;
                break;
            case "Salinan Izin Galian dari Pemda/Kementerian terkait dalam hal membangun FO dengan jalur yang melintasi jalan umum":
                $result = 134;
                break;        
            default:
              $result = null;
        }
        return $result;

    }

    public function TelsusBadanHukumSeratoptik($komitmen){
        switch($komitmen->teks_judul) {

            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 136;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 140;
                break;
            case "Susunan Kepemilikan Saham":
                $result = null;
                break;
            case "Hak Labuh (Jika Menggunakan Satelit Asing)":
                $result = null;
                break;
            case "Salinan Landing Right (Satelit Asing)":
                $result = null;
                break;
            case "Salinan Izin Stasiun Radio":
                $result = null;
                break;
            case "Maksud, tujuan, dan alasan membangun telekomunikasi khusus":
                $result = 135;
                break;
            case "Konfigurasi sistem dan teknologi jaringan yang telah dibangun":
                $result = null;
                break;
            case "Diagram dan rute serta peta jaringan":
                $result = null;
                break;
            case "Surat bukti ketidaksanggupan dari penyelenggara jaringan telekomunikasi dan/atau penyelenggara jasa telekomunikasi":
                $result = 138;
                break;
            case "Izin Stasiun Radio (ISR) yang masih berlaku, jika menggunakan spektrum frekuensi radio":
                $result = 139;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = null;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = null;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;   
            case "Kawat":
                $result = null;
                break;
            case "Serat Optik":
                $result = null;
                break;
            case "Spektrum Frekuensi Radio untuk Sistem Komunikasi Radio":
                $result = null;
                break;
            case "Spektrum Frekuensi Radio untuk Sistem Komunikasi Satelit":
                $result = null;
                break;
            case "Sistem Elektromagnetik Lainnya":
                $result = null;
                break;
            case "Nama Satelit":
                $result = null;
                break;
            case "Slot Orbit":
                $result = null;
                break;
            case "Bukti Kepemilikan Perangkat":
                $result = 141;
                break;
            case "Salinan Hak Labuh (Landing Right) jika menggunakan satelit asing":
                $result = 142;
                break;
            case "Salinan Izin Galian dari Pemda/Kementerian terkait dalam hal membangun FO dengan jalur yang melintasi jalan umum":
                $result = 143;
                break;        
            default:
              $result = null;
        }
        return $result;

    }

    public function TelsusBadanHukumSpektrumRadioTrunking($komitmen){
        switch($komitmen->teks_judul) {

            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 154;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = null;
                break;
            case "Susunan Kepemilikan Saham":
                $result = null;
                break;
            case "Hak Labuh (Jika Menggunakan Satelit Asing)":
                $result = null;
                break;
            case "Salinan Landing Right (Satelit Asing)":
                $result = null;
                break;
            case "Salinan Izin Stasiun Radio":
                $result = null;
                break;
            case "Maksud, tujuan, dan alasan membangun telekomunikasi khusus":
                $result = 153;
                break;
            case "Konfigurasi sistem dan teknologi jaringan yang telah dibangun":
                $result = null;
                break;
            case "Diagram dan rute serta peta jaringan":
                $result = null;
                break;
            case "Surat bukti ketidaksanggupan dari penyelenggara jaringan telekomunikasi dan/atau penyelenggara jasa telekomunikasi":
                $result = 156;
                break;
            case "Izin Stasiun Radio (ISR) yang masih berlaku, jika menggunakan spektrum frekuensi radio":
                $result = 157;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = null;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 158;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;   
            case "Kawat":
                $result = null;
                break;
            case "Serat Optik":
                $result = null;
                break;
            case "Spektrum Frekuensi Radio untuk Sistem Komunikasi Radio":
                $result = null;
                break;
            case "Spektrum Frekuensi Radio untuk Sistem Komunikasi Satelit":
                $result = null;
                break;
            case "Sistem Elektromagnetik Lainnya":
                $result = null;
                break;
            case "Nama Satelit":
                $result = null;
                break;
            case "Slot Orbit":
                $result = null;
                break;
            case "Bukti Kepemilikan Perangkat":
                $result = 159;
                break;
            case "Salinan Hak Labuh (Landing Right) jika menggunakan satelit asing":
                $result = 160;
                break;
            case "Salinan Izin Galian dari Pemda/Kementerian terkait dalam hal membangun FO dengan jalur yang melintasi jalan umum":
                $result = 161;
                break;        
            default:
              $result = null;
        }
        return $result;

    }

    public function TelsusBadanHukumSpektrumRadionSiskomdat($komitmen){
        switch($komitmen->teks_judul) {

            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 163;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 167;
                break;
            case "Susunan Kepemilikan Saham":
                $result = null;
                break;
            case "Hak Labuh (Jika Menggunakan Satelit Asing)":
                $result = null;
                break;
            case "Salinan Landing Right (Satelit Asing)":
                $result = null;
                break;
            case "Salinan Izin Stasiun Radio":
                $result = null;
                break;
            case "Maksud, tujuan, dan alasan membangun telekomunikasi khusus":
                $result = 162;
                break;
            case "Konfigurasi sistem dan teknologi jaringan yang telah dibangun":
                $result = null;
                break;
            case "Diagram dan rute serta peta jaringan":
                $result = null;
                break;
            case "Surat bukti ketidaksanggupan dari penyelenggara jaringan telekomunikasi dan/atau penyelenggara jasa telekomunikasi":
                $result = 165;
                break;
            case "Izin Stasiun Radio (ISR) yang masih berlaku, jika menggunakan spektrum frekuensi radio":
                $result = 166;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = null;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = null;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;   
            case "Kawat":
                $result = null;
                break;
            case "Serat Optik":
                $result = null;
                break;
            case "Spektrum Frekuensi Radio untuk Sistem Komunikasi Radio":
                $result = null;
                break;
            case "Spektrum Frekuensi Radio untuk Sistem Komunikasi Satelit":
                $result = null;
                break;
            case "Sistem Elektromagnetik Lainnya":
                $result = null;
                break;
            case "Nama Satelit":
                $result = null;
                break;
            case "Slot Orbit":
                $result = null;
                break;
            case "Bukti Kepemilikan Perangkat":
                $result = 168;
                break;
            case "Salinan Hak Labuh (Landing Right) jika menggunakan satelit asing":
                $result = 169;
                break;
            case "Salinan Izin Galian dari Pemda/Kementerian terkait dalam hal membangun FO dengan jalur yang melintasi jalan umum":
                $result = 170;
                break;        
            default:
              $result = null;
        }
        return $result;
    }

    public function TelsusBadanHukumSistemKomunikasiSatelit($komitmen){
       
        switch($komitmen->teks_judul) {

            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 172;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 176;
                break;
            case "Susunan Kepemilikan Saham":
                $result = null;
                break;
            case "Hak Labuh (Jika Menggunakan Satelit Asing)":
                $result = null;
                break;
            case "Salinan Landing Right (Satelit Asing)":
                $result = null;
                break;
            case "Salinan Izin Stasiun Radio":
                $result = null;
                break;
            case "Maksud, tujuan, dan alasan membangun telekomunikasi khusus":
                $result = 171;
                break;
            case "Konfigurasi sistem dan teknologi jaringan yang telah dibangun":
                $result = null;
                break;
            case "Diagram dan rute serta peta jaringan":
                $result = null;
                break;
            case "Surat bukti ketidaksanggupan dari penyelenggara jaringan telekomunikasi dan/atau penyelenggara jasa telekomunikasi":
                $result = 174;
                break;
            case "Izin Stasiun Radio (ISR) yang masih berlaku, jika menggunakan spektrum frekuensi radio":
                $result = 175;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = null;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = null;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;   
            case "Kawat":
                $result = null;
                break;
            case "Serat Optik":
                $result = null;
                break;
            case "Spektrum Frekuensi Radio untuk Sistem Komunikasi Radio":
                $result = null;
                break;
            case "Spektrum Frekuensi Radio untuk Sistem Komunikasi Satelit":
                $result = null;
                break;
            case "Sistem Elektromagnetik Lainnya":
                $result = null;
                break;
            case "Nama Satelit":
                $result = null;
                break;
            case "Slot Orbit":
                $result = null;
                break;
            case "Bukti Kepemilikan Perangkat":
                $result = 177;
                break;
            case "Salinan Hak Labuh (Landing Right) jika menggunakan satelit asing":
                $result = 178;
                break;
            case "Salinan Izin Galian dari Pemda/Kementerian terkait dalam hal membangun FO dengan jalur yang melintasi jalan umum":
                $result = 179;
                break;        
            default:
              $result = null;
        }
        return $result;

    }
    
}
?>