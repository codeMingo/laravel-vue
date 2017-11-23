<?php
namespace App\Repositories\Frontend;

use App\Models\Video;

class VideoRepository extends BaseRepository
{

    /**
     * 获取视频列表
     * @param  Array $input [search_form]
     * @return Array
     */
    public function getVideoLists($input)
    {
        $search_form           = $input['search_form'];
        $dictKeyValue          = DictRepository::getInstance()->getDictListsByTextEnArr(['video_is_show', 'video_page_size']);
        $search_form['status'] = $dictKeyValue['video_is_show'];
        $where_params          = $this->parseParams($search_form);
        $page_size             = $dictKeyValue['video_page_size'];
        $result['lists']       = Video::parseWheres($where_params)->with('videoList')->paginate($page_size);

        return [
            'status'  => Parent::ERROR_STATUS,
            'data'    => $result,
            'message' => '数据获取成功',
        ];
    }

    /**
     * 获取视频详情
     * @param  Int $video_id 视频id
     * @return Array
     */
    public function getVideoDetail($video_id)
    {
        $video_show_status = DB::table('dicts')->where('code', 'video_status')->where('text_en', 'video_is_show')->value('value');
        $result['list']    = DB::table('video')->where('id', $video_id)->where('status', $video_show_status)->with('videoList')->first();
        return [
            'status'  => Parent::ERROR_STATUS,
            'data'    => $result,
            'message' => '数据获取成功',
        ];
    }

    public function comment_lists($video_id)
    {
        $dict_lists_value    = DictRepository::getInstance()->getDictListsByTextEnArr(['video_is_show', 'audit_pass']);
        $resultData['lists'] = DB::table('video_comments')->where('video_id', $video_id)->where('is_audit', $dict_lists_value['audit_pass'])->where('status', 1)->where('parent_id', 0)->with('user')->paginate(10);
        if ($resultData['lists']->isEmpty()) {
            return [
                'status'  => Parent::SUCCESS_STATUS,
                'data'    => $resultData,
                'message' => '数据获取成功',
            ];
        }
        $comment_ids = [];
        foreach ($resultData['lists'] as $index => $comment) {
            $comment_ids[] = $comment->id;
        }

        // 找出所有的回复
        if (!empty($comment_ids)) {
            $response_lists = DB::table('video_comments')->whereIn('parent_id', $comment_ids)->with('user')->get();
            if (!empty($response_lists)) {
                $response_temp = [];
                foreach ($response_lists as $index => $response) {
                    $response_temp[$response->parent_id][] = $response;
                }

                foreach ($resultData['lists'] as $index => $comment) {
                    $resultData['lists'][$index]['response'] = isset($response_temp[$comment->id]) ? $response_temp[$comment->id] : [];
                }
            }
        }
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '数据获取成功',
        ];
    }

    public function interactive($video_id, $input)
    {
        $type = isset($input['type']) ? strval($input['type']) : '';
        if (!$video_id || !$type) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '操作失败，发生未知错误',
            ];
        }

        $dict_lists_value = DictRepository::getInstance()->getDictListsByTextEnArr(['video_is_show', 'audit_pass']);
        $video_list       = DB::table('video')->where('id', $video_id)->where('status', $dict_lists_value['video_is_show'])->first();
        if (empty($video_list)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '操作失败，不存在这篇视频',
            ];
        }
        $user_id  = Auth::guard('web')->id();
        $dataList = Interact::where('video_id', $video_id)->where('user_id', $user_id)->where($type, 1)->first();
        if (!empty($dataList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '操作失败，不可重复操作',
            ];
        }
        $result = Interact::create([
            'user_id'  => $user_id,
            'video_id' => $video_id,
            $type      => 1,
        ]);

        // 记录操作日志
        Parent::saveUserOperateRecord([
            'action' => 'Video/interactive',
            'params' => $input,
            'text'   => $result ? '操作成功' : '操作失败',
            'status' => !!$result,
        ]);

        return [
            'status'  => $result ? Parent::SUCCESS_STATUS : Parent::ERROR_STATUS,
            'data'    => [],
            'message' => $result ? '操作成功' : '操作失败，发生未知错误',
        ];
    }

    public function comment($video_id, $input)
    {
        $comment_id = isset($input['comment_id']) ? intval($input['comment_id']) : '';
        $content    = isset($input['content']) ? strval($input['content']) : '';
        if (!$video_id || !$content) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '操作失败，视频id或内容为空',
            ];
        }
        $video_show_status_value = DB::table('dicts')->where('code', 'video_status')->where('text_en', 'video_is_show')->value('value');
        $video_list              = DB::table('video')->where('id', $video_id)->where('status', $video_show_status_value)->first();
        if (empty($videoList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇视频',
            ];
        }
        $video_comment_audit = DB::table('dicts')->where('code', 'system')->where('text_en', 'video_comment_audit')->value('value');
        $dict_lists_value    = DictRepository::getInstance()->getDictListsByTextEnArr(['audit_loading', 'audit_pass']);

        // 表示回复
        if ($comment_id) {
            $comment_list = DB::table('video_comments')->where('id', $comment_id)->where('status', 1)->where('is_audit', $dict_lists_value['audit_pass'])->first();
            if (empty($comment_list)) {
                return [
                    'status'  => Parent::ERROR_STATUS,
                    'data'    => [],
                    'message' => '回复失败，comment_id is null',
                ];
            }
        }
        $user_id      = Auth::guard('web')->id();
        $createResult = DB::table('video_comments')->create([
            'user_id'   => $user_id,
            'parent_id' => $comment_id ? $comment_id : 0,
            'video_id'  => $video_id,
            'content'   => $content,
            'is_audit'  => $video_comment_audit ? $dict_lists_value['audit_loading'] : $dict_lists_value['audit_pass'],
            'status'    => 1,
        ]);

        // 记录操作日志
        Parent::saveUserOperateRecord([
            'action' => 'video/comment',
            'params' => $input,
            'text'   => $createResult ? ($comment_id ? '回复成功' : '评论成功') : ($comment_id ? '回复失败，未知错误' : '评论失败，未知错误'),
            'status' => !!$createResult,
        ]);

        if (!$createResult) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => $comment_id ? '回复失败，未知错误' : '评论失败，未知错误',
            ];
        }
        // 评论成功
        if (!$video_comment_audit) {
            $comment_lists                = videoComment::where('id', $createResult->id)->with('user')->first();
            $comment_lists->response      = [];
            $comment_lists->show_response = true;
        }
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => [
                'list' => $video_comment_audit ? [] : $comment_lists,
            ],
            'message' => $comment_id ? '回复成功' : '评论成功',
        ];
    }
}
