<?php
namespace App\Repositories\Frontend;

use App\Models\Leave;

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
    public function getLists($input)
    {
        $default_search = [
            'filter' => ['id', 'user_id', 'content', 'created_at'],
            'search' => [
                'is_audit'  => $this->dicts['audit']['pass'],
                'status'    => 1,
                'parent_id' => 0,
            ],
            'sort'   => [
                'created_at' => 'desc',
            ],
        ];
        $search = $this->parseParams($default_search, $input);
        $result = $this->model->parseWheres($search)->with('user')->paginate();
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
        $result = $this->model->create([
            'user_id'    => getCurrentUserId(),
            'parent_id'  => $leave_id,
            'content'    => $content,
            'is_audit'   => $this->dicts['system']['leave_audit'] ? $this->dicts['audit']['loading'] : $this->dicts['audit']['pass'],
            'ip_address' => getClientIp(),
        ]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Leave/publish',
            'params' => [
                'content'  => $content,
                'leave_id' => $leave_id,
            ],
            'text'   => $leave_id ? '回复成功' : '留言成功',
        ]);
        return $result;
    }

    /**
     * 是否存在这条留言
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function existLeave($id)
    {
        return $this->existList([
            'id'       => $id,
            'status'   => 1,
            'is_audit' => $this->dicts['audit']['pass'],
        ]);
    }
}
