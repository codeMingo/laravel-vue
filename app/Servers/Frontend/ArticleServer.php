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
     * @param  Array $input [search]
     * @return Array
     */
    public function lists($input)
    {
        $search                        = isset($input['search']) ? (array) $input['search'] : [];
        $result['lists']               = $this->articleRepository->getArticleLists($search);
        $result['options']['category'] = $this->categoryRepository->getCategoryLists(['type' => 'article']);

        return returnSuccess($result);
    }

    /**
     * 文章详情
     * @param  int $id 文章id
     * @return Array
     */
    public function detail($id)
    {
        $result['list'] = $this->articleRepository->getArticleDetail($id);
        if (empty($result['list'])) {
            return returnError('文章不存在或已经被删除');
        }
        // 获取文章评论
        $result['comment_lists'] = $this->articleRepository->getArticleCommentLists($id);
        // 文章阅读量 +1
        $this->articleRepository->read($id);
        // 获取上一篇文章
        $result['prev_article'] = $this->articleRepository->getPrevlist($id);
        // 获取下一篇文章
        $result['next_article'] = $this->articleRepository->getNextlist($id);
        // 文章标签
        if (!empty($result['list']->tag_ids)) {
            $tag_ids                   = explode(',', $result['list']->tag_ids);
            $result['list']->tag_lists = $this->tagRepository->getArticleTags($tag_ids);
        }
        return returnSuccess($result);
    }

    /**
     * 点赞 or 反对 or 收藏
     * @param  Array $input [id, type]
     * @return Array
     */
    public function interactive($id, $input)
    {
    	$type      = isset($input['type']) ? strval($input['type']) : '';
    	$type_text = !$type ? '' : ($type == 'like' ? '点赞' : ($type == 'hate' ? '反对' : ($type == 'collect' ? '收藏' : '')));
        if (!$id || !$type || !$type_text) {
            return returnError($type_text . '失败，发生未知错误');
        }

        $list = $this->articleRepository->getArticleDetail($id);
        if (empty($list)) {
            return returnError($type_text . '失败，文章不存在或已经被删除');
        }

        // 重复操作判断
        $list = $this->articleRepository->getListByWhere(['article_id' => $id, $typ => 1, 'user_id' => $user_id]);
        if (!empty($list)) {
            return returnError($type_text . '失败，您已经操作过了');
        }

        $result = $this->articleRepository->interactive($id, $type, $type_text);
        return returnSuccess($type_text . '成功', $result);
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
            return returnError('操作失败，参数错误，请刷新后重试');
        }

        $list = $this->articleRepository->getArticleDetail($id);
        if (empty($list)) {
            return returnError($type_text . '失败，文章不存在或已经被删除');
        }

        if ($comment_id) {
            $flag = $this->articleRepository->hasComment($comment_id);
            if (!$flag) {
                return returnError('回复失败，评论不存在或已被删除');
            }
        }

        $result['list'] = $this->articleRepository->comment($id, $content, $comment_id);
        return returnSuccess($comment_id ? '回复成功' : '评论成功', $result);
    }
}
