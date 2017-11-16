<?php
namespace App\Repositories\Frontend;

use App\Models\Video;
use App\Models\VideoLists;

class VideoRepository extends BaseRepository
{

    public function index()
    {
        $result['lists'] = $this->getVideoLists($search_form);
        return [
            'status'  => Parent::ERROR_STATUS,
            'data'    => $result,
            'message' => '数据获取成功',
        ];
    }

    public function getVideoLists()
    {
        $dictKeyValue          = DictRepository::getInstance()->getDictListsByTextEnArr(['video_is_show', 'video_page_size']);
        $search_form['status'] = $dictKeyValue['video_is_show'];
        $where_params          = $this->parseParams($search_form);
        $page_size             = $dictKeyValue['video_page_size'];
        return Video::parseWheres($where_params)->with('videoList')->paginate($page_size);
    }
}
