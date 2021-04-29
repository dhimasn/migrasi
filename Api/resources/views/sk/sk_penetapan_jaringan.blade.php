<style type="text/css">  
  .t{border-top: 1px solid #000;}
  .b{border-bottom: 1px solid #000;}
  
  .l{border-left: 1px solid #000;}
  .r{border-right: 1px solid #000;}
  .text-center{text-align: center;}
  .text-left{text-align: left;}
  .text-justify{text-align: justify;}

  .table_layanan {
  border: 1px solid black;
  }
  table{
    border-collapse: collapse;
  }
  .th_layanan, .td_layanan{
  border: 1px solid black;
  }
</style>

<table>
  <tr>
    <td style="font-size:10px">&nbsp;</td>
  </tr>
  <tr>
    <td class="text-center">
        <img width="200" src="{{env('APP_URL')}}/assets/media/logos/kominfo.png">
    </td>
  </tr>
  <tr>
    <td style="font-size:10px">&nbsp;</td>
  </tr>
  <tr>
    <td style="width: 100%; text-align: center">PENETAPAN KOMITMEN<br>PENYELENGGARAN JARINGAN {{$jenis_layanan_k}}<br/>{{$nama_pt}}
    </td>
  </tr>
  <tr>
    <td style="font-size:10px">&nbsp;</td>
  </tr>
  <tr>
      <td width="20%">Menetapkan</td>
      <td width="3%">:</td>
      <td width="72%" style="text-align:justify">{{$nama_pt}}<br>Sebagai Penyelenggara jaringan telekomunikasi berdasarkan Keputusan Menteri Komunikasi dan Informatika No. {{$no_sk}} tentang Izin Penyelenggaraaan Jaringan Telekomunikasi {{$nama_pt}}<br></td> 
  </tr>
  <tr>
      <td width="5%">1.</td>
      <td width="95%" style="text-align:justify">telah memenuhi pernyataan komitmen persyaratan perizinan dan penyelenggaraan jaringan telekomunikasi berdasarkan permohonan nomor {{$no_permohonan}}.</td>
  </tr>
  <tr>
      <td width="5%">2.</td>
      <td width="90%" style="text-align:justify">wajib memenuhi komitmen pembangunan dan/atau penyediaan jaringan telekomunikasi sebagai berikut:<br>Penyelenggaraan Jaringan {{$jenis_layanan}} {{$dengan_teknologi}}<br></td>
  </tr>

  {!!$list_komitmen!!}

  <tr>
      <td width="5%">3.</td>
      <td width="95%" style="text-align:justify">wajib memenuhi komitmen kinerja pelayanan jaringan telekomunikasi sebagai berikut: <br>Penyelenggaraan Jaringan {{$jenis_layanan}} {{$dengan_teknologi}}<br></td>
  </tr>
 
  {!!$list_kinerja!!}
</table>

<table>
   <tr>
    <td style="font-size:10px"></td>
  </tr>
  <tr>
    <td style="width:45%"></td>
    <td style="width:55%">Ditetapkan di Jakarta<br />pada tanggal {{$tanggal}}</td>
  </tr>
  <tr>
    <td style="font-size:7px"></td>
  </tr>
  <tr>
    <td class="text-center" style="width:45%"> 
    </td>
    <td class="text-center" style="width:55%">a.n MENTERI KOMUNIKASI DAN INFORMATIKA <br />
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
    <td style="font-size:4px"></td>
  </tr>
  <tr>
    <td width="95%" style="text-align:left;">Salinan Keputusan ini disampaikan kepada Yth. :</td>
  </tr>
  <tr>
    <td width="95%" style="text-align:left; font-size:9px">1. Menteri Komunikasi dan Informatika (sebagai laporan);</td>
  </tr>
  <tr>
    <td width="95%" style="text-align:left; font-size:9px">2. Kepala Badan Koordinasi Penanaman Modal;</td>
  </tr>
  <tr>
    <td width="95%" style="text-align:left; font-size:9px">3. Direktur Jenderal Penyelenggaraan Pos dan Informatika (sebagai laporan);</td>
  </tr>
  <tr>
    <td style="font-size:4px"></td>
  </tr>
      </table>
  <table style="border-top-width:0.5; border-left-width:0.5; border-bottom-width:0.5; border-right-width:0.5;">
  <tr>
    <td width="95%" style="text-align:left;font-size:8px; font-weight:bold;">UNTUK MENJADI PERHATIAN :</td>
  </tr>
  <tr style="font-size:7px">
    <td width="3%">1.</td>
    <td width="92%" style="text-align:left; font-size:8px;">Dokumen Izin Penyelenggaraan ini merupakan dokumen asli yang berbentuk elektronik dan menggunakan tanda tangan elektronik yang sah dan memiliki kekuatan hukum.
    </td>
  </tr>
  <tr style="font-size:7px">
    <td width="3%">2.</td>
    <td width="92%" style="text-align:left; font-size:8px;">Dokumen Izin Penyelenggaraan ini tidak membutuhkan legalisir.</td>
  </tr>
  <tr style="font-size:7px">
    <td width="3%">3.</td>                        
    <td width="92%" style="text-align:left; font-size:8px;">Dokumen Izin Penyelenggaraan ini dilindungi berdasarkan UU No. 36/1999 tentang Telekomunikasi dan UU No. 11/2008 tentang Informasi dan Transaksi Elektronik, dan Peraturan Pelaksananya.</td>
  </tr>
  <tr style="font-size:7px">
    <td width="3%">4.</td>
    <td width="92%" style="text-align:left; font-size:8px;">Segala Penyalahgunaan Terhadap Dokumen ini akan ditindak Sesuai dengan Ketentuan Peraturan Perundang-undangan.</td>
  </tr>
  <tr style="font-size:7px">
    <td width="3%">5.</td>
    <td width="92%" style="text-align:left; font-size:8px;">Verifikasi dokumen ini pada QR Code.</td>
  </tr>
</table>
