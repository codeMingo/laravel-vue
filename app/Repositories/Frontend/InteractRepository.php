<?php
namespace App\Repositories\Frontend;

use App\Models\Interact;

class InteractRepository extends CommonRepository
{

    public function __construct(Interact $interact)
    {
        parent::__construct($interact);
    }

    public function getCollectLists($search)
    {
        $search['user_id'] = $this->getCurrentId();
        return $this->getInteractLists($search);
    }

    /**
     * 获取收藏列表
     * @param  Int $user_id 用户id
     * @return Object
     */
    public function getInteractLists($search)
    {
        $default_search = [
            'user_id' => $this->getCurrentId()
        ];
        $search = array_merge($default_search, $search);
        return $this->model->parseWheres([
            'search' => $search,
            'sort' => [
                'created_at' => 'desc'
            ]
        ])->with('article', 'videoList')->paginate();
        return $this->getPaginateLists($search);
    }
}
