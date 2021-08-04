<?php

namespace App\Repo\Migrasi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SipppdihatiOldDb extends Model {

    protected $connection = 'mysql2';

    public function __construct()
    {

    }

    public function GetTablePermohonanPenomoran()
    {
        $result = DB::table('ditjenppi.tbl_t_permohonan')
            ->where('id_jenis_izin', 4)
            ->get();
        return $result;
    }

    public function GetTableLogPenomoran($data)
    {
        $result = DB::table('ditjenppi.tbl_t_log')
            ->where('id_permohonan', $data->id_permohonan)
            ->orderBy('created','desc')
            ->get();
        return $result;

    }

    public function GetTableBerkasPersyaratan($id_permohonan)
    {
        $result = DB::table('ditjenppi.tbl_t_berkas_persyaratan')
            ->where('id_permohonan', $id_permohonan)
            ->get();
        return $result;
    }

    public function GetTableHistoriLogPenomoran($id_permohonan)
    {
        $result = DB::table('ditjenppi.tbl_t_log')
            ->where('id_permohonan', $id_permohonan)
            ->get();
        return $result;
    }

    public function GetTableStatusPermohonan($id_status_permohonan)
    {
        $result = DB::table('ditjenppi.tbl_m_status_permohonan')
            ->where('id_status_permohonan', $id_status_permohonan)
            ->first();
        return $result;
    }

    public function GetTablePersonil($id_personil)
    {
        $result = DB::table('ditjenppi.tbl_m_personil')
            ->where('id_personil', $id_personil)
            ->first();
        return $result;
    }

    public function GetTableJabatan($id_jabatan)
    {
        $result = DB::table('ditjenppi.tbl_m_jabatan')
            ->where('id_jabatan', $id_jabatan)
            ->first();
        return $result;
    }


    public function FindNewSubJenisIzin($data){
        $result  = DB::table('ditjenppi.tbl_m_sub_jenis_izin')
            ->where('id_sub_jenis_izin', $data->id_sub_jenis_izin)
            ->first();
        return $result;
    }
    
    public function FindTableMPenomoranPrima($nomor)
    {   
        $result = DB::table('ditjenppi.tbl_m_pertel')
            ->where('nomor', $nomor)
            ->first();
        return $result;
    }

    public function findPermohonanLog($id_status_permohonan)
    {
        $result = DB::table('ditjenppi.tbl_m_status_permohonan')
            ->where('id_status_permohonan', $id_status_permohonan)
            ->first();
        return $result;
    }

    
    //DITJENPPI2
    
    public function FindTablePenomoranSipppdihati($id_permohonan)
    {
        $result = DB::table('ditjenppi2.t_syarat_izin_p')
            ->where('id_permohonan', $id_permohonan)
            ->where('id_syarat_izin_s', 104)
            ->where('id_syarat_izin_p', 423)
            ->get();
        return $result;
    }

    public function getFilePenomoran($id_permohonan)
    {
        $result = DB::table('ditjenppi2.t_syarat_izin_f')
            ->where('id_permohonan', $id_permohonan)
            ->whereIn('id_syarat_izin_s' , [294, 295, 296, 297, 298, 299])
            ->get();
        return $result;
    }

    public function FindTablePenomoranSipppdihati1($id_permohonan)
    {
        $result = DB::table('ditjenppi2.t_syarat_izin_p')
            ->where('id_permohonan', $id_permohonan)
            ->where('id_syarat_izin_s', 295)
            ->first();
        return $result;
    }

    public function FindJenisPenomoran ($id_permohonan)
    {
        $result = DB::table('ditjenppi2.t_syarat_izin_p')
            ->where('id_permohonan', $id_permohonan)
            ->where('id_syarat_izin_s', 104)
            ->where('id_syarat_izin_p', 422)
            ->first();
        return $result;
    }

    public function FindMuserOld($id_user)
    {
        $result = DB::table('ditjenppi2.m_user')
            ->where('id_user', $id_user)
            ->first();
        return $result;
    }

