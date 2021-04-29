@extends('layouts.master')
@section('title', 'Penyelenggaraan POS')
@section('content')
<link href="/assets/css/app/select2.css" rel="stylesheet" type="text/css" />
<div class="banner">
    <h3>Komitmen Penyelenggaraan POS</h3>
    <h3>{{ $jenis_izin }}</h3>
</div>
<div class="container">
    <div class="row margin_card">
        <div class="col-xl-12">
            <div class="card card-custom">
                <div class="card-header h-auto py-4">
                    <div class="card-title">
                        <span class="card-icon">
                            <i class="flaticon2-checking text-primary"></i>
                        </span>
                        <h3 class="card-label">
                            Susunan Kepemilikian Saham
                        </h3>
                    </div>
                </div>
                <div class="card-body py-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Nama</th>
                                <th scope="col">Kewarganegaraan</th>
                                <th scope="col">Nilai Saham</th>
                                <th scope="col">Prosentase</th>
                            </tr>
                        </thead>
                        <tbody id="data_saham">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
                            <span class="form-control-plaintext font-weight-bolder" id="noskizin"></span>
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
    <div id="add_html_k_komitmen">
    </div>
    <div class="row margin_card">
        <div class="col-xl-12">
            <div class="card card-custom">
                <div class="card-footer">
                    <div class="row mt-4">
                        <div class="col-5">
                        </div>
                        <div class="col-7">
                            <input type="hidden" id="type_izin_jenis_pos" value="{{ $type_izin_jenis_pos }}">
                            <input type="hidden" id="no_sk_izin" value="{{ $no_sk_izin }}">
                            <input type="hidden" id="id_permohonan">
                            <input type="hidden" id="idpermohonan" value="{{ $idpermohonan }}">
                            <button type="reset" class="btn btn-secondary" onclick="BackPage()">Batal</button>
                            <button type="button" class="btn btn-primary mr-2" onclick="ModalPostPermohonanKomit()">Kirim Komitmen</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modal_proses_permohonan_komit" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Proses Permohonan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-custom alert-primary" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">Apakah anda yakin ingin mengirim Permohonan Komitmen ini ? </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                            <button type="button" class="btn btn-primary" onclick="PostPermohonanKomit()">Ya</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modal_merge_komitmen" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Merge Row Tabel Komitmen</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-custom alert-primary" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">Apakah anda yakin ingin mengirim Permohonan Komitmen ini ? </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                            <button type="button" class="btn btn-primary" onclick="Merge()">Ya</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/assets/js/app/pos/set_model_permohonan_komit.js"></script>
<script src="/assets/js/app/pos/post_permohonan_komit.js"></script>
<script src="/assets/js/app/tools/change_label_file.js"></script>
@endsection