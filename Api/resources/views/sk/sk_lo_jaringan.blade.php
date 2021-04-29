<style type="text/css">
  .t{border-top: 1px solid #000;}
  .b{border-bottom: 1px solid #000;}
  .l{border-left: 1px solid #000;}
  .r{border-right: 1px solid #000;}
  .text-center{text-align: center;}
  .text-justify{text-align: justify;}
</style>

<br />

<table>
  <tr>
    <td class="text-center">
        <img width="100" src="{{env('APP_URL')}}/assets/media/logos/logo_kominfo.png">
    </td>
  </tr>
  <tr>
    <td style="font-size:3px">&nbsp;</td>
  </tr>
  <tr>
    <td style="width: 100%; text-align: center">SURAT KETERANGAN LAIK OPERASI</td>
  </tr>
  <tr>
    <td style="width: 38%; text-align: left"></td>
    <td style="width: 62%; text-align: left">Nomor : {{$no_sk}}<br/></td>
  </tr>
  <tr>
    <td style="width: 15%">Dasar :</td>
    <td style="width: 5%">a.</td>
    <td style="width: 80%; text-align: justify;">Keputusan Menteri Komunikasi dan Informatika Nomor : {{$no_sk_izin}} tanggal {{$tanggal_sk_izin}} tentang Izin {{$jenis_layanan}}<br></td>
  </tr>
  <tr>
    <td style="width: 15%"></td>
    <td style="width: 5%">b.</td>
    <td style="width: 80%; text-align: justify;">Surat Tugas Direktur Telekomunikasi {{$no_surat_tugas_dir}} untuk melaksanakan Uji Laik Operasi (ULO) {{$jenis_layanan}}
    <br></td>
  </tr>
  <tr>
    <td style="width: 15%"></td>
    <td style="width: 5%">c.</td>
    <td style="width: 80%; text-align: justify;">Berita Acara Evaluasi Hasil Pelaksanaan Uji Laik Operasi {{$jenis_layanan}} tanggal {{$tgl_ulo}};
    <br><br></td>
  </tr>
  <tr>
    <td style="width: 100%; text-align: left">Ditetapkan bahwa hasil pembangunan sarana dan prasarana yang dilaksanakan oleh :<br></td>
  </tr>
  <tr>
    <td style="width: 20%; text-align: center;">a.</td>
    <td style="width: 25%; text-align: justify;">Nama Perusahaan</td>
    <td style="width: 5%; text-align: justify;">:</td>
    <td style="width: 50%; text-align: left;">{{$nama_pt}}</td>
  </tr>
  <tr>
    <td style="width: 20%; text-align: center;">b.</td>
    <td style="width: 25%; text-align: justify;">Jenis Penyelenggaraan</td>
    <td style="width: 5%; text-align: justify;">:</td>
    <td style="width: 50%; text-align: left;">{{$jenis_layanan}}<br></td>
  </tr>
  <tr>
    <td style="width: 100%; text-align: justify;">Telah memenuhi syarat kelaikan operasi untuk penyelenggaraan telekomunikasi pada alamat {{$alamat}} sebagaimana tercantum dalam lampiran BERITA ACARA EVALUASI UJI LAIK OPERASI sesuai Keputusan Direktur Jenderal Pos dan Telekomunikasi No. 191/Dirjen/2009 tentang Tata Cara Pelaksanaan Uji Laik Operasi Penyelenggaraan Telekomunikasi yang merupakan bagian tidak terpisahkan dari SURAT KETERANGAN LAIK OPERASI Nomor : {{$no_sk}}<br></td>
  </tr>
  <tr>
    <td style="font-size:8px"></td>
  </tr>
</table>

<table>
  <tr>
    <td style="width:40%"></td>
    <td style="width:60%">Ditetapkan di Jakarta<br />pada tanggal {{$tanggal}}</td>
  </tr>
  <tr>
    <td style="font-size:7px"></td>
  </tr>
  <tr>
    <td class="text-center" style="width:40%"> 
    </td>
    <td class="text-center" style="width:60%">a.n MENTERI KOMUNIKASI DAN INFORMATIKA <br />
    REPUBLIK INDONESIA<br />
    DIREKTUR JENDERAL<br/>
    PENYELENGGARAAN POS DAN INFORMATIKA<br />
    u.b<br />
    {{$jabatan}},<br />
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    {{$nama_penanda_tangan}}<br/>
    </td>
  </tr>
</table>

<table>
  <tr>
    <td style="font-size:7px"></td>
  </tr>
</table>
<table style="border-top-width:0.5; border-left-width:0.5; border-bottom-width:0.5; border-right-width:0.5;">
  <tr>
    <td width="95%" style="text-align:left; font-weight:bold; ">UNTUK MENJADI PERHATIAN :</td>
  </tr>
  <tr style="font-size:9px">
    <td width="3%">1.</td>
    <td width="92%" style="text-align:left;">Dokumen ini merupakan dokumen asli yang berbentuk elektronik dan menggunakan tanda tangan elektronik yang sah dan memiliki kekuatan hukum.</td>
  </tr>
  <tr style="font-size:9px">
    <td width="3%">2.</td>
    <td width="92%" style="text-align:left;">Dokumen ini tidak membutuhkan legalisir.</td>
  </tr>
  <tr style="font-size:9px">
    <td width="3%">3.</td>                        
    <td width="92%" style="text-align:left;">Dokumen ini Dilindungi Berdasarkan UU.36 Tahun 1999 Tentang Telekomunikasi Dan UU. 11 Tahun 2008 Tentang Informasi Dan Transaksi Elektronik, Dan Peraturan Pelaksananya.</td>
  </tr>
  <tr style="font-size:9px">
    <td width="3%">4.</td>
    <td width="92%" style="text-align:left;">Segala Penyalahgunaan Terhadap Dokumen Ini Akan Ditindak Dengan Hukum Dan Peraturan Yang Berlaku.</td>
  </tr>
  <tr style="font-size:9px">
    <td width="3%">5.</td>
    <td width="92%" style="text-align:left;">Verifikasi dokumen ini pada QR Code.</td>
  </tr>
</table>
