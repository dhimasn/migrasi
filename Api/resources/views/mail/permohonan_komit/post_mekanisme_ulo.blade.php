@extends('layouts.master')
@section('title', 'Mekanisme ULO Telekomunikasi')
@section('content')

<div class="banner">
    <h3>Komitmen Mekanisme ULO Telekomunikasi</h3>
</div>
<!--begin::Entry-->
<div class="container">
    <div class="row margin_card">
        <div class="col-xl-12">
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-header h-auto py-4">
                    <div class="card-title">
                        <span class="card-icon">
                            <i class="flaticon-interface-10 text-primary"></i>
                        </span>
                        <h3 class="card-label">
                            Komitmen Mekanisme ULO Telekomunikasi
                        </h3>
                    </div>
                </div>
            </div>
            <!--end::Card-->
        </div>
    </div>
    <input type="hidden" id="id_permohonan" value="{{$id_permohonan}}">
    <input type="hidden" id="penanda" value="{{$penanda}}">
    <div id="add_html_mekanisme_ulo">
    </div>

    <div id="add_html_mekanisme_ulo_catatan">
    </div>

    <div class="row">
        <div class="col-xl-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b">
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
                            <button type="reset" class="btn btn-secondary" onclick="BackPage()">Batal</button>
                            <button type="button" class="btn btn-primary mr-2" onclick="ModalPostMekanismeULO()">Proses</button>
                        </div>
                    </div>
                </div>
                <!--end::Form-->
            </div>
            <!--end::Card-->

            <div class="modal fade" id="modal_post_mekanisme_ulo" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Proses Mekanisme ULO</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-custom alert-primary" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">Apakah anda yakin ingin mengirim data ini ? </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                            <button type="button" class="btn btn-primary" onclick="PostMekanismeULO()">Ya</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_mekanisme_ulo_histori" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Mekanisme ULO Sebelumnya</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="add_html_mekanisme_ulo_histori">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->

    <script src="/assets/js/app/tel/post_mekanisme_ulo.js"></script>

    @endsection