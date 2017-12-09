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
        $default_search = [
            'user_id'            => $user_id,
            '__not_select__'     => ['deleted_at', 'updated_at'],
            '__relation_table__' => ['article', 'videoList'],
            '__order_by__'       => ['created_at' => 'desc'],
        ];
        $search = array_merge($default_search, $search);
        return $this->getPaginateLists($search);
    }
}
