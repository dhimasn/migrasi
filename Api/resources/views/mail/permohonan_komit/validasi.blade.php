@extends('layouts.master')
@section('title', 'Penyelenggaraan Telekomunikasi')
@section('content')


<div class="banner">
    <h3>Validasi Komitmen Penyelenggaraan Telekomunikasi</h3>
    <h3>{{ $jenis_izin }}</h3>
</div>
<div class="container">
    <div class="row margin_card">
        <div class="col-xl-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <div class="card-title">
                        <span class="card-icon">
                            <i class="flaticon2-file text-primary"></i>
                        </span>
                        <h3 class="card-label">
                            Validasi
                        </h3>
                    </div>
                </div>
                <!--begin::Form-->
                <div class="form">
                    <div class="card-body">
                        <input type="hidden" id="type_izin_jenis_tel" value="{{ $type_izin_jenis_tel }}">
                        <div class="form-group row mt-4">
                            <label class="col-2 col-form-label">No Izin Penyelenggaraan</label>
                            <div class="col-10">
                                <input type="text" class="form-control" id="no_sk_izin" placeholder="Input No Izin Penyelenggaraan">
                                <span class="form-text text-muted">Contoh : 1/TEL.02.01/2018</span>
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
                                <button type="button" class="btn btn-primary mr-2" onclick="ValidasiPermohonanKomit()">Submit Validasi</button>
                            </div>
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

        </div>
    </div>
</div>

<script src="/assets/js/app/tel/validasi_permohonan_komit.js"></script>

@endsection