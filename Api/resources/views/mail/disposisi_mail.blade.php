@component('mail::message')
Yth. {{$jabatan}}<br>
{{ $nama }}

Permohonan baru dengan Nomor Permohonan {{ $no_permohonan }} telah masuk kedalam sistem e-Licensing dan siap diproses. Berikut adalah detail Permohonan

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