<?php
namespace App\Repositories\Frontend;

use App\Models\Leave;
use App\Repositories\Frontend\DictRepository;
use Illuminate\Support\Facades\Auth;

class LeaveRepository extends BaseRepository
{
    public $table_name = 'articles';

    /**
     * 文章列表
     * @param  Array $input [search_form]
     * @return Array
     */
    public function lists($input)
    {
        $resultData['lists'] = $this->getLeaveLists();
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '数据获取成功',
        ];
    }

    public function getLeaveLists()
    {
        $audit_pass_value      = DictRepository::getInstance()->getDictValueByTextEn('audit_pass');
        $leave_lists = Leave::where('is_audit', $audit_pass_value)->where('status', 1)->where('parent_id', 0)->with('user')->paginate(10);
        if ($leave_lists->isEmpty()) {
            return [];
        }
        $leave_ids = [];
        foreach ($leave_lists as $index => $leave) {
            $leave_ids[] = $leave->id;
        }

        // 找出所有的回复
        if (!empty($leave_ids)) {
            $response_lists = Leave::whereIn('parent_id', $leave_ids)->with('user')->where('status', 1)->get();
            if (!empty($response_lists)) {
                $response_temp = [];
                foreach ($response_lists as $index => $response) {
                    $response_temp[$response->parent_id][] = $response;
                }

                foreach ($leave_lists as $index => $leave) {
                    $leave_lists[$index]['response'] = isset($response_temp[$leave->id]) ? $response_temp[$leave->id] : [];
                }
            }
        }
        return $leave_lists;
    }

    public function publish($input)
    {
        $leave_id = isset($input['leave_id']) ? intval($input['leave_id']) : '';
        $content    = isset($input['content']) ? strval($input['content']) : '';
        if (!$content) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '操作失败，内容不得为空',
            ];
        }
        $leave_audit = DictRepository::getInstance()->getDictValueByTextEn('leave_audit');
        $dictListsValue        = DictRepository::getInstance()->getDictListsByTextEnArr(['audit_loading', 'audit_pass']);

        // 表示回复
        if ($leave_id) {
            $leave_list = Leave::where('id', $leave_id)->where('status', 1)->where('is_audit', $dictListsValue['audit_pass'])->first();
            if (empty($leave_list)) {
                return [
                    'status'  => Parent::ERROR_STATUS,
                    'data'    => [],
                    'message' => '回复失败，回复的留言为空',
                ];
            }
        }
        $user_id      = Auth::guard('web')->id();
        $createResult = Leave::create([
            'user_id'    => $user_id,
            'parent_id'  => $leave_id ? $leave_id : 0,
            'content'    => $content,
            'is_audit'   => $leave_audit ? $dictListsValue['audit_loading'] : $dictListsValue['audit_pass'],
            'ip_address'   => getClientIp(),
            'status'     => 1,
        ]);

        // 记录操作日志
        Parent::saveUserOperateRecord([
            'action' => 'Leave/publish',
            'params' => $input,
            'text'   => $createResult ? ($leave_id ? '回复成功' : '留言成功') : ($leave_id ? '回复失败，未知错误' : '留言失败，未知错误'),
            'status' => !!$createResult,
        ]);

        if (!$createResult) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => $leave_id ? '回复失败，未知错误' : '留言失败，未知错误',
            ];
        }
        // 留言成功
        if (!$leave_audit) {
            $publish_leave_list                = Leave::where('id', $createResult->id)->with('user')->first();
            $publish_leave_list->response      = [];
            $publish_leave_list->show_response = true;
        }
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => [
                'list' => $leave_audit ? [] : $publish_leave_list,
            ],
            'message' => $leave_id ? '回复成功' : '留言成功',
        ];
    }
}
