<?php
return [
    /**
     * Recaptcha enabled
     */
    'enabled'    => env('RECAPTCHA_ENABLED', true),

    /**
     * Recaptcha origin
     */
    'origin'    => env('RECAPTCHA_ORIGIN', 'https://www.google.com/recaptcha'),

    /**
     * Site sitekey @see https://www.google.com/recaptcha/admin
     */
    'site_key'   => env('RECAPTCHA_SITE_KEY', ''),

    /**
     * Site secret @see https://www.google.com/recaptcha/admin
     */
    'secret_key'    => env('RECAPTCHA_SECRET_KEY', ''),

    /**
     * Recaptcha input name
     */
    'input_name'    => env('RECAPTCHA_INPUT_NAME', '_recaptcha_token'),

    /**
     * Recaptcha localization
     */
    'locale'        => env('RECAPTCHA_LOCALE', ''),

    /**
     * Recaptcha default values
     */
    'default'    => [

        /**
         * Minimum score in middleware
         */
        'min_score' => env('RECAPTCHA_DEFAULT_MIN_SCORE', 0.5),
    ],
];
