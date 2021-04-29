@extends('layouts.master')
@section('title', 'Penambahan Penomoran Telekomunikasi')
@section('content')

<link href="/assets/css/app/select2.css" rel="stylesheet" type="text/css" />

<div class="banner">
    <h3>Penambahan Penomoran</h3>
    <h3>{{ $jenis_izin }} Telekomunikasi</h3>
</div>
<div class="container">
    <div class="row margin_card">
        <div class="col-xl-12">
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-body py-4">
                    <div class="row">
                        <div class="col-xl-6">
                            <!--begin::Card-->
                            <div class="card card-custom">
                                <!--begin::Header-->
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
                                <!--end::Header-->
                                <!--begin::Body-->
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
                                <!--end::Body-->

                            </div>
                            <!--end::Card-->
                        </div>
                        <div class="col-xl-6">
                            <!--begin::Card-->
                            <div class="card card-custom">
                                <!--begin::Header-->
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
                                <!--end::Header-->
                                <!--begin::Body-->
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
                                <!--end::Body-->

                            </div>
                            <!--end::Card-->
                        </div>
                    </div>
                </div>
                <!--end::Body-->

            </div>
            <!--end::Card-->
        </div>
    </div>

    <div id="add_html_penambahan_penomoran">
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
                            <input type="hidden" id="id_perusahaan">
                            <input type="hidden" id="jenis_layanan" value="{{ $jenis_layanan }}">
                            <input type="hidden" id="type_izin_jenis_tel" value="{{ $type_izin_jenis_tel }}">
                            <input type="hidden" id="no_sk_izin" value="{{ $no_sk_izin }}">
                            <input type="hidden" id="no_sk_izin_penomoran" value="{{ $no_sk_izin_penomoran }}">
                            <input type="hidden" id="id_permohonan">
                            <button type="reset" class="btn btn-secondary" onclick="BackPage()">Batal</button>
                            <button type="button" class="btn btn-primary mr-2" onclick="ModalPostPenambahanPenomoran()">Kirim Permohonan Penomoran</button>
                        </div>
                    </div>
                </div>
                <!--end::Form-->
            </div>
            <!--end::Card-->

            <div class="modal fade" id="modal_proses_penambahan_penomoran" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Proses Permohonan Penambahan Penomoran</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-custom alert-primary" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">Apakah anda yakin ingin mengirim Permohonan Penambahan Penomoran ini ? </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                            <button type="button" class="btn btn-primary" onclick="PostPenambahanPenomoran()">Ya</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="/assets/js/app/tel/set_model_permohonan_penomoran.js"></script>
<script src="/assets/js/app/tel/post_penambahan_penomoran.js"></script>
<script src="/assets/js/app/tools/change_label_file.js"></script>

@endsection