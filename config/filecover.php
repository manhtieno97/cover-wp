<?php

return [
    'fileTypes' => explode(",", env('FILE_TYPE_COVER', 'psd,tif,jpeg,jpg,png,ai,eps,pdf,cdr,heic')),
    'fileSize' => [
        'lagger' => [
            'width' => env('IMAGE_COVER_WIDTH_LG', 720),
        ],
        'medium' => [
            'width' => env('IMAGE_COVER_WIDTH_MD', 500),
        ],
        'small' => [
            'width' => env('IMAGE_COVER_WIDTH_SM', 250),
        ],
    ],
];

