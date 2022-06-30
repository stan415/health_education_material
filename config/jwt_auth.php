<?php

return [
    'alg' => 'HS256',
    'key' => config('APP_KEY', 'key'),
    'exp' => 3600 * 24 * 10
];
