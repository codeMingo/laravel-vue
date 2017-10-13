<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;

class BaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            return $next($request);
        });
    }
}
