@component('mail::message')
Yth. {{$jabatan}}<br>
{{ $nama }}

Anda mendapat permohonan terkait perbaikan berkas oleh pemohon dengan detail sebagai berikut:

@component('mail::table')
| | |
| ------------- | ------------- |
| Nomor Permohonan | {{ $no_permohonan }} |
| Nama Perusahaan | {{ $nama_pt }} |
| Tanggal Permohonan | {{ $tanggal_input }} |
| Jenis Permohonan | {{ $jenis_permohonan }} |
@endcomponent

Mohon untuk segera ditindaklanjuti.
Terima kasih

@endcomponent