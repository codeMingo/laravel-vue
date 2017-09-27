<?php
namespace App\Repositories\Backend;

use App\Models\Article;
use App\Models\Category;
use App\Repositories\Backend\DictRepository;

class ArticleRepository extends BaseRepository
{

    /**
     * 获取列表
     */
    public function lists($input)
    {
        $resultData['lists']           = Article::lists($input['searchForm']);
        $resultData['options']['categories'] = Category::lists('article');
        $resultData['options']['status']   = DictRepository::getInstance()->getDictListsByCode('article_status');
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '',
        ];
    }

    /**
     * 新增
     */
    public function create($input)
    {
        $insert = Article::create([
            'category_id' => $input['category_id'],
            'title'       => $input['title'],
            'auther'      => $input['auther'],
            'content'     => $input['content'],
            'source'      => $input['source'],
            'reading'     => $input['reading'],
            'status'      => $input['status'],
        ]);
        return [
            'status'  => $insert ? $this->successStatus : $this->errorStatus,
            'data'    => '',
            'message' => $insert ? '数据新增成功' : '未知错误，数据新增失败',
        ];
    }

    /**
     * 编辑
     */
    public function update($input)
    {
        $data = Article::where('id', $input['id'])->first();
        if (empty($data)) {
            return [
                'status'  => 0,
                'data'    => '',
                'message' => '文章不存在',
            ];
        }
        $updateData = [
            'category_id' => $input['category_id'],
            'title'       => $input['title'],
            'auther'      => $input['auther'],
            'content'     => $input['content'],
            'source'      => $input['source'],
            'reading'     => $input['reading'],
            'status'      => $input['status'],
        ];
        $update = Article::where('id', $input['id'])->update($updateData);
        return [
            'status'  => $update ? 1 : 0,
            'data'    => '',
            'message' => $update ? '数据更新成功' : '数据更新失败',
        ];
    }

    /**
     * 删除
     */
    public function delete($id)
    {
        $deleted = Article::where('id', $id)->delete();
        return [
            'status'  => $deleted ? 1 : 0,
            'data'    => '',
            'message' => $deleted ? '删除成功' : '删除失败',
        ];
    }

    /*
     * 获取options
     */
    public function getOptions()
    {
        $resultData['options']['status'] = DictRepository::getInstance()->getDictListsByCode('article_status');;
        $resultData['options']['categories'] = Category::lists('article');
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '获取成功',
        ];
    }

    /*
     * 获取一条文章
     */
    public function show($id)
    {
        $this->data['data']          = Article::where('id', $id)->first();
        $this->data['statusOptions'] = [
            ['value' => '0', 'text' => '已下架'],
            ['value' => '1', 'text' => '正常'],
            ['value' => '2', 'text' => '推荐'],
        ];
        $this->data['categoryOptions'] = Category::getLists('article');
        return [
            'status'  => 1,
            'data'    => $this->data,
            'message' => '',
        ];
    }
}
