<?php

namespace App\Services;

use Intervention\Image\Image;
use Werk365\IdentityDocuments\Interfaces\OCR;
use Werk365\IdentityDocuments\Responses\OcrResponse;
use thiagoalessio\TesseractOCR\TesseractOCR;

class tesseract implements OCR
{
    public function ocr(Image $image): OcrResponse
    {
        // TODO: Add OCR and return text
        $response = (new TesseractOCR($image))
        ->run();
        return new OcrResponse($response);
    }
}
