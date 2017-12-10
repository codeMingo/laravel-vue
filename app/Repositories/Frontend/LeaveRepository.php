<?php
namespace App\Repositories\Frontend;

use App\Models\Leave;
use Illuminate\Support\Facades\DB;

class LeaveRepository extends CommonRepository
{

    public function __construct(Leave $leave)
    {
        parent::__construct($leave);
    }

    /**
     * 获取列表
     * @return Object
     */
    public function getLeaveLists($search)
    {
        $dicts  = $this->getRedisDictLists(['audit' => ['pass']]);
        $result = $this->model->where('is_audit', $dicts['audit']['pass'])->where('status', 1)->where('parent_id', 0)->with('user')->paginate();
        if ($result->isEmpty()) {
            return $result;
        }

        $leave_ids = [];
        foreach ($result as $index => $leave) {
            $leave_ids[] = $leave->id;
        }
        // 找出所有的回复
        if (!empty($leave_ids)) {
            $response_lists = $this->model->whereIn('parent_id', $leave_ids)->with('user')->where('status', 1)->get();
            if (!empty($response_lists)) {
                $response_temp = [];
                foreach ($response_lists as $index => $response) {
                    $response_temp[$response->parent_id][] = $response;
                }
                foreach ($result as $index => $leave) {
                    $result[$index]['response'] = isset($response_temp[$leave->id]) ? $response_temp[$leave->id] : [];
                }
            }
        }
        return $result;
    }

    /**
     * 留言
     * @param  Array $input [leave_id, content] 留言数据
     * @return Array
     */
    public function leave($content, $leave_id = 0)
    {
        $dicts = $this->getRedisDictLists(['system' => ['leave_audit'], 'audit' => ['loading', 'pass']]);
        // 表示回复
        if ($leave_id) {
            $list = $this->model->where('id', $leave_id)->where('status', 1)->where('is_audit', $dicts['audit']['pass'])->first();
            if (empty($list)) {
                return ['flag' => false, 'message' => '回复失败，该留言不存在或已被删除'];
            }
        }
        $result = $this->model->create([
            'user_id'    => $this->getCurrentId(),
            'parent_id'  => $leave_id,
            'content'    => $content,
            'is_audit'   => $dicts['system']['leave_audit'] ? $dicts['audit']['loading'] : $dicts['audit']['pass'],
            'ip_address' => getClientIp(),
        ]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Leave/publish',
            'params' => [
                'content' => $content,
                'leave_id' => $leave_id
            ],
            'text'   => $leave_id ? '回复成功' : '留言成功',
        ]);
        $result['response']      = [];
        $result['show_response'] = true;
        $result['user']          = DB::table('users')->where('id', $this->getCurrentId())->first();
        return $result;
    }
}
