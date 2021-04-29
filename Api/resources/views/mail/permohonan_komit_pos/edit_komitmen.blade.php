@extends('layouts.master')
@section('title', 'Perbaikan Komitment Permohonan Pos')
@section('content')
<div class="banner">
    <h3>Perbaikan Komitmen Permohonan Pos</h3>
</div>
<div class="container">
    <div class="row margin_card">
        <div class="col-xl-12">
            <div class="card card-custom">
                <div class="card-header h-auto py-4">
                    <div class="card-title">
                        <span class="card-icon">
                            <i class="flaticon-interface-10 text-primary"></i>
                        </span>
                        <h3 class="card-label">
                            Perbaikan Komitmen Permohonan Pos
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="id_permohonan" value="{{$id_permohonan}}">
    <input type="hidden" id="id_permohonan_komit">
    <input type="hidden" id="type_disposisi">
    <div id="add_html_perbaikan">
    </div>
    <div class="row">
        <div class="col-xl-12">
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
                            <button type="button" class="btn btn-primary mr-2" onclick="ModalEditPermohonanKomit()">Proses</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modal_download_kelengkapan" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Kelengkapan File</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-custom alert-primary" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">Silahkan pilih button di bawah ini untuk melihat file kelengkapan ini</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" id="id_permohonan_komit_file">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-success" onclick="ViewKelengkapan()">Lihat</button>
                            <button type="button" class="btn btn-primary" onclick="DownloadKelengkapan()">Unduh</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modal_komit_catatan" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Catatan File Kelengkapan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body" id="komitmen_catatan">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modal_proses_edit_permohonan_komit" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Proses Perbaikan Permohonan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-custom alert-primary" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">Apakah anda yakin ingin mengirim Perbaikan Permohonan Komitmen ini ? </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                            <button type="button" class="btn btn-primary" onclick="EditPermohonanKomit()">Ya</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/assets/js/app/pos/download_kelengkapan.js"></script>
    <script src="/assets/js/app/pos/get_view_file_kelengkapan.js"></script>
    <script src="/assets/js/app/pos/edit_komitmen.js"></script>
    <script src="/assets/js/app/tools/change_label_file.js"></script>
    @endsection