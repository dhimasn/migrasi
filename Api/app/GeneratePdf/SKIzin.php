<?php

namespace App\GeneratePdf;

use stdClass;
use Exception;
use App\Repo\UserDb;
use App\Repo\GetNoDb;
use App\Enums\TypeIzin;
use Elibyy\TCPDF\TCPDF;
use App\Helper\Iotentik;
use App\Repo\PerusahaanDb;
use Smalot\PdfParser\Parser;
use App\Enums\TypeUnitTeknis;
use App\Helper\GenerateNomor;
use Illuminate\Support\Carbon;
use App\Enums\TypeIzinJenisTel;
use App\Enums\TypeLevelJabatan;
use App\Repo\Tel\PermohonanTelDb;
use Illuminate\Support\Facades\DB;
use App\Enums\TypeEvaluasiUloValue;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;

class SKIzin
{
    private $udb;
    private $iot; 
    private $pteldb;
    private $perdb;
    private $get_nomor;
    private $gen_nomor;
    private $array_abjad;
    public function __construct()
    {
        Carbon::setLocale('id');
        $this->iot = new Iotentik();
        $this->udb = new UserDb();
        $this->pteldb = new PermohonanTelDb();
        $this->perdb = new PerusahaanDb();
        $this->get_nomor = new GetNoDb();
        $this->gen_nomor = new GenerateNomor();
        $this->array_abjad = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'];
    }

