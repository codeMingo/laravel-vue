<?php
namespace App\Repositories\Backend;

use App\Models\Tag;
use App\Repositories\Backend\DictRepository;

class TagRepository extends BaseRepository
{

    /**
     * 新增标签
     * @param  Array $input [text_en, tag_name ]
     * @return Array
     */
    public function store($input)
    {
        $text_en  = isset($input['text_en']) ? strval($input['text_en']) : '';
        $tag_name = isset($input['tag_name']) ? strval($input['tag_name']) : '';

        if (!$text_en || !$tag_name) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '发生错误，请刷新后重试',
            ];
        }

        $dictValue = DictRepository::getInstance()->getDictValueByTextEn($text_en);
        if ($dictValue === '') {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '发生错误，请刷新后重试',
            ];
        }

        $insertResult = Tag::create([
            'admin_id' => Auth::guard('admin')->id(),
            'tag_type' => $dictValue,
            'tag_name' => $tag_name,
        ]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Common/store',
            'params' => [
                'input' => $input,
            ],
            'text'   => !$insertResult ? '新增标签失败，未知错误' : '新增标签成功',
            'status' => !!$insertResult,
        ]);

        return [
            'status'  => !$insertResult ? Parent::ERROR_STATUS, Parent::SUCCESS_STATUS,
            'data'    => [
                'data' => [
                    'tag_id'   => $insertResult->id,
                    'tag_name' => $insertResult->tag_name,
                ],
            ],
            'message' => !!$insertResult ? '新增标签失败，未知错误' : '新增标签成功',
        ];
    }
}
