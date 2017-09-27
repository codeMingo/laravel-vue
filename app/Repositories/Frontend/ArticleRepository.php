<?php
namespace App\Repositories\Frontend;

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ArticleInteractive;
use App\Models\ArticleRead;
use App\Repositories\Frontend\DictRepository;
use DB;

class ArticleRepository extends BaseRepository
{

    /**
     * 文章列表
     * @param  Array $input [searchForm]
     * @return Array
     */
    public function lists($input)
    {
        $resultData['lists'] = Article::lists($input['searchForm']);
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '数据获取成功',
        ];
    }

    /**
     * 文章详情
     * @param  int $article_id
     * @return Array
     */
    public function detail($article_id)
    {
        $resultData['data'] = Article::where('id', $article_id)->where('status', 1)->comments()->first();
        if (empty($resultData['data'])) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }
        $resultData['data']['likeCount'] = ArticleInteractive::where('article_id', $article_id)->where('like', 1)->count();
        $resultData['data']['hateCount'] = ArticleInteractive::where('article_id', $article_id)->where('hate', 1)->count();
        $resultData['data']['readCount'] = ArticleRead::where('article_id', $article_id)->count();
        // 上一篇文章
        $resultData['prevData'] = Article::where('id', '<', $article_id)->where('status', 1)->orderBy('id', 'desc')->first();
        if (!empty($resultData['prevData'])) {
            $resultData['prevData']['likeCount'] = ArticleInteractive::where('article_id', $resultData['prevData']->id)->where('like', 1)->count();
            $resultData['prevData']['hateCount'] = ArticleInteractive::where('article_id', $resultData['prevData']->id)->where('hate', 1)->count();
            $resultData['prevData']['readCount'] = ArticleRead::where('article_id', $resultData['prevData']->id)->count();
        }
        // 下一篇文章
        $resultData['nextData'] = Article::where('id', '>', $article_id)->where('status', 1)->orderBy('id', 'asc')->first();
        if (!empty($resultData['nextData'])) {
            $resultData['nextData']['likeCount'] = ArticleInteractive::where('article_id', $resultData['nextData']->id)->where('like', 1)->count();
            $resultData['nextData']['hateCount'] = ArticleInteractive::where('article_id', $resultData['nextData']->id)->where('hate', 1)->count();
            $resultData['nextData']['readCount'] = ArticleRead::where('article_id', $resultData['nextData']->id)->count();
        }
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '数据获取成功',
        ];
    }

    /**
     * 点赞 or 反对 or 收藏
     * @param  Array $input [article_id, type]
     * @return Array
     */
    public function interactive($input, $article_id)
    {
        $type = isset($input['type']) ? strval($input['type']) : '';
        if (!$article_id || !$type) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '发生未知错误',
            ];
        }
        $articleList = Article::where('id', $article_id)->where('status', 1)->first();
        if (empty($articleList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }
        $dataList = ArticleInteractive::where('article_id', $article_id)->where($type, 1)->first();
        if (empty($dataList)) {
            $user_id = Auth::guard('web')->id();
            $result  = ArticleInteractive::create([
                'user_id'    => $user_id,
                'article_id' => $article_id,
                $type        => 1,
            ]);
        } else {
            $result = ArticleInteractive::where('article_id', $article_id)->update($type, 0);
        }
        if (!$result) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '操作失败',
            ];
        }
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => [],
            'message' => '操作成功',
        ];
    }

    /**
     * 评论 or 回复
     * @param  Array $input [article_id, commnet_id, content]
     * @return Array
     */
    public function comment($input)
    {
        $article_id = isset($input['article_id']) ? intval($input['article_id']) : '';
        $commnet_id = isset($input['commnet_id']) ? intval($input['commnet_id']) : '';
        $content    = isset($input['content']) ? intval($input['content']) : '';
        if (!$article_id || !$content) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '未知错误',
            ];
        }
        $articleList = Article::where('id', $article_id)->where('status', 1)->first();
        if (empty($articleList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }
        $dictListsValue = DictRepository::getInstance()->getDictListsByTextEnArr(['open_audit', 'audit_loading', 'audit_pass']);
        // 表示一级评论
        if (!$commnet_id) {
            $commentList = ArticleComment::where('id', $commnet_id)->where('status', 1)->where('is_audit', $dictListsValue['audit_pass'])->first();
            if (empty($commentList)) {
                return [
                    'status'  => Parent::ERROR_STATUS,
                    'data'    => [],
                    'message' => '未知错误，comment_id is null',
                ];
            }
        }
        $user_id      = Auth::guard('web')->id();
        $createResult = ArticleComment::create([
            'user_id'    => $user_id,
            'parent_id'  => $commnet_id ? $commnet_id : 0;
            'article_id' => $article_id,
            'content'    => $content,
            'is_audit'   => $dictListsValue['open_audit'] ? $dictListsValue['audit_loading'] : $dictListsValue['audit_pass'],
            'status'     => 1,
        ]);

        if (!$createResult) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '操作失败',
            ];
        }
        // 评论成功

        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => [
                'data' => [
                    'comment_id' => $createResult->parent_id,
                    'content'    => $createResult->content,
                    'create_at'  => $createResult->create_at,
                ],
            ],
            'message' => '操作成功',
        ];
    }

    /**
     * 获取点赞 or 反对 or 收藏详情
     * @param  Array $input [type]
     * @param  int $article_id
     * @return Array
     */
    public function interactiveDetail($input, $article_id)
    {
        $type = isset($input['type']) ? strval($input['type']) : '';
        if (!$article_id || !$type) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '发生未知错误',
            ];
        }
        $articleList = Article::where('id', $article_id)->where('status', 1)->first();
        if (empty($articleList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }
        $resultData['lists'] = ArticleInteractive::where('article_id', $article_id)->where($type, 1)->where('status', 1)->user()->get();
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '数据获取成功',
        ];
    }

    /**
     * 推荐文章
     * @param  Array $input [type]
     * @return Array
     */
    public function recommendList($input)
    {
        $resultData = [];
        if (empty($input)) {
            $resultData['lists'] = Article::where('recommend', 1)->where('status', 1)->get();
        } else {
            $type = isset($input['type']) ? strval($input['type']) : '';
            switch ($type) {
                case 'hot':
                    $resultData['lists'] = $this->getHotLists();
                    break;
                case 'most-like':
                    $resultData['lists'] = $this->getMostLikeList();
                    break;
                case 'most-collect':
                    $resultData['lists'] = $this->getMostCollectList();
                    break;
                case 'most-comment':
                    $resultData['lists'] = $this->getMostCommentList();
                    break;
                case 'most-read':
                    $resultData['lists'] = $this->getMostReadList();
                    break;
                default:
                    $resultData['lists'] = $this->getHotLists();
            }
        }
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '数据获取成功',
        ];
    }

    /**
     * 热门文章
     * 点赞 + 反对 + 评论 + 收藏 最多
     * @return Obejct
     */
    public function getHotLists()
    {

    }

    /**
     * 热最多人点赞文章
     * @return Obejct
     */
    public function getMostLikeList()
    {
        $articleLists = Article::withCount('interactives', function($query) {
            $query->where('like', 1)->where('status', 1);
        })->get()->sortBy('interactives_count');
        return $articleLists;
    }

    /**
     * 最多人收藏文章
     * @return Obejct
     */
    public function getMostCollectList()
    {
        $articleLists = Article::withCount('interactives', function($query) {
            $query->where('collect', 1)->where('status', 1);
        })->get()->sortBy('interactives_count');
        return $articleLists;
    }

    /**
     * 最多人评论文章
     * @return Obejct
     */
    public function getMostCommentList()
    {
        $articleLists = Article::withCount('comments', function($query) {
            $query->where('status', 1);
        })->get()->sortBy('comments_count');
        return $articleLists;
    }

    /**
     * 最多人浏览文章
     * @return Obejct
     */
    public function getMostReadList()
    {
        $articleLists = Article::withCount('reads', function($query) {
            $query->where('status', 1);
        })->get()->sortBy('reads_count');
        return $articleLists;
    }
}
