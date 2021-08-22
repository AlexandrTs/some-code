<?php

return [
    'log_request' => true,
    'log_failed_request' => true,
    'log_event' => true,
    'debug' => env('API_DEBUG', false),
    'admin_email' => env('ADMIN_EMAIL', null),

    'loans_base_url' => env('LOANS_BASE_URL'),
    'loans_auth_user' => env('LOANS_AUTH_USER'),
    'loans_auth_password' => env('LOANS_AUTH_PASSWORD'),

    'jysan_payment_client_shared_key' => env('JYSAN_PAYMENT_CLIENT_SHARED_KEY'),
    'jysan_payment_client_username' => env('JYSAN_PAYMENT_CLIENT_USERNAME'),
    'jysan_payment_client_password' => env('JYSAN_PAYMENT_CLIENT_PASSWORD'),

    'ecom_url' => env('ECOM_URL'),

];
