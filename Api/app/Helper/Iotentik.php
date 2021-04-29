<?php

namespace App\Helper;

use Illuminate\Support\Carbon;
use App\Repo\IotentikDb;
use stdClass;

class Iotentik
{
    private $oss_server;
    private $idb;
    public function __construct()
    {
        //$this->middleware('auth');
        $this->idb = new IotentikDb();
        $this->oss_server = 'http://36.89.68.38/';
    }
	
	function getToken($username, $password) {
		$url = $this->oss_server.'login';

		$post 	= '{
			"username": "'.$username.'",
			"password": "'.$password.'" 
		}';
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_POST, 1);

		$headers = array();
		$headers[] = "Content-Type: application/json";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close ($ch);

		$res 	= json_decode($result, true);		
		return $res;
	}	

	function getCert($username, $token) {		
		$url = $this->oss_server.'listCert';

		$post 	= '{
			"username": "'.$username.'"
		}';
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_POST, 1);

		$headers = array();
		$headers[] = "Authorization: Bearer ".$token;
		$headers[] = "Content-Type: application/json";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close ($ch);

		$res 	= json_decode($result, true);		
		return $res;
	}		

	function getTokenUser($username, $token) {		
		$url = $this->oss_server.'getToken';

		$post 	= '{
			"username": "'.$username.'"
		}';
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_POST, 1);

		$headers = array();
		$headers[] = "Authorization: Bearer ".$token;
		$headers[] = "Content-Type: application/json";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close ($ch);

		$res 	= json_decode($result, true);		
		return $res;
	}		

	function signProcess($model) {		
		$url = $this->oss_server.'signPDF';
		
		$ch = curl_init();
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$finfo = finfo_file($finfo, $model->dataPDF);
		$finfo2 = finfo_open(FILEINFO_MIME_TYPE);
		$finfo2 = finfo_file($finfo2, $model->imageSign);

		if (function_exists('curl_file_create')) {
			$cFile = new \CURLFILE($model->dataPDF,$finfo,basename($model->dataPDF));
			$cImage = new \CURLFILE($model->imageSign,$finfo2,basename($model->imageSign));
		} else {
			$cFile = '@' . realpath($model->dataPDF);
			$cImage = '@' . realpath($model->imageSign);
		}

		$post 	= array(
				'username' 		=> $model->certUser,
				'passphrase'	=> $model->certPass,
				'idkeystore'	=> $model->certId,
				'token'			=> $model->tokenUser,
				'urx'			=> $model->urx + 110,
				'ury'			=> $model->ury + 10,
				'llx'			=> $model->llx + 110,
				'lly'			=> $model->lly + 10,
				'pdf'			=> $cFile,
				'imageSign'		=> $cImage,
				'page'			=> $model->page,
				'reason'		=> 'Sistem Layanan Online Perizinan Penyelenggaraan Pos dan Informatika (e-Licensing)',
				'location'		=> 'Kominfo, Jakarta'	
		);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

		$headers = array();
		$headers[] = "Authorization: Bearer ".$model->token;
		$headers[] = "Content-Type: multipart/form-data";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error CURL:' . curl_error($ch);
		}
		curl_close ($ch);

		$res 	= json_decode($result, true);
		return $res;
	}

	function sign_iotentik($model) {	
        $tanggal_now  = Carbon::now();	
        $tanggal_expired  = Carbon::now()->addMonths(11);

		$token_iot = $this->idb->GetLoginIotentik(1);

		if(!empty($token_iot)){
            // if($token_iot[0]->timestamp <= $tanggal_now){
            //     $authToken = $token_iot[0]->token;
            // }else{
            //     $getToken = $this->getToken($token_iot[0]->username, $token_iot[0]->password);
            //     $authToken = $getToken['data']['token'];

			// 	$input = new stdClass();
			// 	$input->token = $authToken;
			// 	$input->timestamp = $tanggal_expired;

			// 	$resultUpdate = $this->idb->UpdateTokenIotentik($input);
            // }

            $getToken = $this->getToken($token_iot[0]->username, $token_iot[0]->password);
            $authToken = $getToken['data']['token'];
            
            $input = new stdClass();
            $input->token = $authToken;
            $input->timestamp = $tanggal_expired;

           //$resultUpdate = $this->idb->UpdateTokenIotentik($input);
        }else{
            return false;
        }

        $data_user = $this->idb->GetUserIotentikByUser($model->id_user);
        if(empty($data_user)){
            return false;
        }
        
        $getTokenUser = $this->getTokenUser($data_user[0]->username,$authToken);
        if($getTokenUser['code'] == '200'){
            $newToken = $getTokenUser['message'];
            $arr = explode(', ',trim($newToken));
            $tokenUser = $arr[0];
        }
        
        $model_send = new stdClass();
        $model_send->certUser = $data_user[0]->username;
        $model_send->certPass = $data_user[0]->password;
        $model_send->certId = $data_user[0]->id_cert;
        $model_send->tokenUser = $tokenUser;
        $model_send->urx = $this->getCoordinate($model->urx);
        $model_send->ury = $this->getCoordinate($model->ury);
        $model_send->llx = $this->getCoordinate($model->llx);
        $model_send->lly = $this->getCoordinate($model->lly);
        $model_send->dataPDF = $model->dataPDF;
        $model_send->imageSign = $model->imageSign;
        $model_send->page = $model->page;
        $model_send->token = $authToken;

        $signPDF = $this->signProcess($model_send);
		if($signPDF['status'] == '200'){
			$base64PDF = $signPDF['data'];
			return $base64PDF;
		}else{
			return false;
		}
    }
    
    function getCoordinate($input){
        $arr = explode('.',trim($input));
        $result = $arr[0];
        
        return $result;
    }

}
