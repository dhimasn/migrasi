@component('mail::message')
@lang('Yth. Pimpinan ')
{{ $nama_pt }}

Bersama ini kami sampaikan bahwa Uji Lapangan dengan nomor permohonan : {{ $no_permohonan }} serta nomor SK {{ $no_sk }} dinyatakan Belum Sesuai dan/atau Tidak Laik, dengan rincian sebagai berikut:

{!! $catatan !!}

Mohon segera melakukan pemilihan tanggal Uji Lapangan/ Upload Berkas Mandiri, melalui klik link yang berada di bawah email ini, untuk dapat diproses lebih lanjut.

Demikian disampaikan. Atas perhatiannya diucapkan terima kasih.

@component('mail::button', ['url' => $url_mekanisme_ulo, 'color' => 'green'])
Klik Link
@endcomponent

@endcomponent