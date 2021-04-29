@extends('layouts.master')
@section('title', 'Penyelenggaraan Telekomunikasi')
@section('content')

<link href="/assets/css/app/select2.css" rel="stylesheet" type="text/css" />

<div class="banner">
    <h3>Komitmen Penyelenggaraan Telekomunikasi</h3>
    <h3>{{ $jenis_izin }}</h3>
</div>
<div class="container">
    <div class="row margin_card">
        <div class="col-xl-12">
            <!--begin::Card-->
            <div class="card card-custom">
                <!--begin::Header-->
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
                <!--end::Header-->
                <!--begin::Body-->
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
                <!--end::Body-->

            </div>
            <!--end::Card-->
        </div>
    </div>

    <div id="add_html_k_komitmen">
    </div>

    <div class="row margin_card">
        <div class="col-xl-12">
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-body py-4">
                    <div class="form-group row mt-4">
                        <label class="col-2 col-form-label"></label>
                        <div class="col-2">
                            <input type="text" class="form-control" id="kode_unik" placeholder="Kode Unik">
                        </div>
                        <div class="col-2">
                            <img id="gambar_cap">
                        </div>
                        <div class="col-1">
                            <a href="javascript:;" onclick="ReloadCap()" class="btn btn-icon btn-light-success btn-circle btn-sm mr-2"><i class="flaticon2-refresh-arrow"></i></a>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row mt-4">
                        <div class="col-3">
                        </div>
                        <div class="col-9">
                            <input type="hidden" id="type_izin_jenis_tel" value="{{ $type_izin_jenis_tel }}">
                            <input type="hidden" id="no_sk_izin" value="{{ $no_sk_izin }}">
                            <input type="hidden" id="id_permohonan">
                            <button type="reset" class="btn btn-secondary" onclick="BackPage()">Batal</button>
                            <button type="button" class="btn btn-primary mr-2" onclick="ModalPostPermohonanKomit()">Kirim Komitmen</button>
                        </div>
                    </div>
                </div>
                <!--end::Form-->
            </div>
            <!--end::Card-->

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

<script src="/assets/js/app/tel/set_model_permohonan_komit.js"></script>
<script src="/assets/js/app/tel/post_permohonan_komit.js"></script>
<script src="/assets/js/app/tools/change_label_file.js"></script>

@endsection