    public function GetTablePermohonanPenomoranSipppdihati()
    {
        $result = DB::table('ditjenppi2.t_permohonan')
            ->leftJoin('ditjenppi2.t_syarat_izin_p', 't_syarat_izin_p.id_permohonan', '=', 't_permohonan.id_permohonan')
            
            ->where('t_syarat_izin_p.id_syarat_izin_s', 295)
            ->where('t_permohonan.id_jenis_izin', 51)
            ->where('t_permohonan.aktif', 1)
            
            // ->rightJoin('ditjenppi2.t_izin_terbit', 't_izin_terbit.id_permohonan', '=', 't_permohonan.id_permohonan')
            // ->whereIn('t_izin_terbit.aktif', [1,2])
            // ->where('t_permohonan.id_jenis_izin', 7)
            // ->where('t_syarat_izin_p.id_syarat_izin_p', 423)
            // ->select('t_permohonan.*','t_izin_terbit.no_izin')

            ->get();
        return $result;
    }

    public function GetTablePermohonanSipppdb1()
    {
        $result = DB::table('sippp_db1.p_permohonan_ok')->get();
        return $result;
    }

    public function findTableTnomorSK($data)
    {
        $result = DB::table('ditjenppi.tbl_t_nomor_sk')
            //->where('id_pemohon', $data)
            ->first();
        return $result;
    }

    public function findPpiUsers($data)
    {
        $result = DB::table('ditjenppi.ppi_users')
            ->where('id', $data->user_id)
            ->first();
        return $result; 
    }

    public function TableTtpermohonan($data)
    {
        $result = DB::table('ditjenppi.tbl_t_permohonan')
            ->where('kode', $data->no_penyelenggaraan)
            ->first();
        return $result;
    }

    public function Table_t_nomor_Sk($data)
    {
        $result = DB::table('ditjenppi.tbl_t_nomor_sk')
            ->where('id_permohonan', $data->id_permohonan)
            ->first();
        return $result;
    }

    public function Table_t_izin_penomoran($data)
    {
        $result = DB::table('ditjenppi.tbl_t_izin_penomoran')
            ->where('id_permohonan', $data->id_permohonan)
            ->first();
        return $result;
    }

    public function GetTablePemohon($id_pemohon){
        $result = DB::table('ditjenppi2.t_pemohon')
            ->where('id_pemohon', $id_pemohon)
            ->first();
        return $result;
    }

    public function GetTablePermohonanByJaringan()
    {
        $result = DB::table('ditjenppi2.t_permohonan')
            ->rightJoin('ditjenppi2.t_izin_terbit', 't_permohonan.id_permohonan', '=', 't_izin_terbit.id_permohonan')
            ->where('t_permohonan.id_jenis_izin','=', 6)
            ->where('t_permohonan.aktif','=', 1)
            ->whereIn('t_izin_terbit.aktif', [1,2])
            //->where('t_permohonan.id_permohonan', 1087)
            ->select('t_permohonan.*', 't_izin_terbit.pdf_generate','t_izin_terbit.no_izin')
            ->get();
        return $result;
    }

    public function GetTablePermohonanByTelsus(){
        $result = DB::table('ditjenppi2.t_permohonan')
            ->rightJoin('ditjenppi2.t_izin_terbit', 't_permohonan.id_permohonan', '=', 't_izin_terbit.id_permohonan')
            ->where('t_permohonan.id_jenis_izin','=', 8)
            ->where('t_permohonan.aktif','=',1)
            ->whereIn('t_izin_terbit.aktif', [1,2])
            //->where('t_permohonan.id_permohonan', 6535)
            ->select('t_permohonan.*', 't_izin_terbit.pdf_generate','t_izin_terbit.no_izin')
            ->get();
        return $result;
    }

    public function findPermohonanPrima($no_permohonan){
      
        $result = DB::table('ditjenppi.tbl_t_permohonan')
            ->where('kode', $no_permohonan)
            ->first();
        return $result;

    }

    public function findTabelLogPermohonan($old_permohonan){
        $result = DB::table('ditjenppi.tbl_t_log')
            ->where('id_permohonan', $old_permohonan->id_permohonan)
            ->get();
        return $result;
    }

