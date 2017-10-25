<?php

return [
    'prefix' => env('AESCRYPT_PREFIX', '__AESCRYPT__:'),
    'aeskey' => env('AESCRYPT_AESKEY', '__REPLACE!!!__:'),
    'base64_output' => env('AESCRYPT_BASE64_OUTPUT', false),

];
