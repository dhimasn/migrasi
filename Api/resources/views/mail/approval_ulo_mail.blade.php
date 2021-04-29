@component('mail::message')
@lang('Yth. Pimpinan ')
{{ $nama_pt }}

Bersama ini terlampir kami sampaikan bahwa proses Uji Laik Operasi {{ $nama_pt }} dengan nomor permohonan {{ $no_permohonan }} dan nomor izin penyelenggaraan {{ $no_sk }} telah selesai dilaksanakan dan dinyatakan laik operasi.

Terlampir disampaikan Surat Keterangan Laik Operasi (SKLO) dan Penetapan Komitmen yang diterbitkan yang diterbitkan dalam bentuk elektronik dengan menggunakan tanda tangan elektronik (digital) dan memiliki kekuatan hukum yang sama dalam bentuk tertulis.

Dengan diterbitkannya Surat Keterangan Laik Operasi (SKLO) dan Penetapan Komitmen, maka Izin {{ $jenis_izin }} {{ $nama_pt }} Nomor {{ $no_sk }} telah berlaku efektif dan wajib menyelenggarakan layanan tersebut secara komersial paling lambat 120 (seratus dua puluh) hari kalender sejak Izin Penyelenggaraan berlaku efektif.

Untuk pengecekan keabsahan dokumen elektronik tersebut, dapat menggunakan software pdf reader, menghubungi Call Center Kominfo 159, atau mengunjungi PTSP Kominfo. Jika terjadi kehilangan atau kerusakan, Surat Keterangan Laik Operasi (SKLO) dan Penetapan Komitmen dapat diunduh kembali melalui klik link di bawah email ini.

@component('mail::button', ['url' => $url_download_sk, 'color' => 'green'])
Klik Link
@endcomponent

@endcomponent