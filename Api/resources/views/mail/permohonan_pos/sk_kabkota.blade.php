
<style type="text/css">
  .t{border-top: 1px solid #000;}
  .b{border-bottom: 1px solid #000;}
  .l{border-left: 1px solid #000;}
  .r{border-right: 1px solid #000;}
  .text-center{text-align: center;}
  .text-justify{text-align: justify;}
</style>

  <?php
    //   $condition2[]   = ['aktif', 1, 'where'];
    //   $condition2[]   = ['id_permohonan', $id_permohonan, 'where'];
    //   $id_jenis_izin    = $this->M_admin->get_master_spec('t_permohonan', 'id_jenis_izin', $condition2)->row_array()['id_jenis_izin'];

    //   if ($id_jenis_izin == 1) {
    //      $id_syarat_izin_s = 1;
    //   }elseif ($id_jenis_izin == 3) {
    //      $id_syarat_izin_s = 20;
    //   }elseif ($id_jenis_izin == 5) {
    //      $id_syarat_izin_s = 25;
    //   }else{
    //      $id_syarat_izin_s = 1;
    //   }

    //   $condition     = [];
    //   $condition[]   = ['aktif', 1, 'where'];
    //   $condition[]   = ['id_permohonan', $id_permohonan, 'where'];
    //   $condition[]   = ['id_syarat_izin_s', $id_syarat_izin_s, 'where'];
    //   $jenis_layanan = $this->M_admin->get_master_spec('t_syarat_izin_p', 'nilai_string', $condition)->result_array();

    //   $get_jenis_layanan = [];
    //   foreach ($jenis_layanan as $jl) {
    //      $get_jenis_layanan[] = $jl['nilai_string'];
    //   }

    //   $count = count($get_jenis_layanan);

    // if ($count == 1) {
    //     $jenis_lay    = $get_jenis_layanan[0];
    // }else{
    //       $data_jen_layanan = array_pop($get_jenis_layanan); // c
    //     $jenis_lay    = implode('; ', $get_jenis_layanan); // a, b
    //     $jenis_lay    .= ' dan '.$data_jen_layanan;
    // }
    
    ?>

<br>
<table>
  <tr>
    <td class="text-center">
      <img width="200" src="/assets/media/logos/logo_kominfo2.png">
    </td>
  </tr>
  <tr><td style="font-size:4px;"></td></tr>
  <tr>
    <td class="text-center">
    <b>KEPUTUSAN MENTERI KOMUNIKASI DAN INFORMATIKA</b>
    </td>
  </tr>
  <tr>
    <td class="text-center">
      <b>REPUBLIK INDONESIA</b>
    </td>
  </tr>
  <!-- Spacing -->
  <tr><td style="font-size:10px;"></td></tr>
  <!-- /Spacing -->
  <tr>
    <td class="text-center">NOMOR <?= $no_sk; ?></td>
  </tr>
  <!-- Spacing -->
  <tr><td style="font-size:10px;"></td></tr>
  <!-- /Spacing -->
  <tr>
    <td class="text-center">TENTANG<br />IZIN PENYELENGGARAAN POS<br /><?= $nama_pt; ?></td>
  </tr>
  <!-- Spacing -->
  <tr><td style="font-size:10px;"></td></tr>
  <!-- /Spacing -->
  <tr>
    <td class="text-center">MENTERI KOMUNIKASI DAN INFORMATIKA<br />REPUBLIK INDONESIA,</td>
  </tr>
  <!-- Spacing -->
  <tr><td style="font-size:10px;"></td></tr>
  <!-- /Spacing -->

  <!-- Content Table -->
  <tr>
    <td style="width: 14%">Menimbang</td>  
    <td style="width: 3%">:</td>  
    <td style="width: 3%">a.</td>  
    <td style="width: 80%; text-align: justify;">bahwa <?= $nama_pt; ?> telah memiliki NIB Nomor <?= $nib; ?>;</td>  
  </tr>
  <tr>
    <td style="width: 14%">&nbsp;</td>  
    <td style="width: 3%">&nbsp;</td>  
    <td style="width: 3%">b.</td>  
    <td style="width: 80%; text-align: justify;">bahwa <?= $nama_pt; ?> telah menyetujui untuk memenuhi komitmen penyelenggaraan pos;</td>  
  </tr>
  <tr>
    <td style="width: 14%">&nbsp;</td>  
    <td style="width: 3%">&nbsp;</td>  
    <td style="width: 3%">c.</td>  
    <td style="width: 80%; text-align: justify;">berdasarkan pertimbangan huruf a dan huruf b, perlu menetapkan keputusan menteri komunikasi dan informatika tentang izin penyelenggaraan pos <?= $nama_pt; ?>.</td>  
  </tr>

  <!-- Spacing -->
  <tr><td style="font-size:10px;"></td></tr>
  <!-- /Spacing -->

  <tr><td style="text-align: center; width: 100%">MEMUTUSKAN :</td></tr>

  <!-- Spacing -->
  <tr><td style="font-size:10px;"></td></tr>
  <!-- /Spacing -->

  <tr>
    <td style="width: 14%">Menetapkan</td>  
    <td style="width: 3%">:</td>  
    <td style="width: 83%; text-align: justify;">KEPUTUSAN MENTERI KOMUNIKASI DAN INFORMATIKA TENTANG IZIN PENYELENGGARAAN POS <?= $nama_pt; ?>.</td>  
  </tr>

  <!-- Spacing -->
  <tr><td style="font-size:10px;"></td></tr>
  <!-- /Spacing -->

  <tr>
    <td style="width: 14%">KESATU</td>  
    <td style="width: 3%">:</td>  
    <td style="width: 83%; text-align: justify;">Memberikan Izin Penyelenggaraan Pos dengan cakupan wilayah <?php echo $jenis_izin ?> untuk jenis layanan : <?php echo $layanan; ?>.</td>  
  </tr>
  <!-- Spacing -->
  <tr><td style="font-size:3px;"></td></tr>
  <!-- /Spacing -->
  <tr>
    <td style="width: 14%">KEDUA</td>  
    <td style="width: 3%">:</td>  
    <td style="width: 83%; text-align: justify;">Menyampaikan Kelengkapan Dokumen Rencana Penyelenggaraan Pos paling lambat 3 bulan sejak izin Penyelenggaraan Pos diterbitkan.</td>  
  </tr>
  <!-- Spacing -->
  <tr><td style="font-size:3px;"></td></tr>
  <!-- /Spacing -->
  <tr>
    <td style="width: 14%">KETIGA</td>  
    <td style="width: 3%">:</td>  
    <td style="width: 83%; text-align: justify;">Izin Penyelenggaraan Pos untuk setiap layanan sebagaimana dimaksud pada diktum pertama efektif berlaku sejak dinyatakan memenuhi pernyataan komitmen.</td>  
  </tr>
  <!-- Spacing -->
  <tr><td style="font-size:3px;"></td></tr>
  <!-- /Spacing -->
  <tr>
    <td style="width: 14%">KEEMPAT</td>  
    <td style="width: 3%">:</td>  
    <td style="width: 83%; text-align: justify;">Keputusan Menteri ini mulai berlaku pada tanggal ditetapkan.</td>  
  </tr>
   <!-- /Content Table -->

  <!-- Spacing -->
  <tr>
    <td style="font-size:3px;"></td>
  </tr>
  <!-- /Spacing -->

  <tr>
    <td class="text-center" style="width:40%">
      <br />
      <br />
      <br />
      <br />
      <br />
      <!-- Qr Code -->
      <!-- /Qr Code -->
    </td>
    <td class="text-center" style="width:60%">
    <span style="text-align:left !important;"><br/>Ditetapkan di Jakarta,<br/>pada tangggal <?= $tanggal_approved; ?><br /></span><br/>
    a.n MENTERI KOMUNIKASI DAN INFORMATIKA <br />
    REPUBLIK INDONESIA<br />
    DIREKTUR JENDERAL<br/>
    PENYELENGGARAAN POS DAN INFORMATIKA<br />
    u.b<br />
    DIREKTUR POS,<br />
    <!-- <img width="40" src="<?php // echo base_url().'berkas/core/images/tanda_tangan/ikhsan_baidirus.jpg' ?>" /> -->
    <br/>
    <u>IKHSAN BAIDIRUS</u>
    </td>
  </tr>
  <tr>
    <td style="width:100%"><br>Salinan Keputusan ini disampaikan kepada Yth. :<br/>
      1. Menteri Komunikasi dan Informatika (sebagai laporan);<br/>
      2. Kepala Badan Koordinasi Penanaman Modal;<br/>
      3. Direktur Jenderal Penyelenggaraan Pos dan Informatika (sebagai laporan);
    </td>
  </tr>
  <tr style="font-size: 10px"><td></td></tr>
</table><br><br><br><br>
<table style="font-size: 8px">
  <tr>
    <td style="width: 100%; font-size: 2px;" class="t l r"></td>
  </tr>
  <tr>
    <td style="width: 1%;" class="l"></td>
    <td style="width: 98%;" class="">UNTUK MENJADI PERHATIAN :</td>
    <td style="width: 1%;" class="r"></td>
  </tr>
  <tr>
    <td style="width: 1%;" class="l"></td>
    <td style="width: 3%;" class="">1.</td>
    <td style="width: 95%; text-align: justify;" class="">Dokumen Izin Penyelenggaraan ini merupakan dokumen asli yang berbentuk elektronik dan menggunakan tanda tangan elektronik yang sah dan memiliki kekuatan hukum.</td>
    <td style="width: 1%;" class="r"></td>
  </tr>
  <tr>
    <td style="width: 1%;" class="l"></td>
    <td style="width: 3%;" class="">2.</td>
    <td style="width: 95%; text-align: justify;" class="">Dokumen Izin Penyelenggaraan ini tidak membutuhkan legalisir.</td>
    <td style="width: 1%;" class="r"></td>
  </tr>
  <tr>
    <td style="width: 1%;" class="l"></td>
    <td style="width: 3%;" class="">3.</td>
    <td style="width: 95%; text-align: justify;" class="">Dokumen Izin Penyelenggaraan ini Dilindungi Berdasarkan UU.38/2009 Tentang Pos Dan UU. 11/2008 Tentang Informasi Dan Transaksi Elektronik, Dan Peraturan Pelaksananya.</td>
    <td style="width: 1%;" class="r"></td>
  </tr>
  <tr>
    <td style="width: 1%;" class="l"></td>
    <td style="width: 3%;" class="">4.</td>
    <td style="width: 95%; text-align: justify;" class="">Segala Penyalahgunaan Terhadap Dokumen Ini Akan Ditindak Sesuai Dengan Ketentuan Peraturan Perundang-undangan.</td>
    <td style="width: 1%;" class="r"></td>
  </tr>
  <tr>
    <td style="width: 1%;" class="l"></td>
    <td style="width: 3%;" class="">5.</td>
    <td style="width: 95%; text-align: justify;" class="">Verifikasi dokumen ini pada QR Code.</td>
    <td style="width: 1%;" class="r"></td>
  </tr>
  <tr>
    <td style="width: 100%; font-size: 2px;" class="b l r"></td>
  </tr>

</table>


