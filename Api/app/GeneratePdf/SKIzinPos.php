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

class SKIzinPos
{
    private $udb;
    private $iot; 
    private $pteldb;
    private $perdb;
    private $get_nomor;
    private $gen_nomor;
    public function __construct()
    {
        Carbon::setLocale('id');
        $this->iot = new Iotentik();
        $this->udb = new UserDb();
        $this->pteldb = new PermohonanTelDb();
        $this->perdb = new PerusahaanDb();
        $this->get_nomor = new GetNoDb();
        $this->gen_nomor = new GenerateNomor();
    }

    private function SetPdf()
    {
        $pdf_settings = Config::get('tcpdf');
        $pdf = new TCPdf($pdf_settings['page_orientation'], $pdf_settings['page_units'], $pdf_settings['page_format'], true);
        $pdf->SetFont('times', '', 11 );
        $pdf->SetMargins(PDF_MARGIN_LEFT, 20 ,PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(false);
        $pdf->SetFooterMargin(false);
        $pdf->AddPage();
        return $pdf;
    }

    public function Spm($input)
    {
        $model_return = new stdClass();
        try
        {
            $pdf = $this->SetPdf();
            
            $string_tanggal_input = '2020-10-01 06:37:10';
            $id_izin_jenis = $input->id_izin_jenis;
            $id_permohonan = $input->id_permohonan;
            $q_get_layanan = DB::select('SELECT a.*,b.layanan FROM p_permohonan_layanan a LEFT JOIN k_layanan b ON a.id_layanan=b.id WHERE a.id_permohonan="'.$id_permohonan.'" ORDER BY a.id DESC');
            $jenis_layanan = array();
            foreach ($q_get_layanan as $key => $value) {
                array_push($jenis_layanan,'Layanan '.$value->layanan);
            }
            $q_get_izin_jenis = DB::select('SELECT a.* FROM k_izin_jenis a WHERE a.id="'.$id_izin_jenis.'" ORDER BY a.id DESC LIMIT 1');
            $izin_jenis = '';
            if($q_get_izin_jenis==NULL){
                $izin_jenis = '-';
            }else{
                foreach ($q_get_izin_jenis as $key => $value) {
                    $izin_jenis = $value->jenis;
                }
            }
            $total_bayar = 0;
            if($id_izin_jenis==7){
                $total_bayar = 5000000*count($q_get_layanan);
            }elseif($id_izin_jenis==1){
                $total_bayar = 2000000*count($q_get_layanan);
            }elseif($id_izin_jenis==6){
                $total_bayar = 1500000*count($q_get_layanan);
            }else{
                echo'';
            }
            $date_now = date('Y-m-d H:i:s');
            $tanggal_input = Carbon::parse($string_tanggal_input)->translatedFormat('d F Y');
            $convert_date_now = Carbon::parse($date_now)->translatedFormat('d F Y');
            $data = array(
                'id_permohonan' => $id_permohonan,
                'npwp' => $input->npwp,
                'nama_perusahaan' => $input->nama_pt,
                'no_penyelenggaraan' => $input->no_penyelenggaraan,
                'date_now' => $convert_date_now,
                'tanggal_input' => $convert_date_now,
                'izin_jenis' => 'Pos '.$izin_jenis,
                'jenis_layanan' => implode(', ',$jenis_layanan),
                'alamat_perusahaan' => $input->alamat_perusahaan,
                'no_telp_perusahaan' => $input->no_telp_perusahaan,
                'total' => $total_bayar,
            );

            $pdf->writeHTML(view('permohonan_pos.spm_pos', $data)->render());
            
            $nama_file_sk = uniqid().date('Ymdhis').'_spm.pdf';
            $file_pdf_path = storage_path('app/sk_temp/'.$nama_file_sk);
            $pdf->output($file_pdf_path, 'F');

            $model_return->list_path = $file_pdf_path;
            $model_return->result = true;
            return $model_return;
        }
        catch(Exception $ex)
        {
            $model_return->result = false;
            return $model_return;
        }
    }

    public function Kesanggupan($input)
    {
        $model_return = new stdClass();
        try
        {
            $pdf = $this->SetPdf();
            
            $id_permohonan = $input->id_permohonan;
            $q_get_layanan = DB::select('SELECT a.*,b.layanan FROM p_permohonan_layanan a LEFT JOIN k_layanan b ON a.id_layanan=b.id WHERE a.id_permohonan="'.$id_permohonan.'" ORDER BY a.id DESC');
            $jenis_layanan = array();
            foreach ($q_get_layanan as $key => $value) {
                array_push($jenis_layanan,'Layanan '.$value->layanan);
            }
            $data = array(
                'nama_perusahaan' => $input->nama_pt,
                'jenis_layanan' => implode(', ',$jenis_layanan)
            );

            $pdf->writeHTML(view('permohonan_pos.kesanggupan', $data)->render());
            
            $nama_file_sk = uniqid().date('Ymdhis').'_form_kesanggupan.pdf';
            $file_pdf_path = storage_path('app/sk_temp/'.$nama_file_sk);
            $pdf->output($file_pdf_path, 'F');

            $model_return->list_path = $file_pdf_path;
            $model_return->result = true;
            return $model_return;
        }
        catch(Exception $ex)
        {
            $model_return->result = false;
            return $model_return;
        }
    }

    public function GenerateSK($input)
    {
        $model_return = new stdClass();
        try
        {
            $pdf = $this->SetPdf();
            
            $id_permohonan = $input->id_permohonan;
            $q_get_layanan = DB::select('SELECT a.*,b.layanan FROM p_permohonan_layanan a LEFT JOIN k_layanan b ON a.id_layanan=b.id WHERE a.id_permohonan="'.$id_permohonan.'" ORDER BY a.id DESC');
            $jenis_layanan = array();
            foreach ($q_get_layanan as $key => $value) {
                array_push($jenis_layanan,'Layanan '.$value->layanan);
            }
            $data = array(
                'nama_pt' => $input->nama_pt,
                'nib' => $input->nib,
                'no_sk' => $input->no_sk,
                'jenis_izin' => $input->jenis_izin,
                'tanggal_approved' => $input->tanggal_input,
                'layanan' => implode('; ',$jenis_layanan)
            );

            if($input->id_izin_jenis=='7'){
                $pdf->writeHTML(view('permohonan_pos.sk_kabkota', $data)->render());
            }elseif($input->id_izin_jenis=='6'){
                $pdf->writeHTML(view('permohonan_pos.sk_nasional', $data)->render());
            }else{
                $pdf->writeHTML(view('permohonan_pos.sk_provinsi', $data)->render());
            }
            
            $nama_file_sk = uniqid().date('Ymdhis').'_sk_pos.pdf';
            $file_pdf_path = storage_path('app/sk_temp/'.$nama_file_sk);
            $pdf->output($file_pdf_path, 'F');

            $model_return->list_path = $file_pdf_path;
            $model_return->result = true;
            return $model_return;
        }
        catch(Exception $ex)
        {
            $model_return->result = false;
            return $model_return;
        }
    }
}