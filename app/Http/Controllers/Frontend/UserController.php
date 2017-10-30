<?php
namespace App\Http\Controllers\Frontend;

use App\Repositories\Frontend\userRepository;
use Illuminate\Http\Request;

class UserController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = UserRepository::getInstance()->index();
        return response()->json($result);
    }

    public function updateUser(Request $request, $user_id)
    {
        $input  = $request->input('data');
        $result = UserRepository::getInstance()->updateUser($input, $user_id);
        return response()->json($result);
    }
}
