<?php

namespace App\Repo\Migrasi;


use Illuminate\Database\Eloquent\Model;

class SyaratKomitmenJaringanDb extends Model {

    public function findNewSyaratKomitmen($komitmen, $id_layanan){

        switch($id_layanan){
            case 6:
                $result = $this->BergerakTerestrialRadioTrunking($komitmen);
                break;
            case 7:
                $result = $this->BergerakSatelit($komitmen);
                break;
            case 8:
                $result = $this->TetapTertutupFiberOptikTerestrial($komitmen);
                break;
            case 9:
                $result = $this->TetapLokalBerbasisPacketSwitched($komitmen);
                break;
            case 10:
                $result = $this->TetapTertutupFiberOptikSKKL($komitmen);
                break;
            case 11:
                $result = $this->TetapTertutupMicrowaveLink($komitmen);
                break; 
            case 12:
                $result = $this->TetapTertutupSatelit($komitmen);
                break;  
            case 13:
                $result = $this->TetapTertutupVSAT($komitmen);
                break;  
            default:
              $result = null;
        }

        return $result;
    }

    public function BergerakTerestrialRadioTrunking($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 114;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 115;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 117;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (jasteldas)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (pglinfo)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (pglkelola)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (itkp)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (ktnpgl)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (ktnsms)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (isp)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (grbinet)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (iptv)":
                $result = null;
                break;   
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Fiber Optik Terestrial)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Fiber Optik SKKL)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (VSAT)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Satelit)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Microwave Link)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Lokal Berbasis Packet Switched":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Bergerak Terestrial Radio Trunking":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Bergerak Satelit":
                $result = null;
                break;
            case "Salinan Landing Right (Jika menggunakan Satelit Asing)":
                $result = null;
                break;
            case "Salinan Landing Right (Jika menggunakan Fiber Optik)":
                $result = null;
                break;
            case "Salinan Izin penempatan/galian kabel serat optik dari Pemerintah Daerah (Pemda) setempat dan/atau Kementerian terkait":
                $result = null;
                break; 
            case "Rencana Penjualan":
                $result = 119;
                break; 
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 120;
                break; 
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 121;
                break;         
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = 115;
                break; 
            case "Izin Stasiun Radio (ISR)":
                $result = 118;
                break; 
            case "Hak Labuh Kabel":
                $result = null;
                break;
            case "Hak Labuh Satelit":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 122;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 123;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 124;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 125;
                break;
            case "Dokumen File Tabel Komitmen":
                $result = null;
                break;
            default:
              $result = null;
        }
        return $result;
    }

    public function BergerakSatelit($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 100;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 101;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 103;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (jasteldas)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (pglinfo)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (pglkelola)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (itkp)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (ktnpgl)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (ktnsms)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (isp)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (grbinet)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (iptv)":
                $result = null;
                break;   
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Fiber Optik Terestrial)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Fiber Optik SKKL)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (VSAT)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Satelit)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Microwave Link)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Lokal Berbasis Packet Switched":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Bergerak Terestrial Radio Trunking":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Bergerak Satelit":
                $result = null;
                break;
            case "Salinan Landing Right (Jika menggunakan Satelit Asing)":
                $result = 105;
                break;
            case "Salinan Landing Right (Jika menggunakan Fiber Optik)":
                $result = null;
                break;
            case "Salinan Izin penempatan/galian kabel serat optik dari Pemerintah Daerah (Pemda) setempat dan/atau Kementerian terkait":
                $result = null;
                break; 
            case "Rencana Penjualan":
                $result = 107;
                break; 
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 108;
                break; 
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 109;
                break;         
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = 102;
                break; 
            case "Izin Stasiun Radio (ISR)":
                $result = 106;
                break; 
            case "Hak Labuh Kabel":
                $result = null;
                break;
            case "Hak Labuh Satelit":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 110;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 111;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 112;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 113;
                break;
            case "Dokumen File Tabel Komitmen":
                $result = null;
                break;
            default:
              $result = null;
        }
        return $result;
    }

    public function TetapTertutupFiberOptikTerestrial($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 25;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 27;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 29;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (jasteldas)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (pglinfo)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (pglkelola)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (itkp)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (ktnpgl)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (ktnsms)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (isp)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (grbinet)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (iptv)":
                $result = null;
                break;   
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Fiber Optik Terestrial)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Fiber Optik SKKL)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (VSAT)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Satelit)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Microwave Link)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Lokal Berbasis Packet Switched":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Bergerak Terestrial Radio Trunking":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Bergerak Satelit":
                $result = null;
                break;
            case "Salinan Landing Right (Jika menggunakan Satelit Asing)":
                $result = null;
                break;
            case "Salinan Landing Right (Jika menggunakan Fiber Optik)":
                $result = null;
                break;
            case "Salinan Izin penempatan/galian kabel serat optik dari Pemerintah Daerah (Pemda) setempat dan/atau Kementerian terkait":
                $result = 32;
                break; 
            case "Rencana Penjualan":
                $result = 33;
                break; 
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 34;
                break; 
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 35;
                break;         
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = null;
                break; 
            case "Izin Stasiun Radio (ISR)":
                $result = 31;
                break; 
            case "Hak Labuh Kabel":
                $result = null;
                break;
            case "Hak Labuh Satelit":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 36;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 37;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 38;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 39;
                break;
            case "Dokumen File Tabel Komitmen":
                $result = null;
                break;
            default:
              $result = null;
        }
        return $result;
    }

    public function TetapLokalBerbasisPacketSwitched($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 298;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 299;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 301;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (jasteldas)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (pglinfo)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (pglkelola)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (itkp)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (ktnpgl)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (ktnsms)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (isp)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (grbinet)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (iptv)":
                $result = null;
                break;   
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Fiber Optik Terestrial)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Fiber Optik SKKL)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (VSAT)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Satelit)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Microwave Link)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Lokal Berbasis Packet Switched":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Bergerak Terestrial Radio Trunking":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Bergerak Satelit":
                $result = null;
                break;
            case "Salinan Landing Right (Jika menggunakan Satelit Asing)":
                $result = 302;
                break;
            case "Salinan Landing Right (Jika menggunakan Fiber Optik)":
                $result = null;
                break;
            case "Salinan Izin penempatan/galian kabel serat optik dari Pemerintah Daerah (Pemda) setempat dan/atau Kementerian terkait":
                $result = 304;
                break; 
            case "Rencana Penjualan":
                $result = null;
                break; 
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 306;
                break; 
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 307;
                break;         
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = 300;
                break; 
            case "Izin Stasiun Radio (ISR)":
                $result = 303;
                break; 
            case "Hak Labuh Kabel":
                $result = null;
                break;
            case "Hak Labuh Satelit":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 308;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 309;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 310;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 311;
                break;
            case "Dokumen File Tabel Komitmen":
                $result = null;
                break;
            default:
              $result = null;
        }
        return $result;
    }

    public function TetapTertutupFiberOptikSKKL($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 40;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 42;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 44;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (jasteldas)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (pglinfo)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (pglkelola)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (itkp)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (ktnpgl)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (ktnsms)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (isp)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (grbinet)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (iptv)":
                $result = null;
                break;   
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Fiber Optik Terestrial)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Fiber Optik SKKL)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (VSAT)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Satelit)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Microwave Link)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Lokal Berbasis Packet Switched":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Bergerak Terestrial Radio Trunking":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Bergerak Satelit":
                $result = null;
                break;
            case "Salinan Landing Right (Jika menggunakan Satelit Asing)":
                $result = 45;
                break;
            case "Salinan Landing Right (Jika menggunakan Fiber Optik)":
                $result = 45;
                break;
            case "Salinan Izin penempatan/galian kabel serat optik dari Pemerintah Daerah (Pemda) setempat dan/atau Kementerian terkait":
                $result = 47;
                break; 
            case "Rencana Penjualan":
                $result = 48;
                break; 
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 49;
                break; 
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 50;
                break;         
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = 43;
                break; 
            case "Izin Stasiun Radio (ISR)":
                $result = 46;
                break; 
            case "Hak Labuh Kabel":
                $result = null;
                break;
            case "Hak Labuh Satelit":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 51;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 52;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 53;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 54;
                break;
            case "Dokumen File Tabel Komitmen":
                $result = null;
                break;
            default:
              $result = null;
        }
        return $result;
    }

    public function TetapTertutupMicrowaveLink($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 55;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 57;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 59;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (jasteldas)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (pglinfo)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (pglkelola)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (itkp)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (ktnpgl)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (ktnsms)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (isp)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (grbinet)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (iptv)":
                $result = null;
                break;   
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Fiber Optik Terestrial)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Fiber Optik SKKL)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (VSAT)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Satelit)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Microwave Link)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Lokal Berbasis Packet Switched":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Bergerak Terestrial Radio Trunking":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Bergerak Satelit":
                $result = null;
                break;
            case "Salinan Landing Right (Jika menggunakan Satelit Asing)":
                $result = 60;
                break;
            case "Salinan Landing Right (Jika menggunakan Fiber Optik)":
                $result = 60;
                break;
            case "Salinan Izin penempatan/galian kabel serat optik dari Pemerintah Daerah (Pemda) setempat dan/atau Kementerian terkait":
                $result = 62;
                break; 
            case "Rencana Penjualan":
                $result = 63;
                break; 
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 64;
                break; 
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 65;
                break;         
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = null;
                break; 
            case "Izin Stasiun Radio (ISR)":
                $result = 61;
                break; 
            case "Hak Labuh Kabel":
                $result = null;
                break;
            case "Hak Labuh Satelit":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 66;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 67;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 68;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 69;
                break;
            case "Dokumen File Tabel Komitmen":
                $result = null;
                break;
            default:
              $result = null;
        }
        return $result;
    }

    public function TetapTertutupSatelit($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 70;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 72;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 74;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (jasteldas)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (pglinfo)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (pglkelola)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (itkp)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (ktnpgl)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (ktnsms)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (isp)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (grbinet)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (iptv)":
                $result = null;
                break;   
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Fiber Optik Terestrial)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Fiber Optik SKKL)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (VSAT)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Satelit)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Microwave Link)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Lokal Berbasis Packet Switched":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Bergerak Terestrial Radio Trunking":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Bergerak Satelit":
                $result = null;
                break;
            case "Salinan Landing Right (Jika menggunakan Satelit Asing)":
                $result = 75;
                break;
            case "Salinan Landing Right (Jika menggunakan Fiber Optik)":
                $result = 75;
                break;
            case "Salinan Izin penempatan/galian kabel serat optik dari Pemerintah Daerah (Pemda) setempat dan/atau Kementerian terkait":
                $result = 77;
                break; 
            case "Rencana Penjualan":
                $result = 78;
                break; 
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 79;
                break; 
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 80;
                break;         
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = 73;
                break; 
            case "Izin Stasiun Radio (ISR)":
                $result = 76;
                break; 
            case "Hak Labuh Kabel":
                $result = null;
                break;
            case "Hak Labuh Satelit":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 81;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 82;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 83;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 84;
                break;
            case "Dokumen File Tabel Komitmen":
                $result = null;
                break;
            default:
              $result = null;
        }
        return $result;
    }

    public function TetapTertutupVSAT($komitmen){
        switch($komitmen->teks_judul) {
            case "Konfigurasi Sistem (Jaringan, Alat, dan Perangkat Telekomunikasi)":
                $result = 85;
                break;
            case "Daftar Perangkat dan Sertifikat":
                $result = 87;
                break;
            case "PKS Dengan Penyelenggara Lainnya":
                $result = 89;
                break;
            case "PKS Kolokasi (Colocation)":
                $result = null;
                break;
            case "Ip Number dan As Number (Jika diperlukan)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (jasteldas)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (pglinfo)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (pglkelola)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (itkp)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (ktnpgl)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (ktnsms)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (isp)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (grbinet)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (siskomdat)":
                $result = null;
                break;
            case "Komitmen Layanan Untuk Keperluan ULO (iptv)":
                $result = null;
                break;   
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Fiber Optik Terestrial)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Fiber Optik SKKL)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (VSAT)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Satelit)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Tertutup (Microwave Link)":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Tetap Lokal Berbasis Packet Switched":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Bergerak Terestrial Radio Trunking":
                $result = null;
                break;
            case "Komitmen Layanan Penyelenggaraan Jaringan Bergerak Satelit":
                $result = null;
                break;
            case "Salinan Landing Right (Jika menggunakan Satelit Asing)":
                $result = 90;
                break;
            case "Salinan Landing Right (Jika menggunakan Fiber Optik)":
                $result = 90;
                break;
            case "Salinan Izin penempatan/galian kabel serat optik dari Pemerintah Daerah (Pemda) setempat dan/atau Kementerian terkait":
                $result = 92;
                break; 
            case "Rencana Penjualan":
                $result = 93;
                break; 
            case "Dokumen dukungan Pra-Jual sampai dengan Purna Jual":
                $result = 94;
                break; 
            case "Dokumen (Standard Operation Procedure/SOP) (Monitor Jaringan)":
                $result = 95;
                break;         
            case "Dokumen bukti kepemilikan Alat dan Perangkat Telekomunikasi":
                $result = 88;
                break; 
            case "Izin Stasiun Radio (ISR)":
                $result = 91;
                break; 
            case "Hak Labuh Kabel":
                $result = null;
                break;
            case "Hak Labuh Satelit":
                $result = null;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Penanganan Gangguan)":
                $result = 96;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Billing & Penagihan)":
                $result = 97;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Registrasi - Unregistrasi dan Aktivasi - Deaktivasi)":
                $result = 98;
                break;
            case "Dokumen (Standard Operation Procedure/SOP) (Pelayanan Pengguna/Pelanggan)":
                $result = 99;
                break;
            case "Dokumen File Tabel Komitmen":
                $result = null;
                break;
            default:
              $result = null;
        }
        return $result;
    }
    
}
?>