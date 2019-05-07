<?php

Route::group('auth', function () {
    Route::get('/passport/login', 'auth\\Passport@login')->name('tpadmin.auth.passport.login');
    Route::post('/passport/login', 'auth\\Passport@loginAuth');

    Route::get('/passport/logout', 'auth\\Passport@logout')->name('tpadmin.auth.passport.logout')->middleware('tpadmin.admin');
    Route::get('/passport/user', 'auth\\Passport@user')->name('tpadmin.auth.passport.user')->middleware('tpadmin.admin');
});

Route::group([
    'middleware' => ['tpadmin.admin'],
], function () {
    Route::group('auth', function () {
        Route::get('/adminer/create', 'auth\\Adminer@create')->name('tpadmin.auth.adminer.create');
        Route::get('/adminer/:id/edit', 'auth\\Adminer@edit')->name('tpadmin.auth.adminer.edit');
        Route::get('/adminer/:id', 'auth\\Adminer@read')->name('tpadmin.auth.adminer.read');
        Route::put('/adminer/:id', 'auth\\Adminer@update')->name('tpadmin.auth.adminer.update');
        Route::delete('/adminer/:id', 'auth\\Adminer@delete')->name('tpadmin.auth.adminer.delete');
        Route::get('/adminer', 'auth\\Adminer@index')->name('tpadmin.auth.adminer.index');
        Route::post('/adminer', 'auth\\Adminer@save')->name('tpadmin.auth.adminer.save');
    });

    // 首页
    Route::get('/', 'Index@index')->name('tpadmin.index');
    Route::get('/dashboard', 'Index@index');

    // 系统配置
    Route::get('/config/add', 'Config@add')->name('tadmin.config.add');
    Route::post('/config/add', 'Config@create')->name('tadmin.config.create');
    Route::get('/config', 'Config@index')->name('tadmin.config');
    Route::post('/config', 'Config@save')->name('tadmin.config');
});

// 首页
Route::get('/', 'Index@index')->name('tpadmin.index');
Route::get('/dashboard', 'Index@index');