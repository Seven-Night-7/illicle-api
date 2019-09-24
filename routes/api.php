<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//  登录
Route::post('login', 'Api\UserController@login');
//  注销
Route::get('logout', 'Api\UserController@logout');

//  ...
Route::get('zh/test', 'Api\UserController@storeDemo');

Route::group([
    'middleware' => ['api_check_login']
], function () {
    //  - 交警中队
    Route::post('add-detachment', 'Api\DetachmentController@store');
    Route::get('terse-detachments', 'Api\DetachmentController@terse');

    //  - 违章车辆
    Route::get('illegal-vehicles', 'Api\IllegalVehicleController@index');
    Route::get('illegal-vehicle/{id}', 'Api\IllegalVehicleController@show');

    //  - 上传图片
    Route::post('upload-image', 'Api\ImageController@upload');
});
