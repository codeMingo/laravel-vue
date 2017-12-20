<?php
namespace App\Repositories\Backend;

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ArticleRead;
use App\Models\Category;
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
        User $user,
        Category $category
    ) {
        parent::__construct($article);
        $this->articleComment = $articleComment;
        $this->articleRead    = $articleRead;
        $this->tag            = $tag;
        $this->interact       = $interact;
        $this->user           = $user;
        $this->category       = $category;
    }

    /**
     * 列表
     * @param  array $input 查询条件
     * @return array
     */
    public function getLists($input)
    {
        $default_search = [
            'filter' => ['id', 'title', 'content', 'auther', 'category_id', 'status'],
            'sort'   => [
                'created_at' => 'desc',
            ],
        ];
        $search = $this->parseParams($default_search, $input);
        return $this->model->parseWheres($search)->with('comment', 'read', 'interact')->paginate();
    }

    /**
     * 新增
     * @param  int $category_id 菜单id
     * @param  string $title       标题
     * @param  string $thumbnail   缩略图
     * @param  string $auther      作者
     * @param  string $content     内容
     * @param  string $tag_ids     标签id
     * @param  string $source      来源
     * @param  int $is_audit    审核
     * @param  boolean $recommend   推荐
     * @param  int $status      状态
     * @return object
     */
    public function store($category_id, $title, $thumbnail, $auther, $content, $tag_ids, $source, $is_audit, $recommend, $status)
    {
        $result = $this->model->create([
            'category_id' => $category_id,
            'title'       => $title,
            'thumbnail'   => $thumbnail,
            'auther'      => $auther,
            'content'     => $content,
            'tag_ids'     => $tag_ids,
            'source'      => $source,
            'is_audit'    => $is_audit,
            'recommend'   => $recommend,
            'status'      => $status,
        ]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Article/store',
            'params' => [
                'category_id' => $category_id,
                'title'       => $title,
                'thumbnail'   => $thumbnail,
                'auther'      => $auther,
                'content'     => $content,
                'tag_ids'     => $tag_ids,
                'source'      => $source,
                'is_audit'    => $is_audit,
                'recommend'   => $recommend,
                'status'      => $status,
            ],
            'text'   => !!$result ? '新增文章成功' : '新增文章失败',
            'status' => !!$result,
        ]);

        return $result;
    }

    /**
     * 编辑
     * @param  int $id
     * @param  int $category_id 菜单id
     * @param  string $title       标题
     * @param  string $thumbnail   缩略图
     * @param  string $auther      作者
     * @param  string $content     内容
     * @param  string $tag_ids     标签id
     * @param  string $source      来源
     * @param  int $is_audit    审核
     * @param  boolean $recommend   推荐
     * @param  int $status      状态
     * @return object
     */
    public function update($id, $category_id, $title, $thumbnail, $auther, $content, $tag_ids, $source, $is_audit, $recommend, $status)
    {
        $result = $this->model->where('id', $id)->update([
            'category_id' => $category_id,
            'title'       => $title,
            'thumbnail'   => $thumbnail,
            'auther'      => $auther,
            'content'     => $content,
            'tag_ids'     => $tag_ids,
            'source'      => $source,
            'is_audit'    => $is_audit,
            'recommend'   => $recommend,
            'status'      => $status,
        ]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Article/update',
            'params' => [
                'id'          => $id,
                'category_id' => $category_id,
                'title'       => $title,
                'thumbnail'   => $thumbnail,
                'auther'      => $auther,
                'content'     => $content,
                'tag_ids'     => $tag_ids,
                'source'      => $source,
                'is_audit'    => $is_audit,
                'recommend'   => $recommend,
                'status'      => $status,
            ],
            'text'   => $result ? '更新文章成功' : '更新文章失败',
            'status' => $result,
        ]);

        return $result;
    }

    /**
     * 删除
     * @param  array $id
     * @return array
     */
    public function destroy($id)
    {
        $result = $this->deleteById($id);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Article/update',
            'params' => [
                'article_id' => $id,
            ],
            'text'   => $result ? '删除文章成功' : '删除文章失败',
            'status' => $result,
        ]);

        return $result;
    }

    /**
     * 获取一篇文章所有的 点赞 or 反对 or 收藏
     * @param  array $id
     * @return array
     */
    public function getInteractLists($id, $type)
    {
        $default_search = [
            'search' => [
                'article_id' => $id,
                $type        => 1,
            ],
        ];
        $result = $this->articleComment->parseWheres($default_search)->with('user')->paginate();
        return $result;
    }

    /**
     * 获取文章评论列表
     * @param  int $id
     * @return object
     */
    public function getCommentLists($id)
    {
        $default_search = [
            'search' => [
                'article_id' => $id,
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
     * 获取浏览列表
     * @param  int $id
     * @return array
     */
    public function getReadLists($id)
    {
        $default_search = [
            'search' => [
                'article_id' => $id,
            ],
        ];
        $result = $this->articleRead->parseWheres($default_search)->with('user')->paginate();
        return $result;
    }

    /**
     * 获取options
     * @return array
     */
    public function getOptions()
    {
        $dicts               = $this->getRedisDictLists(['category_type' => 'article']);
        $result['category']  = $this->category->select(['id', 'title'])->where('category_type', $dicts['category_type']['article'])->get();
        $result['status']    = $this->dictRepository->getListsByCode('article_status');
        $result['recommend'] = [['text' => '是', 'value' => 1], ['text' => '否', 'value' => 0]];

        return $result;
    }
}