    public function GetTablePermohonanByJasa()
    {
        $result = DB::table('ditjenppi2.t_permohonan') 
            ->leftJoin('ditjenppi2.t_izin_terbit', 't_permohonan.id_permohonan', '=', 't_izin_terbit.id_permohonan')
            ->where('t_permohonan.id_jenis_izin','=',7)
            ->where('t_permohonan.aktif','=',1)
            ->whereIn('t_izin_terbit.aktif', [1,2])
            //->whereIn('t_permohonan.id_permohonan', [847, 1728, 6703])
            ->select('t_permohonan.*', 't_izin_terbit.pdf_generate','t_izin_terbit.no_izin')
            ->get();
        return $result;    
    }

    public function GetTablePermohonanbyPos(){
        $result = DB::table('ditjenppi2.t_permohonan')
            ->leftJoin('ditjenppi2.t_izin_terbit', 't_permohonan.id_permohonan', '=', 't_izin_terbit.id_permohonan')
            ->whereIn('t_permohonan.id_jenis_izin', [1, 3, 5])
            ->where('t_permohonan.aktif', 1)
            ->whereIn('t_izin_terbit.aktif', [1,2])
            //->whereIn('t_permohonan.id_permohonan', [1835, 6039, 7209])
            ->select('t_permohonan.*', 't_izin_terbit.pdf_generate','t_izin_terbit.no_izin')
            ->get();
        return $result;
    }

    public function GetTableIzinTerbitbyIdPermohonan($id_permohonan)
    {
        $result = DB::table('ditjenppi2.t_izin_terbit')
            ->where('id_permohonan', $id_permohonan)
            ->where('aktif', 1)
            ->first();
        return $result;
    }

    public function GetTableDataNib($id_data_nib)
    {
        $q = sprintf("SELECT * from ditjenppi2.t_data_nib where id_data_nib = $id_data_nib");
        $result = DB::select($q);
        return $result;
    }

    public function GetTableHistoriPermohonan($id_permohonan){   
        $result = DB::table('ditjenppi2.t_histori_permohonan')
            ->where('id_permohonan',$id_permohonan)
            ->whereIn('aktif', [1, 0])
            ->orderBy('id_histori_permohonan', 'asc')
            ->get();
        return $result;
    }

    public function get_t_log_permohonan($id_permohonan)
    {
        $result = DB::table('ditjenppi.tbl_t_log')
            ->where('id_permohonan',$id_permohonan)
            ->get();
        return $result;
    }

    public function GetTableHistoriPermohonanAktf($id_permohonan){
        $result = DB::table('ditjenppi2.t_histori_permohonan')
            ->where('id_permohonan',$id_permohonan)
            ->where('aktif', 1)
            ->first();
        return $result;
    }

    public function GetTableAktivitasWorkFlow($id_aktivitas_workflow)
    {
        $q = sprintf("SELECT * from ditjenppi2.t_aktivitas_workflow where id_aktivitas_workflow = $id_aktivitas_workflow");
        $result = DB::select($q);
        return $result;
    }

    public function GetTableMUser($id_user)
    {
        $result = DB::table('ditjenppi2.m_user')
            ->where('id_user', $id_user)
            ->first();
        return $result;
    }

    public function GetTableMRole($id_role)
    {
        $result = DB::table('ditjenppi2.m_role')
            ->where('id_role', $id_role)
            ->first();
        return $result;
    }
    
    public function GetTableSyaratIzinPJasa($id_permohonan)
    {
        $result = DB::table('ditjenppi2.t_syarat_izin_p')
                    ->where('id_permohonan',$id_permohonan)
                    ->whereIn('id_syarat_izin_s', [181, 184, 197])
                    ->where('aktif',1)
                    ->get();
        return $result;            
    }

    public function GetTableSyaratIzinPJaringan($id_permohonan){
        $result = DB::table('ditjenppi2.t_syarat_izin_p')
                ->where('id_permohonan',$id_permohonan)
                ->whereIn('id_syarat_izin_s', [182, 185])
                ->where('aktif',1)
                ->get();
        return $result;        
    }

    public function GetTableSyaratIzinPTelsus($id_permohonan){
        
        $result = DB::table('ditjenppi2.t_syarat_izin_p')
            ->where('id_permohonan', $id_permohonan)
            ->whereIn('id_syarat_izin_s', [216])
            ->where('aktif',1)
            ->get();
        return $result; 
    }

