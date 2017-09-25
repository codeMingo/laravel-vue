<?php

namespace App\Http\Controllers\Frontend;

class IndexController extends BaseController
{

    // 初始化页面
    public function index()
    {
        return view('frontend.index');
    }

}
