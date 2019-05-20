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

        Route::get('/role/create', 'auth\\Role@create')->name('tpadmin.auth.role.create');
        Route::get('/role/:id/edit', 'auth\\Role@edit')->name('tpadmin.auth.role.edit');
        Route::get('/role/:id', 'auth\\Role@read')->name('tpadmin.auth.role.read');
        Route::put('/role/:id', 'auth\\Role@update')->name('tpadmin.auth.role.update');
        Route::delete('/role/:id', 'auth\\Role@delete')->name('tpadmin.auth.role.delete');
        Route::get('/role', 'auth\\Role@index')->name('tpadmin.auth.role.index');
        Route::post('/role', 'auth\\Role@save')->name('tpadmin.auth.role.save');
    });

    // 首页
    Route::get('/', 'Index@index')->name('tpadmin.index');
    Route::get('/dashboard', 'Index@index');

    // 系统配置
    Route::any('/config/site', 'Config@site')->name('tpadmin.config.site.edit');
});

// // 首页
// Route::get('/', 'Index@index')->name('tpadmin.index');
// Route::get('/dashboard', 'Index@index');