    private function SetPdf()
    {
        $pdf_settings = Config::get('tcpdf');
        $pdf = new TCPdf($pdf_settings['page_orientation'], $pdf_settings['page_units'], $pdf_settings['page_format'], true);
        $pdf->SetFont('times', '', 11 );
        $pdf->SetMargins(PDF_MARGIN_LEFT, 20 ,PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(false);
        $pdf->SetFooterMargin(false);
        //$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM + 15);
        //$pdf->changeFormat($pdf_settings);
        $pdf->AddPage();
        return $pdf;
    }

    public function Penomoran($model)
    {
        $model_return = new stdClass();
        try
        {
            $permohonan = $this->pteldb->GetById($model->id_permohonan);
            $user = $this->udb->GetUserByLevelUnitTeknis(TypeLevelJabatan::Direktur, TypeUnitTeknis::Telekomunikasi)[0];
            //$perusahaan = $this->perdb->GetByIdPemohon($permohonan->id_pemohon)[0];
            $list_path = array();

            $a = new QrCodeGen();
            $file_qr_path = $a->Generate(sprintf("%s/check_sk/%s", env('BASE_URL_APP_FO'), Crypt::encryptString($model->id_permohonan)), $model->id_permohonan);

            foreach($permohonan->data_layanan as $pdl)
            {
                $penomoran = $pdl->penomoran;

                $data_penomoran = [
                    'jenis_penomoran_u' => strtoupper($penomoran->jenis_penomoran),
                    'jenis_penomoran' => $penomoran->jenis_penomoran,
                    'no_sk_penomoran' => $penomoran->no_sk_penomoran,
                    'no_permohonan' => $permohonan->data_header->no_penyelenggaraan,
                    'tanggal_permohonan' =>  Carbon::parse($permohonan->data_header->tanggal_input)->translatedFormat('d F Y'),
                    'nomor_penomoran' => $penomoran->nomor,
                    'nama_pt' => strtoupper($permohonan->data_header->nama_perusahaan),
                    'no_nib' => $permohonan->data_header->nib,
                    'npwp' => $permohonan->data_header->npwp,
                    'alamat_perusahaan' => $permohonan->data_header->alamat_perusahaan,
                    'tanggal' => Carbon::parse($model->tanggal)->translatedFormat('d F Y'),
                    'jabatan' => $user->nama_jabatan,
                    'nama_penanda_tangan' => strtoupper($user->nama)
                ];
                $pdf_penomoran = $this->SetPdf();
                $nama_file_penomoran = uniqid().time().'.pdf';
                $file_pdf_penomoran_path = storage_path('app/sk_temp/'.$nama_file_penomoran);
                $pdf_penomoran->writeHTML(view('sk.sk_penyelenggaraan_penomoran', $data_penomoran)->render());
                $pdf_penomoran->output($file_pdf_penomoran_path, 'F');

                $parser = new Parser();
                $pdf_parser = $parser->parseFile($file_pdf_penomoran_path);
                $pages = $pdf_parser->getPages();
                $i = 1;
                $model_coordinat = new stdClass();
                $base64_file = '';
                foreach ($pages as $page) {
                    if ($page->getText($page, strtoupper($user->nama), true)) {
                        $coordinates = $page->getText($page, strtoupper($user->nama), true);
                        $model_coordinat->x = $coordinates['x'];
                        $model_coordinat->y = $coordinates['y'];
                        $model_coordinat->page = $i;
    
                        $base64_file = $this->Sign($model_coordinat, $file_pdf_penomoran_path, $file_qr_path, $user);
                    }
                    $i++;
                }
                
                $model_path_penomoran = new stdClass(); 
                $model_path_penomoran->file_pdf_path = $file_pdf_penomoran_path;
                $model_path_penomoran->id_penomoran_tel_pakai = $penomoran->id_penomoran_tel_pakai;
                $model_path_penomoran->nama_file = $nama_file_penomoran;
                $model_path_penomoran->stream_file = $base64_file;

                array_push($list_path, $model_path_penomoran);
            }
            $model_return->result = true;
            $model_return->list_path = $list_path;
            return $model_return;
        }
        catch(Exception $ex)
        {
            $model_return->result = false;
            return $model_return;
        }
    }

    public function Tel($model, $type_izin_jenis_tel)
    {
        $model_return = new stdClass();
        try
        {
            $pdf = $this->SetPdf();
            
            $list_path = array();

            $layanans = $this->pteldb->GetByNoSKIzin($model->no_sk, $type_izin_jenis_tel, $model->id_pemohon);

            $a = new QrCodeGen();
            $file_qr_path = $a->Generate($model->url, $model->id_permohonan);

            $tabel_layanan = sprintf('<table class="table_layanan">
            <thead>
			<tr>
				<th class="text-center th_layanan">KATEGORI</th>
				<th class="text-center th_layanan">JENIS LAYANAN</th>
            </tr>
            </thead><tbody>');
            foreach($layanans->data_layanan as $l)
            {
                $tabel_layanan .= sprintf('<tr><td class="td_layanan">%s</td><td class="td_layanan">%s</td></tr>', $layanans->permohonan->izin_jenis, $l->layanan->layanan);
            }
            $tabel_layanan .= sprintf('</tbody></table>');

            $user = $this->udb->GetUserByLevelUnitTeknis(TypeLevelJabatan::Direktur, TypeUnitTeknis::Telekomunikasi)[0];
            $perusahaan = $this->perdb->GetByIdPemohon($model->id_pemohon)[0];

            $data = [
                'no_sk' => $model->no_sk,
                'nama_pt' => strtoupper($perusahaan->nama_perusahaan),
                'no_nib' => $perusahaan->nib,
                'tanggal' => Carbon::parse($model->tanggal)->translatedFormat('d F Y'),
                'jabatan' => $user->nama_jabatan,
                'nama_penanda_tangan' => strtoupper($user->nama),
                'tabel_layanan' => $tabel_layanan
            ];

            if($type_izin_jenis_tel == TypeIzinJenisTel::Jasa)
            {
                $pdf->writeHTML(view('sk.sk_penyelenggaraan_jasa', $data)->render());
                $pdf->AddPage();
                $pdf->writeHTML(view('sk.sk_lamp_jasa', $data)->render());

                //untuk penomoran
                foreach ($layanans->data_layanan as $l) 
                {
                    $with_nomor = $l->with_nomor;
                    if ($with_nomor) 
                    {
                        $data_penomoran = [
                            'jenis_penomoran_u' => strtoupper($l->penomoran->jenis_penomoran),
                            'jenis_penomoran' => $l->penomoran->jenis_penomoran,
                            'no_sk_penomoran' => $l->penomoran->no_sk_penomoran,
                            'no_permohonan' => $layanans->permohonan->no_penyelenggaraan,
                            'tanggal_permohonan' =>  Carbon::parse($layanans->permohonan->tanggal_input)->translatedFormat('d F Y'),
                            'nomor_penomoran' => $l->penomoran->nomor,
                            'nama_pt' => strtoupper($perusahaan->nama_perusahaan),
                            'no_nib' => $perusahaan->nib,
                            'npwp' => $perusahaan->npwp,
                            'alamat_perusahaan' => $perusahaan->alamat,
                            'tanggal' => Carbon::parse($model->tanggal)->translatedFormat('d F Y'),
                            'jabatan' => $user->nama_jabatan,
                            'nama_penanda_tangan' => strtoupper($user->nama)
                        ];
                        $pdf_penomoran = $this->SetPdf();
                        $nama_file_penomoran = uniqid().time().'.pdf';
                        $file_pdf_penomoran_path = storage_path('app/sk_temp/'.$nama_file_penomoran);
                        $pdf_penomoran->writeHTML(view('sk.sk_penyelenggaraan_penomoran', $data_penomoran)->render());
                        $pdf_penomoran->output($file_pdf_penomoran_path, 'F');

                        $model_path_penomoran = new stdClass();
                        $model_path_penomoran->type_izin_jenis_tel = TypeIzinJenisTel::Penomoran;
                        $model_path_penomoran->file_pdf_path = $file_pdf_penomoran_path;
                        $model_path_penomoran->nama_file = $nama_file_penomoran;
                        $model_path_penomoran->id_penomoran_tel_pakai = $l->penomoran->id_penomoran_tel_pakai;

                        array_push($list_path, $model_path_penomoran);
                    }
                }
            }
            else if($type_izin_jenis_tel == TypeIzinJenisTel::Jaringan)
            {
                $pdf->writeHTML(view('sk.sk_penyelenggaraan_jaringan', $data)->render());
                $pdf->AddPage();
                $pdf->writeHTML(view('sk.sk_lamp_jaringan', $data)->render());
            }
            else if($type_izin_jenis_tel == TypeIzinJenisTel::Khusus)
            {
                $pdf->writeHTML(view('sk.sk_penyelenggaraan_telsus', $data)->render());
            }
            
            $nama_file_sk = uniqid().time().'.pdf';
            $file_pdf_path = storage_path('app/sk_temp/'.$nama_file_sk);
            $pdf->output($file_pdf_path, 'F');

            $model_path = new stdClass();
            $model_path->type_izin_jenis_tel = $type_izin_jenis_tel;
            $model_path->file_pdf_path = $file_pdf_path;
            $model_path->nama_file = $nama_file_sk;
            $model_path->id_permohonan = $model->id_permohonan;

            array_push($list_path, $model_path);
            
            foreach($list_path as $lp)
            {
                $parser = new Parser();
                $pdf_parser = $parser->parseFile($lp->file_pdf_path);
                $pages = $pdf_parser->getPages();
                $i = 1;
                $model_coordinat = new stdClass();
                $base64_file = '';
                foreach ($pages as $page) {
                    if ($page->getText($page, strtoupper($user->nama), true)) {
                        $coordinates = $page->getText($page, strtoupper($user->nama), true);
                        $model_coordinat->x = $coordinates['x'];
                        $model_coordinat->y = $coordinates['y'];
                        $model_coordinat->page = $i;

                        $base64_file = $this->Sign($model_coordinat, $lp->file_pdf_path, $file_qr_path, $user);
                    }
                    $i++;
                }
                $lp->stream_file = $base64_file;
            }
            $model_return->list_path = $list_path;
            $model_return->result = true;
            return $model_return;
        }
        catch(Exception $ex)
        {
            $model_return->result = false;
            return $model_return;
        }
    }

    public function Sklo($model)
    {
        $model_return = new stdClass();
        try
        {
            $list_path_ulo = array();
            $layanans = $this->pteldb->GetByNoSKIzinWithoutPemohon($model->id_permohonan);
            $user = $this->udb->GetUserByLevelUnitTeknis(TypeLevelJabatan::Direktur, TypeUnitTeknis::Telekomunikasi)[0];

            $a = new QrCodeGen();
            $nama_file_ulo = uniqid() . time() . '.pdf';
            $file_qr_path = $a->Generate(sprintf("%s/check_sk/%s", env('BASE_URL_APP_FO'), Crypt::encryptString($model->id_permohonan)), $model->id_permohonan);
            $file_pdf_ulo_path = storage_path('app/sk_temp/' . $nama_file_ulo);

            $last_no_sklo = $this->get_nomor->GetLastSklo();
            $no_sklo = $this->gen_nomor->no_sk_izin($last_no_sklo, TypeIzin::Telekomunikasi, TypeIzinJenisTel::Ulo);

            $pdf = $this->SetPdf();
            $ll = 0;
            $jenis_layanan = '';
            $lll = 0;
            foreach ($layanans->data_layanan as $l) {
                $evaluasi_ulo = $l->evaluasi_ulo;
                $tgl_spt = '';
                $no_spt = '';
                $tgl_uji = '';
                foreach ($evaluasi_ulo as $eu) {
                    if ($eu->jenis == TypeEvaluasiUloValue::TglSpt) {
                        $_tgl_spt = $eu->value;
                        $a_tgl_spt = explode('/', $_tgl_spt);
                        $tgl_spt = sprintf('%s-%s-%s', $a_tgl_spt[2], $a_tgl_spt[1], $a_tgl_spt[0]);
                    } else if ($eu->jenis == TypeEvaluasiUloValue::NoSpt) {
                        $no_spt = $eu->value;
                    } else if ($eu->jenis == TypeEvaluasiUloValue::TglUjiPetik) {
                        $_tgl_uji = $eu->value;
                        $a_tgl_uji = explode('/', $_tgl_uji);
                        $tgl_uji = sprintf('%s-%s-%s', $a_tgl_uji[2], $a_tgl_uji[1], $a_tgl_uji[0]);
                    }
                }

                if ($layanans->permohonan->id_izin_jenis == TypeIzinJenisTel::Jasa) {
                    $jenis_layanan .= sprintf('Jasa Telekomunikasi %s', $l->layanan->layanan);
                } else if ($layanans->permohonan->id_izin_jenis == TypeIzinJenisTel::Jaringan) {
                    $jenis_layanan .= sprintf('Jaringan Telekomunikasi %s', $l->layanan->layanan);
                } else if($layanans->permohonan->id_izin_jenis == TypeIzinJenisTel::Khusus){
                    $jenis_layanan .= sprintf('Telekomunikasi Khusus');
                }

                if ($lll != count($layanans->data_layanan) - 1) {
                    $jenis_layanan .= ', ';
                }

                if ($lll == count($layanans->data_layanan) - 1) {
                    $data_ulo = [
                        'no_sk' => $no_sklo,
                        'no_sk_izin' => $layanans->permohonan->no_sk_izin,
                        'tanggal_sk_izin' => Carbon::parse($layanans->permohonan->tanggal_input)->translatedFormat('d F Y'),
                        'jenis_layanan' => $jenis_layanan,
                        'no_surat_tugas_dir' => $no_spt,
                        'tgl_surat_tugas_dir' => Carbon::parse($tgl_spt)->translatedFormat('d F Y'),
                        'tgl_ulo' => Carbon::parse($tgl_uji)->translatedFormat('d F Y'),
                        'nama_pt' => strtoupper($layanans->permohonan->nama_perusahaan),
                        'tanggal' => Carbon::parse($model->tanggal)->translatedFormat('d F Y'),
                        'jabatan' => $user->nama_jabatan,
                        'nama_penanda_tangan' => strtoupper($user->nama),
                        'alamat' => $layanans->permohonan->alamat
                    ];
                }


                if ($lll == count($layanans->data_layanan) - 1) {
                    if ($layanans->permohonan->id_izin_jenis == TypeIzinJenisTel::Jasa) {
                        $pdf->writeHTML(view('sk.sk_lo_jasa', $data_ulo)->render());
                    } else if ($layanans->permohonan->id_izin_jenis == TypeIzinJenisTel::Jaringan) {
                        $pdf->writeHTML(view('sk.sk_lo_jaringan', $data_ulo)->render());
                    } else if($layanans->permohonan->id_izin_jenis == TypeIzinJenisTel::Khusus){
                        $pdf->writeHTML(view('sk.sk_lo_telsus', $data_ulo)->render());
                    }
                }

                $lll++;
            }

            foreach($layanans->data_layanan as $l)
            {
                $evaluasi_ulo = $l->evaluasi_ulo;
                $tgl_spt = '';
                $no_spt = '';
                $tgl_uji = '';
                $mekanisme = '';
                $tabel_evaluasi_ulo = '<table class="table_layanan">
                <thead>
                <tr>
                    <th class="text-center th_layanan" style= "width:5%;">No</th>
                    <th class="text-center th_layanan" style= "width:30%;">Metode Evaluasi</th>
                    <th class="text-center th_layanan" style= "width:30%;">Alamat Pusat Pelayanan Pelanggan</th>
                    <th class="text-center th_layanan" style= "width:20%;">Alamat Pelaksanaan ULO</th>
                    <th class="text-center th_layanan" style= "width:15%;">Hasil Evaluasi</th>
                </tr>
                </thead><tbody>';
                $penanda_baris = 0;
                $i = 0;
                foreach($evaluasi_ulo as $eu)
                {
                    $baris = $eu->baris;
                    if($penanda_baris != $baris)
                    {
                        //awal baris
                        if($i != 0)
                        {
                            $tabel_evaluasi_ulo .= '</tr>';
                        }
                        $tabel_evaluasi_ulo .= sprintf('<tr><td class="td_layanan">%s</td>', $baris) ;
                        $penanda_baris = $baris;
                    }
                    
                    if($eu->jenis == TypeEvaluasiUloValue::TglSpt)
                    {
                        $_tgl_spt = $eu->value;
                        $a_tgl_spt = explode('/', $_tgl_spt);
                        $tgl_spt = sprintf('%s-%s-%s', $a_tgl_spt[2], $a_tgl_spt[1], $a_tgl_spt[0]);
                    }
                    else if($eu->jenis == TypeEvaluasiUloValue::NoSpt)
                    {
                        $no_spt = $eu->value;
                    }
                    else if($eu->jenis == TypeEvaluasiUloValue::TglUjiPetik)
                    {
                        $_tgl_uji = $eu->value;
                        $a_tgl_uji = explode('/', $_tgl_uji);
                        $tgl_uji = sprintf('%s-%s-%s', $a_tgl_uji[2], $a_tgl_uji[1], $a_tgl_uji[0]);
                    }
                    else if($eu->jenis == TypeEvaluasiUloValue::Mekanisme)
                    {
                        $mekanisme = $eu->value;
    
                        $tabel_evaluasi_ulo .= sprintf('<td class="td_layanan">%s</td>', $eu->value) ;
                    }
                    else if($eu->jenis == TypeEvaluasiUloValue::AlamatPusat)
                    {
                        $tabel_evaluasi_ulo .= sprintf('<td class="td_layanan">%s</td>', $eu->value) ;
                    }
                    else if($eu->jenis == TypeEvaluasiUloValue::AlamatUlo)
                    {
                        $tabel_evaluasi_ulo .= sprintf('<td class="td_layanan">%s</td>', $eu->value) ;
                    }
                    else if($eu->jenis == TypeEvaluasiUloValue::Hasil)
                    {
                        $tabel_evaluasi_ulo .= sprintf('<td class="td_layanan">%s</td>', $eu->value) ;
                    }
    
                    $i++;
                    if($i == count($evaluasi_ulo))
                    {
                        $tabel_evaluasi_ulo .= '</tr></table>';
                    }
                }

                $format_tanggal = explode(' ', Carbon::parse($tgl_uji)->translatedFormat('l d F Y'));
                $tgl_ulo = sprintf('%s tanggal %s bulan %s tahun %s', $format_tanggal[0],  $format_tanggal[1], $format_tanggal[2], $format_tanggal[3]); 
                $data_ulo_lampiran = [
                    'jenis_layanan' => $l->layanan->layanan,
                    'jenis_layanan_k' => strtoupper($l->layanan->layanan),
                    'nama_pt' => strtoupper($layanans->permohonan->nama_perusahaan),
                    'tgl_ulo' => $tgl_ulo,
                    'jabatan' => $user->nama_jabatan,
                    'nama_penanda_tangan' => strtoupper($user->nama), 
                    'metode_uji' => $mekanisme,
                    'tabel_evaluasi_ulo' => $tabel_evaluasi_ulo
                ];
    
                if($layanans->permohonan->id_izin_jenis == TypeIzinJenisTel::Jasa)
                {
                    $pdf->AddPage();
                    $pdf->writeHTML(view('sk.sk_lo_jasa_lampiran', $data_ulo_lampiran)->render());
                }
                else if($layanans->permohonan->id_izin_jenis == TypeIzinJenisTel::Jaringan)
                {
                    $pdf->AddPage();
                    $pdf->writeHTML(view('sk.sk_lo_jaringan_lampiran', $data_ulo_lampiran)->render());
                }
                else if($layanans->permohonan->id_izin_jenis == TypeIzinJenisTel::Khusus)
                {
                    $pdf->AddPage();
                    $pdf->writeHTML(view('sk.sk_lo_telsus_lampiran', $data_ulo_lampiran)->render());
                }

                $ll++;
            }
            $pdf->output($file_pdf_ulo_path, 'F');

            $parser = new Parser();
            $pdf_parser = $parser->parseFile($file_pdf_ulo_path);
            $pages = $pdf_parser->getPages();
            $i = 1;
            $model_coordinat = new stdClass();
            $base64_file = '';
            foreach ($pages as $page) {
                if ($page->getText($page, strtoupper($user->nama), true)) {
                    $coordinates = $page->getText($page, strtoupper($user->nama), true);
                    $model_coordinat->x = $coordinates['x'];
                    $model_coordinat->y = $coordinates['y'];
                    $model_coordinat->page = $i;

                    $base64_file = $this->Sign($model_coordinat, $file_pdf_ulo_path, $file_qr_path, $user);
                }
                $i++;
            }

            $model_path = new stdClass(); 
            $model_path->file_pdf_path = $file_pdf_ulo_path;
            $model_path->id_permohonan = $model->id_permohonan;
            $model_path->no_sklo = $no_sklo;
            $model_path->nama_file = $nama_file_ulo;
            $model_path->stream_file = $base64_file;
            $model_path->is_sklo = true;
         
            array_push($list_path_ulo, $model_path);
            
            $model_return->list_path = $list_path_ulo;
            $model_return->result = true;
            return $model_return;
        }
        catch(Exception $ex)
        {
            $model_return->result = false;
            return $model_return;
        }
    }

    public function SkPenetapan($model)
    {
        $model_return = new stdClass();
        try
        {
            $permohonan = $this->pteldb->GetById($model->id_permohonan);
            $user = $this->udb->GetUserByLevelUnitTeknis(TypeLevelJabatan::Direktur, TypeUnitTeknis::Telekomunikasi)[0];

            $a = new QrCodeGen();
            $nama_file = uniqid() . time() . '.pdf';
            $file_qr_path = $a->Generate(sprintf("%s/check_sk/%s", env('BASE_URL_APP_FO'), Crypt::encryptString($model->id_permohonan)), $model->id_permohonan);
            $file_pdf_path = storage_path('app/sk_temp/' . $nama_file);
            $pdf = $this->SetPdf();

            $array_layanan_cetak = array();
            $penanda_media = '';
            $array_tertutup = 0;
            $type_izin_jenis_tel = $permohonan->data_layanan[0]->layanan->id_izin_jenis;

            foreach ($permohonan->data_layanan as $l) {
                $lj = explode(',', $l->layanan->layanan);  //check apakah jaringan atau tidak
                $media_jaringan = '';
                if (count($lj) > 1) {
                    //untuk jaringan tertutup
                    $media_jaringan = $lj[1];
                }

                $komitmens = $l->komitmen;
                $kinerjas = $l->kinerja;

                $list_komitmen = '';
                $list_komitmen_kinerja = '';

                $t_head_komitmen = '';
                $t_body_komitmen = '';
                $jumlah_header = 0;

                foreach ($komitmens as $ko) {
                    if ($ko->baris == 1) {
                        $t_head_komitmen .= sprintf('<th class="text-center th_layanan">%s</th>', $ko->value_header);
                        $jumlah_header++;
                    } else {
                        break;
                    }
                }

                $k = 0;
                foreach ($komitmens as $ko) {
                    if (($k + 1) % $jumlah_header === 1) {
                        $t_body_komitmen .= '<tr>';
                    }

                    $t_body_komitmen .= sprintf('<td class="text-center td_layanan">%s</td>', $ko->value);

                    if (($k + 1) % $jumlah_header === 0) {
                        $t_body_komitmen .= '</tr>';
                    }
                    $k++;
                }

                $table_komitmen = sprintf('<table class="table_layanan"><thead><tr>%s</tr></thead><tbody>%s</tbody></table>', $t_head_komitmen, $t_body_komitmen);
                
                $thead_komitmen_kinerja = '<th class="text-center th_layanan">Tahun</th>';
                $jumlah_header_kinerja = 0;
                foreach ($kinerjas as $ki) {
                    if ($ki->baris == 1) {
                        $thead_komitmen_kinerja .= sprintf('<th class="text-center th_layanan">%s</th>', $ki->tahun);
                        $jumlah_header_kinerja++;
                    } else {
                        break;
                    }
                }

                $kk = 0;
                $tbody_komitmen_kinerja = '';
                foreach ($kinerjas as $ki) {
                    if (($kk + 1) % $jumlah_header_kinerja === 1) {
                        $tbody_komitmen_kinerja .= sprintf('<tr><td class="text-center td_layanan">%s</td>', $ki->jenis);
                    }

                    $tbody_komitmen_kinerja .= sprintf('<td class="text-center td_layanan">%s</td>', $ki->value);

                    if (($kk + 1) % $jumlah_header_kinerja === 0) {
                        $tbody_komitmen_kinerja .= '</tr>';
                    }

                    $kk++;
                }

                $table_komitmen_kinerja = sprintf('<table class="table_layanan"><thead><tr>%s</tr></thead><tbody>%s</tbody></table>', $thead_komitmen_kinerja, $tbody_komitmen_kinerja);

                if($type_izin_jenis_tel == TypeIzinJenisTel::Jaringan || $type_izin_jenis_tel == TypeIzinJenisTel::Khusus)
                {
                    if (count($lj) > 1) {
                        $list_komitmen .= sprintf('<tr>
                    <td width="5%%">&nbsp;</td>
                    <td width="95%%" style="text-align:justify">%s. %s
                    </td>
                </tr>
                <tr>
                  <td style="font-size:10px">&nbsp;</td>
                </tr>
                <tr>
                    <td width="5%%">&nbsp;</td>
                    <td width="95%%" style="text-align:justify">%s
                    </td>
                </tr>
                <tr>
                  <td width="5%%">&nbsp;</td>
                  <td width="3%%" style="font-size: 10px; font-weight: bold;">*)</td>
                  <td width="92%%" style="text-align:justify;font-size: 10px; font-weight: bold;">Izin penyelenggaraan berlaku nasional, komitmen yang tertuang dalam tabel diatas merupakan komitmen minimal yang wajib dipenuhi oleh %s</td>
                </tr>
                <tr>
                  <td style="font-size:10px">&nbsp;</td>
                </tr>', $this->array_abjad[$array_tertutup], $media_jaringan, $table_komitmen, $permohonan->data_header->nama_perusahaan);

                        $list_komitmen_kinerja .= sprintf('<tr>
                <td width="5%%">&nbsp;</td>
                <td width="95%%" style="text-align:justify">%s. %s
                </td>
            </tr>
            <tr>
              <td style="font-size:10px">&nbsp;</td>
            </tr>
            <tr>
                <td width="5%%">&nbsp;</td>
                <td width="95%%" style="text-align:justify">%s
                </td>
            </tr>
            <tr>
              <td width="5%%">&nbsp;</td>
              <td width="3%%" style="font-size: 10px; font-weight: bold;">*)</td>
              <td width="92%%" style="text-align:justify;font-size: 10px; font-weight: bold;">Izin penyelenggaraan berlaku nasional, komitmen yang tertuang dalam tabel diatas merupakan komitmen minimal yang wajib dipenuhi oleh %s</td>
            </tr>
            <tr>
              <td style="font-size:10px">&nbsp;</td>
            </tr>', $this->array_abjad[$array_tertutup], $media_jaringan, $table_komitmen_kinerja, $permohonan->data_header->nama_perusahaan);
                    } else {
                        $list_komitmen .= sprintf('
                <tr>
                    <td width="5%%">&nbsp;</td>
                    <td width="95%%" style="text-align:justify">%s
                    </td>
                </tr>
                <tr>
                  <td width="5%%">&nbsp;</td>
                  <td width="3%%" style="font-size: 10px; font-weight: bold;">*)</td>
                  <td width="92%%" style="text-align:justify;font-size: 10px; font-weight: bold;">Izin penyelenggaraan berlaku nasional, komitmen yang tertuang dalam tabel diatas merupakan komitmen minimal yang wajib dipenuhi oleh %s</td>
                </tr>
                <tr>
                  <td style="font-size:10px">&nbsp;</td>
                </tr>', $table_komitmen, $permohonan->data_header->nama_perusahaan);

                        $list_komitmen_kinerja .= sprintf('
            <tr>
                <td width="5%%">&nbsp;</td>
                <td width="95%%" style="text-align:justify">%s
                </td>
            </tr>
            <tr>
              <td width="5%%">&nbsp;</td>
              <td width="3%%" style="font-size: 10px; font-weight: bold;">*)</td>
              <td width="92%%" style="text-align:justify;font-size: 10px; font-weight: bold;">Izin penyelenggaraan berlaku nasional, komitmen yang tertuang dalam tabel diatas merupakan komitmen minimal yang wajib dipenuhi oleh %s</td>
            </tr>
            <tr>
              <td style="font-size:10px">&nbsp;</td>
            </tr>', $table_komitmen_kinerja, $permohonan->data_header->nama_perusahaan);
                    }
                }
                else if($type_izin_jenis_tel == TypeIzinJenisTel::Jasa)
                {
                    $list_komitmen .= sprintf('
                <tr>
                    <td width="5%%">&nbsp;</td>
                    <td width="95%%" style="text-align:justify">%s
                    </td>
                </tr>
                <tr>
                    <td style="font-size:10px">&nbsp;</td>
                </tr>
                <tr>
                <td width="5%%">&nbsp;</td>
                <td width="3%%" style="font-size: 10px; font-weight: bold;">*)</td>
                <td width="92%%" style="text-align:justify;font-size: 10px">Periode tahun pertama penyelenggaraan terhitung sejak berlaku efektifnya izin penyelenggaraan sampai dengan akhir tahun buku;</td>
              </tr>
              <tr>
                <td width="5%%">&nbsp;</td>
                <td width="3%%" style="font-size: 10px; font-weight: bold;">*)</td>
                <td width="92%%" style="text-align:justify;font-size: 10px">Dalam hal izin penyelenggaraan berlaku efektif setelah tanggal 31 Oktober, periode tahun pertama penyelenggaraan terhitung sejak tanggal izin penyelenggaraan berlaku efektif sampai dengan akhir tahun buku berikutnya; dan</td>
              </tr>
              <tr>
                <td width="5%%">&nbsp;</td>
                <td width="3%%" style="font-size: 10px; font-weight: bold;">*)</td>
                <td width="92%%" style="text-align:justify;font-size: 10px">Periode tahun kedua dan seterusnya terhitung sesuai dengan tahun buku (1 Januari sampai dengan 31 Desember)</td>
              </tr>
              <tr>
                <td width="5%%">&nbsp;</td>
                <td width="3%%" style="font-size: 10px; font-weight: bold;">*)</td>
                <td width="92%%" style="text-align:justify;font-size: 10px; font-weight: bold;">Izin penyelenggaraan berlaku nasional, komitmen yang tertuang dalam tabel diatas merupakan komitmen minimal yang wajib dipenuhi oleh %s</td>
              </tr>
              <tr>
                <td style="font-size:10px">&nbsp;</td>
              </tr>
              <tr>
                  <td width="5%%">&nbsp;</td>
                  <td width="95%%" style="text-align:justify">Dengan ditetapkannya penetapan ini, Izin Penyelenggaraan Jasa untuk Layanan %s dinyatakan mulai berlaku efektif.</td>
              </tr>', $table_komitmen, $permohonan->data_header->nama_perusahaan, $lj[0]);
                }
                
                $model_komitmen = new stdClass();
                $model_komitmen->nama_layanan = $lj[0];
                $model_komitmen->dengan_teknologi = '';
                $model_komitmen->table_komitmen = $list_komitmen;
                $model_komitmen->table_komitmen_kinerja = $list_komitmen_kinerja;

                if ($penanda_media != $lj[0]) {
                    array_push($array_layanan_cetak, $model_komitmen);
                }
                else{
                    foreach ($array_layanan_cetak as $alc) {
                        if ($alc->nama_layanan == $lj[0]) {
                            $list_komitmen_tertutup = $alc->table_komitmen;
                            $list_komitmen_tertutup .= $list_komitmen;

                            $list_komitmen_kinerja_tertutup = $alc->table_komitmen_kinerja;
                            $list_komitmen_kinerja_tertutup .= $list_komitmen_kinerja;
                            $alc->nama_layanan = $lj[0];
                            $alc->dengan_teknologi = 'dengan teknologi :';
                            $alc->table_komitmen = $list_komitmen_tertutup;
                            $alc->table_komitmen_kinerja = $list_komitmen_kinerja_tertutup;

                        }
                    }
                }
                
                if (count($lj) > 1) {
                    $penanda_media = $lj[0];
                    $array_tertutup++;
                }
            }

            $cetak_i = 0;
            foreach($array_layanan_cetak as $alc)
            {
                $data = [
                    'jenis_layanan_k' => strtoupper($alc->nama_layanan),
                    'jenis_layanan' => $alc->nama_layanan,
                    'nama_pt' => strtoupper($permohonan->data_header->nama_perusahaan),
                    'no_sk' => $permohonan->data_header->no_sk_izin,
                    'no_permohonan' => $permohonan->data_header->no_komitmen,
                    'dengan_teknologi' => $alc->dengan_teknologi,
                    'tanggal' => Carbon::parse($model->tanggal)->translatedFormat('d F Y'),
                    'jabatan' => $user->nama_jabatan,
                    'nama_penanda_tangan' => strtoupper($user->nama),
                    'list_komitmen' => $alc->table_komitmen,
                    'list_kinerja' => $alc->table_komitmen_kinerja
                ];
                $cetak_i++;
                if($type_izin_jenis_tel == TypeIzinJenisTel::Jaringan)
                {
                    $pdf->writeHTML(view('sk.sk_penetapan_jaringan', $data)->render());
                }
                else if($type_izin_jenis_tel == TypeIzinJenisTel::Jasa)
                {
                    $pdf->writeHTML(view('sk.sk_penetapan_jasa', $data)->render());
                }
               
                if($cetak_i != count($array_layanan_cetak))
                {
                    $pdf->AddPage();
                }
            }

          
            $pdf->output($file_pdf_path, 'F');

            $parser = new Parser();
            $pdf_parser = $parser->parseFile($file_pdf_path);
            $pages = $pdf_parser->getPages();
            $i = 1;
            $model_coordinat = new stdClass();
            $base64_file = '';
            foreach ($pages as $page) {
                if ($page->getText($page, strtoupper($user->nama), true)) {
                    $coordinates = $page->getText($page, strtoupper($user->nama), true);
                    $model_coordinat->x = $coordinates['x'];
                    $model_coordinat->y = $coordinates['y'];
                    $model_coordinat->page = $i;

                    $base64_file = $this->Sign($model_coordinat, $file_pdf_path, $file_qr_path, $user);
                }
                $i++;
            }
            
            $model_return->file_pdf_path = $file_pdf_path;
            $model_return->result = true;
            $model_return->id_permohonan = $model->id_permohonan;
            $model_return->nama_file = $nama_file;
            $model_return->stream_file = $base64_file;
            $model_return->is_sklo = false;
            return $model_return;
        }
        catch(Exception $ex)
        {
            $model_return->result = false;
            return $model_return;
        }
    }

    private function Sign($model_coordinat, $file_pdf_path, $file_qr_path, $user)
    {
        $model_iot = new stdClass();
        $model_iot->id_user = $user->id;
        $model_iot->urx = $model_coordinat->x + 72;
        $model_iot->ury = $model_coordinat->y + 72;
        $model_iot->llx = $model_coordinat->x;
        $model_iot->lly = $model_coordinat->y;
        $model_iot->page = $model_coordinat->page;
        $model_iot->dataPDF = $file_pdf_path;
        $model_iot->imageSign = $file_qr_path;

        $base64File = $this->iot->sign_iotentik($model_iot);

        $pdf_decoded = base64_decode($base64File);
        $pdf = fopen ($file_pdf_path,'w');
        fwrite ($pdf,$pdf_decoded);
        fclose ($pdf);
        return $base64File;
    }

    public function PostSkloFile($model)
    {
        $q = sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = 'p_ulo_sklo' ", env('DB_DATABASE'));
        $id_ulo_sklo = DB::select($q)[0]->AUTO_INCREMENT;

        $q = sprintf("INSERT into p_ulo_sklo(id, id_permohonan, no_sklo, tanggal_input) values(%d, %d, '%s', '%s')", $id_ulo_sklo, $model->id_permohonan, $model->no_sklo, Carbon::now()->format('Y-m-d H:i:s'));
        $a = DB::insert($q);

        $q = sprintf("INSERT into p_sk_ulo_file(id_ulo_sklo, nama, stream) values(%d, '%s', '%s')", $id_ulo_sklo, $model->nama_file, $model->stream_file);
        $a = DB::insert($q);
    }

    public function PostSKFile($model)
    {
        $q = sprintf("INSERT into p_sk_izin_file(id_permohonan, nama, stream) values(%d, '%s', '%s')", $model->id_permohonan, $model->nama_file, $model->stream_file);
        $a = DB::insert($q);
    }

    public function PostSKPenomoranFile($model)
    {
        $q = sprintf("INSERT into p_sk_penomoran_file(id_penomoran_tel_pakai, nama, stream) values(%d, '%s', '%s')", $model->id_penomoran_tel_pakai, $model->nama_file, $model->stream_file);
        $a = DB::insert($q);
    }

    public function PostSKPenetapanKomit($model)
    {
        $q = sprintf("INSERT into p_sk_penetapan_komit_file(id_permohonan, nama, stream) values(%d, '%s', '%s')", $model->id_permohonan, $model->nama_file, $model->stream_file);
        $a = DB::insert($q);
    }

}