<?php
namespace App\Servers\Backend;

use App\Repositories\Backend\ArticleRepository;
use App\Repositories\Backend\CategoryRepository;
use App\Repositories\Backend\TagRepository;

class ArticleServer extends CommonServer
{

    public function __construct(
        ArticleRepository $articleRepository,
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository
    ) {
        $this->articleRepository  = $articleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository      = $tagRepository;
    }

    public function index()
    {
        $result['lists']   = $this->articleRepository->getLists($input);
        $result['options'] = $this->articleRepository->getOptions();

        return ['获取成功', $result];
    }

    /**
     * 文章详情
     * @param  int $id 主键
     * @return array
     */
    public function show($id)
    {
        $list = $this->articleRepository->getDetail($id);
        if (empty($list)) {
            return ['code' => ['x00001', 'article']];
        }
        $result['list'] = $list;

        // 获取文章评论
        $result['comment_lists'] = $this->articleRepository->getCommentLists($id);

        // 文章标签
        if (!empty($result['list']->tag_ids)) {
            $tag_ids                   = explode(',', $result['list']->tag_ids);
            $result['list']->tag_lists = $this->tagRepository->getArticleTags($tag_ids);
        }

        return ['获取成功', $result];
    }

    /**
     * 新增文章
     * @param  array $input
     * @return array
     */
    public function store($input)
    {
        $category_id = isset($input['category_id']) ? intval($input['category_id']) : 0;
        $title       = isset($input['title']) ? strval($input['title']) : '';
        $thumbnail   = isset($input['thumbnail']) ? strval($input['thumbnail']) : '';
        $auther      = isset($input['auther']) ? strval($input['auther']) : '';
        $content     = isset($input['content']) ? strval($input['content']) : '';
        $tag_ids     = isset($input['tag_ids']) ? implode(',', $input['tag_ids']) : '';
        $source      = isset($input['source']) ? strval($input['source']) : '';
        $is_audit    = isset($input['is_audit']) ? intval($input['is_audit']) : 0;
        $recommend   = isset($input['recommend']) ? intval($input['recommend']) : 0;
        $status      = isset($input['status']) ? intval($input['status']) : 0;

        if (!$category_id || !$title || !$content) {
            return ['code' => ['x00004', 'system']];
        }

        // 是否存在这个dict
        if (!$this->dictRepository->existDict(['article_status' => $status, 'audit' => $is_audit])) {
            return ['code' => ['x00001', 'system']];
        }

        $result = (bool) $this->articleRepository->store($category_id, $title, $thumbnail, $auther, $content, $tag_ids, $source, $is_audit, $recommend, $status);

        if (!$result) {
            return ['code' => ['x00001', 'system']];
        }
        return ['新增成功', $result];
    }

    /**
     * 编辑
     * @param int $id 主键
     * @param  array $input
     * @return array
     */
    public function update($id, $input)
    {
        // 是否存在这篇文章
        if (!$is_exist = $this->articleRepository->existList([
            'id' => $id,
        ])) {
            return ['code' => ['x00002', 'system']];
        }

        $category_id = isset($input['category_id']) ? intval($input['category_id']) : 0;
        $title       = isset($input['title']) ? strval($input['title']) : '';
        $thumbnail   = isset($input['thumbnail']) ? strval($input['thumbnail']) : '';
        $auther      = isset($input['auther']) ? strval($input['auther']) : '';
        $content     = isset($input['content']) ? strval($input['content']) : '';
        $tag_ids     = isset($input['tag_ids']) ? implode(',', $input['tag_ids']) : '';
        $source      = isset($input['source']) ? strval($input['source']) : '';
        $is_audit    = isset($input['is_audit']) ? intval($input['is_audit']) : 0;
        $recommend   = isset($input['recommend']) ? intval($input['recommend']) : 0;
        $status      = isset($input['status']) ? intval($input['status']) : 0;

        if (!$category_id || !$title || !$content) {
            return ['code' => ['x00004', 'system']];
        }

        // 是否存在这个dict
        if (!$this->dictRepository->existDict(['article_status' => $status, 'audit' => $is_audit])) {
            return ['code' => ['x00001', 'system']];
        }

        $result = $this->articleRepository->update($id, $category_id, $title, $thumbnail, $auther, $content, $tag_ids, $source, $is_audit, $recommend, $status);

        if (!$result) {
            return ['code' => ['x00001', 'system']];
        }
        return ['新增成功', $result];
    }

    /**
     * 删除
     * @param  int|array $id 文章id
     * @return array
     */
    public function destroy($id)
    {
        $result = $this->articleRepository->destroy($id);

        if (!$result) {
            return ['code' => ['x00002', 'system']];
        }

        return ['删除成功', $result];
    }
}
