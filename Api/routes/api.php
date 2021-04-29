<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login_process', 'LoginController@LoginProcess');
Route::post('/oss/post/niblama','OssController@nibLama');
Route::post('/oss/post/userlama','OssController@UserLama');

Route::group(['prefix' => 'tel', 'middleware' => ['auth:sanctum']], function(){
    Route::post('/permohonan/post','Tel\PermohonanTelController@Post');
    Route::get('/permohonan/get/all','Tel\PermohonanTelController@GetAll');
    Route::get('/permohonan/get/disposisi','Tel\PermohonanTelController@GetByDisposisi');
    Route::get('/permohonan/get/disposisi_send','Tel\PermohonanTelController@GetByDisposisiSend');
    Route::get('/permohonan/get/by_id','Tel\PermohonanTelController@GetById');
    Route::get('/permohonan/get/by_no_sk_izin','Tel\PermohonanTelController@GetByNoSKIzin');
    Route::get('/permohonan/get/validasi','Tel\PermohonanTelController@GetValidasiPermohonan');
    Route::get('/permohonan/get/for_evaluasi_ulo','Tel\PermohonanTelController@GetForEvaluasiUlo');

    Route::get('/permohonan/get/komit_file','Tel\PermohonanKomitController@GetKomitFile');

    Route::get('/permohonan_komit/get/komit_k_kelengkapan','Tel\PermohonanKomitController@GetKKomitKelengkapan');
    Route::post('/permohonan_komit/post','Tel\PermohonanKomitController@PostPermohonanKomit');
    Route::post('/permohonan_komit/post_kelengkapan','Tel\PermohonanKomitController@PostPermohonanKomitKelengkapan');
    Route::post('/permohonan_komit/post_kelengkapan_file','Tel\PermohonanKomitController@PostPermohonanKomitKelengkapanFile');
    Route::put('/permohonan_komit/update_status','Tel\PermohonanTelController@UpdateStatusPermohonanKomit');
    
    Route::post('/permohonan_disposisi/to_staf','Tel\PermohonanDisposisiTelController@DisposisiToStaff');
    Route::post('/permohonan_disposisi/to_up','Tel\PermohonanDisposisiTelController@DisposisiToUp');
    Route::post('/permohonan_disposisi/to','Tel\PermohonanDisposisiTelController@Disposisi');

    Route::post('/permohonan/post_evaluasi_ulo_proses','Tel\PermohonanTelController@PostEvaluasiUloProses');
    Route::post('/permohonan/post_ulo_file_proses','Tel\PermohonanTelController@PostUloFileProses');
    Route::put('/permohonan_komit/update_status_ulo','Tel\PermohonanTelController@UpdateStatusUlo');
    Route::post('/permohonan/post_evaluasi_ulo_up_proses','Tel\PermohonanTelController@PostEvaluasiUloUpProses');
    Route::post('/permohonan/post_evaluasi_ulo_kembali_proses','Tel\PermohonanTelController@PostEvaluasiUloKembaliProses');

    Route::get('/permohonan/get_ulo_file','Tel\PermohonanTelController@GetUloFile');
    Route::get('/permohonan/get_sk_izin','Tel\PermohonanTelController@GetSKIzin');
    Route::get('/permohonan/get_sk_komit','Tel\PermohonanTelController@GetSKKomit');
    Route::get('/permohonan/get_sk_nomor','Tel\PermohonanTelController@GetSKPenomoran');
    Route::get('/permohonan/get_sk_ulo','Tel\PermohonanTelController@GetSKUlo');

    Route::get('/penomoran/get/by_id_layanan','Tel\PenomoranController@GetByIdLayanan');
    Route::get('/penomoran/get_tidak_aktif/by_id_penomoran_tel','Tel\PenomoranController@GetListNomorTidakAktif');

    Route::get('/permohonan/get/disposisi_nomor','Tel\PermohonanTelController@GetDisposisiNoKomit');
    Route::get('/permohonan/get/disposisi_nomor_send','Tel\PermohonanTelController@GetDisposisiSendNoKomit');
    Route::put('/permohonan/update_status','Tel\PermohonanTelController@UpdateStatus');
});

Route::group(['prefix' => 'pos', 'middleware' => ['auth:sanctum']], function(){
    Route::get('/permohonan/get','Pos\PermohonanPosController@GetAll');
    Route::get('/permohonan/get_pembayaran','Pos\PermohonanPosController@GetAllPembayaran');
    Route::get('/permohonan/get_by_id','Pos\PermohonanPosController@GetById');
    Route::get('/permohonan/get_by_pembayaran_id','Pos\PermohonanPosController@GetPembayaranById');
    Route::put('/permohonan/validasi_spm','Pos\PermohonanPosController@ValidasiBuktiBayar');
    Route::get('/permohonan/get/disposisi','Pos\PermohonanPosController@GetDisposisi');
    Route::get('/permohonan/get/disposisi_send','Pos\PermohonanPosController@GetDisposisiSend');
    Route::get('/permohonan/get/komit_file','Pos\PermohonanPosController@GetKomitFile');
    Route::get('/permohonan/get/get_spm_file','Pos\PermohonanPosController@GetBuktiBayarFile');
    
    Route::put('/permohonan_komit/update_status','Pos\PermohonanPosController@UpdateStatusPermohonanKomit');
    
    Route::post('/permohonan_disposisi/to_staf','Pos\PermohonanDisposisiPosController@DisposisiToStaff');
    Route::post('/permohonan_disposisi/to','Pos\PermohonanDisposisiPosController@Disposisi');

});

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::get('/layanan/get','LayananController@GetByIdIzinJenis');
    Route::get('/permohonan_log/get/by_id_pemohon','PermohonanLogController@GetByIdPermohonan');
    Route::get('/perusahaan/get/by_id_pemohon','PerusahaanController@GetByIdPemohon');
    
    Route::get('/wilayah/kabupaten/get_by_nama','WilayahController@GetKabupatenByNama');
    Route::get('/user/get/staff_disposisi','UserController@GetUserStaf');
    Route::get('/device/get_fcm_id','DeviceController@Get');
    Route::post('/device/post','DeviceController@PostData');
});
