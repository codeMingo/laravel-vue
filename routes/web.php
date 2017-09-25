<?php

/*前台*/
Route::post('/login', 'Auth\LoginController@userLogin');
Route::post('/logout', 'Auth\LoginController@userLogout');
Route::get('/login-status', 'Auth\LoginController@loginStatus');
Route::group(['namespace' => 'Frontend'], function () {
    // 测试
    Route::get('/test', 'TestController@index');
    Route::get('/', 'IndexController@index');

    Route::post('/upload-image', 'CommonController@uploadImage');
    Route::post('/sendEmail', 'CommonController@sendEmail');
    // 注册
    Route::post('/register/create-user', 'RegisterController@createUser');
    // 文章模块
    Route::get('/article/lists', 'ArticleController@lists');
    Route::get('/article/detail/{article_id}', 'ArticleController@detail');
    Route::put('/article/interactive/{article_id}', 'ArticleController@interactive');
    Route::post('/article/comment', 'ArticleController@comment');
});

/*后台*/
Route::get('/backend', 'Auth\LoginController@index');
Route::post('/backend/login', 'Auth\LoginController@adminLogin');
Route::post('/backend/logout', 'Auth\LoginController@adminLogout');
Route::group(['namespace' => 'Backend', 'prefix' => 'backend', 'middleware' => 'auth.admin'], function () {

    Route::get('/index', 'IndexController@index');
    //管理员
    Route::resource('admins', 'AdminController');
    Route::post('admin/change-field-value/{id}', 'AdminController@changeFieldValue');
    //用户
    Route::resource('users', 'UserController');
    Route::post('user/change-field-value/{id}', 'UserController@changeFieldValue');
});
