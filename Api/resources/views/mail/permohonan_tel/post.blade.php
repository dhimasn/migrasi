@extends('layouts.master')
@section('title', 'Permohonan Telekomunikasi')
@section('content')


<div class="banner">
    <h3>Permohonan Izin Telekomunikasi</h3>
    <h3>{{ $jenis_izin }}</h3>
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

    <div class="row margin_card">
        <div class="col-xl-12">
            <!--begin::Card-->
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
                <!--begin::Form-->
                <div class="form">
                    <div class="card-body">
                        <input type="hidden" id="id_perusahaan">
                        <input type="hidden" id="type_izin_jenis_tel" value="{{ $type_izin_jenis_tel }}">
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
                        <div class="form-group row mt-4" id="add_html_penomoran">
                            <label class="col-2 col-form-label">Jenis Layanan</label>
                            <div class="col-10">
                                <select class="form-control select2 select2-multiple" id="jenis_layanan" name="param" onchange="GetPenomoran()" multiple="multiple">

                                </select>
                                <span class="form-text text-muted">Dapat dipilih lebih dari satu</span>
                            </div>
                        </div>
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
                                <button type="reset" class="btn btn-secondary" onclick="BackPage()">Batal</button>
                                <button type="button" class="btn btn-primary mr-2" onclick="ModalPostPermohonan()">Kirim Permohonan</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Form-->
            </div>
            <!--end::Card-->

            <div class="modal fade" id="modal_proses_permohonan" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                <div class="alert-text">Apakah anda yakin ingin mengirim Permohonan ini ? </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                            <button type="button" class="btn btn-primary" onclick="PostPermohonan()">Ya</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="disclaimer_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Disclaimer Telekomunikasi</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body" style='text-align:justify'>
                            <ol type="1">
                                <li>Isilah dengan lengkap dan benar</li>
                                <li>Permohonan disampaikan langsung tanpa melalui calo/perantara. Informasikan kepada kami jika mengetahui ada praktek percaloan dalam proses perizinan. Nama pelapor akan dirahasiakan</li>
                                <li>Tidak ada Biaya permohonan Izin Penyelenggaraan Telekomunikasi (Gratis).</li>
                                <li>Izin diterbitkan dalam hari yang sama (same day service)</li>
                                <li>Izin Penyelenggaraan Telekomunikasi ini adalah dokumen elektronik yang menggunakan tanda tangan digital yang memiliki kekuatan hukum dan tidak memerlukan tanda tangan basah serta legalisir. Untuk verifikasi dan keamanan dapat mengecek melalui QR Code</li>
                                <li>Informasi dan konsultasi terkait perizinan dapat menghubungi call center 159</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="/assets/js/app/tel/set_model_permohonan.js"></script>
<script src="/assets/js/app/tel/post_permohonan.js"></script>

@endsection