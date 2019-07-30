<?php

Route::redirect('/', "login");

Auth::routes([
    'register'  => false,
    'reset'     => true,
    'verify'    => false
]);

Route::get('healthcheck', function() {
    return 'ok';
});

Route::group(['middleware' => 'auth'], function () {
    Route::resource('users', 'UserController', ['except' => ['show']]);

    Route::get('cron', ['as' => 'cron.index', 'uses' => 'CronController@index']);
    Route::patch('cron/{name}', ['as' => 'cron.update', 'uses' => 'CronController@update']);
    Route::post('cron/{name}/trigger', ['as' => 'cron.trigger', 'uses' => 'CronController@trigger']);

    Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
	Route::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
	Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);
});
