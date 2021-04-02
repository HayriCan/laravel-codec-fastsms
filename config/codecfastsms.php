<?php


return [
    /*
    |--------------------------------------------------------------------------
    | Codec Fast Sms Api Url
    |--------------------------------------------------------------------------
    |
     */
    'base_url'=>"https://fastsms-api.codec.com.tr/Soap.asmx/",

    /*
    |--------------------------------------------------------------------------
    | Codec Fast Sms Api Credentials
    |--------------------------------------------------------------------------
    |
     */

    'username'=> "CODEC_USERNAME",
    'password'=> "CODEC_PASSWORD",
    'sender'=> "CODEC_SENDER",
    /*
    |--------------------------------------------------------------------------
    | Router
    |--------------------------------------------------------------------------
    | Here you can specify Router Prefix and Middleware
    |
     */

    'route_prefix'=> 'api',

    'middleware'=> ['api'],


    /*
    |--------------------------------------------------------------------------
    | Sms Request Records
    |--------------------------------------------------------------------------
    | Here you can choose to save requests on database
    |
     */

    /*
     * If you set record true it is going to save your requests to database
     */
    'record'=> false,

];