<?php
namespace App\Repositories\Frontend;

use App\Models\Interact;

class InteractRepository extends CommonRepository
{

    public function __construct(Interact $interact)
    {
        parent::__construct($interact);
    }

    /**
     * 获取收藏列表
     * @param  Int $user_id 用户id
     * @return Object
     */
    public function getInteractLists($user_id, $search)
    {
        $params = $this->parseParams($search);
        return  = $this->model->parseWheres($params)->with('article')->with('videoList')->paginate();
    }
}
