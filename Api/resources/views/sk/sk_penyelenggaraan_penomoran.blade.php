<style type="text/css">  
  .t{border-top: 1px solid #000;}
  .b{border-bottom: 1px solid #000;}
  .b-2{border-bottom: 2px solid #000;}
  .l{border-left: 1px solid #000;}
  .r{border-right: 1px solid #000;}
  .text-center{text-align: center;}
  .text-left{text-align: left;}
  .text-justify{text-align: justify;}
</style>

<table>
  <tr>
    <td class="text-center b-2" style="width: 21%;">
        <img width="100" src="{{env('APP_URL')}}/assets/media/logos/logo_kominfo.png">
    </td>
    <td class="text-left b-2" style="width: 79%;"><span style="font-size: 18.5px;"><br/></span><b>KEMENTERIAN KOMUNIKASI DAN INFORMATIKA<br/>
        DIREKTORAT JENDERAL PENYELENGGARAAN POS DAN INFORMATIKA<br/>
      </b><i style="letter-spacing: 2px;">"Menuju Masyarakat Informasi Indonesia "</i><br/>
      Jl. Medan Merdeka Barat No.9, Jakarta 10110, Pusat Layanan Informasi : 159 
    </td>
  </tr>
  <tr>
    <td style="font-size:12px">&nbsp;</td>
  </tr>
  <tr>
    <td class="text-center" style="width: 100%; text-transform: uppercase;">PENETAPAN {{$jenis_penomoran_u}}</td>
  </tr>
  <tr>
    <td style="font-size:12px">&nbsp;</td>
  </tr>
  <tr>
    <td class="text-center" style="width: 100%">NOMOR : {{$no_sk_penomoran}}</td>
  </tr>
  <br />

  <tr>
      <td width="15%">Dasar</td>
      <td width="3%">:</td>
      <td width="3%">a. </td>
      <td width="74%" style="text-align:justify">Peraturan Menteri Komunikasi dan Informatika Nomor 14 Tahun 2018 tentang Rencana Dasar Teknis (<i>Fundamental Technical Plan</i>) Telekomunikasi Nasional;<br />
      </td>
  </tr>
  <tr>
      <td width="15%"></td>
      <td width="3%"></td>
      <td width="3%">b. </td>
      <td width="74%" style="text-align:justify">Keputusan Menteri Komunikasi dan Informatika Nomor 793 Tahun 2018 tentang Pemberian Kewenangan Penandatanganan Dokumen Bidang Penyelenggaraan Pos dan Informatika Dalam Rangka Pelayanan Prima di Lingkungan Direktorat Jenderal Penyelenggaraan Pos dan Informatika;<br />
      </td>
  </tr>
  <tr>
      <td width="15%"></td>
      <td width="3%"></td>
      <td width="3%">c. </td>
      <td width="74%" style="text-align:justify">Keputusan Direktur Jenderal Penyelenggaraan Pos dan Informatika Nomor 167 Tahun 2018 tentang Pemberian Kewenangan Penandatanganan Dokumen Bidang Penyelenggaraan Pos dan Informatika Dalam Rangka Pelayanan Prima di Lingkungan Direktorat Jenderal Penyelenggaraan Pos dan Informatika;<br />
      </td>
  </tr>
  <tr>
      <td width="15%"></td>
      <td width="3%"></td>
      <td width="3%">d. </td>
      <td width="74%" style="text-align:justify">Permohonan Penetapan Penomoran Nomor {{$no_permohonan}} tanggal {{$tanggal_permohonan}} <br />
      </td>
  </tr>
  <tr>
    <td style="width: 100%;">Menetapkan {{$jenis_penomoran}} {{$nomor_penomoran}} untuk Penyelenggaraan Jasa Nilai Tambah Teleponi {{$jenis_penomoran}} kepada :<br/></td>
  </tr>
  <tr>
      <td width="4%">&nbsp;</td>
      <td width="25%">Nama Perusahaan</td>
      <td width="3%">:</td>
      <td width="63%" style="text-align:justify">{{$nama_pt}}<br />
      </td>
  </tr>
  <tr>
      <td width="4%">&nbsp;</td>
      <td width="25%">NPWP</td>
      <td width="3%">:</td>
      <td width="63%" style="text-align:justify">{{$npwp}}<br />
      </td>
  </tr>
   <tr>
      <td width="4%">&nbsp;</td>
      <td width="25%">Alamat</td>
      <td width="3%">:</td>
      <td width="63%" style="text-align:justify">{{$alamat_perusahaan}}<br />
      </td>
  </tr>
  <tr>
    <td style="width: 100%;">Dalam menggunakan {{$jenis_penomoran}} tersebut di atas, {{$nama_pt}} wajib melaporkan penggunaannya setiap 1 (satu) tahun sejak ditetapkan.<br />
    </td>
  </tr>
  <tr>
    <td style="width: 100%;">Direktorat Jenderal Penyelenggaraan Pos dan Informatika akan melakukan monitoring dan evaluasi terhadap penggunaan {{$jenis_penomoran}} tersebut.<br />
    </td>
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
    <td style="width:40%"> 
    </td>
    <td class="text-center" style="width:60%">a.n  MENTERI KOMUNIKASI DAN INFORMATIKA<br />
    REPUBLIK INDONESIA<br />
    DIREKTUR JENDERAL PENYELENGGARAAN <br />POS DAN INFORMATIKA<br/>
    u.b<br/>
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