    public function GetTableSyaratIzinPpos($id_permohonan){
        $result = DB::table('ditjenppi2.t_syarat_izin_p')
                    ->where('id_permohonan',$id_permohonan)
                    ->whereIn('id_syarat_izin_s',[ 1, 20, 25])
                    ->where('aktif',1)
                    ->get();
        return $result;
    }

    public function GetTableSyaratIzinPposCakWilayah($id_permohonan){
        $result = DB::table('ditjenppi2.t_syarat_izin_p')
                    ->where('id_permohonan',$id_permohonan)
                    ->where('aktif',1)
                    ->whereIn('id_syarat_izin_s', [30, 31, 77, 78, 79, 241, 242, 243, 244, 246])
                    ->get();
        return $result;
    }

    public function GetTablePermohonanByNoizin($no_izin, $id_data_nib){        
        $result = DB::table('ditjenppi2.t_permohonan')
                ->where('no_izin_ref',$no_izin)
                ->where('id_data_nib',$id_data_nib)
                ->where('aktif', 1)
                ->first();
        return $result;
    }

    public function GetTablePermohonanByNoizinRef($no_permohonan){        
        $result = DB::table('ditjenppi2.t_permohonan')
                ->where('no_permohonan',$no_permohonan)
                ->where('aktif', 1)
                ->first();
        return $result;
    }

    public function GetJenisULo($permohonan_old){
        $id_syarat_izin_s = $this->GetIdjenisULObyIdjenis($permohonan_old->id_jenis_izin);
        $result = DB::table('ditjenppi2.t_syarat_izin_p')
                    ->where('id_permohonan', $permohonan_old->id_permohonan)
                    ->whereIn('id_syarat_izin_s', $id_syarat_izin_s)
                    ->where('aktif', 1)
                    ->get();
        return $result;
    }

    public function GetIdjenisULObyIdjenis($id_jenis)
    {   
        switch($id_jenis) {
            case 11:
              $id_syarat_izin_s = [182,185];
              break;
            case 13:
              $id_syarat_izin_s = [197];
              break;
            case 15:
              $id_syarat_izin_s = [181,184];
              break;  
            default:
              $id_syarat_izin_s = null;
        }

        return $id_syarat_izin_s;
    }

    public function GetIdSyaratIzinSbyLayanan($jenisLayanan){
        $result = DB::table('ditjenppi2.m_syarat_izin_s')
                    ->where('teks_judul', 'like', '%' . $jenisLayanan . '%')
                    ->where('aktif', 1)
                    ->get();
        return $result;
    }

    public function GetMsyaratIzinPByIdSyaratIzinS($id_syarat_izin_s)
    {
        $result = DB::table('ditjenppi2.m_syarat_izin_p')
                    ->where('id_syarat_izin_s', $id_syarat_izin_s)
                    ->where('aktif', 1)
                    ->get();
        return $result;
    }

    public function GetMsyaratIzinPByIdSyaratIzinSandP($id_syarat_izin_s,$id_syarat_izin_p){
        $result = DB::table('ditjenppi2.m_syarat_izin_p')
            ->where('id_syarat_izin_s', $id_syarat_izin_s)
            ->where('id_syarat_izin_p', $id_syarat_izin_p)
            ->where('aktif', 1)
            ->first();
        return $result;
    }

    public function GetMsyaratIzinPByIdSyaratIzinP($id_syarat_izin_p){
        $result = DB::table('ditjenppi2.m_syarat_izin_p')
            ->where('id_syarat_izin_p', $id_syarat_izin_p)
            ->where('aktif', 1)
            ->first();
        return $result;
    }

    public function GetMsyaratIzinSbyIdIzinGrup($id_syarat_izin_group){
        $result = DB::table('ditjenppi2.m_syarat_izin_s')
                    ->where('id_syarat_izin_grup', $id_syarat_izin_group)
                    ->where('aktif', 1)
                    ->get();  
        return $result;
    }

