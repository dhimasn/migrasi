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
    <td style="font-weight: bold; width: 100%; text-align: center">BERITA ACARA EVALUASI UJI LAIK OPERASI<br>
                          JARINGAN TELEKOMUNIKASI LAYANAN {{$jenis_layanan_k}}
    </td>
  </tr>
  <tr>
    <td style="font-weight: bold; width: 100%; text-align: center">{{$nama_pt}}
    </td>
  </tr>
  <tr>
    <td style="font-weight: bold; width: 100%; text-align: center">DIREKTORAT JENDERAL PENYELENGGARAAN POS DAN INFORMATIKA
    <br><br></td>
  </tr>
  <tr>
    <td style="width: 10%; text-align: center;">1.</td>
    <td style="width: 90%; text-align: justify;">Pada hari ini {{$tgl_ulo}}, telah selesai dilakukan Evaluasi Uji Laik Operasi dengan metode {{$metode_uji}} pada Jaringan Telekomunikasi Layanan {{$jenis_layanan}} milik {{$nama_pt}} dengan hasil sebagai berikut :<br></td>
  </tr>
  <tr>
    <td style="width: 10%; text-align: center;"></td>
    <td style="width: 90%;">
    {!! $tabel_evaluasi_ulo !!}
    <br></td>
  </tr> 

  <tr>
    <td style="width: 10%; text-align: center;">2.</td>
    <td style="width: 90%; text-align: justify;">Berita Acara ini merupakan bagian yang tidak terpisahkan dari proses uji laik operasi secara keseluruhan sebagaimana tercantum dalam Peraturan Menteri Komunikasi dan Informatika Nomor 7 Tahun 2018 tentang Tata Cara Perizinan dan Layanan Bidang Komunikasi dan Informatika.<br><br><br></td>
  </tr>
</table>

<table>
  <tr>
    <td style="font-size:7px"></td>
  </tr>
  <tr>
    <td class="text-center" style="width:40%"> 
    </td>
    <td class="text-center" style="width:60%">
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