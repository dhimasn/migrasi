<?php

namespace App\Repo\Migrasi;


use Illuminate\Database\Eloquent\Model;

class SyaratKomitmenJasaDb extends Model {

    public function findNewSyaratKomitmen($komitmen, $id_layanan){

        switch($id_layanan){
            case 1:
                $result = $this->pusatPanggilanInformasiCallCenter($komitmen);
                break;
            case 2:
                $result = $this->PanggilanTerkelolaCalingCard($komitmen);
                break;
            case 42:
                $result = $this->TeleponiDasarmelaluiJaringanTelekomunikasi($komitmen);
                break;
            case 41:
                $result = $this->InternetTeleponiuntukKeperluanPublik($komitmen);
                break;
            case 46:
                $result = $this->KontenPanggilanPremium($komitmen);
                break;
            case 44:
                $result = $this->KontenSMSPremium($komitmen);
                break; 
            case 40:
                $result = $this->AksesInternetServiceProvider($komitmen);
                break;  
            case 45:
                $result = $this->GerbangAksesInternet($komitmen);
                break;
            case 47:
                $result = $this->SistemKomunikasiData($komitmen);
                break;
            case 39:
                $result = $this->TelevisiProtokolInternet($komitmen);
                break;
            case 43:
                $result = $this->TeleponiDasarmelaluiSatelitHakLabuh($komitmen);
                break;  
            default:
              $result = null;
        }

        return $result;

    }

