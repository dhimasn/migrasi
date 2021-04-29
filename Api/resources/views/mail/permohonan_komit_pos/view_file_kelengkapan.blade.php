@extends('layouts.master')
@section('title', 'Preview File Kelengkapan Permohonan Pos')
@section('content')

<div class="banner">
    <h3>Preview File Kelengkapan Permohonan Pos</h3>
</div>
<!--begin::Entry-->
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <!--begin::Card-->
                <div class="card card-custom gutter-b">
                    <div class="card-header h-auto py-4">
                        <div class="card-title">
                            <span class="card-icon">
                                <i class="fas fa-file-pdf text-danger"></i>
                            </span>
                            <h3 class="card-label" id="nama_file">

                            </h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <center><canvas id="lihat_pdf" style="border:1px solid #000000;"></canvas></center>
                    </div>
                    <div class="card-footer">
                        <center>
                            <div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-secondary" id="prev">Sebelumnya</button>
                                    <button type="button" class="btn btn-secondary" id="next">Selanjutnya</button>
                                </div>
                                &nbsp; &nbsp;
                                <span>Halaman: <span id="page_num"></span> / <span id="page_count"></span></span>
                            </div>
                        </center>
                    </div>
                </div>
                <!--end::Card-->
            </div>
        </div>
        <input type="hidden" id="id_permohonan_komit_file" value="{{$id_permohonan_komit_file}}">
        <!--end::Container-->
    </div>
    <!--end::Entry-->

<script src="/assets/js/app/tools/pdfjs/pdf.js"></script>
<script src="/assets/js/app/tools/pdfjs/viewer.js"></script>
<script src="/assets/js/app/pos/view_file_kelengkapan.js"></script>

@endsection