    public function GetDataUlo($id_izin_s, $id_izin_p, $id_permohonan){
        
       $result = DB::table('ditjenppi2.t_syarat_izin_p')
            ->where('id_permohonan', $id_permohonan)
            ->where('id_syarat_izin_s', $id_izin_s)
            ->where('id_syarat_izin_p', $id_izin_p)
            ->where('aktif', 1)
            ->get();
        return $result;
    }

    public function GetDataKinerja($id_izin_s, $id_permohonan){
        $result = DB::table('ditjenppi2.t_syarat_izin_p')
            ->where('id_permohonan', $id_permohonan)
            ->where('id_syarat_izin_s', $id_izin_s)
            ->where('nilai_string','!=',"Network Availability (%)")
            ->where('nilai_string','!=',"Pencapaian Mean Time To Restore (jam)")
            ->where('nilai_string','!=',"")
            ->where('nilai_string','!=',null)
            ->where('aktif', 1)
            ->get();
        return $result;
    }

    public function GetStatusbyIdAktivitasWorkfolow($id_aktivitas_workflow){
        $result = DB::table('ditjenppi2.t_aktivitas_workflow')
            ->where('id_aktivitas_workflow', $id_aktivitas_workflow)
            ->where('aktif', 1)
            ->first();
        return $result;
    }

    public function GetMSyaratIzinGroup($id_jenis_izin){
        $result = DB::table('ditjenppi2.m_syarat_izin_grup')
            ->where('id_jenis_izin', $id_jenis_izin)
            ->where('aktif', 1)
            ->get();
        return $result;
    }

    public function GetTableTsyaratIzinSByIdSyarat($id_permohonan,$id_syarat_izin_s){
        $result = DB::table('ditjenppi2.t_syarat_izin_s')
            ->where('id_permohonan', $id_permohonan)
            ->where('id_syarat_izin_s', $id_syarat_izin_s)
            ->where('aktif', 1)
            ->first();
        return $result;
    }

    public function GetTableTsyaratIzinFByIdsIdPermohonan($id_permohonan, $id_syarat_izin_s){
        $result = DB::table('ditjenppi2.t_syarat_izin_f')
            ->where('id_permohonan', $id_permohonan)
            ->where('id_syarat_izin_s', $id_syarat_izin_s)
            ->where('aktif', 1)
            ->get();
        return $result;
    }

    public function GetTableTsyaratIzinPByIdSyarat($id_permohonan, $id_syarat_izin_s){
        $result = DB::table('ditjenppi2.t_syarat_izin_p')
            ->where('id_permohonan', $id_permohonan)
            ->where('id_syarat_izin_s', $id_syarat_izin_s)
            ->where('nilai_string','!=',"")
            ->where('aktif', 1)
            ->get();
        return $result;
    }
    
    public function GetTableTsyaratIzinFPByIdSyarat($id_permohonan, $id_syarat_izin_s){
        
        $result = DB::table('ditjenppi2.t_syarat_izin_f_p')
            ->where('id_permohonan', $id_permohonan)
            ->where('id_syarat_izin_s', $id_syarat_izin_s)
            ->where('aktif', 1)
            ->get();
        return $result;
    }

    public function GetTsyaratIzinSFile($syarat_komitmen){
        $result = DB::table('ditjenppi2.t_syarat_izin_f')
            ->where('id_permohonan', $syarat_komitmen->id_permohonan)
            ->where('id_syarat_izin_s', $syarat_komitmen->id_syarat_izin_s)
            ->where('aktif',1)
            ->get();
        return $result;
    }

    public function GetTsyaratIzinFp($syarat_izin_p, $komitmen, $m_syarat_izin_p){

        $result = DB::table('ditjenppi2.t_syarat_izin_f_p')
            ->where('id_permohonan', $syarat_izin_p->id_permohonan)
            ->where('id_syarat_izin_s', $komitmen->id_syarat_izin_s)
            //->where('id_syarat_izin_p', $komitmen->id_syarat_izin_p)
            ->where('aktif',1)
            ->get();
        return $result;

    }

    public function findKomitmenUloMsyaratIzinP($syarat_izin_p){
        $result = DB::table('ditjenppi2.m_syarat_izin_p')
                ->where('id_syarat_izin_s', $syarat_izin_p->id_syarat_izin_s)
                ->where('id_syarat_izin_p', $syarat_izin_p->id_syarat_izin_p)
                ->where('aktif',1)
            ->first();
        return $result;
    }

