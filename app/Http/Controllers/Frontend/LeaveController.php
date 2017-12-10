<?php
namespace App\Http\Controllers\Frontend;

use App\Servers\Frontend\LeaveServer;
use Illuminate\Http\Request;

class LeaveController extends BaseController
{
    
    public function __construct(LeaveServer $leaveServer)
    {
        parent::__construct();
        $this->server = $leaveServer;
    }

    // 留言列表
    public function lists(Request $request)
    {
        $input  = json_decode($request->input('data'), true);
        $result = $this->server->lists($input);
        return response()->json($result);
    }

    // 留言
    public function leave(Request $request)
    {
        $input  = $request->input('data');
        $result = $this->server->leave($input);
        return response()->json($result);
    }
}
