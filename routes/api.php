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

//  测试：生成用户
Route::get('zh/test', 'Api\UserController@storeDemo');

Route::group([
    'middleware' => ['api_check_login']
], function () {
    //  - 交警中队
    Route::resource('detachments', 'Api\DetachmentController');

    //  - 违章车辆
    Route::resource('illegal-vehicles', 'Api\IllegalVehicleController');

    //  - 图片
    Route::post('images', 'Api\ImageController@store');
});
