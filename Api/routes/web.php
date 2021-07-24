<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//route untuk migrasi
$router->get('/migrasi_jasa','Migrasi\MigrasiJasaController@MigrasiJasa');
$router->get('/migrasi_pos','Migrasi\MigrasiPosController@MigrasiPos');
$router->get('/migrasi_jaringan','Migrasi\MigrasiJaringanController@MigrasiJaringan');
$router->get('/migrasi_telsus','Migrasi\MigrasiTelsusController@MigrasiTelsus');
$router->get('/migrasi_penomoran_prima','Migrasi\MigrasiPenomoranPrimaController@MigrasiPenomoranPrima');
$router->get('/migrasi_penomoran','Migrasi\MigrasiPenomoranController@MigrasiPenomoran');
$router->get('/migrasi_berkas_jaringan','Migrasi\MigrasiBerkasJaringanController@MigrasiBerkasJaringan');
$router->get('/migrasi_berkas_khusus','Migrasi\MigrasiBerkasKhususController@MigrasiBerkaskhusus');
$router->get('/migrasi_berkas_jasa','Migrasi\MigrasiBerkasJasaController@MigrasiBerkasJasa');
$router->get('/migrasi_berkas_pos','Migrasi\MigrasiBerkasPosController@MigrasiBerkasPos');
$router->get('/migrasi_telsus_prima','Migrasi\MigrasiTelsusController@MigrasiTelsusPrima');

//route migrasi data nib
$router->get('/migrasi_nib', 'OssController@nibLama');