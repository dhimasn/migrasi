<?php

namespace App\GeneratePdf;

use PHPQRCode\QRcode;
use PHPQRCode\Constants;

class QrCodeGen
{
    public function __construct()
    {
    }

    public function Generate($url, $id_permohonan)
    {
        $filepath = storage_path('app/qr_temp/'.$id_permohonan.'.png');
        $logopath = public_path() . sprintf("/assets/media/logos/logo_kominfo.png");
       
        $codeContents = $url;
        QRcode::png($codeContents, $filepath, Constants::QR_ECLEVEL_H, 3, 2);
        $QR = imagecreatefrompng($filepath);
        $logo = imagecreatefromstring(file_get_contents($logopath));

        imagecolortransparent($logo, imagecolorallocatealpha($logo, 0, 0, 0, 127));
        imagealphablending($logo, false);
        imagesavealpha($logo, true);

        $QR_width = imagesx($QR);
        $QR_height = imagesy($QR);

        $logo_width = imagesx($logo);
        $logo_height = imagesy($logo);
        $logo_qr_width = $QR_width / 3;
        $scale = $logo_width / $logo_qr_width;
        $logo_qr_height = $logo_height / $scale;
        imagecopyresampled($QR, $logo, $QR_width / 3, $QR_height / 3, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        imagepng($QR, $filepath);//save to path server

        return $filepath;
    }
}