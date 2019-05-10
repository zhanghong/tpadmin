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

        Route::get('/rule/create', 'auth\\Rule@create')->name('tpadmin.auth.rule.create');
        Route::get('/rule/:id/edit', 'auth\\Rule@edit')->name('tpadmin.auth.rule.edit');
        Route::get('/rule/:id', 'auth\\Rule@read')->name('tpadmin.auth.rule.read');
        Route::put('/rule/:id', 'auth\\Rule@update')->name('tpadmin.auth.rule.update');
        Route::delete('/rule/:id', 'auth\\Rule@delete')->name('tpadmin.auth.rule.delete');
        Route::get('/rule', 'auth\\Rule@index')->name('tpadmin.auth.rule.index');
        Route::post('/rule', 'auth\\Rule@save')->name('tpadmin.auth.rule.save');
    });

    // 首页
    Route::get('/', 'Index@index')->name('tpadmin.index');
    Route::get('/dashboard', 'Index@index');

    // 系统配置
    Route::get('/config/edit', 'Config@edit')->name('tpadmin.config.edit');
    Route::get('/config/update', 'Config@edit')->name('tpadmin.config.update');
});

// 首页
Route::get('/', 'Index@index')->name('tpadmin.index');
Route::get('/dashboard', 'Index@index');