<?php
// 前后台登录验证 和 初始页
Route::group(['namespace' => 'Auth'], function () {
    Route::get('/', 'LoginController@index');
    Route::post('/login', 'LoginController@userLogin');
    Route::post('/logout', 'LoginController@userLogout');
    Route::get('/login-status', 'LoginController@loginStatus');

    Route::get('/backend', 'LoginController@adminIndex');
    Route::post('/backend/login', 'LoginController@adminLogin');
    Route::post('/backend/logout', 'LoginController@adminLogout');
    Route::get('/backend/login-status', 'LoginController@adminLoginStatus');
});

// 前后台公共api
Route::group(['namespace' => 'Common', 'prefix' => '/api'], function () {
    Route::get('/qiniu/token', 'ApiController@uploadToken');
    Route::get('/refreshCache', 'ApiController@refreshCache');
});

// 前台
Route::group(['namespace' => 'Frontend'], function () {
    // 公共模块
    Route::get('/test', 'TestController@index');
    Route::post('/sendEmail', 'CommonController@sendEmail');
    Route::get('/article-category', 'CategoryController@articleCategoryLists');

    // 注册模块
    Route::post('/register', 'RegisterController@register');
    Route::get('/active', 'RegisterController@active');

    // 文章模块
    Route::get('/article/lists', 'ArticleController@lists');
    Route::get('/article/detail/{article_id}', 'ArticleController@detail');
    Route::get('/article/comment-lists/{article_id}', 'ArticleController@commentLists');

    // 留言板模块
    Route::get('/leave/lists', 'LeaveController@lists');

    // 需登录后操作的模块
    Route::group(['middleware' => 'auth'], function () {
        // 文章模块
        Route::put('/article/interactive/{article_id}', 'ArticleController@interactive');
        Route::put('/article/comment/{article_id}', 'ArticleController@comment');
        Route::put('/article/collect/{article_id}', 'ArticleController@collect');

        // 留言
        Route::post('/leave', 'LeaveController@leave');

        // 用户模块
        Route::get('/user/show', 'UserController@show');
        Route::get('/user/index', 'UserController@index');
        Route::put('/user/update', 'UserController@update');
        Route::get('/user/collect', 'UserController@collect');
    });
});

// 后台
Route::group(['namespace' => 'Backend', 'prefix' => 'backend', 'middleware' => 'auth.admin'], function () {
    // 公共模块
    Route::get('/index', 'IndexController@index');
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
