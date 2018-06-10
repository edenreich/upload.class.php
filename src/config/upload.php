<?php

return [

     /*
    |--------------------------------------------------------------------------
    | Protocols
    |--------------------------------------------------------------------------
    |
    | You can specify what protocol you want to use: 
    | http(Hypertext Transfer Protocol) or ftp(File Transfer Protocol)
    |
    */

    'protocols' => [

        'default' => 'ftp',

        'http' => [
            'port' => '80'
        ],

        'ftp' => [
            'host'     => '127.0.0.1',
            'username' => 'test',
            'password' => 'secret',
            'port' => '21'
        ]

    ],


     /*
    |--------------------------------------------------------------------------
    | Disks
    |--------------------------------------------------------------------------
    |
    | You can specify what disk you want to use: s3 or local
    |
    */

    
    'disks' => [

        'default' => 'local',

    	's3' => [
    		'key'    => 'AWS_ACCESS_KEY_ID',
            'secret' => 'AWS_SECRET_ACCESS_KEY',
            'region' => 'AWS_DEFAULT_REGION',
            'bucket' => 'AWS_BUCKET',
            'url'    => 'AWS_URL',
    	],

    	'local' => [
    		'path' => ''

    	]

    ],


    /*
    |--------------------------------------------------------------------------
    | Encryption
    |--------------------------------------------------------------------------
    |
    | Here you can set the encryption key for the files encryptions.
    |
    */

    'encryption_key' => 'fc01e8d00a90c1d392ec45459deb6f15',


    /*
    |--------------------------------------------------------------------------
    | Rules
    |--------------------------------------------------------------------------
    |
    | Here you can set the rules for the size and the allowed extensions.
    |
    */

    'rules' => [
        'size' => 2000, // in Kilobytes
        'extensions' => 'jpg|png'
    ],

    'messages' => [
        'size' => 'Please upload files that are less than 2MB size',
        'extensions' => 'Please upload only jpg, png or pdf'
    ],


    /*
    |--------------------------------------------------------------------------
    | Async
    |--------------------------------------------------------------------------
    |
    | Here you can set async to true if you wish to speed up the upload time.
    |
    */

    'async' => true
];