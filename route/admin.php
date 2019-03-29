<?php

// 首页
Route::get('/', 'Index@index')->name('tpadmin.index');
Route::get('/dashboard', 'Index@index');