<?php
namespace App\Servers\Frontend;

use App\Repositories\Frontend\LeaveRepository;

class LeaveServer extends CommonServer
{

    public function __construct(
        LeaveRepository $leaveRepository
    ) {
        $this->leaveRepository = $leaveRepository;
    }

    /**
     * 列表
     * @param  Array $input [search]
     * @return Array
     */
    public function lists($input)
    {
        $result['lists'] = $this->leaveRepository->getLists($input);

        return ['获取成功', $result];
    }

    /**
     * 留言
     * @param  Array $input 数据
     * @return Array
     */
    public function leave($input)
    {
        $leave_id = isset($input['leave_id']) ? intval($input['leave_id']) : 0;
        $content  = isset($input['content']) ? strval($input['content']) : '';
        if (!$content) {
            return ['code' => ['x00004', 'system']];
        }

        if ($leave_id) {
            if (!$is_exist = $this->leaveRepository->existLeave($leave_id)) {
                return ['code' => ['x00001', 'leave']];
            }
        }

        $result['list'] = $this->leaveRepository->leave($content, $leave_id);

        return ['获取成功', $result];
    }

}