    public function getBuktibayar($data){
        $result = DB::table('ditjenppi2.t_syarat_izin_f')
            ->where('id_permohonan', $data->id_permohonan)
            ->whereIn('id_syarat_izin_s', [73, 75, 76])
            ->where('aktif',1)
            ->get();
        return $result;
    }

    public function GetjenisLayananPos($id_permohonan){

        $result = DB::table('ditjenppi2.t_syarat_izin_p')
            ->where('id_permohonan', $id_permohonan)
            ->where('id_syarat_izin_s', 1)
            ->where('aktif', 1)
            ->get();            
        return $result;
    }

    public function GetMediaJaringan($id_permohonan)
    {
        $result = DB::table('ditjenppi2.t_syarat_izin_p')
            ->where('id_permohonan', $id_permohonan)
            ->where('id_syarat_izin_s', 183)
            ->where('aktif', 1)
            ->get();            
        return $result;
    }

    public function getTableRekomTerbit($histori)
    {

        $result = DB::table('ditjenppi2.t_rekom_terbit')
            ->where('id_permohonan', $histori->id_permohonan)
            ->get();
        return $result;
        
    }

    public function findFlaggingdata($rekom){

        $result = array();
        
        $flagging_data = $this->getFlaggingdata($rekom->id_permohonan);
        
        if(!empty($flagging_data)){

            foreach($flagging_data as $fla_data){
           
                $ts   = strtotime($fla_data->date_added);

                $date_rekom = date('Y-m-d', $ts);
                
                if($date_rekom == $rekom->tgl_terbit){

                    $result = $fla_data;
                    return $result;
                    
                }

            }

        }    
        return $result;
    }
    
    public function findFlaggingdataCabutPenomoran($id_permohonan){
        $result = DB::table('ditjenppi2.t_flagging_data')
            ->where('id_permohonan', $id_permohonan)
            ->where('id_flagging_izin', 33)
            ->first();
        return $result;    
    }
    
    public function findRekomTerbit($id_permohonan){
        $result = DB::table('ditjenppi2.t_rekom_terbit')
            ->where('id_permohonan', $id_permohonan)
            ->where('aktif', 1)
            ->first();
        return $result; 
    }

    public function getFlaggingdata($id_permohonan){
        $result = DB::table('ditjenppi2.t_flagging_data')
            ->where('id_permohonan', $id_permohonan)
            ->whereIn('id_flagging_izin', [24, 28, 29, 38])
            ->first();
        return $result;    
    }

    public function getThistoribyUploadSpm($bayar){
        $result = DB::table('ditjenppi2.t_histori_permohonan')
            ->where('id_permohonan', $bayar->id_permohonan)
            ->where('id_aktivitas_workflow', [1,57,63])
            ->first();            
        return $result;

    }

    public function GetTableNoizinRef($no_sk_izin){
        $result = DB::table('ditjenppi2.t_permohonan')
            ->where('no_izin_ref', $no_sk_izin)
            ->where('aktif', 1)
            ->first();            
        return $result;
    }

    public function getTablePermohonan($data)
    {
        $result = DB::table('ditjenppi2.t_permohonan')
            ->where('no_permohonan', $data->no_penyelenggaraan)
            ->where('aktif', 1)
            ->first();            
        return $result;
    }

    public function GetTableRekomByIdPermohonan($id_permohonan){
        $result = DB::table('ditjenppi2.t_rekom_terbit')
            ->where('id_permohonan', $id_permohonan)
            ->where('aktif', 1)
            ->first();            
        return $result;
    }

    public function FindBlokPenomoran($id_permohonan)
    {
        $result = DB::table('ditjenppi2.t_syarat_izin_p')
            ->where('id_permohonan', $id_permohonan)
            ->where('id_syarat_izin_s', 104)
            ->whereIn('id_syarat_izin_p', [423, 557, 690, 950, 1491])
            ->where('aktif', 1)
            ->orderBy('date_added','desc')
            ->first();            
        return $result;
    }
   
}
?>