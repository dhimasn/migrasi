@extends('layouts.master')
@section('title', 'Validasi Penambahan Penomoran Telekomunikasi')
@section('content')


<div class="banner">
    <h3>Validasi Penambahan Penomoran <br> {{ $jenis_izin }} Telekomunikasi</h3>
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
                            Validasi No Izin
                        </h3>
                    </div>
                </div>
                <!--begin::Form-->
                <div class="form">
                    <div class="card-body">
                        <div class="form-group row mt-4">
                        <input type="hidden" id="type_izin_jenis_tel" value="{{ $type_izin_jenis_tel }}">
                            <label class="col-2 col-form-label">No Izin Penyelenggaraan</label>
                            <div class="col-10">
                                <input type="text" class="form-control" id="no_sk_izin" placeholder="Input No SK Izin Penyelenggaraan">
                                <span class="form-text text-muted">Contoh : 1/TEL.02.02/2018</span>
                            </div>
                        </div>
                        <div class="form-group row mt-4">
                            <label class="col-2 col-form-label">No Izin Penomoran</label>
                            <div class="col-10">
                                <input type="text" class="form-control" id="no_sk_izin_penomoran" placeholder="Input No SK Izin Penomoran">
                                <span class="form-text text-muted">Contoh : 1/TEL.05.05/2018</span>
                            </div>
                        </div>
                        <div class="form-group row mt-4" id="add_html_penomoran">
                            <label class="col-2 col-form-label">Jenis Layanan</label>
                            <div class="col-10">
                                <select class="form-control select2 select2-multiple" id="jenis_layanan" name="param" multiple="multiple">

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
                                <button type="button" class="btn btn-primary mr-2" onclick="ValidasiPenomoran()">Submit Validasi</button>
                            </div>
                        </div>

                    </div>
                </div>
                <!--end::Form-->
            </div>
            <!--end::Card-->

        </div>
    </div>
</div>

<script src="/assets/js/app/tel/validasi_penomoran.js"></script>

@endsection