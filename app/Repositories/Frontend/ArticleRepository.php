<?php
namespace App\Repositories\Frontend;

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\Interact;
use App\Models\ArticleRead;
use App\Models\User;
use App\Repositories\Frontend\CategoryRepository;
use App\Repositories\Frontend\DictRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArticleRepository extends BaseRepository
{

    /**
     * 文章列表
     * @param  Array $input [search_form]
     * @return Array
     */
    public function lists($input)
    {
        $result['lists'] = $this->getArticleLists($input['search_form'])->toArray();
        $result['options']['category'] = CategoryRepository::getInstance()->getArticleCategories();
        return $this->responseResult(true, $result);
    }

    /**
     * 文章详情
     * @param  int $article_id
     * @return Array
     */
    public function detail($article_id)
    {
        // 审核通过的value
        $audit_pass_value = DictRepository::getInstance()->getValueByCodeAndTextEn('audit', 'pass');
        // 文章正常显示的value
        $show_status_value = DictRepository::getInstance()->getValueByCodeAndTextEn('article_status', 'show');

        $result['list'] = Article::where('id', $article_id)->where('status', $show_status_value)->where('is_audit', $audit_pass_value)->with('read')->with('interactive')->with('category')->first();
        if (empty($result['list'])) {
            return $this->responseResult(false, $result, '获取失败，文章不存在或已被删除');
        }

        // 文章阅读量 +1
        $this->read($article_id, Auth::guard('web')->id());

        // 获取上一篇文章
        $result['prev'] = $this->getPrevArticleList($article_id);
        // 获取下一篇文章
        $result['next'] = $this->getNextArticleList($article_id);

        // 文章标签
        if (!empty($result['list']->tag_ids)) {
            $tag_id_arr   = explode(',', $result['list']->tag_ids);
            $result['list']->tag_lists  = DB::table('tags')->whereIn('id', $tag_id_arr)->where('status', 1)->get();
        }
        return $this->responseResult(true, $result);
    }

    // 阅读数 + 1
    public function read($article_id, $user_id)
    {
        ArticleRead::create([
            'user_id'    => $user_id,
            'article_id' => $article_id,
            'ip_address' => getClientIp(),
        ]);
        return true;
    }

    // 获取上一篇文章
    public function getPrevArticleList($article_id)
    {
        // 审核通过的value
        $audit_pass_value = DictRepository::getInstance()->getValueByCodeAndTextEn('audit', 'pass');
        // 文章正常显示的value
        $show_status_value = DictRepository::getInstance()->getValueByCodeAndTextEn('article_status', 'show');
        return DB::table('articles')->where('status', $show_status_value)->where('is_audit', $audit_pass_value)->where('article_id', '<', $article_id)->orderBy('article_id', 'desc')->first();
    }

    // 获取下一篇文章
    public function getNextArticleList($article_id)
    {
        // 审核通过的value
        $audit_pass_value = DictRepository::getInstance()->getValueByCodeAndTextEn('audit', 'pass');
        // 文章正常显示的value
        $show_status_value = DictRepository::getInstance()->getValueByCodeAndTextEn('article_status', 'show');
        return DB::table('articles')->where('status', $show_status_value)->where('is_audit', $audit_pass_value)->where('article_id', '>', $article_id)->orderBy('article_id', 'asc')->first();
    }

    /**
     * 获取文章评论列表
     * @param  int $article_id 文章id
     * @return Object
     */
    public function commentLists($article_id)
    {
        $audit_pass_value = DictRepository::getInstance()->getValueByCodeAndTextEn('audit', 'pass');
        $show_status_value = DictRepository::getInstance()->getValueByCodeAndTextEn('article_status', 'show');
        $result['lists'] = ArticleComment::where('article_id', $article_id)->where('is_audit', $audit_pass_value)->where('status', 1)->where('parent_id', 0)->with('user')->paginate();
        if ($result['lists']->isEmpty()) {
            return $this->responseResult(true, $result);
        }
        $comment_ids = [];
        foreach ($result['lists'] as $index => $comment) {
            $comment_ids[] = $comment->id;
        }

        // 找出所有的回复
        if (!empty($comment_ids)) {
            $response_lists = ArticleComment::whereIn('parent_id', $comment_ids)->with('user')->get();
            if (!empty($response_lists)) {
                $response_temp = [];
                foreach ($response_lists as $index => $response) {
                    $response_temp[$response->parent_id][] = $response;
                }

                foreach ($result['lists'] as $index => $comment) {
                    $result['lists'][$index]['response'] = isset($response_temp[$comment->id]) ? $response_temp[$comment->id] : [];
                }
            }
        }
        return $this->responseResult(true, $result);
    }

    /**
     * 点赞 or 反对 or 收藏
     * @param  Array $input [article_id, type]
     * @return Array
     */
    public function interactive($input, $article_id)
    {
        $type = isset($input['type']) ? strval($input['type']) : '';
        $type_text = !$type ? '' : ($type == 'like' ? '点赞' : ($type == 'hate' ? '反对' : ($type == 'collect' ? '收藏' : '')));
        if (!$article_id || !$type || !$type_text) {
            return $this->responseResult(false, [], $type_text . '失败，发生未知错误');
        }

        $audit_pass_value = DictRepository::getInstance()->getValueByCodeAndTextEn('audit', 'pass');
        $show_status_value = DictRepository::getInstance()->getValueByCodeAndTextEn('article_status', 'show');

        $articleList    = Article::where('id', $article_id)->where('status', $show_status_value)->first();
        if (empty($articleList)) {
            return $this->responseResult(false, [], $type_text . '失败，文章不存在或已被删除');
        }
        $user_id  = Auth::guard('web')->id();
        $dataList = Interact::where('article_id', $article_id)->where('user_id', $user_id)->where($type, 1)->first();
        if (!empty($dataList)) {
            return $this->responseResult(false, [], $type_text . '失败，您已经操作过了');
        }
        Interact::create([
            'user_id'    => $user_id,
            'article_id' => $article_id,
            $type        => 1,
        ]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Article/interactive',
            'params' => $input,
            'text'   => $type_text . '成功',
        ]);
        return $this->responseResult(true, [], $type_text . '成功');
    }

    /**
     * 评论 or 回复
     * @param  Array $input [article_id, comment_id, content]
     * @return Array
     */
    public function comment($input, $article_id)
    {
        $comment_id = isset($input['comment_id']) ? intval($input['comment_id']) : '';
        $content    = isset($input['content']) ? strval($input['content']) : '';
        if (!$article_id || !$content) {
            return $this->responseResult(false, [], '操作失败，参数错误，请刷新后重试');
        }
        $show_status_value = DictRepository::getInstance()->getValueByCodeAndTextEn('article_status', 'show');

        $list = Article::where('id', $article_id)->where('status', $show_status_value)->first();
        if (empty($list)) {
            return $this->responseResult(false, [], '操作失败，参数错误，请刷新后重试');
        }
        $dict_key_value_lists = DictRepository::getInstance()->getKeyValueByCode('audit');
        $comment_is_audit = DictRepository::getInstance()->getValueByCodeAndTextEn('system', 'article_comment_audit');

        // 表示回复
        if ($comment_id) {
            $commentList = ArticleComment::where('id', $comment_id)->where('status', 1)->where('is_audit', $dict_key_value_lists['pass'])->first();
            if (empty($commentList)) {
                return $this->responseResult(false, [], '操作失败，参数错误，请刷新后重试');
            }
        }
        $user_id      = Auth::guard('web')->id();
        $result['list'] = ArticleComment::create([
            'user_id'    => $user_id,
            'parent_id'  => $comment_id ? $comment_id : 0,
            'article_id' => $article_id,
            'content'    => $content,
            'is_audit'   => $comment_is_audit ? $dict_key_value_lists['loading'] : $dict_key_value_lists['pass'],
            'status'     => 1,
        ]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Article/comment',
            'params' => $input,
            'text'   => $comment_id ? '回复成功' : '评论成功',
        ]);
        return $this->responseResult(true, $result, $comment_id ? '回复成功' : '评论成功');
    }

    /**
     * 获取点赞 or 反对 or 收藏详情
     * @param  Array $input [type]
     * @param  int $article_id
     * @return Array
     */
    public function interactiveDetail($input, $article_id)
    {
        $articleList = Article::where('id', $article_id)->where('status', 1)->first();
        $type = isset($input['type']) ? strval($input['type']) : '';
        $type_text = !$type ? '' : ($type == 'like' ? '点赞' : ($type == 'hate' ? '反对' : ($type == 'collect' ? '收藏' : '')));
        if (!$article_id || !$type_text) {
            return $this->responseResult(false, [], '操作失败，参数错误，请刷新后重试');
        }

        $result['lists'] = Interact::where('article_id', $article_id)->where($type, 1)->where('status', 1)->with('user')->get();
        return $this->responseResult(true, $result);
    }

    /**
     * 推荐文章
     * @param  Array $input [type]
     * @return Array
     */
    public function recommendList($input)
    {
        $result = [];
        if (empty($input)) {
            $result['lists'] = Article::where('recommend', 1)->where('status', 1)->get();
        } else {
            $type = isset($input['type']) ? strval($input['type']) : '';
            switch ($type) {
                case 'hot':
                    $result['lists'] = $this->hotLists();
                    break;
                case 'most-like':
                    $result['lists'] = $this->mostLikeLists();
                    break;
                case 'most-collect':
                    $result['lists'] = $this->mostCollectLists();
                    break;
                case 'most-comment':
                    $result['lists'] = $this->mostCommentLists();
                    break;
                case 'most-read':
                    $result['lists'] = $this->mostReadLists();
                    break;
                default:
                    $result['lists'] = $this->hotLists();
            }
        }
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $result,
            'message' => '数据获取成功',
        ];
    }

    /**
     * 热门文章
     * 点赞 + 反对 + 收藏 最多
     * @return Obejct
     */
    public function hotLists()
    {
        $articleLists = Article::withCount('interactives', function ($query) {
            $query->where('status', 1);
        })->sortBy('interactives_count')->paginate();
        return $articleLists;
    }

    /**
     * 热最多人点赞文章
     * @return Obejct
     */
    public function mostLikeLists()
    {
        $articleLists = Article::withCount('interactives', function ($query) {
            $query->where('like', 1)->where('status', 1);
        })->sortBy('interactives_count')->paginate();
        return $articleLists;
    }

    /**
     * 最多人收藏文章
     * @return Obejct
     */
    public function mostCollectLists()
    {
        $articleLists = Article::withCount('interactives', function ($query) {
            $query->where('collect', 1)->where('status', 1);
        })->sortBy('interactives_count')->paginate();
        return $articleLists;
    }

    /**
     * 最多人评论文章
     * @return Obejct
     */
    public function mostCommentLists()
    {
        $articleLists = Article::withCount('comments', function ($query) {
            $query->where('status', 1);
        })->sortBy('comments_count')->paginate();
        return $articleLists;
    }

    /**
     * 最多人浏览文章
     * @return Obejct
     */
    public function mostReadLists()
    {
        $articleLists = Article::withCount('reads', function ($query) {
            $query->where('status', 1);
        })->sortBy('reads_count')->paginate();
        return $articleLists;
    }

    /**
     * 获取互动文章
     * @param  Array $input []
     * @return [type]        [description]
     */
    public function interactiveLists($input)
    {
        $interactive_type_arr = isset($input['interactive_type']) ? explode(',', strval($input['interactive_type'])) : [];
        if (empty($interactive_type_arr)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '参数错误，请联系管理员',
            ];
        }
        $user_id = Auth::guard('web')->id();
        $query   = Interact::where('user_id', $user_id)->where('status', 1);

        if (isset($interactive_type_arr['like'])) {
            $query = $query->orwhere('like', 1);
        }

        if (isset($interactive_type_arr['hate'])) {
            $query = $query->orWhere('hate', 1);
        }

        if (isset($interactive_type_arr['collect'])) {
            $query = $query->orWhere('collect', 1);
        }

        $result['lists'] = $query->paginate();

        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $result,
            'message' => '数据获取成功',
        ];
    }

    /**
     * 获取文章数据
     * @param  Array $search_form [所有可搜索字段]
     * @return Object
     */
    public function getArticleLists($search_form)
    {
        $show_status_value          = DictRepository::getInstance()->getValueByCodeAndTextEn('article_status', 'show');
        $search_form['status'] = $show_status_value;
        $where_params          = $this->parseParams('articles', $search_form);
        return Article::parseWheres($where_params)->with('comment')->with('read')->with('interactive')->paginate();
    }
}
