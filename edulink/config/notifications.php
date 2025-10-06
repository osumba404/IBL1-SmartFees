<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email Notifications
    |--------------------------------------------------------------------------
    */
    'email_enabled' => env('EMAIL_NOTIFICATIONS_ENABLED', true),
    
    /*
    |--------------------------------------------------------------------------
    | SMS Notifications
    |--------------------------------------------------------------------------
    */
    'sms_enabled' => env('SMS_NOTIFICATIONS_ENABLED', false),
    
    /*
    |--------------------------------------------------------------------------
    | SMS Provider Configuration
    |--------------------------------------------------------------------------
    */
    'sms' => [
        'provider' => env('SMS_PROVIDER', 'africastalking'),
        'api_key' => env('SMS_API_KEY'),
        'username' => env('SMS_USERNAME'),
        'sender_id' => env('SMS_SENDER_ID', 'EDULINK'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Notification Types
    |--------------------------------------------------------------------------
    */
    'types' => [
        'payment_confirmation' => [
            'email' => true,
            'sms' => true,
        ],
        'payment_reminder' => [
            'email' => true,
            'sms' => true,
        ],
        'enrollment_confirmation' => [
            'email' => true,
            'sms' => false,
        ],
        'password_reset' => [
            'email' => true,
            'sms' => false,
        ],
    ],
];