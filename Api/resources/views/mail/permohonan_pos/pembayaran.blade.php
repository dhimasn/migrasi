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
                                            Data Permohonan
                                        </h3>
                                    </div>
                                </div>
                                <div class="card-body py-4">
                                    <div class="form-group row my-2">
                                        <label class="col-4 col-form-label">Nama Pemohon</label>
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
                                    <div class="form-group row my-2">
                                        <label class="col-4 col-form-label">No Penyelenggaraan</label>
                                        <div class="col-8">
                                            <span class="form-control-plaintext font-weight-bolder" id="no_penyelenggaraan"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row my-2">
                                        <label class="col-4 col-form-label">No SK Izin</label>
                                        <div class="col-8">
                                            <span class="form-control-plaintext font-weight-bolder" id="no_sk_izin"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row my-2">
                                        <label class="col-4 col-form-label">Izin Jenis</label>
                                        <div class="col-8">
                                            <span class="form-control-plaintext font-weight-bolder" id="izin_jenis"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row my-2">
                                        <label class="col-4 col-form-label">Waktu Pengajuan</label>
                                        <div class="col-8">
                                            <span class="form-control-plaintext font-weight-bolder" id="tanggal_input"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row my-2">
                                        <label class="col-4 col-form-label">Cakupan Wilayah</label>
                                        <div class="col-8">
                                            <span class="form-control-plaintext font-weight-bolder">
                                                <?php
                                                foreach ($data_wilayah as $key => $value) {
                                                    echo $value.'<br>';
                                                }
                                                ?>
                                            </span>
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
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-layanan-tab" data-toggle="pill" href="#pills-layanan" role="tab" aria-controls="pills-layanan" aria-selected="true"><span class="card-icon"><i class="flaticon2-laptop text-warning"></i></span>&nbsp;Layanan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-riwayat-tab" data-toggle="pill" href="#pills-riwayat" role="tab" aria-controls="pills-riwayat" aria-selected="false"><span class="card-icon"><i class="flaticon2-layers text-warning"></i></span>&nbsp;Riwayat</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-layanan" role="tabpanel" aria-labelledby="pills-layanan-tab">
                        <div class="form">
                            <div class="card-body">
                                <input type="hidden" id="id_permohonan" value='{{ $id_permohonan }}'>
                                <div class="form-group row mt-4">
                                    <label class="col-2 col-form-label">Bukti Pembayaran</label>
                                    <div class="col-10">
                                        <input type="file" class="form-control" id="bukti">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row mt-4">
                                    <div class="col-2">
                                    </div>
                                    <div class="col-10">
                                        <button type="reset" class="btn btn-secondary" onclick="BackPage()">Batal</button>
                                        <button type="button" class="btn btn-primary mr-2" onclick="ModalPostUploadBuktiPembayaran()">Unggah</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-riwayat" role="tabpanel" aria-labelledby="pills-riwayat-tab">
                        <div class="card-body">
                            <?php
                            $no = 1;
                            if($data_log_pembayaran==NULL){
                                echo'
                                <div class="form-group row mt-4">
                                    <label class="col-12 col-form-label">Belum ada file yang diunggah.</label>
                                </div>
                                ';
                            }else{
                                echo'<div class="accordion accordion-light accordion-light-borderless accordion-svg-toggle" style="margin:unset;" id="accordionExample7">';
                                foreach ($data_log_pembayaran as $key => $value) {
                                ?>
                                <div class="card">
                                    <div class="card-header" id="heading<?= $value->id; ?>7">
                                        <div class="card-title collapsed" data-toggle="collapse" data-target="#collapse<?= $value->id; ?>7" aria-expanded="false">
                                            <span class="svg-icon svg-icon-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <polygon points="0 0 24 0 24 24 0 24">
                                                    </polygon>
                                                    <path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" fill="#000000" fill-rule="nonzero">
                                                    </path>
                                                    <path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999) ">
                                                    </path>
                                                    </g>
                                                </svg>
                                            </span>                                
                                            <div class="card-label pl-4">Foto Bukti Pembayaran <?= $no++; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="collapse<?= $value->id; ?>7" class="collapse" data-parent="#accordionExample7" style="">
                                        <div class="card-body pl-12">
                                            <div class="form-group row my-2">
                                                <label class="col-4 col-form-label">Tanggal Input</label>
                                                <div class="col-8">
                                                    <?= $value->tanggal_input; ?>
                                                </div>
                                            </div>
                                            <div class="form-group row my-2">
                                                <label class="col-4 col-form-label">Nama File</label>
                                                <div class="col-8">
                                                    <?= $value->nama; ?>
                                                </div>
                                            </div>
                                            <div class="form-group row my-2">
                                                <label class="col-4 col-form-label">File</label>
                                                <div class="col-8">
                                                    <a href='/pos/permohonan/file_pembayaran/<?= md5($value->id); ?>' target='__blank' style='font-size: unset;padding-left: unset;'><font color='black'>Lihat File</font></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php }
                                echo'</div>';
                            }?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modal_proses_permohonan" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Form Konfirmasi</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-custom alert-primary" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">Apakah anda yakin ingin mengunggah file ini ? </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                            <button type="button" class="btn btn-primary" onclick="PostUploadBuktiPembayaran()">Ya</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/assets/js/app/pos/post_pembayaran_permohonan.js"></script>
@endsection