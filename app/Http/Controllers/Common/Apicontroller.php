<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Repositories\Common\ApiRepository;
use Illuminate\Http\Request;

class Apicontroller extends Controller
{

    public function uploadToken(Request $request)
    {
        $result = ApiRepository::getInstance()->createToken();
        return response()->json($result);
    }
}
