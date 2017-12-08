<?php
namespace App\Repositories\Backend;

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ArticleInteractive;
use App\Models\Category;
use App\Repositories\Backend\CategoryRepository;
use App\Repositories\Common\DictRepository;

class ArticleRepository extends CommonRepository
{

    public $dictRepository;
    public $categoryRepository;

    public function __construct(Article $article, CategoryRepository $categoryRepository, DictRepository $dictRepository)
    {
        parent::__construct($article);
        $this->dictRepository = $dictRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * 文章列表
     * @param  Array $input [search]
     * @return Array
     */
    public function lists($input)
    {
        $search            = isset($input['search']) ? (array) $input['search'] : [];
        $result['lists']   = $this->getArticleLists($search);
        $result['options'] = $this->getOptions();
        return responseResult(true, $result);
    }

    /**
     * 新增文章
     * @param  Array $input [category_id, title, auther, content, tag_ids, source, is_audit, recommend, status]
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
            return responseResult(false, [], '新增失败，必填字段不得为空');
        }

        // 是否存在这个dict
        if (!$this->dictRepository->existDict(['article_status' => $status, 'audit' => $is_audit])) {
            return responseResult(false, [], '新增失败，参数错误，请刷新后重试');
        }

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
                'input' => $input,
            ],
            'text'   => '新增成功',
        ]);

        return responseResult(true, $result, '新增成功');
    }

    /**
     * 编辑
     * @param Int $id 文章id
     * @param  Array $input [category_id, title, auther, content, tag_ids, source, is_audit, recommend, status]
     * @return array
     */
    public function update($id, $input)
    {
        $list = $this->model->find($id);
        if (empty($list)) {
            return responseResult(false, [], '更新失败，不存在这篇文章');
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
            return responseResult(false, [], '更新失败，必填字段不得为空');
        }

        // 是否存在这个dict
        if (!$this->dictRepository->existDict(['article_status' => $status, 'audit' => $is_audit])) {
            return responseResult(false, [], '更新失败，参数错误，请刷新后重试');
        }

        $this->model->where('id', $id)->update([
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
                'article_id' => $id,
                'input'      => $input,
            ],
            'text'   => '更新文章成功',
        ]);

        return responseResult(true, [], '更新成功');
    }

    /**
     * 删除
     * @param  Array $id 文章id
     * @return Array
     */
    public function destroy($id)
    {
        $result = $this->model->where('id', $id)->delete();

        if (!$result) {
            return responseResult(false, [], '该文章不存在或已被删除');
        }
        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Article/update',
            'params' => [
                'article_id' => $id,
            ],
            'text'   => '删除文章成功',
        ]);

        return responseResult(true, [], '删除成功');
    }

    /**
     * 详情
     * @param  Int $article_id
     * @return Array
     */
    public function show($article_id)
    {
        $result['list'] = $this->model->where('id', $article_id)->first();
        if (empty($result['list'])) {
            return responseResult(false, [], '获取失败，不存在这篇文章');
        }
        $result['options'] = $this->getOptions();

        return responseResult(true);
    }

    /**
     * 获取一篇文章所有的 点赞 or 反对 or 收藏
     * @param  Array $id
     * @return Array
     */
    public function getInteractives($id, $type)
    {
        $list = $this->model->where('id', $id)->first();
        if (empty($list)) {
            return responseResult(false, [], '获取失败，不存在这篇文章');
        }
        $result['lists'] = ArticleInteractive::where('article_id', $id)->with('user')->get();

        return responseResult(true, $result);
    }

    /**
     * 获取评论列表
     * @param  Int $article_id
     * @return Array
     */
    public function getComments($article_id)
    {
        $list = $this->model->where('id', $article_id)->first();
        if (empty($list)) {
            return responseResult(false, [], '获取失败，不存在这篇文章');
        }
        $result['lists'] = ArticleComment::where('article_id', $article_id)->with('user')->get();

        return responseResult(true, $result);
    }

    /**
     * 获取浏览列表
     * @param  Int $article_id
     * @return Array
     */
    public function getReads($article_id)
    {
        $list = $this->model->where('id', $article_id)->first();
        if (empty($list)) {
            return responseResult(false, [], '获取失败，不存在这篇文章');
        }
        $result['lists'] = ArticleRead::where('article_id', $article_id)->with('user')->get();

        return responseResult(true, $result);
    }

    /**
     * 列表
     * @param  Array $search [permission_id, status, username]
     * @return Object              结果集
     */
    public function getArticleLists($search)
    {
        $where_params = $this->parseParams('articles', $search);

        return $this->model->parseWheres($where_params)->paginate();
    }

    /**
     * 获取options
     * @return Array
     */
    public function getOptions()
    {
        $dicts               = $this->getRedisDictLists(['category_type' => 'article']);
        $result['category']  = $this->categoryRepository->getCategoryLists(['category_type' => $dicts['category_type']['article']]);
        $result['status']    = $this->dictRepository->getListsByCode('article_status');
        $result['recommend'] = [['text' => '是', 'value' => 1], ['text' => '否', 'value' => 0]];

        return $result;
    }
}
