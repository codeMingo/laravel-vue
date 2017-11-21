<?php
namespace App\Repositories\Backend;

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ArticleInteractive;
use App\Models\Category;
use App\Repositories\Backend\CategoryRepository;
use App\Repositories\Backend\DictRepository;

class ArticleRepository extends BaseRepository
{

    /**
     * 文章列表
     * @param  Array $input [search_form]
     * @return Array
     */
    public function lists($input)
    {
        $result['lists']   = $this->getArticleLists($input['search_form']);
        $result['options'] = $this->getOptions();
        return $this->responseResult(true, $result);
    }

    /**
     * 新增文章
     * @param  Array $input [category_id, title, auther, content, tag_ids, source, is_audit, recommend, status]
     * @return array
     */
    public function store($input)
    {
        $category_id = validateValue($input['category_id'], 'int');
        $title       = validateValue($input['title']);
        $thumbnail   = validateValue($input['thumbnail']);
        $auther      = validateValue($input['auther']);
        $content     = validateValue($input['content']);
        $tag_ids     = isset($input['tag_ids']) ? implode(',', $input['tag_ids']) : '';
        $source      = validateValue($input['source']);
        $is_audit    = validateValue($input['is_audit'], 'int');
        $recommend   = validateValue($input['recommend'], 'int');
        $status      = validateValue($input['status'], 'int');

        if (!$category_id || !$title || !$content) {
            return $this->responseResult(false, [], '新增失败，必填字段不得为空');
        }

        // 是否存在这个dict
        $flag = DictRepository::getInstance()->existDict(['article_status' => $status, 'audit' => $is_audit]);
        if (!$flag) {
            return $this->responseResult(false, [], '新增失败，参数错误，请刷新后重试');
        }

        $result = Article::create([
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
            'text'   => !$result ? '新增失败，未知错误' : '新增成功',
            'status' => !!$result,
        ]);

        return $this->responseResult(!!$result, [], !$result ? '新增失败，未知错误' : '新增成功');
    }

    /**
     * 编辑
     * @param Int $article_id
     * @param  Array $input [category_id, title, auther, content, tag_ids, source, is_audit, recommend, status]
     * @return array
     */
    public function update($article_id, $input)
    {
        $articleList = Article::where('id', $article_id)->first();
        if (empty($articleList)) {
            return $this->responseResult(false, [], '不存在这篇文章');
        }

        $category_id = validateValue($input['category_id'], 'int');
        $title       = validateValue($input['title']);
        $thumbnail   = validateValue($input['thumbnail']);
        $auther      = validateValue($input['auther']);
        $content     = validateValue($input['content']);
        $tag_ids     = isset($input['tag_ids']) ? implode(',', $input['tag_ids']) : '';
        $source      = validateValue($input['source']);
        $is_audit    = validateValue($input['is_audit'], 'int');
        $recommend   = validateValue($input['recommend'], 'int');
        $status      = validateValue($input['status'], 'int');

        if (!$category_id || !$title || !$content) {
            return $this->responseResult(false, [], '更新失败，必填字段不得为空');
        }

        // 是否存在这个dict
        $flag = DictRepository::getInstance()->existDict(['article_status' => $status, 'audit' => $is_audit]);
        if (!$flag) {
            return $this->responseResult(false, [], '更新失败，参数错误，请刷新后重试');
        }

        $result = Article::where('id', $article_id)->update([
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
                'article_id' => $article_id,
                'input'      => $input,
            ],
            'text'   => !$result ? '更新文章失败，未知错误' : '更新文章成功',
            'status' => !!$result,
        ]);

        return $this->responseResult(!!$result, [], !$result ? '更新失败，未知错误' : '更新成功');
    }

    /**
     * 删除
     * @param  Array $article_id
     * @return Array
     */
    public function destroy($article_id)
    {
        $result = Article::where('id', $article_id)->delete();

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Article/update',
            'params' => [
                'article_id' => $article_id,
            ],
            'text'   => !$result ? '删除文章失败，未知错误' : '删除文章成功',
            'status' => !!$result,
        ]);

        return $this->responseResult(!!$result, [], !$result ? '删除失败，未知错误' : '删除成功');
    }

    /**
     * 详情
     * @param  Int $article_id
     * @return Array
     */
    public function show($article_id)
    {
        $result['data'] = Article::where('id', $article_id)->first();
        if (empty($result)) {
            return $this->responseResult(false, [], '获取失败，不存在这篇文章');
        }
        $result['options'] = $this->getOptions();

        return $this->responseResult(true);
    }

    /**
     * 获取一篇文章所有的 点赞 or 反对 or 收藏
     * @param  Array $article_id
     * @return Array
     */
    public function getInteractives($article_id)
    {
        $list = Article::where('id', $article_id)->first();
        if (empty($list)) {
            return $this->responseResult(false, [], '获取失败，不存在这篇文章');
        }
        $lists    = ArticleInteractive::where('article_id', $article_id)->user()->get();
        $result['lists'] = [];
        if (!empty($lists)) {
            foreach ($lists as $key => $item) {
                if ($item->like) {
                    $result['lists']['like'][] = $item;
                } else if ($item->hate) {
                    $result['lists']['hate'][] = $item;
                } else if ($item->collect) {
                    $result['lists']['collect'][] = $item;
                }
            }
        }

        return $this->responseResult(true, $result);
    }

    /**
     * 获取评论列表
     * @param  Int $article_id
     * @return Array
     */
    public function getComments($article_id)
    {
        $list = Article::where('id', $article_id)->first();
        if (empty($list)) {
            return $this->responseResult(false, [], '获取失败，不存在这篇文章');
        }
        $result['lists'] = ArticleComment::where('article_id', $article_id)->user()->get();

        return $this->responseResult(true, $result);
    }

    /**
     * 获取浏览列表
     * @param  Int $article_id
     * @return Array
     */
    public function getReads($article_id)
    {
        $list = Article::where('id', $article_id)->first();
        if (empty($list)) {
            return $this->responseResult(false, [], '获取失败，不存在这篇文章');
        }
        $result['lists'] = ArticleRead::where('article_id', $article_id)->user()->get();

        return $this->responseResult(true, $result);
    }

    /**
     * 列表
     * @param  Array $search_form [permission_id, status, username]
     * @return Object              结果集
     */
    public function getArticleLists($search_form)
    {
        $where_params = $this->parseParams('articles', $search_form);
        return Article::parseWheres($where_params)->paginate();
    }

    /**
     * 获取options
     * @return Array
     */
    public function getOptions()
    {
        $result['category']   = CategoryRepository::getInstance()->getArticleCategories();
        $result['status']     = DictRepository::getInstance()->getDictListsByCode('article_status');
        $result['recommend']  = [['text' => '是', 'value' => 1], ['text' => '否', 'value' => 0]];
        return $result;
    }
}
