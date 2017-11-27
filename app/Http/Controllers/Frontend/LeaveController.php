<?php
namespace App\Http\Controllers\Frontend;

use App\Repositories\Frontend\LeaveRepository;
use Illuminate\Http\Request;

class LeaveController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function lists(Request $request)
    {
        $input  = json_decode($request->input('data'), true);
        $result = LeaveRepository::getInstance()->lists($input);
        return response()->json($result);
    }

    public function leave(Request $request)
    {
        $input  = $request->input('data');
        $result = LeaveRepository::getInstance()->leave($input);
        return response()->json($result);
    }
}
