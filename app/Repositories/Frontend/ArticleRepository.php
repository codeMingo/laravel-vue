<?php
namespace App\Repositories\Frontend;

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ArticleRead;
use App\Models\Interact;
use App\Models\Tag;
use App\Models\User;

class ArticleRepository extends CommonRepository
{

    public $articleComment;
    public $articleRead;
    public $tags;
    public $interact;
    public $user;
    public $categoryRepository;

    public function __construct(
        Article $article,
        ArticleComment $articleComment,
        Tag $tag,
        ArticleRead $articleRead,
        Interact $interact,
        User $user
    ) {
        parent::__construct($article);
        $this->articleComment = $articleComment;
        $this->articleRead    = $articleRead;
        $this->tag            = $tag;
        $this->interact       = $interact;
        $this->user           = $user;
    }

    /**
     * 文章列表
     * @param  Array $search 查询条件
     * @return Array
     */
    public function getLists($search)
    {
        $dicts          = $this->getRedisDictLists(['audit' => ['pass'], 'article_status' => ['show']]);
        $default_search = [
            'status'             => $dicts['article_status']['show'],
            'is_audit'           => $dicts['audit']['pass'],
            '__not_select__'     => ['deleted_at', 'updated_at', 'tag_ids', 'source', 'is_audit', 'admin_id', 'user_id'],
            '__relation_table__' => ['comment', 'read', 'interact'],
            '__order_by__'       => ['created_at' => 'desc'],
        ];
        $search = array_merge($default_search, $search);
        return parent::getLists($search);
    }

    /**
     * 文章详情
     * @param  int $id 文章id
     * @return Array
     */
    public function getDetail($id)
    {
        $dicts = $this->getRedisDictLists(['audit' => ['pass'], 'article_status' => ['show']]);
        return $this->getDetail($id, [
            'status'             => $dicts['article_status']['show'],
            'is_audit'           => $dicts['audit']['pass'],
            '__not_select__'     => ['admin_id', 'user_id', 'deleted_at', 'updated_at', 'is_audit', 'recommend'],
            '__relation_table__' => ['interact', 'category', 'read'],
        ]);
    }

    // 阅读数 + 1
    public function read($id)
    {
        $this->articleRead->create([
            'user_id'    => $this->getCurrentId(),
            'article_id' => $id,
            'ip_address' => getClientIp(),
        ]);
        return true;
    }

    // 获取上一篇文章
    public function getPrevlist($id)
    {
        $dicts = $this->getRedisDictLists(['audit' => ['pass'], 'article_status' => ['show']]);
        return $this->model->where('status', $dicts['article_status']['show'])->where('is_audit', $dicts['audit']['pass'])->where('id', '<', $id)->with('read')->with('interact')->with('comment')->orderBy('id', 'desc')->first();
    }

    // 获取下一篇文章
    public function getNextlist($id)
    {
        $dicts = $this->getRedisDictLists(['audit' => ['pass'], 'article_status' => ['show']]);
        return $this->model->where('status', $dicts['article_status']['show'])->where('is_audit', $dicts['audit']['pass'])->where('id', '>', $id)->with('read')->with('interact')->with('comment')->orderBy('id', 'asc')->first();
    }

    /**
     * 获取文章评论列表
     * @param  int $id 文章id
     * @return Object
     */
    public function getCommentLists($id)
    {
        $dicts = $this->getRedisDictLists(['audit' => ['pass'], 'article_status' => ['show']]);

        $lists = $this->articleComment->where('article_id', $id)->where('is_audit', $dicts['audit']['pass'])->where('status', 1)->where('parent_id', 0)->with('user')->paginate();
        if ($lists->isEmpty()) {
            return $lists;
        }
        $comment_ids = [];
        foreach ($lists as $index => $comment) {
            $comment_ids[] = $comment->id;
        }

        // 找出所有的回复
        $response_lists = $this->articleComment->whereIn('parent_id', $comment_ids)->with('user')->get();
        if (!$response_lists->isEmpty()) {
            $response_temp = [];
            foreach ($response_lists as $index => $response) {
                $response_temp[$response->parent_id][] = $response;
            }

            foreach ($lists as $index => $comment) {
                $lists[$index]['response'] = isset($response_temp[$comment->id]) ? $response_temp[$comment->id] : [];
            }
        }
        return $lists;
    }

    /**
     * 点赞 or 反对 or 收藏
     * @param  Int $id 文章id
     * @param  Array $type [like | hate | collect]
     * @return Array
     */
    public function interactive($id, $type)
    {
        $result = $this->interact->create([
            'user_id'    => $user_id,
            'article_id' => $id,
            $type        => 1,
        ]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Article/interactive',
            'params' => [
                'id'   => $id,
                'type' => $type,
            ],
            'text'   => '操作成功',
        ]);
        return $result;
    }

    public function hasComment($comment_id)
    {
        $dicts = $this->getRedisDictLists(['audit' => ['loading', 'pass'], 'system' => ['article_comment_audit']]);
        return (bool) $this->articleComment->where('id', $comment_id)->where('status', 1)->where('is_audit', $dicts['audit']['pass'])->first();
    }

    /**
     * 评论 or 回复
     * @param  Int $id 文章id
     * @param  String $content 文章内容
     * @param  Int $comment_id 评论id，有值表示回复
     * @return Array
     */
    public function comment($id, $content, $comment_id = 0)
    {
        $dicts = $this->getRedisDictLists(['audit' => ['loading', 'pass'], 'system' => ['article_comment_audit']]);
        $user_id = $this->getCurrentId();
        $result  = $this->articleComment->create([
            'user_id'    => $user_id,
            'parent_id'  => $comment_id ? $comment_id : 0,
            'article_id' => $id,
            'content'    => $content,
            'is_audit'   => $dicts['system']['article_comment_audit'] ? $dicts['audit']['loading'] : $dicts['audit']['pass'],
            'status'     => 1,
        ]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Article/comment',
            'params' => [
                'id'         => $id,
                'content'    => $content,
                'comment_id' => $comment_id,
            ],
            'text'   => $comment_id ? '回复成功' : '评论成功',
        ]);
        $result['response'] = [];
        $result['user']     = $this->user->where('id', $user_id)->first();

        return $result;
    }
}