    public function pusatPanggilanInformasiCallCenter($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 1;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO":
                $result = null;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 4;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 2;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Jaringan Telekomunikasi":
                $result = null;
                break;
            case "Komitmen Layanan Pusat Panggilan Informasi (Call Center)":
                $result = null;
                break;
            case "Komitmen Layanan Panggilan Terkelola (Caling Card)":
                $result = null;
                break;
            case "Komitmen Layanan Internet Teleponi untuk Keperluan Publik (ITKP)":
                $result = null;
                break;
            case "Komitmen Layanan Konten Panggilan Premium (Premium Call)":
                $result = null;
                break;
            case "Komitmen Layanan Konten SMS Premium (Content Provider)":
                $result = null;
                break;
            case "Komitmen Layanan Akses Internet (Internet Service Provider/ISP)":
                $result = null;
                break;
            case "Komitmen Layanan Gerbang Akses Internet (Network Access Provider/NAP)":
                $result = null;
                break;   
            case "Komitmen Layanan Sistem Komunikasi Data (Siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Televisi Protokol Internet (Internet Protocol Television/IPTV)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Satelit yang telah memperoleh Hak Labuh (Landing Right)":
                $result = null;
                break;
            case "Salinan Landing Right (Satelit Asing)":
                $result = null;
                break;
            case "Salinan Izin Stasiun Radio":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Landing Right (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Izin Stasiun Radio (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Surat Pernyataan Tanggung Jawab Bersama antara Penyelenggara Jaringan Bergerak Seluler dengan Penyelenggara Jasa Telekomunikasi Layanan Konten Pesan Pendek Premium (SMS Premium)":
                $result = null;
                break;
            case "Dokumen Pengujian Fungsi Layanan (SMS Premium)":
                $result = null;
                break;
            case "Salinan Hasil User Acceptance Test (UAT) dengan Penyelenggara Jaringan Bergerak Seluler (SMS Premium)":
                $result = null;
                break; 
            case "Rencana Penjualan":
                $result = 6;
                break;  
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 7;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 8;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 9;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 10;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 11;
                break;
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = 3;
                break;
            case "Surat Penetapan Kode Akses":
                $result = 5;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 12;
                break;   
            case "PKS Dengan Penyedia Kontem Independen":
                $result = null;
                break;                      
            default:
                $result = null;
        }
        return $result;
    }

    public function PanggilanTerkelolaCalingCard($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 13;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO":
                $result = null;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 16;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 14;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Jaringan Telekomunikasi":
                $result = null;
                break;
            case "Komitmen Layanan Pusat Panggilan Informasi (Call Center)":
                $result = null;
                break;
            case "Komitmen Layanan Panggilan Terkelola (Caling Card)":
                $result = null;
                break;
            case "Komitmen Layanan Internet Teleponi untuk Keperluan Publik (ITKP)":
                $result = null;
                break;
            case "Komitmen Layanan Konten Panggilan Premium (Premium Call)":
                $result = null;
                break;
            case "Komitmen Layanan Konten SMS Premium (Content Provider)":
                $result = null;
                break;
            case "Komitmen Layanan Akses Internet (Internet Service Provider/ISP)":
                $result = null;
                break;
            case "Komitmen Layanan Gerbang Akses Internet (Network Access Provider/NAP)":
                $result = null;
                break;   
            case "Komitmen Layanan Sistem Komunikasi Data (Siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Televisi Protokol Internet (Internet Protocol Television/IPTV)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Satelit yang telah memperoleh Hak Labuh (Landing Right)":
                $result = null;
                break;
            case "Salinan Landing Right (Satelit Asing)":
                $result = null;
                break;
            case "Salinan Izin Stasiun Radio":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Landing Right (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Izin Stasiun Radio (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Surat Pernyataan Tanggung Jawab Bersama antara Penyelenggara Jaringan Bergerak Seluler dengan Penyelenggara Jasa Telekomunikasi Layanan Konten Pesan Pendek Premium (SMS Premium)":
                $result = null;
                break;
            case "Dokumen Pengujian Fungsi Layanan (SMS Premium)":
                $result = null;
                break;
            case "Salinan Hasil User Acceptance Test (UAT) dengan Penyelenggara Jaringan Bergerak Seluler (SMS Premium)":
                $result = null;
                break; 
            case "Rencana Penjualan":
                $result = 18;
                break;  
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 19;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 20;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 21;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 22;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 23;
                break;
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = 15;
                break;
            case "Surat Penetapan Kode Akses":
                $result = 17;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 24;
                break;   
            case "PKS Dengan Penyedia Kontem Independen":
                $result = null;
                break;                      
            default:
                $result = null;
        }
        return $result;
    }

    public function TeleponiDasarmelaluiJaringanTelekomunikasi($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 228;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO":
                $result = null;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 231;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 229;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Jaringan Telekomunikasi":
                $result = null;
                break;
            case "Komitmen Layanan Pusat Panggilan Informasi (Call Center)":
                $result = null;
                break;
            case "Komitmen Layanan Panggilan Terkelola (Caling Card)":
                $result = null;
                break;
            case "Komitmen Layanan Internet Teleponi untuk Keperluan Publik (ITKP)":
                $result = null;
                break;
            case "Komitmen Layanan Konten Panggilan Premium (Premium Call)":
                $result = null;
                break;
            case "Komitmen Layanan Konten SMS Premium (Content Provider)":
                $result = null;
                break;
            case "Komitmen Layanan Akses Internet (Internet Service Provider/ISP)":
                $result = null;
                break;
            case "Komitmen Layanan Gerbang Akses Internet (Network Access Provider/NAP)":
                $result = null;
                break;   
            case "Komitmen Layanan Sistem Komunikasi Data (Siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Televisi Protokol Internet (Internet Protocol Television/IPTV)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Satelit yang telah memperoleh Hak Labuh (Landing Right)":
                $result = null;
                break;
            case "Salinan Landing Right (Satelit Asing)":
                $result = null;
                break;
            case "Salinan Izin Stasiun Radio":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Landing Right (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Izin Stasiun Radio (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Surat Pernyataan Tanggung Jawab Bersama antara Penyelenggara Jaringan Bergerak Seluler dengan Penyelenggara Jasa Telekomunikasi Layanan Konten Pesan Pendek Premium (SMS Premium)":
                $result = null;
                break;
            case "Dokumen Pengujian Fungsi Layanan (SMS Premium)":
                $result = null;
                break;
            case "Salinan Hasil User Acceptance Test (UAT) dengan Penyelenggara Jaringan Bergerak Seluler (SMS Premium)":
                $result = null;
                break; 
            case "Rencana Penjualan":
                $result = 232;
                break;  
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 233;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 234;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 235;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 236;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 237;
                break;
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = 230;
                break;
            case "Surat Penetapan Kode Akses":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 238;
                break;   
            case "PKS Dengan Penyedia Kontem Independen":
                $result = null;
                break;                      
            default:
                $result = null;
        }
        return $result;
    }

    public function InternetTeleponiuntukKeperluanPublik($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 228;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO":
                $result = null;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 231;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 229;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Jaringan Telekomunikasi":
                $result = null;
                break;
            case "Komitmen Layanan Pusat Panggilan Informasi (Call Center)":
                $result = null;
                break;
            case "Komitmen Layanan Panggilan Terkelola (Caling Card)":
                $result = null;
                break;
            case "Komitmen Layanan Internet Teleponi untuk Keperluan Publik (ITKP)":
                $result = null;
                break;
            case "Komitmen Layanan Konten Panggilan Premium (Premium Call)":
                $result = null;
                break;
            case "Komitmen Layanan Konten SMS Premium (Content Provider)":
                $result = null;
                break;
            case "Komitmen Layanan Akses Internet (Internet Service Provider/ISP)":
                $result = null;
                break;
            case "Komitmen Layanan Gerbang Akses Internet (Network Access Provider/NAP)":
                $result = null;
                break;   
            case "Komitmen Layanan Sistem Komunikasi Data (Siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Televisi Protokol Internet (Internet Protocol Television/IPTV)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Satelit yang telah memperoleh Hak Labuh (Landing Right)":
                $result = null;
                break;
            case "Salinan Landing Right (Satelit Asing)":
                $result = null;
                break;
            case "Salinan Izin Stasiun Radio":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Landing Right (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Izin Stasiun Radio (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Surat Pernyataan Tanggung Jawab Bersama antara Penyelenggara Jaringan Bergerak Seluler dengan Penyelenggara Jasa Telekomunikasi Layanan Konten Pesan Pendek Premium (SMS Premium)":
                $result = null;
                break;
            case "Dokumen Pengujian Fungsi Layanan (SMS Premium)":
                $result = null;
                break;
            case "Salinan Hasil User Acceptance Test (UAT) dengan Penyelenggara Jaringan Bergerak Seluler (SMS Premium)":
                $result = null;
                break; 
            case "Rencana Penjualan":
                $result = 232;
                break;  
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 233;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 234;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 235;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 236;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 237;
                break;
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = 230;
                break;
            case "Surat Penetapan Kode Akses":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 238;
                break;   
            case "PKS Dengan Penyedia Kontem Independen":
                $result = null;
                break;                      
            default:
                $result = null;
        }
        return $result;

    }

    public function KontenPanggilanPremium($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 274;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO":
                $result = null;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 277;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 275;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Jaringan Telekomunikasi":
                $result = null;
                break;
            case "Komitmen Layanan Pusat Panggilan Informasi (Call Center)":
                $result = null;
                break;
            case "Komitmen Layanan Panggilan Terkelola (Caling Card)":
                $result = null;
                break;
            case "Komitmen Layanan Internet Teleponi untuk Keperluan Publik (ITKP)":
                $result = null;
                break;
            case "Komitmen Layanan Konten Panggilan Premium (Premium Call)":
                $result = null;
                break;
            case "Komitmen Layanan Konten SMS Premium (Content Provider)":
                $result = null;
                break;
            case "Komitmen Layanan Akses Internet (Internet Service Provider/ISP)":
                $result = null;
                break;
            case "Komitmen Layanan Gerbang Akses Internet (Network Access Provider/NAP)":
                $result = null;
                break;   
            case "Komitmen Layanan Sistem Komunikasi Data (Siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Televisi Protokol Internet (Internet Protocol Television/IPTV)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Satelit yang telah memperoleh Hak Labuh (Landing Right)":
                $result = null;
                break;
            case "Salinan Landing Right (Satelit Asing)":
                $result = null;
                break;
            case "Salinan Izin Stasiun Radio":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Landing Right (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Izin Stasiun Radio (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Surat Pernyataan Tanggung Jawab Bersama antara Penyelenggara Jaringan Bergerak Seluler dengan Penyelenggara Jasa Telekomunikasi Layanan Konten Pesan Pendek Premium (SMS Premium)":
                $result = null;
                break;
            case "Dokumen Pengujian Fungsi Layanan (SMS Premium)":
                $result = null;
                break;
            case "Salinan Hasil User Acceptance Test (UAT) dengan Penyelenggara Jaringan Bergerak Seluler (SMS Premium)":
                $result = null;
                break; 
            case "Rencana Penjualan":
                $result = 278;
                break;  
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 279;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 280;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 281;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 282;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 283;
                break;
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = 276;
                break;
            case "Surat Penetapan Kode Akses":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 284;
                break;   
            case "PKS Dengan Penyedia Kontem Independen":
                $result = null;
                break;                      
            default:
                $result = null;
        }
        return $result;

    }

    public function KontenSMSPremium($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 250;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO":
                $result = null;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 253;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 251;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Jaringan Telekomunikasi":
                $result = null;
                break;
            case "Komitmen Layanan Pusat Panggilan Informasi (Call Center)":
                $result = null;
                break;
            case "Komitmen Layanan Panggilan Terkelola (Caling Card)":
                $result = null;
                break;
            case "Komitmen Layanan Internet Teleponi untuk Keperluan Publik (ITKP)":
                $result = null;
                break;
            case "Komitmen Layanan Konten Panggilan Premium (Premium Call)":
                $result = null;
                break;
            case "Komitmen Layanan Konten SMS Premium (Content Provider)":
                $result = null;
                break;
            case "Komitmen Layanan Akses Internet (Internet Service Provider/ISP)":
                $result = null;
                break;
            case "Komitmen Layanan Gerbang Akses Internet (Network Access Provider/NAP)":
                $result = null;
                break;   
            case "Komitmen Layanan Sistem Komunikasi Data (Siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Televisi Protokol Internet (Internet Protocol Television/IPTV)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Satelit yang telah memperoleh Hak Labuh (Landing Right)":
                $result = null;
                break;
            case "Salinan Landing Right (Satelit Asing)":
                $result = null;
                break;
            case "Salinan Izin Stasiun Radio":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Landing Right (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Izin Stasiun Radio (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Surat Pernyataan Tanggung Jawab Bersama antara Penyelenggara Jaringan Bergerak Seluler dengan Penyelenggara Jasa Telekomunikasi Layanan Konten Pesan Pendek Premium (SMS Premium)":
                $result = 254;
                break;
            case "Dokumen Pengujian Fungsi Layanan (SMS Premium)":
                $result = 255;
                break;
            case "Salinan Hasil User Acceptance Test (UAT) dengan Penyelenggara Jaringan Bergerak Seluler (SMS Premium)":
                $result = 256;
                break; 
            case "Rencana Penjualan":
                $result = 257;
                break;  
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 259;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = null;
                break;
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = 252;
                break;
            case "Surat Penetapan Kode Akses":
                $result = 258;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = null;
                break;   
            case "PKS Dengan Penyedia Kontem Independen":
                $result = null;
                break;                      
            default:
                $result = null;
        }
        return $result;
    }

    public function AksesInternetServiceProvider($komitmen){

        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 203;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO":
                $result = null;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 206;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 204;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = 207;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Jaringan Telekomunikasi":
                $result = null;
                break;
            case "Komitmen Layanan Pusat Panggilan Informasi (Call Center)":
                $result = null;
                break;
            case "Komitmen Layanan Panggilan Terkelola (Caling Card)":
                $result = null;
                break;
            case "Komitmen Layanan Internet Teleponi untuk Keperluan Publik (ITKP)":
                $result = null;
                break;
            case "Komitmen Layanan Konten Panggilan Premium (Premium Call)":
                $result = null;
                break;
            case "Komitmen Layanan Konten SMS Premium (Content Provider)":
                $result = null;
                break;
            case "Komitmen Layanan Akses Internet (Internet Service Provider/ISP)":
                $result = null;
                break;
            case "Komitmen Layanan Gerbang Akses Internet (Network Access Provider/NAP)":
                $result = null;
                break;   
            case "Komitmen Layanan Sistem Komunikasi Data (Siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Televisi Protokol Internet (Internet Protocol Television/IPTV)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Satelit yang telah memperoleh Hak Labuh (Landing Right)":
                $result = null;
                break;
            case "Salinan Landing Right (Satelit Asing)":
                $result = 208;
                break;
            case "Salinan Izin Stasiun Radio":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Landing Right (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Izin Stasiun Radio (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Surat Pernyataan Tanggung Jawab Bersama antara Penyelenggara Jaringan Bergerak Seluler dengan Penyelenggara Jasa Telekomunikasi Layanan Konten Pesan Pendek Premium (SMS Premium)":
                $result = null;
                break;
            case "Dokumen Pengujian Fungsi Layanan (SMS Premium)":
                $result = null;
                break;
            case "Salinan Hasil User Acceptance Test (UAT) dengan Penyelenggara Jaringan Bergerak Seluler (SMS Premium)":
                $result = null;
                break; 
            case "Salinan Hasil User Acceptance Test (UAT) dengan Penyelenggara Jaringan Bergerak Seluler (SMS Premium)":
                $result = null;
                break;
            case "Rencana Penjualan":
                $result = 209;
                break;  
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 210;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 211;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 212;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 213;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 214;
                break;
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = 205;
                break;
            case "Surat Penetapan Kode Akses":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 215;
                break;   
            case "PKS Dengan Penyedia Kontem Independen":
                $result = null;
                break;                      
            default:
                $result = null;
        }
        return $result;

    }

    public function GerbangAksesInternet($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 260;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO":
                $result = null;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 263;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 261;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = 264;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Jaringan Telekomunikasi":
                $result = null;
                break;
            case "Komitmen Layanan Pusat Panggilan Informasi (Call Center)":
                $result = null;
                break;
            case "Komitmen Layanan Panggilan Terkelola (Caling Card)":
                $result = null;
                break;
            case "Komitmen Layanan Internet Teleponi untuk Keperluan Publik (ITKP)":
                $result = null;
                break;
            case "Komitmen Layanan Konten Panggilan Premium (Premium Call)":
                $result = null;
                break;
            case "Komitmen Layanan Konten SMS Premium (Content Provider)":
                $result = null;
                break;
            case "Komitmen Layanan Akses Internet (Internet Service Provider/ISP)":
                $result = null;
                break;
            case "Komitmen Layanan Gerbang Akses Internet (Network Access Provider/NAP)":
                $result = null;
                break;   
            case "Komitmen Layanan Sistem Komunikasi Data (Siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Televisi Protokol Internet (Internet Protocol Television/IPTV)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Satelit yang telah memperoleh Hak Labuh (Landing Right)":
                $result = null;
                break;
            case "Salinan Landing Right (Satelit Asing)":
                $result = 265;
                break;
            case "Salinan Izin Stasiun Radio":
                $result = 266;
                break;
            case "Nomor dan Tanggal Penerbitan Landing Right (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Izin Stasiun Radio (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Surat Pernyataan Tanggung Jawab Bersama antara Penyelenggara Jaringan Bergerak Seluler dengan Penyelenggara Jasa Telekomunikasi Layanan Konten Pesan Pendek Premium (SMS Premium)":
                $result = null;
                break;
            case "Dokumen Pengujian Fungsi Layanan (SMS Premium)":
                $result = null;
                break;
            case "Salinan Hasil User Acceptance Test (UAT) dengan Penyelenggara Jaringan Bergerak Seluler (SMS Premium)":
                $result = null;
                break; 
            case "Rencana Penjualan":
                $result = 267;
                break;  
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 268;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 269;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 270;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 271;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 272;
                break;
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = 262;
                break;
            case "Surat Penetapan Kode Akses":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 273;
                break;   
            case "PKS Dengan Penyedia Kontem Independen":
                $result = null;
                break;                      
            default:
                $result = null;
        }
        return $result;
    }

    public function SistemKomunikasiData($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 285;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO":
                $result = null;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 288;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 286;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Jaringan Telekomunikasi":
                $result = null;
                break;
            case "Komitmen Layanan Pusat Panggilan Informasi (Call Center)":
                $result = null;
                break;
            case "Komitmen Layanan Panggilan Terkelola (Caling Card)":
                $result = null;
                break;
            case "Komitmen Layanan Internet Teleponi untuk Keperluan Publik (ITKP)":
                $result = null;
                break;
            case "Komitmen Layanan Konten Panggilan Premium (Premium Call)":
                $result = null;
                break;
            case "Komitmen Layanan Konten SMS Premium (Content Provider)":
                $result = null;
                break;
            case "Komitmen Layanan Akses Internet (Internet Service Provider/ISP)":
                $result = null;
                break;
            case "Komitmen Layanan Gerbang Akses Internet (Network Access Provider/NAP)":
                $result = null;
                break;   
            case "Komitmen Layanan Sistem Komunikasi Data (Siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Televisi Protokol Internet (Internet Protocol Television/IPTV)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Satelit yang telah memperoleh Hak Labuh (Landing Right)":
                $result = null;
                break;
            case "Salinan Landing Right (Satelit Asing)":
                $result = null;
                break;
            case "Salinan Izin Stasiun Radio":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Landing Right (Sistem Komunikasi Data)":
                $result = 289;
                break;
            case "Nomor dan Tanggal Penerbitan Izin Stasiun Radio (Sistem Komunikasi Data)":
                $result = 290;
                break;
            case "Surat Pernyataan Tanggung Jawab Bersama antara Penyelenggara Jaringan Bergerak Seluler dengan Penyelenggara Jasa Telekomunikasi Layanan Konten Pesan Pendek Premium (SMS Premium)":
                $result = null;
                break;
            case "Dokumen Pengujian Fungsi Layanan (SMS Premium)":
                $result = null;
                break;
            case "Salinan Hasil User Acceptance Test (UAT) dengan Penyelenggara Jaringan Bergerak Seluler (SMS Premium)":
                $result = null;
                break; 
            case "Rencana Penjualan":
                $result = 291;
                break;  
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 292;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 293;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 294;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 295;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 296;
                break;
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = 287;
                break;
            case "Surat Penetapan Kode Akses":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 297;
                break;   
            case "PKS Dengan Penyedia Kontem Independen":
                $result = null;
                break;                      
            default:
                $result = null;
        }
        return $result;
    }

    public function TelevisiProtokolInternet($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 192;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO":
                $result = null;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 195;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 193;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Jaringan Telekomunikasi":
                $result = null;
                break;
            case "Komitmen Layanan Pusat Panggilan Informasi (Call Center)":
                $result = null;
                break;
            case "Komitmen Layanan Panggilan Terkelola (Caling Card)":
                $result = null;
                break;
            case "Komitmen Layanan Internet Teleponi untuk Keperluan Publik (ITKP)":
                $result = null;
                break;
            case "Komitmen Layanan Konten Panggilan Premium (Premium Call)":
                $result = null;
                break;
            case "Komitmen Layanan Konten SMS Premium (Content Provider)":
                $result = null;
                break;
            case "Komitmen Layanan Akses Internet (Internet Service Provider/ISP)":
                $result = null;
                break;
            case "Komitmen Layanan Gerbang Akses Internet (Network Access Provider/NAP)":
                $result = null;
                break;   
            case "Komitmen Layanan Sistem Komunikasi Data (Siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Televisi Protokol Internet (Internet Protocol Television/IPTV)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Satelit yang telah memperoleh Hak Labuh (Landing Right)":
                $result = null;
                break;
            case "Salinan Landing Right (Satelit Asing)":
                $result = null;
                break;
            case "Salinan Izin Stasiun Radio":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Landing Right (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Izin Stasiun Radio (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Surat Pernyataan Tanggung Jawab Bersama antara Penyelenggara Jaringan Bergerak Seluler dengan Penyelenggara Jasa Telekomunikasi Layanan Konten Pesan Pendek Premium (SMS Premium)":
                $result = null;
                break;
            case "Dokumen Pengujian Fungsi Layanan (SMS Premium)":
                $result = null;
                break;
            case "Salinan Hasil User Acceptance Test (UAT) dengan Penyelenggara Jaringan Bergerak Seluler (SMS Premium)":
                $result = null;
                break; 
            case "Rencana Penjualan":
                $result = 196;
                break;  
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 197;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 198;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 199;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 200;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 201;
                break;
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = 194;
                break;
            case "Surat Penetapan Kode Akses":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 202;
                break;   
            case "PKS Dengan Penyedia Kontem Independen":
                $result = null;
                break;                      
            default:
                $result = null;
        }
        return $result;
    }

    public function TeleponiDasarmelaluiSatelitHakLabuh($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 239;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO":
                $result = null;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 242;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 240;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Jaringan Telekomunikasi":
                $result = null;
                break;
            case "Komitmen Layanan Pusat Panggilan Informasi (Call Center)":
                $result = null;
                break;
            case "Komitmen Layanan Panggilan Terkelola (Caling Card)":
                $result = null;
                break;
            case "Komitmen Layanan Internet Teleponi untuk Keperluan Publik (ITKP)":
                $result = null;
                break;
            case "Komitmen Layanan Konten Panggilan Premium (Premium Call)":
                $result = null;
                break;
            case "Komitmen Layanan Konten SMS Premium (Content Provider)":
                $result = null;
                break;
            case "Komitmen Layanan Akses Internet (Internet Service Provider/ISP)":
                $result = null;
                break;
            case "Komitmen Layanan Gerbang Akses Internet (Network Access Provider/NAP)":
                $result = null;
                break;   
            case "Komitmen Layanan Sistem Komunikasi Data (Siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Televisi Protokol Internet (Internet Protocol Television/IPTV)":
                $result = null;
                break;
            case "Komitmen Layanan Teleponi Dasar melalui Satelit yang telah memperoleh Hak Labuh (Landing Right)":
                $result = null;
                break;
            case "Salinan Landing Right (Satelit Asing)":
                $result = null;
                break;
            case "Salinan Izin Stasiun Radio":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Landing Right (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Nomor dan Tanggal Penerbitan Izin Stasiun Radio (Sistem Komunikasi Data)":
                $result = null;
                break;
            case "Surat Pernyataan Tanggung Jawab Bersama antara Penyelenggara Jaringan Bergerak Seluler dengan Penyelenggara Jasa Telekomunikasi Layanan Konten Pesan Pendek Premium (SMS Premium)":
                $result = null;
                break;
            case "Dokumen Pengujian Fungsi Layanan (SMS Premium)":
                $result = null;
                break;
            case "Salinan Hasil User Acceptance Test (UAT) dengan Penyelenggara Jaringan Bergerak Seluler (SMS Premium)":
                $result = null;
                break; 
            case "Rencana Penjualan":
                $result = 243;
                break;  
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 244;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 245;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 246;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 247;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 248;
                break;
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = 241;
                break;
            case "Surat Penetapan Kode Akses":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 249;
                break;   
            case "PKS Dengan Penyedia Kontem Independen":
                $result = null;
                break;                      
            default:
                $result = null;
        }
        return $result;
    }
    
}
?>