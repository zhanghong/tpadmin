<?php

Route::group('auth', function () {
    Route::get('/passport/login', 'auth\\Passport@login')->name('tpadmin.auth.passport.login');
    Route::post('/passport/login', 'auth\\Passport@loginAuth');

    Route::get('/passport/logout', 'auth\\Passport@logout')->name('tpadmin.auth.passport.logout');
    Route::get('/passport/user', 'auth\\Passport@user')->name('tpadmin.auth.passport.user');
});

// 首页
Route::get('/', 'Index@index')->name('tpadmin.index');
Route::get('/dashboard', 'Index@index');