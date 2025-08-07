<?php

/*
 * This file is part of the Chapa Laravel package.
 *
 * Kidus Yared - @kidus363 <kidusy@chapa.co>
 *
 * 
 */
return [


    /**
     * Secret Key: Your Chapa secretKey. Sign up on https://dashboard.chapa.co/ to get one from your settings page
     *
     */
    'secretKey' => env('CHAPA_SECRET_KEY'),

    'guzzle_options' => [
        // ** ADD THIS 'verify' OPTION **
        'verify' => storage_path('certs/cacert.pem')
    ],

];