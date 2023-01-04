<?php

use Werk365\IdentityDocuments\Services\Google;

return [
    'ocrService' => tesseract::class,
    'faceDetectionService' => Google::class,
    'mergeImages' => false, // bool
];
