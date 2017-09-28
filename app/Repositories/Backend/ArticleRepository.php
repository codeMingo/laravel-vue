<?php
namespace App\Repositories\Backend;

use App\Models\Article;
use App\Models\Category;
use App\Repositories\Backend\DictRepository;

class ArticleRepository extends BaseRepository
{

    /**
     * 获取列表
     */
    public function lists($input)
    {
        $resultData['lists']                 = Article::lists($input['searchForm']);
        $resultData['options']['categories'] = Category::lists('article');
        $resultData['options']['status']     = DictRepository::getInstance()->getDictListsByCode('article_status');
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '',
        ];
    }

    /**
     * 新增
     */
    public function create($input)
    {
        $categoty_id = isset($input['categoty_id']) ? intval($input['categoty_id']) : '';
        $title       = isset($input['title']) ? strstr($input['title']) : '';
        $auther      = isset($input['auther']) ? strstr($input['auther']) : '';
        $content     = isset($input['content']) ? strstr($input['content']) : '';
        $tag_include = isset($input['tag_include']) ? implode(',', $input['tag_include']) : '';
        $source      = isset($input['source']) ? strstr($input['source']) : '';
        $is_audit    = isset($input['is_audit']) ? strstr($input['is_audit']) : '';
        $recommend   = isset($input['recommend']) ? intval($input['recommend']) : '';
        $status      = isset($input['status']) ? intval($input['status']) : '';

        if (!$categoty_id || !$title || !$content) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '必填字段不得为空',
            ];
        }

        // 是否存在这个dict
        $flag = DictRepository::getInstance()->existDict(['article_status' => $status, 'audit' => $is_audit]);
        if (!$flag) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '参数错误，请刷新后重试',
            ];
        }

        $insertResult = Article::create([
            'category_id' => $category_id,
            'title'       => $title,
            'auther'      => $auther,
            'content'     => $content,
            'tag_include' => $tag_include,
            'source'      => $source,
            'is_audit'    => $is_audit,
            'recommend'   => $recommend,
            'status'      => $status,
        ]);
        if (!$insertResult) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '未知错误，请联系管理员',
            ];
        }
        // 操作成功写入日志
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => '',
            'message' => '新增成功',
        ];
    }

    /**
     * 编辑
     */
    public function update($id, $input)
    {
        $articleList = Article::where('id', $id)->first();
        if (empty($articleList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }
        $categoty_id = isset($input['categoty_id']) ? intval($input['categoty_id']) : '';
        $title       = isset($input['title']) ? strstr($input['title']) : '';
        $auther      = isset($input['auther']) ? strstr($input['auther']) : '';
        $content     = isset($input['content']) ? strstr($input['content']) : '';
        $tag_include = isset($input['tag_include']) ? implode(',', $input['tag_include']) : '';
        $source      = isset($input['source']) ? strstr($input['source']) : '';
        $is_audit    = isset($input['is_audit']) ? strstr($input['is_audit']) : '';
        $recommend   = isset($input['recommend']) ? intval($input['recommend']) : '';
        $status      = isset($input['status']) ? intval($input['status']) : '';

        if (!$categoty_id || !$title || !$content) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '必填字段不得为空',
            ];
        }

        // 是否存在这个dict
        $flag = DictRepository::getInstance()->existDict(['article_status' => $status, 'audit' => $is_audit]);
        if (!$flag) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '参数错误，请刷新后重试',
            ];
        }

        $updateResult = Article::where('id', $id)->update([
            'category_id' => $category_id,
            'title'       => $title,
            'auther'      => $auther,
            'content'     => $content,
            'tag_include' => $tag_include,
            'source'      => $source,
            'is_audit'    => $is_audit,
            'recommend'   => $recommend,
            'status'      => $status,
        ]);
        if (!$updateResult) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '未知错误，请联系管理员',
            ];
        }
        // 操作成功写入日志
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => '',
            'message' => '新增成功',
        ];
    }

    /**
     * 删除
     */
    public function destroy($id)
    {
        $deleteResult = Article::where('id', $id)->delete();
        if (!$deleteResult) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '未知错误，请联系管理员',
            ];
        }
        // 操作成功写入日志
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => '',
            'message' => '删除成功',
        ];
    }

    /*
     * 获取options
     */
    public function getOptions()
    {
        $resultData['options']['status']     = DictRepository::getInstance()->getDictListsByCode('article_status');
        $resultData['options']['categories'] = Category::lists('article');
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '获取成功',
        ];
    }

    /*
     * 获取一条文章
     */
    public function show($id)
    {
        $resultData['data'] = Article::where('id', $id)->first();
        if (empty($resultData)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }
        $resultData['options']['categories'] = Category::lists('article');
        $resultData['options']['status']     = DictRepository::getInstance()->getDictListsByCode('article_status');
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '获取成功',
        ];
    }
}
