<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

class IndexController extends CommonController
{

    public function index()
    {
        return view('backend.index');
    }

}
