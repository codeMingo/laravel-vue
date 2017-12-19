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
    public function lists($input)
    {
        $dicts          = $this->getRedisDictLists(['audit' => ['pass'], 'article_status' => ['show']]);
        $default_search = [
            'filter' => ['id', 'title', 'content', 'auther', 'category_id'],
            'search' => [
                'status'   => $dicts['article_status']['show'],
                'is_audit' => $dicts['audit']['pass'],
            ],
            'sort'   => [
                'created_at' => 'desc',
            ],
        ];
        $search = $this->parseParams($default_search, $input);
        return $this->model->parseWheres($search)->with('comment', 'read', 'interact')->paginate();
    }

    /**
     * 文章详情
     * @param  int $id 文章id
     * @return Array
     */
    public function show($id)
    {
        $dicts          = $this->getRedisDictLists(['audit' => ['pass'], 'article_status' => ['show']]);
        $default_search = [
            'search' => [
                'id'       => $id,
                'status'   => $dicts['article_status']['show'],
                'is_audit' => $dicts['audit']['pass'],
            ],
        ];
        return $this->model->parseWheres($default_search)->with('interact', 'category', 'read')->first();
    }

    // 阅读数 + 1
    public function read($id)
    {
        $this->articleRead->create([
            'user_id'    => getCurrentUserId(),
            'article_id' => $id,
            'ip_address' => getClientIp(),
        ]);
        return true;
    }

    // 获取上一篇文章
    public function prevlist($id)
    {
        $dicts          = $this->getRedisDictLists(['audit' => ['pass'], 'article_status' => ['show']]);
        $default_search = [
            'search' => [
                'id'       => ['<', $id],
                'status'   => $dicts['article_status']['show'],
                'is_audit' => $dicts['audit']['pass'],
            ],
            'sort'   => [
                'id' => 'desc',
            ],
        ];
        return $this->model->parseWheres($default_search)->with('interact', 'category', 'read')->first();
    }

    // 获取下一篇文章
    public function nextlist($id)
    {
        $dicts          = $this->getRedisDictLists(['audit' => ['pass'], 'article_status' => ['show']]);
        $default_search = [
            'search' => [
                'id'       => ['>', $id],
                'status'   => $dicts['article_status']['show'],
                'is_audit' => $dicts['audit']['pass'],
            ],
            'sort'   => [
                'id' => 'asc',
            ],
        ];
        return $this->model->parseWheres($default_search)->with('interact', 'category', 'read')->first();
    }

    /**
     * 获取文章评论列表
     * @param  int $id 文章id
     * @return Object
     */
    public function commentLists($id)
    {
        $dicts          = $this->getRedisDictLists(['audit' => ['pass'], 'article_status' => ['show']]);
        $default_search = [
            'search' => [
                'article_id' => $id,
                'status'     => 1,
                'is_audit'   => $dicts['audit']['pass'],
                'parent_id'  => 0,
            ],
        ];
        $lists = $this->articleComment->parseWheres($default_search)->with('user')->paginate();
        if ($lists->isEmpty()) {
            return $lists;
        }
        $comment_ids = [];
        foreach ($lists as $index => $comment) {
            $comment_ids[] = $comment->id;
        }

        // 找出所有的回复
        $response_lists = $this->articleComment->parseWheres([
            'search' => [
                'parent_id' => ['in', $comment_ids],
                'status'    => 1,
                'is_audit'  => $dicts['audit']['pass'],
            ],
        ])->with('user')->get();
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
            'user_id'    => getCurrentUserId(),
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

    /**
     * 判断是否有这条评论
     * @param  int  $comment_id 评论id
     * @return boolean
     */
    public function existComment($comment_id)
    {
        $dicts = $this->getRedisDictLists(['audit' => ['pass']]);
        return (bool) $this->model->parseSearch([
            'id'       => $comment_id,
            'status'   => 1,
            'is_audit' => $dicts['audit']['pass'],
        ])->first();
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
        $dicts  = $this->getRedisDictLists(['audit' => ['loading', 'pass'], 'system' => ['article_comment_audit']]);
        $result = $this->articleComment->create([
            'user_id'    => getCurrentUserId(),
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

        return $result;
    }
}
