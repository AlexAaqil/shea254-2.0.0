<?php

return [
    'pixel_id' => env('META_PIXEL_ID'),
    'access_token' => env('META_CONVERSIONS_API_TOKEN'),
    'app_secret' => env('META_APP_SECRET'),
    'enabled' => env('META_PIXEL_ENABLED', false),
    'test_code' => env('META_TEST_EVENT_CODE'),
];