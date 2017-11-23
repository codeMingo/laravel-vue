<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Repositories\Common\ApiRepository;
use Illuminate\Http\Request;

class Apicontroller extends Controller
{

    public function uploadImage(Request $request)
    {
        $input  = $request->file('file');
        $result = ApiRepository::getInstance()->uploadImage($input);
        return response()->json($result);
    }
}
