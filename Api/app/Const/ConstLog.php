<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class ConstLog extends Enum
{
    const disposisi_jabatan = 'Telah didisposisikan';
    const evaluasi_jabatan = 'Berkas telah dievaluasi';
    const perbaikan = 'Dokumen telah diperbaiki';
    const approval_jabatan = 'Rencana Usaha telah disetujui';
    const pemilihan_tgl_ulo = 'Pemilihan Tanggal Pengajuan ULO';
    const izin_efektif = 'Izin Berlaku Efektif';
    const approval_ulo_jabatan = 'Rencana ULO telah disetujui';
    const pengajuan_komitmen = 'Pengajuan Komitmen Permohonan';
    const pengembalian_permohonan = 'Permohonan telah dikembalikan ke Pemohon';
    const pengembalian_ulo = 'Rencana ULO telah dikembalikan ke Pemohon';
    const approval_no_komit = 'Permohonan telah disetujui';

    const i_input_permohonan_spm = 'Pemohon Harap Menindaklanjuti SPM';
    const i_input_permohonan = 'Pemohon Input Data Permohonan';
    const i_input_spm = 'Pemohon Input Bukti Bayar';
    const i_input_spm_diterima = 'Bukti Bayar Diterima';
    const i_input_spm_ditolak = 'Bukti Bayar Ditolak';
    const i_proses_disposisi_jabatan = 'Proses Disposisi';
    const i_proses_evaluasi = 'Proses Evaluasi';
    const i_proses_perbaikan = 'Proses Perbaikan';
    const i_proses_pemilihan_tgl_ulo = 'Proses Pemilihan Tanggal ULO';
    const i_proses_ulang_ulo = 'Proses Pengajuan Ulang Mekanisme ULO';
    const i_proses_setuju_ulo = 'Proses Persetujuan Rencana ULO';
    const i_izin_efektif = 'Izin Berlaku Efektif';
    const i_proses_setuju = 'Proses Persetujuan';

    const pengajuan_permohonan = 'Pemohon Input Data Permohonan';
}