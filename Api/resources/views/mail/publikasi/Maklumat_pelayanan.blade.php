@extends('layouts.master')
@section('content')
<?php
use Illuminate\Support\Facades\Storage;
$storagePath = Storage::url('dokumen_maklumat\SK Dirpos No.17 ttg VISI DAN MISI PELAYANAN 2020.pdf');
?>
<!-- Banner Section Start -->
<div class="banner">
    <h3>MAKLUMAT PELAYANAN PERIZINAN DIREKTORAT JENDERAL PENYELENGGARAAN POS DAN INFORMATIKA</h3>
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
@endsection