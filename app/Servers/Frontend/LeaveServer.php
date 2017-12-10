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
        $search          = isset($input['search']) ? (array) $input['search'] : [];
        $result['lists'] = $this->leaveRepository->getLeaveLists($search);
        return responseResult(true, $result);
    }

    /**
     * 留言
     * @param  Array $input [leave_id, content] 留言数据
     * @return Array
     */
    public function leave($input)
    {
        $leave_id = isset($input['leave_id']) ? intval($input['leave_id']) : 0;
        $content  = isset($input['content']) ? strval($input['content']) : '';
        if (!$content) {
            return responseResult(false, [], '留言失败，参数错误，请刷新后重试');
        }

        $result['list'] = $this->leaveRepository->leave($content, $leave_id);
        return responseResult(true, $result, $leave_id ? '回复成功' : '留言成功');
    }

}
