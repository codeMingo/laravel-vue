<?php
namespace App\Repositories\Frontend;

use App\Models\Video;

class VideoRepository extends CommonRepository
{

    public $categoryRepository;

    public function __construct(Video $video, CategoryRepository $categoryRepository)
    {
        parent::__construct($video);
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * 视频列表页面
     * @param  Array $input [search]
     * @return Array
     */
    public function lists($input)
    {
        $search                        = isset($input['search']) ? (array) $input['search'] : [];
        $result['lists']               = $this->getVideoLists($search);
        $result['options']['category'] = $this->categoryRepository->getCategoryLists(['type' => 'video']);
        return responseResult(true, $result);
    }

    /**
     * 获取视频列表
     * @param  Array $search [所有可搜索字段]
     * @return Object
     */
    public function getVideoLists($search)
    {
        $dicts              = $this->getRedisDictLists(['audit' => ['pass'], 'video_status' => ['show']]);
        $search['status']   = $dicts['video_status']['show'];
        $search['is_audit'] = $dicts['audit']['pass'];
        $params             = $this->parseParams($search);
        return $this->model->parseWheres($params)->paginate();
    }
}
