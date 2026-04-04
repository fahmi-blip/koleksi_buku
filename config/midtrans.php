<?php

return [
    /*
     * Midtrans Server Key
     */
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),

    /*
     * Midtrans Client Key
     */
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),

    /*
     * Midtrans API URL
     * Options: 'production', 'sandbox'
     */
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    /*
     * Enable Snap API
     */
    'enable_snap' => env('MIDTRANS_ENABLE_SNAP', true),

    /*
     * Enable Core API
     */
    'enable_core_api' => env('MIDTRANS_ENABLE_CORE_API', true),
];
