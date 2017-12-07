<?php
namespace App\Http\Controllers\Frontend;

use App\Repositories\Frontend\LeaveRepository;
use Illuminate\Http\Request;

class LeaveController extends BaseController
{

    public $repository;

    public function __construct(LeaveRepository $leaveRepository)
    {
        parent::__construct();
        $this->repository = $leaveRepository;
    }

    // 留言列表
    public function lists(Request $request)
    {
        $input  = json_decode($request->input('data'), true);
        $result = $this->repository->lists($input);
        return response()->json($result);
    }

    // 留言
    public function leave(Request $request)
    {
        $input  = $request->input('data');
        $result = $this->repository->leave($input);
        return response()->json($result);
    }
}
