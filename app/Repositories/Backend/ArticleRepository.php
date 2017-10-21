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
     * @param  Array $input [searchForm]
     * @return Array
     */
    public function lists($input)
    {
        $resultData['lists']                 = $this->getArticleLists($input['searchForm']);
        $resultData['options']['categories'] = CategoryRepository::getInstance()->getListsByDictText('article_category');
        $resultData['options']['status']     = DictRepository::getInstance()->getDictListsByCode('article_status');
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '获取成功',
        ];
    }

    /**
     * 新增文章
     * @param  Array $input [category_id, title, auther, content, tag_include, source, is_audit, recommend, status]
     * @return array
     */
    public function store($input)
    {
        $category_id = isset($input['category_id']) ? intval($input['category_id']) : 0;
        $title       = isset($input['title']) ? strval($input['title']) : '';
        $thumbnail   = isset($input['thumbnail']) ? strval($input['thumbnail']) : '';
        $auther      = isset($input['auther']) ? strval($input['auther']) : '';
        $content     = isset($input['content']) ? strval($input['content']) : '';
        $tag_include = isset($input['tag_include']) ? implode(',', $input['tag_include']) : '';
        $source      = isset($input['source']) ? strval($input['source']) : '';
        $is_audit    = isset($input['is_audit']) ? intval($input['is_audit']) : 0;
        $recommend   = isset($input['recommend']) && !empty($input['recommend']) ? 1 : 0;
        $status      = isset($input['status']) ? intval($input['status']) : 0;

        if (!$category_id || !$title || !$content) {
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
            'thumbnail'   => $thumbnail,
            'auther'      => $auther,
            'content'     => $content,
            'tag_include' => $tag_include,
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
            'text'   => !$insertResult ? '新增文章失败，未知错误' : '新增文章成功',
            'status' => !!$insertResult,
        ]);

        return [
            'status'  => !$insertResult ? Parent::ERROR_STATUS : Parent::SUCCESS_STATUS,
            'data'    => [],
            'message' => !$insertResult ? '新增文章失败，未知错误' : '新增文章成功',
        ];
    }

    /**
     * 编辑文章
     * @param Int $article_id
     * @param  Array $input [category_id, title, auther, content, tag_include, source, is_audit, recommend, status]
     * @return array
     */
    public function update($article_id, $input)
    {
        $articleList = Article::where('id', $article_id)->first();
        if (empty($articleList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }
        $category_id = isset($input['category_id']) ? intval($input['category_id']) : 0;
        $title       = isset($input['title']) ? strval($input['title']) : '';
        $thumbnail   = isset($input['thumbnail']) ? strval($input['thumbnail']) : '';
        $auther      = isset($input['auther']) ? strval($input['auther']) : '';
        $content     = isset($input['content']) ? strval($input['content']) : '';
        $tag_include = isset($input['tag_include']) ? implode(',', $input['tag_include']) : '';
        $source      = isset($input['source']) ? strval($input['source']) : '';
        $is_audit    = isset($input['is_audit']) ? intval($input['is_audit']) : 0;
        $recommend   = isset($input['recommend']) && !empty($input['recommend']) ? 1 : 0;
        $status      = isset($input['status']) ? intval($input['status']) : 0;

        if (!$category_id || !$title || !$content) {
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

        $updateResult = Article::where('id', $article_id)->update([
            'category_id' => $category_id,
            'title'       => $title,
            'thumbnail'   => $thumbnail,
            'auther'      => $auther,
            'content'     => $content,
            'tag_include' => $tag_include,
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
            'text'   => !$updateResult ? '更新文章失败，未知错误' : '更新文章成功',
            'status' => !!$updateResult,
        ]);

        return [
            'status'  => !$updateResult ? Parent::ERROR_STATUS : Parent::SUCCESS_STATUS,
            'data'    => '',
            'message' => !$updateResult ? '更新文章失败，未知错误' : '更新文章成功',
        ];
    }

    /**
     * 删除文章
     * @param  Array $article_id
     * @return Array
     */
    public function destroy($article_id)
    {
        $deleteResult = Article::where('id', $article_id)->delete();

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Article/update',
            'params' => [
                'article_id' => $article_id,
                'input'      => $input,
            ],
            'text'   => !$deleteResult ? '删除文章失败，未知错误' : '删除文章成功',
            'status' => !!$deleteResult,
        ]);

        return [
            'status'  => !$deleteResult ? Parent::ERROR_STATUS : Parent::SUCCESS_STATUS,
            'data'    => '',
            'message' => !$deleteResult ? '删除文章失败，未知错误' : '删除文章成功',
        ];
    }

    /**
     * 获取options
     * @return Array
     */
    public function getOptions()
    {
        $resultData['options']['status']     = DictRepository::getInstance()->getDictListsByCode('article_status');
        $resultData['options']['categories'] = CategoryRepository::getInstance()->getListsByDictText('article_category');
        $resultData['options']['recommends'] = [['text' => '是', 'value' => 1], ['text' => '否', 'value' => 0]];
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '获取成功',
        ];
    }

    /**
     * 获取一篇文章详情
     * @param  Int $article_id
     * @return Array
     */
    public function show($article_id)
    {
        $resultData['data'] = Article::where('id', $article_id)->first();
        if (empty($resultData)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }
        $resultData['options']['categories'] = Category::lists('article_category');
        $resultData['options']['status']     = DictRepository::getInstance()->getDictListsByCode('article_status');
        $resultData['options']['recommends'] = [['text' => '是', 'value' => 1], ['text' => '否', 'value' => 0]];
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '获取成功',
        ];
    }

    /**
     * 获取一篇文章编辑
     * @param  Int $article_id
     * @return Array
     */
    public function edit($article_id)
    {
        $resultData['data'] = Article::where('id', $article_id)->first();
        if (empty($resultData)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }
        $resultData['options']['categories'] = Category::lists('article_category');
        $resultData['options']['status']     = DictRepository::getInstance()->getDictListsByCode('article_status');
        $resultData['options']['recommends'] = [['text' => '是', 'value' => 1], ['text' => '否', 'value' => 0]];
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '获取成功',
        ];
    }

    /**
     * 获取一篇文章所有的 点赞 or 反对 or 收藏
     * @param  Array $article_id
     * @return Array
     */
    public function getInteractives($article_id)
    {
        $articleList = Article::where('id', $article_id)->first();
        if (empty($articleList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }
        $interactiveLists    = ArticleInteractive::where('article_id', $article_id)->user()->get();
        $resultData['lists'] = [];
        if (!empty($interactiveLists)) {
            foreach ($interactiveLists as $key => $item) {
                if ($item->like) {
                    $resultData['lists']['like'][] = $item;
                } else if ($item->hate) {
                    $resultData['lists']['hate'][] = $item;
                } else if ($item->collect) {
                    $resultData['lists']['collect'][] = $item;
                }
            }
        }
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '获取成功',
        ];
    }

    /**
     * 获取评论列表
     * @param  Int $article_id
     * @return Array
     */
    public function getComments($article_id)
    {
        $articleList = Article::where('id', $article_id)->first();
        if (empty($articleList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }
        $resultData['lists'] = ArticleComment::where('article_id', $article_id)->user()->get();
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '获取成功',
        ];
    }

    /**
     * 获取浏览列表
     * @param  Int $article_id
     * @return Array
     */
    public function getReads($article_id)
    {
        $articleList = Article::where('id', $article_id)->first();
        if (empty($articleList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }
        $resultData['lists'] = ArticleRead::where('article_id', $article_id)->user()->get();
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '获取成功',
        ];
    }

    /**
     * 改变某一个字段的值
     * @param  Int $article_id
     * @param  Array $data [field, value]
     * @return Array
     */
    public function changeFieldValue($article_id, $input)
    {
        $updateResult = Article::where('id', $article_id)->update([$input['field'] => $input['value']]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Article/changeFieldValue',
            'params' => [
                'article_id' => $article_id,
                'input'      => $input,
            ],
            'text'   => !$updateResult ? '操作失败，未知错误' : '操作成功',
            'status' => !!$updateResult,
        ]);

        return [
            'status'  => !$updateResult ? Parent::ERROR_STATUS : Parent::SUCCESS_STATUS,
            'data'    => [],
            'message' => !$updateResult ? '操作失败，未知错误' : '操作成功',
        ];
    }

    /**
     * 获取文章数据
     * @param  Array $search_form [所有可搜索字段]
     * @return Object
     */
    public function getArticleLists($search_form)
    {
        $page_size    = 10;
        $where_params = [];
        if (empty($search_form)) {
            return Article::where($where_params)->paginate($page_size);
        }

        if (isset($search_form['status']) && $search_form['status'] !== '') {
            $where_params['status'] = $search_form['status'];
        }

        if (isset($search_form['is_audit'])) {
            $where_params['is_audit'] = $search_form['is_audit'];
        }

        if (isset($search_form['recommend'])) {
            $where_params['recommend'] = $search_form['recommend'];
        }

        if (isset($search_form['category_id']) && !empty($search_form['category_id'])) {
            $where_params['category_id'] = $search_form['category_id'];
        }

        if (isset($search_form['admin_id']) && !empty($search_form['admin_id'])) {
            $where_params['admin_id'] = $search_form['admin_id'];
        }

        if (isset($search_form['user_id']) && !empty($search_form['user_id'])) {
            $where_params['user_id'] = $search_form['user_id'];
        }

        $query = Article::where($where_params);
        if (isset($search_form['title']) && $search_form['title'] !== '') {
            $query->where('title', 'like', '%' . $search_form['title'] . '%');
        }

        if (isset($search_form['auther']) && $search_form['auther'] !== '') {
            $query->where('auther', 'like', '%' . $search_form['auther'] . '%');
        }

        if (isset($search_form['tag_include']) && is_array($search_form['tag_include']) && !empty($search_form['tag_include'])) {
            $query->whereIn('tag_include', $search_form['tag_include']);
        }
        return $query->paginate($page_size);
    }
}
