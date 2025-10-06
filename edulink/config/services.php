<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | M-Pesa Daraja API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for M-Pesa Daraja API integration for mobile payments
    | in Kenya. Supports both sandbox and production environments.
    |
    */

    'mpesa' => [
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_SHORTCODE'),
        'passkey' => env('MPESA_PASSKEY'),
        'callback_url' => env('MPESA_CALLBACK_URL', env('APP_URL') . '/webhooks/mpesa'),
        'timeout_url' => env('MPESA_TIMEOUT_URL', env('APP_URL') . '/webhooks/mpesa/timeout'),
        'result_url' => env('MPESA_RESULT_URL', env('APP_URL') . '/webhooks/mpesa/result'),
        'sandbox' => env('MPESA_SANDBOX', true),
        'base_url' => env('MPESA_SANDBOX', true) 
            ? 'https://sandbox.safaricom.co.ke' 
            : 'https://api.safaricom.co.ke',
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Payment Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Stripe payment processing for credit/debit cards
    | and other international payment methods.
    |
    */

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'currency' => env('STRIPE_CURRENCY', 'usd'),
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'sandbox' => env('PAYPAL_SANDBOX', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for available payment methods and their settings
    |
    */

    'payment_methods' => [
        'mpesa' => [
            'enabled' => env('MPESA_ENABLED', true),
            'name' => 'M-Pesa',
            'description' => 'Pay with M-Pesa mobile money',
            'fee_percentage' => env('MPESA_FEE_PERCENTAGE', 0),
            'fee_fixed' => env('MPESA_FEE_FIXED', 0),
        ],
        'stripe' => [
            'enabled' => env('STRIPE_ENABLED', true),
            'name' => 'Credit/Debit Card',
            'description' => 'Pay with Visa, Mastercard, or other cards',
            'fee_percentage' => env('STRIPE_FEE_PERCENTAGE', 2.9),
            'fee_fixed' => env('STRIPE_FEE_FIXED', 0.30),
        ],
        'bank_transfer' => [
            'enabled' => env('BANK_TRANSFER_ENABLED', true),
            'name' => 'Bank Transfer',
            'description' => 'Pay via bank transfer',
            'fee_percentage' => env('BANK_TRANSFER_FEE_PERCENTAGE', 0),
            'fee_fixed' => env('BANK_TRANSFER_FEE_FIXED', 0),
        ],
        'paypal' => [
            'enabled' => env('PAYPAL_ENABLED', true),
            'name' => 'PayPal',
            'description' => 'Pay with PayPal or credit card',
            'fee_percentage' => env('PAYPAL_FEE_PERCENTAGE', 2.9),
            'fee_fixed' => env('PAYPAL_FEE_FIXED', 0.30),
        ],
        'cash' => [
            'enabled' => env('CASH_ENABLED', true),
            'name' => 'Cash Payment',
            'description' => 'Pay in cash at the college',
            'fee_percentage' => env('CASH_FEE_PERCENTAGE', 0),
            'fee_fixed' => env('CASH_FEE_FIXED', 0),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Services Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for notification delivery channels
    |
    */

    'notifications' => [
        'email_enabled' => env('EMAIL_NOTIFICATIONS_ENABLED', true),
        'sms_enabled' => env('SMS_NOTIFICATIONS_ENABLED', false),
        'email' => [
            'enabled' => env('EMAIL_NOTIFICATIONS_ENABLED', true),
            'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@edulink.ac.ke'),
            'from_name' => env('MAIL_FROM_NAME', 'Edulink International College'),
        ],
        'sms' => [
            'enabled' => env('SMS_NOTIFICATIONS_ENABLED', false),
            'provider' => env('SMS_PROVIDER', 'africastalking'),
            'api_key' => env('SMS_API_KEY'),
            'username' => env('SMS_USERNAME'),
            'sender_id' => env('SMS_SENDER_ID', 'EDULINK'),
        ],
        'push' => [
            'enabled' => env('PUSH_NOTIFICATIONS_ENABLED', false),
            'firebase_key' => env('FIREBASE_SERVER_KEY'),
        ],
        'in_app' => [
            'enabled' => env('IN_APP_NOTIFICATIONS_ENABLED', true),
            'retention_days' => env('NOTIFICATION_RETENTION_DAYS', 30),
        ],
    ],

    'sms' => [
        'provider' => env('SMS_PROVIDER', 'africastalking'),
        'api_key' => env('SMS_API_KEY'),
        'username' => env('SMS_USERNAME'),
        'sender_id' => env('SMS_SENDER_ID', 'EDULINK'),
    ],

    /*
    |--------------------------------------------------------------------------
    | College Configuration
    |--------------------------------------------------------------------------
    |
    | Basic college information and settings
    |
    */

    'college' => [
        'name' => env('COLLEGE_NAME', 'Edulink International College Nairobi'),
        'short_name' => env('COLLEGE_SHORT_NAME', 'Edulink'),
        'address' => env('COLLEGE_ADDRESS', 'Nairobi, Kenya'),
        'phone' => env('COLLEGE_PHONE', '+254700000000'),
        'email' => env('COLLEGE_EMAIL', 'info@edulink.ac.ke'),
        'website' => env('COLLEGE_WEBSITE', 'https://edulink.ac.ke'),
        'logo' => env('COLLEGE_LOGO', '/images/logo.png'),
        'currency' => env('COLLEGE_CURRENCY', 'KES'),
        'currency_symbol' => env('COLLEGE_CURRENCY_SYMBOL', 'KSh'),
        'timezone' => env('COLLEGE_TIMEZONE', 'Africa/Nairobi'),
    ],

    /*
    |--------------------------------------------------------------------------
    | System Settings
    |--------------------------------------------------------------------------
    |
    | General system configuration and feature flags
    |
    */

    'system' => [
        'maintenance_mode' => env('SYSTEM_MAINTENANCE', false),
        'registration_enabled' => env('STUDENT_REGISTRATION_ENABLED', true),
        'payment_processing_enabled' => env('PAYMENT_PROCESSING_ENABLED', true),
        'auto_enrollment' => env('AUTO_ENROLLMENT_ENABLED', false),
        'email_verification_required' => env('EMAIL_VERIFICATION_REQUIRED', true),
        'admin_approval_required' => env('ADMIN_APPROVAL_REQUIRED', true),
        'grace_period_days' => env('PAYMENT_GRACE_PERIOD_DAYS', 7),
        'late_fee_percentage' => env('LATE_FEE_PERCENTAGE', 5),
        'max_payment_attempts' => env('MAX_PAYMENT_ATTEMPTS', 3),
        'session_timeout_minutes' => env('SESSION_TIMEOUT_MINUTES', 120),
    ],

];
