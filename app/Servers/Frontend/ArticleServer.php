<?php
namespace App\Servers\Frontend;

use App\Repositories\Frontend\ArticleRepository;
use App\Repositories\Frontend\CategoryRepository;
use App\Repositories\Frontend\TagRepository;

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

    /**
     * 文章列表
     * @param  array $input [search]
     * @return array
     */
    public function lists($input)
    {
        $result['lists']               = $this->articleRepository->lists($input);
        $result['options']['category'] = $this->categoryRepository->articleCategoryLists();

        return ['获取成功', $result];
    }

    /**
     * 文章详情
     * @param  int $id 文章id
     * @return Array
     */
    public function detail($id)
    {
        $list = $this->articleRepository->getDetail($id);
        if (empty($list)) {
            return ['code' => ['x00001', 'article']];
        }
        $result['list'] = $list;

        // 文章阅读量 +1
        $this->articleRepository->read($id);

        // 获取文章评论
        $result['comment_lists'] = $this->articleRepository->commentLists($id);

        // 获取上一篇文章
        $result['prev_article'] = $this->articleRepository->prevlist($id);

        // 获取下一篇文章
        $result['next_article'] = $this->articleRepository->nextlist($id);

        // 文章标签
        if (!empty($result['list']->tag_ids)) {
            $tag_ids                   = explode(',', $result['list']->tag_ids);
            $result['list']->tag_lists = $this->tagRepository->getArticleTags($tag_ids);
        }

        return ['获取成功', $result];
    }

    /**
     * 点赞 or 反对 or 收藏
     * @param  Array $input [id, type]
     * @return Array
     */
    public function interactive($id, $input)
    {
        $type = isset($input['type']) ? strval($input['type']) : '';
        if (!$type || !in_array($type, ['like', 'hate', 'collect'])) {
            return ['code' => ['x00001', 'system']];
        }

        $list = $this->articleRepository->getDetail($id);
        if (empty($list)) {
            return ['code' => ['x00002', 'system']];
        }

        // 重复操作判断
        if ($is_exist = $this->articleRepository->existList([
            'id'      => $id,
            'user_id' => getCurrentUserId(),
            $type     => 1,
        ])) {
            return ['code' => ['x00003', 'system']];
        }

        $result = $this->articleRepository->interactive($id, $type);

        return ['操作成功', $result];
    }

    /**
     * 评论 or 回复
     * @param  Array $input [id, comment_id, content]
     * @return Array
     */
    public function comment($id, $input)
    {
        $comment_id = isset($input['comment_id']) ? intval($input['comment_id']) : 0;
        $content    = isset($input['content']) ? strval($input['content']) : '';
        if (!$id || !$content) {
            return ['code' => ['x00001', 'system']];
        }

        if (!$is_exist = $this->articleRepository->existList([
            'id' => $id,
        ])) {
            return ['code' => ['x00002', 'system']];
        }

        if ($comment_id) {
            if (!$is_exist = $this->articleRepository->hasComment($comment_id)) {
                return ['code' => ['x00002', 'system']];
            }
        }

        $result['list'] = $this->articleRepository->comment($id, $content, $comment_id);

        return ['操作成功', $result];
    }
}
