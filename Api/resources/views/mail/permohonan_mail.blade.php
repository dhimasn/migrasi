@component('mail::message')
@lang('Yth. Pimpinan ')
{{ $nama_pt }}

Bersama ini kami sampaikan bahwa permohonan Izin Penyelenggaraan {{ $jenis_izin }} {{ $nama_pt }} dengan nomor permohonan ({{ $no_permohonan }}) telah selesai kami proses dan telah ditetapkan dengan Nomor Izin Penyelenggaraan {{ $no_sk }}.

Terlampir adalah Izin Penyelenggaraan {{ $jenis_izin }} dalam bentuk elektronik yang telah diterbitkan dengan menggunakan tanda tangan elektronik (digital) dan memiliki kekuatan hukum yang sama dalam bentuk tertulis.

Izin Penyelenggaraan {{ $jenis_izin }} belum berlaku efektif untuk keperluan komersial. {{ $nama_pt }}  wajib meyampaikan :

Pemenuhan Pernyataan Komitmen paling lambat 1 (satu) tahun sejak Izin Penyelenggaraan {{ $jenis_izin }} diterbitkan

Permohonan Uji Laik Operasi (ULO) paling lambat 14 (empat belas) hari kerja sebelum berakhirnya waktu pemenuhan atas pernyataan komitmen.

Izin Penyelenggaraan {{ $jenis_izin }} berlaku efektif setelah {{ $nama_pt }} dinyatakan memenuhi hasil evaluasi terhadap kewajiban diatas.

Dokumen Pemenuhan Pernyataan Komitmen disampaikan melalui klik link yang berada di bawah email ini dengan menggunakan username dan password Single ID Layanan Kominfo yang telah dimiliki.

Untuk pengecekan keabsahan dokumen elektronik tersebut, dapat menggunakan software pdf reader, menghubungi Call Center Kominfo 159, atau mengunjungi PTSP Kominfo. Jika terjadi kehilangan atau kerusakan, dokumen Izin Penyelenggaraan dapat diunduh kembali melalui link berikut Klik Disini.

Demikian disampaikan, atas perhatiannya diucapkan terima kasih.

@component('mail::button', ['url' => $url_komitmen, 'color' => 'green'])
Klik Link
@endcomponent

@endcomponent