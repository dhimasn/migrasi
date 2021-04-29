@component('mail::message')
@lang('Yth. Pimpinan ')
{{ $nama_pt }}

Bersama ini kami sampaikan bahwa dokumen pemenuhan pernyataan komitmen dengan Nomor Permohonan {{ $no_permohonan }} dan Nomor Izin Penyelenggaraan {{ $no_sk }} TELAH LULUS EVALUASI.
Selanjutnya perusahaan Saudara wajib melakukan :
1.	Penjadwalan waktu dan memilih metode pelaksanaan Uji Laik Operasi (ULO) melalui klik link yang berada di bawah email ini.
2.	Metode Pelaksanaan ULO berdasarkan Pasal 23 ayat (2) & ayat (3) PM Kominfo No.7 Tahun 2019:
    <br>a. Uji Laik Operasi melalui penilaian mandiri dilakukan oleh Pelaku Usaha.
    <br>b. Uji Laik Operasi juga dapat dilakukan dengan metode uji petik yang dilaksanakan Pelaku Usaha bersama dengan Kementerian Kominfo.
3.	Jika memilih Uji Petik maka perusahaan Saudara melakukan input tanggal pelaksanaan ULO Uji Petik.
4.	Jika memilih Penilaian Mandiri maka perusahaan Saudara melakukan input tanggal upload dokumen ULO Penilaian Mandiri.
5.	Perusahaan Saudara wajib memilih tanggal pelaksanaan ULO Uji Petik dan/atau tanggal upload dokumen ULO Penilaian Mandiri PALING LAMBAT 15 (lima belas) hari kerja sebelum berakhirnya waktu pemenuhan atas pernyataan komitmen (Pasal 23 ayat (4) PM Kominfo No.7 Tahun 2019).

@component('mail::button', ['url' => $url_mekanisme_ulo, 'color' => 'green'])
Klik Link
@endcomponent

@endcomponent