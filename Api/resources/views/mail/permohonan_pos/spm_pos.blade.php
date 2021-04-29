<?php 
$i  = array_map('intval', str_split($npwp));
$npwp_convert 	= $i[0].$i[1].'.'.$i[2].$i[3].$i[4].'.'.$i[5].$i[6].$i[7].'.'.$i[8].'-'.$i[9].$i[10].$i[11].'.'.$i[12].$i[13].$i[14];
?>
<table border="1" cellpadding="3">
    <tr>
        <td>
            <!-- header tabel-->
            <table border="1" cellpadding="2">
                <tr>
                    <td width="10%" >
                        <img width="80" src="/assets/media/logos/logo_kominfo2.png">
                    </td>
                    <td align="center" width="56%">
                        <h4>KEMENTERIAN KOMUNIKASI DAN INFORMATIKA</h4>
                        <h6>DIREKTORAT JENDERAL PENYELENGGARAAN POS DAN INFORMATIKA</h6>
                        
                        <h6>Jl. Medan Merdeka Barat  No. 9 Jakarta Pusat 10110, Call Center 159 | http://sipppdihati.pelayananprimaditjenppi.go.id</h6>
                    </td>
                    <td align="center" width="22%">
                        <h4>S P M</h4>
                        <h6>(Surat Perintah Membayar)</h6>
                    </td>
                    <td align="center" width="12%"><h4>NOMOR</h4><h3><?php echo $id_permohonan; ?></h3></td>
                </tr>
            </table>
            <table border="1" cellpadding="5">
                <tr>
                    <td width="100%" style="font-size:10px;text-align:right">
                        Jakarta, <?php echo $date_now; ?>
                    </td>
                </tr>
            </table>
            <table border="1" cellpadding="5">
                <tr>
                    <td width="32%" style="font-size:10px">
                        Nama Perusahaan
                    </td>
                    <td width="5%" style="font-size:10px;text-align:center">:</td>
                    <td width="63%" style="font-size:10px"><?php echo $nama_perusahaan; ?></td>
                </tr>
                <tr>
                    <td width="32%" style="font-size:10px">
                        Nomor Permohonan
                    </td>
                    <td width="5%" style="font-size:10px;text-align:center">:</td>
                    <td width="63%" style="font-size:10px"><?php echo $no_penyelenggaraan; ?></td>
                </tr>
                <tr>
                    <td width="32%" style="font-size:10px">
                        Tanggal Permohonan
                    </td>
                    <td width="5%" style="font-size:10px;text-align:center">:</td>
                    <td width="63%" style="font-size:10px"><?php echo $tanggal_input; ?></td>
                </tr>
                <tr>
                    <td width="32%" style="font-size:10px">
                        Izin
                    </td>
                    <td width="5%" style="font-size:10px;text-align:center">:</td>
                    <td width="63%" style="font-size:10px">Izin <?php echo $izin_jenis; ?></td>
                </tr>
                <tr>
                    <td width="32%" style="font-size:10px">
                        Jenis Layanan
                    </td>
                    <td width="5%" style="font-size:10px;text-align:center">:</td>
                    <td width="63%" style="font-size:10px"><?php echo $jenis_layanan; ?></td>
                </tr>
                <tr>
                    <td width="32%" style="font-size:10px">
                        Alamat Perusahaan
                    </td>
                    <td width="5%" style="font-size:10px;text-align:center">:</td>
                    <td width="63%" style="font-size:10px"><?php echo $alamat_perusahaan; ?></td>
                </tr>
                <tr>
                    <td width="32%" style="font-size:10px">
                        No. Telp. Perusahaan
                    </td>
                    <td width="5%" style="font-size:10px;text-align:center">:</td>
                    <td width="63%" style="font-size:10px"><?php echo $no_telp_perusahaan; ?></td>
                </tr>
                <tr>
                    <td width="32%" style="font-size:10px">
                        NPWP Perusahaan
                    </td>
                    <td width="5%" style="font-size:10px;text-align:center">:</td>
                    <td width="63%" style="font-size:10px"><?php echo $npwp_convert ?></td>
                </tr>
                <tr>
                    <td width="32%" style="font-size:10px">
                        Jumlah Wajib Bayar
                    </td>
                    <td width="5%" style="font-size:10px;text-align:center">:</td>
                    <td width="63%" style="font-size:10px">Rp. <?php echo number_format($total,2); ?></td>
                </tr>
                <tr>
                    <td width="32%" style="font-size:10px">
                        Setor ke No. Rek. Bank Mandiri
                    </td>
                    <td width="5%" style="font-size:10px;text-align:center">:</td>
                    <td width="63%" style="font-size:10px">103-00-0403138-7 a.n Ditjen Penyelenggaraan Pos dan Informatika</td>
                </tr>
                <tr>
                    <td width="100%" style="font-size:10px;text-align:center">
                        <b>PERHATIAN</b>
                    </td>
                </tr>
            </table>
            <table border="1" cellpadding="5">
                <tr>
                    <td width="5%" style="font-size:10px;text-align:center">
                        1.
                    </td>
                    <td width="95%" style="font-size:10px;text-align:justify">
                        Agar segera dibayar pada bank yang telah ditetapkan, selambat-lambatnya 14 hari kerja sejak <?php echo $date_now; ?>
                    </td>
                </tr>
                <tr>
                    <td width="5%" style="font-size:10px;text-align:center">
                        2.
                    </td>
                    <td width="95%" style="font-size:10px;text-align:justify">
                        Bukti Pembayaran izin wajib diunggah oleh pemohon pada website perizinan www.pelayananprimaditjenppi.go.id dengan menggunakan akun pemohon.
                    </td>
                </tr>
                <tr>
                    <td width="5%" style="font-size:10px;text-align:center">
                        3.
                    </td>
                    <td width="95%" style="font-size:10px;text-align:justify">
                        Apabila dalam waktu 14 (empat belas) hari kerja terhitung sejak tanggal <?php echo $date_now; ?> belum melakukan kewajiban pembayaran biaya izin, maka izin penyelenggaraan pos  dinyatakan tidak berlaku sesuai dengan ketentuan peraturan perundangan-undangan.
                    </td>
                </tr>
                <tr>
                    <td width="5%" style="font-size:10px;text-align:center">
                        4.
                    </td>
                    <td width="95%" style="font-size:10px;text-align:justify">
                        Apabila pembayaran dilakukan setelah 14 hari kerja terhitung sejak tanggal <?php echo $date_now;?> , maka kerugian atas pembayaran tersebut ditanggung oleh Pemohon dan / atau tidak menuntut atas biaya apapun yang sudah disetor ke kas negara.
                    </td>
                </tr>
                <tr>
                    <td width="5%" style="font-size:10px;text-align:center">
                        5.
                    </td>
                    <td width="95%" style="font-size:10px;text-align:justify">
                        Biaya izin tidak dapat ditransfer melalui Anjungan Tunai Mandiri (ATM)
                    </td>
                </tr>
                <tr>
                    <td width="5%" style="font-size:10px;text-align:center">
                        6.
                    </td>
                    <td width="95%" style="font-size:10px;text-align:justify">
                        Untuk informasi lebih lanjut, Saudara dapat menghubungi Pelayanan Terpadu Satu Pintu Kemkominfo di alamat Jl. Merdeka Barat No. 9 Jakarta Pusat atau melalui call center 159 dan alamat e-mail: izinppi@pelayananprimaditjenppi.go.id.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>					