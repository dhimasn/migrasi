@component('mail::message')
@lang('Yth. Pimpinan ')
{{ $nama_pt }}

Bersama ini kami sampaikan bahwa dokumen penyampaian rencana penyelenggaraan dengan nomor permohonan : {{ $no_permohonan }} serta nomor SK {{ $no_sk }} dinyatakan belum lengkap dan/atau belum sesuai, dengan rincian sebagai berikut:

{!! $catatan !!}

Mohon segera memperbaiki dokumen penyampaian {{ $jenis_izin }} di atas, melalui klik link yang berada di bawah email ini, untuk dapat diproses lebih lanjut.

Demikian disampaikan. Atas perhatiannya diucapkan terima kasih.

@component('mail::button', ['url' => $url_edit_komitmen, 'color' => 'green'])
Klik Link
@endcomponent

@endcomponent