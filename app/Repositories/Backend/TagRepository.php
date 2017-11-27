<?php
namespace App\Repositories\Backend;

use App\Models\Tag;

class TagRepository extends BaseRepository
{

    /**
     * 新增标签
     * @param  Array $input [type, title ]
     * @return Array
     */
    public function store($input)
    {
        $type  = isset($input['type']) ? strval($input['type']) : '';
        $title = isset($input['title']) ? strval($input['title']) : '';

        if (!$type || !in_array($type, ['article', 'video']) || !$tag_name) {
            return $this->responseResult(false, [], '新增失败，参数错误，请刷新重试');
        }

        $dicts  = $this->getRedisDictLists(['category' => [$type]]);
        $result = Tag::create([
            'admin_id'      => Auth::guard('admin')->id(),
            'category_type' => $dicts['category'][$type],
            'tag_name'      => $tag_name,
        ]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Tag/store',
            'params' => $input,
            'text'   => '新增成功',
        ]);
        return $this->responseResult(true, $result);
    }
}
