@extends('layouts.master')
@section('title', 'Permohonan Penyelenggaraan POS')
@section('content')
<div class="banner">
    <h3>Permohonan Izin Penyelenggaraan</h3>
    <h3>{{ $jenis_izin }}</h3>
</div>
<div class="container">
    <div class="row margin_card">
        <div class="col-xl-12">
            <div class="card card-custom">
                <div class="card-body py-4">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card card-custom">
                                <div class="card-header h-auto py-4">
                                    <div class="card-title">
                                        <span class="card-icon">
                                            <i class="flaticon2-checking text-primary"></i>
                                        </span>
                                        <h3 class="card-label">
                                            Data Perusahaan
                                        </h3>
                                    </div>
                                </div>
                                <div class="card-body py-4">
                                    <div class="form-group row my-2">
                                        <label class="col-4 col-form-label">NIB</label>
                                        <div class="col-8">
                                            <span class="form-control-plaintext font-weight-bolder" id="nib"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row my-2">
                                        <label class="col-4 col-form-label">Jenis Penanaman Modal</label>
                                        <div class="col-8">
                                            <span class="form-control-plaintext font-weight-bolder" id="jenis_pm"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row my-2">
                                        <label class="col-4 col-form-label">Jenis Perusahaan</label>
                                        <div class="col-8">
                                            <span class="form-control-plaintext font-weight-bolder" id="jenis_per"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row my-2">
                                        <label class="col-4 col-form-label">Nama Perusahaan</label>
                                        <div class="col-8">
                                            <span class="form-control-plaintext font-weight-bolder" id="nama_per"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row my-2">
                                        <label class="col-4 col-form-label">No NPWP</label>
                                        <div class="col-8">
                                            <span class="form-control-plaintext font-weight-bolder" id="no_npwp"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row my-2">
                                        <label class="col-4 col-form-label">No Telp/No Hp</label>
                                        <div class="col-8">
                                            <span class="form-control-plaintext font-weight-bolder" id="no_telp_per"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card card-custom">
                                <div class="card-header h-auto py-4">
                                    <div class="card-title">
                                        <span class="card-icon">
                                            <i class="flaticon2-user text-primary"></i>
                                        </span>
                                        <h3 class="card-label">
                                            Data Pemohon
                                        </h3>
                                    </div>
                                </div>
                                <div class="card-body py-4">
                                    <div class="form-group row my-2">
                                        <label class="col-4 col-form-label">Nama</label>
                                        <div class="col-8">
                                            <span class="form-control-plaintext font-weight-bolder" id="nama_pem"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row my-2">
                                        <label class="col-4 col-form-label">NIK</label>
                                        <div class="col-8">
                                            <span class="form-control-plaintext font-weight-bolder" id="nik"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row my-2">
                                        <label class="col-4 col-form-label">Email</label>
                                        <div class="col-8">
                                            <span class="form-control-plaintext font-weight-bolder" id="email"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row my-2">
                                        <label class="col-4 col-form-label">No Telp/No Hp</label>
                                        <div class="col-8">
                                            <span class="form-control-plaintext font-weight-bolder" id="no_telp"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row margin_card">
        <div class="col-xl-12">
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <div class="card-title">
                        <span class="card-icon">
                            <i class="flaticon2-laptop text-primary"></i>
                        </span>
                        <h3 class="card-label">
                            Layanan
                        </h3>
                    </div>
                </div>
                <div class="form">
                    <div class="card-body">
                        <input type="hidden" id="id_perusahaan">
                        <input type="hidden" id="jenisizin" value="<?= $id_jenis_izin; ?>">
                        <div class="form-group row mt-4">
                            <label class="col-2 col-form-label"></label>
                            <div class="col-10">
                                <label class="checkbox">
                                    <input type="checkbox" id="disclaimer" value='1'>
                                    <span></span>
                                    &nbsp;Saya telah membaca&nbsp;<a href='#' data-toggle="modal" data-target="#disclaimer_modal">Persetujuan Disclaimer</a>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row mt-4">
                            <label class="col-2 col-form-label">Jenis Layanan</label>
                            <div class="col-10">
                                <select class="form-control select2 select2-multiple" id="jenislayanan" name="param" multiple="multiple">
                                    <option value=''>-- Pilih --</option>
                                    <option value='Komunkasi Tertulis dan/atau Surat Elektronik'>Komunkasi Tertulis dan/atau Surat Elektronik</option>
                                    <option value='Paket'>Paket</option>
                                    <option value='Logistik'>Logistik</option>
                                    <option value='Transaksi Keuangan'>Transaksi Keuangan</option>
                                    <option value='Keagenan POS'>Keagenan POS</option>
                                </select>
                                <span class="form-text text-muted">Dapat dipilih lebih dari satu</span>
                            </div>
                        </div>
                        <?php
                        if($id_jenis_izin=='1'){
                        ?>
                        <div class="form-group row mt-4">
                            <label class="col-2 col-form-label">Cakupan Wilayah</label>
                            <div class="col-10">
                                <select class="form-control" id="cakupan_wilayah">
                                    <option value=''>-- Pilih --</option>
                                    <?php
                                    foreach ($provinsi as $key => $value) {
                                        echo"<option value='".$value->id_provinsi."'>".$value->nm_provinsi."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <?php
                        }elseif($id_jenis_izin=='6'){
                        ?>
                        <div class="form-group row mt-4">
                            <label class="col-2 col-form-label">Cakupan Wilayah</label>
                            <div class="col-10">
                                <select class="form-control" id="provinsi">
                                    <option value=''>-- Pilih --</option>
                                    <?php
                                    foreach ($provinsi as $key => $value) {
                                        echo"<option value='".$value->id_provinsi."'>".$value->nm_provinsi."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mt-4">
                            <label class="col-2 col-form-label"></label>
                            <div class="col-10">
                                <select class="form-control " id="cakupan_wilayah" >
                                    <option value=''>-- Pilih --</option>
                                </select>
                            </div>
                        </div>
                        <?php
                        }elseif($id_jenis_izin=='7'){
                        ?>
                        <div class="form-group row mt-4">
                            <label class="col-2 col-form-label">Cakupan Wilayah</label>
                            <div class="col-10">
                                <select class="form-control select2 select2-multiple" id="cakupan_wilayah" multiple="multiple">
                                    <option value=''>-- Pilih --</option>
                                    <?php
                                    foreach ($provinsi as $key => $value) {
                                        echo"<option value='".$value->id_provinsi."'>".$value->nm_provinsi."</option>";
                                    }
                                    ?>
                                </select>
                                <span class="form-text text-muted">Dapat dipilih lebih dari satu</span>
                            </div>
                        </div>
                        <?php
                        }else{
                            echo'';
                        }
                        ?>
                        <!-- <div class="form-group row mt-4">
                            <label class="col-2 col-form-label">Pernyataan Kesanggupan</label>
                            <div class="col-10">
                                <input type="file" class="form-control" id="filepernyataan">
                            </div>
                        </div> -->
                    </div>
                    <div class="card-footer">
                        <div class="row mt-4">
                            <div class="col-2">
                            </div>
                            <div class="col-10">
                                <button type="reset" class="btn btn-secondary" onclick="BackPage()">Batal</button>
                                <button type="button" class="btn btn-primary mr-2" onclick="ModalPostPermohonan()">Kirim Permohonan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modal_proses_permohonan" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Pernyataan Kesanggupan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body" style='text-align:justify'>
                            <!-- <div class="alert alert-custom alert-primary" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">Apakah anda yakin ingin mengirim Permohonan ini ? </div>
                            </div> -->
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.
                            <br>
                            <br>
                            <label class="checkbox">
                                <input type="checkbox" id="kesanggupan" value='1'>
                                <span></span>
                                &nbsp;Saya merasa sanggup ....
                            </label>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" onclick="PostPermohonan()">Proses</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="disclaimer_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Disclaimer Telekomunikasi</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body" style='text-align:justify'>
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/assets/js/app/pos/post_permohonan.js"></script>
@endsection