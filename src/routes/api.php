<?php


Route::group(['prefix'=>config('codecfastsms.route_prefix'),'namespace' => 'HayriCan\CodecFastSms\Controllers','middleware' => config('codecfastsms.middleware')], function () {
    Route::post('codec-fastsms', 'CodecFastSmsController@postSmsVariables');
    Route::post('codec-credit', 'CodecFastSmsController@getCredit');
});