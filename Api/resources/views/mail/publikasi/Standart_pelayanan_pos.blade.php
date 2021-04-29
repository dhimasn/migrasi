@extends('layouts.master')
@section('content')
<?php
use Illuminate\Support\Facades\Storage;
$storagePath = Storage::url('dokumen_standart_pelayanan_pos\Full KepDir No. 5 Tahun 2018.pdf');
?>
<!-- Banner Section Start -->
<div class="banner">
    <h3>Standart Pelayanan Direktorat POS</h3>
</div>
<!-- Banner Section End -->


<section class="section-padding">
  <div class="container">

   <div class="row">
      <div class="col-md-12">
          <div class="row">
            <object width="100%" height="600px;" type="application/pdf" data="<?php echo $storagePath ?>" ></object>
          </div>
      </div>
    </div>
    
  </div>
</section>


<script src="/assets/js/pages/custom/login/login-general.js?v=7.0.6"></script>
@endsection