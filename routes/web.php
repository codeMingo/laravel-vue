<?php

/**
 * 前台
 */
Route::post('/login', 'Auth\LoginController@userLogin');
Route::post('/logout', 'Auth\LoginController@userLogout');
Route::get('/login-status', 'Auth\LoginController@loginStatus');
// 获取前台技术篇菜单
Route::get('/article/category', 'Frontend\ArticleController@categoryLists');
Route::group(['namespace' => 'Frontend'], function () {
    // 公共模块
    Route::get('/test', 'TestController@index');
    Route::get('/', 'IndexController@index');
    Route::post('/upload-image', 'CommonController@uploadImage');
    Route::post('/sendEmail', 'CommonController@sendEmail');

    // 注册模块
    Route::post('/register/create-user', 'RegisterController@createUser');
    Route::post('/register/active-user', 'RegisterController@activeUser');

    // 文章模块
    Route::get('/article/lists', 'ArticleController@lists');
    Route::get('/article/detail/{article_id}', 'ArticleController@detail');
    Route::get('/article/comment-lists/{article_id}', 'ArticleController@commentLists');

    // 留言板模块
    Route::get('/leave/lists', 'LeaveController@lists');

    // 需登录后操作的模块
    Route::group(['middleware' => 'auth'], function () {
        Route::put('/article/interactive/{article_id}', 'ArticleController@interactive');
        Route::put('/article/comment/{article_id}', 'ArticleController@comment');
        Route::put('/leave/publish', 'LeaveController@publish');
        Route::get('/user/user-data', 'UserController@userData');
        Route::put('/user/update-user/{user_id}', 'UserController@updateUser');
    });
});



/**
 * 后台
 */
Route::get('/backend', 'Auth\LoginController@index');
Route::post('/backend/login', 'Auth\LoginController@adminLogin');
Route::post('/backend/logout', 'Auth\LoginController@adminLogout');
Route::get('/backend/login-status', 'Auth\LoginController@adminLoginStatus');
Route::group(['namespace' => 'Backend', 'prefix' => 'backend', 'middleware' => 'auth.admin'], function () {
    // 公共模块
    Route::get('/index', 'IndexController@index');
    Route::post('/upload-image', 'CommonController@uploadImage');
    Route::post('/update-redis', 'CommonController@uploadRedis');

    // 管理员模块
    Route::resource('admins', 'AdminController');
    Route::post('admin/change-field-value/{id}', 'AdminController@changeFieldValue');

    // 用户模块
    Route::resource('users', 'UserController');
    Route::post('user/change-field-value/{id}', 'UserController@changeFieldValue');

    // 文章模块
    Route::resource('articles', 'ArticleController');
    Route::get('article/options', 'ArticleController@options');
});
