<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => ['web']], function () {
    Route::get('/', 'AngularController@serveApp');
    Route::get('/unsupported-browser', 'AngularController@unsupported');
    Route::get('user/verify/{verificationCode}', ['uses' => 'Auth\AuthController@verifyUserEmail']);
    Route::get('auth/{provider}', ['uses' => 'Auth\AuthController@redirectToProvider']);
    Route::get('auth/{provider}/callback', ['uses' => 'Auth\AuthController@handleProviderCallback']);
    Route::get('/api/authenticate/user', 'Auth\AuthController@getAuthenticatedUser');

    Route::get('csvTOtxtTest/', 'CampaignplanningController@csvTOtxtTest');
    Route::get('init/', 'InitController@all');
    Route::get('init/test', 'InitController@test');
    Route::get('init/testsendsms', 'InitController@testsms');
    Route::get('campaigns/campaignfileload/', 'CampaignplanningController@loadCampaignfile');
    Route::get('campaigns/plansms/', 'CampaignplanningController@smsplan');
    Route::get('campaigns/pickupsms/', 'CampaignplanningController@smspickup');
    Route::get('campaigns/sendsms/', 'CampaignplanningController@smssend');
    Route::get('campaigns/dbmajsms/', 'CampaignplanningController@smsdbmaj');
    Route::get('modelvalidate/', 'ModelvalidatingController@modelsvalidate');
});

$api->group(['middleware' => ['api']], function ($api) {
    $api->controller('auth', 'Auth\AuthController');

    // Password Reset Routes...
    $api->post('auth/password/email', 'Auth\PasswordResetController@sendResetLinkEmail');
    $api->get('auth/password/verify', 'Auth\PasswordResetController@verify');
    $api->post('auth/password/reset', 'Auth\PasswordResetController@reset');
});

$api->group(['middleware' => ['api', 'api.auth']], function ($api) {
    $api->get('users/me', 'UserController@getMe');
    $api->put('users/me', 'UserController@putMe');
});

$api->group(['middleware' => ['api', 'api.auth', 'role:admin.super|admin.user']], function ($api) {
    $api->controller('users', 'UserController');
});

$api->group(['middleware' => ['api']], function ($api) {
    $api->controller('tests', 'TestController');
});

$api->group(['middleware' => ['api']], function ($api) {
    $api->controller('campaigns', 'CampaignController');
});

$api->group(['middleware' => ['api', 'api.auth', 'role:admin.super|admin.user']], function ($api) {
    $api->controller('traces', 'TraceController');
});

$api->group(['middleware' => ['api']], function ($api) {
    $api->controller('dashboard', 'DashboardController');
});

$api->group(['middleware' => ['api', 'api.auth', 'role:admin.super|admin.user']], function ($api) {
    $api->controller('seegsmsconfigs', 'SeegsmsconfigController');
});
