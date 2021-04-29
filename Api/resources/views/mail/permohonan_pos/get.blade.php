@extends('layouts.master')
@section('title', 'Daftar Permohonan Pos')
@section('content')
<div class="banner">
    <h3>Daftar Permohonan Izin Pos</h3>
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
                            Daftar Permohonan Izin Pos
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover table-checkable mt-10" id="data_permohonan">
                        <thead>
                            <tr>
                                <th class="text-center">Nomor Penyelenggaraan</th>
                                <th class="text-center">Jenis Izin</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Info</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Keterangan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/assets/js/app/pos/get_permohonan.js"></script>
@endsection