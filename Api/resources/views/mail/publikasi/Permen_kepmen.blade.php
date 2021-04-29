@extends('layouts.master')
@section('title', 'regulasi')
@section('content')

<!-- Banner Section Start -->
<div class="banner">
    <h3>Regulasi</h3>
</div>
<!-- Banner Section End -->


<section class="section-padding">
  <div class="container">
      <div class="row">
        <div class="col-xl-12">
            <ul class="nav">
            <li class="nav-item">
                <a class="nav-link" href="{{route('undang_undang')}}">Undang Undang</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('pp_perpu')}}">PP & Perpu</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('perpres_kepres')}}">Perpres & Kepres</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{route('permen_kepmen')}}">Permen & Kepmen</a>
            </li>    
            </ul>
        </div>
    </div>
    <br>
    <div class="row">
        <!--begin::Entry-->
        <div class="col-xl-12">
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-body">
                    <!--begin: Datatable-->
                    <table class="table table-bordered table-hover table-checkable mt-10" id="data_regulasi"></table>
                    <!--end: Datatable-->
                </div>
            </div>
            <!--end::Card-->
        </div>
    </div>
    
  </div>
</section>
<script src="/assets/js/app/publikasi/get_regulasi_by_permen_kepmen.js"></script>
@endsection