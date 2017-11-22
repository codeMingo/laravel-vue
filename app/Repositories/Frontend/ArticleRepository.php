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
        $audit_pass_value = DictRepository::getInstance()->getValueByCodeAndTextEn('audit', 'pass');
        $show_status_value = DictRepository::getInstance()->getValueByCodeAndTextEn('article_status', 'show');
        $result['list'] = Article::where('id', $article_id)->where('status', $show_status_value)->where('is_audit', $audit_pass_value)->with('category')->first();
        if (empty($result['list'])) {
            return $this->responseResult(false, $result, '获取失败，文章不存在或已被删除');
        }

        // 文章阅读量 +1
        $user_id = Auth::guard('web')->id();
        ArticleRead::create([
            'user_id'    => !empty($user_id) ? $user_id : 0,
            'article_id' => $article_id,
            'ip_address' => getClientIp(),
        ]);

        // 上一篇文章
        $prev_article = Article::where('id', '<', $article_id)->where('status', $show_status_value)->orderBy('id', 'desc')->first();
        // 下一篇文章
        $next_article = Article::where('id', '>', $article_id)->where('status', $show_status_value)->orderBy('id', 'asc')->first();

        $article_id_arr = [];
        if (!empty($prev_article)) {
            $article_id_arr[] = $prev_article->id;
        }
        if (!empty($next_article)) {
            $article_id_arr[] = $next_article->id;
        }
        $article_id_arr[] = $article_id;
        // 获取各自互动总数
        $interactive_count_lists = $this->interactiveCount(['like', 'hate', 'collect'], $article_id_arr);
        // 获取各自阅读总数
        $read_count_lists = $this->readCount($article_id_arr);
        // 获取各自评论总数
        $comment_count_lists = $this->commentCount($article_id_arr);

        // 该篇文章的 hate like collect read 总数
        if (isset($interactive_count_lists[$article_id])) {
            $result['list']->like_count = isset($interactive_count_lists[$article_id]['like_count']) ? $interactive_count_lists[$article_id]['like_count'] : 0;
            $result['list']->hate_count = isset($interactive_count_lists[$article_id]['hate_count']) ? $interactive_count_lists[$article_id]['hate_count'] : 0;
        }
        $result['list']->read_count = (!empty($read_count_lists) && isset($read_count_lists[$article_id])) ? $read_count_lists[$article_id] : 0;

        if (!empty($prev_article)) {
            if (isset($interactive_count_lists[$prev_article->id])) {
                $prev_article->like_count = isset($interactive_count_lists[$prev_article->id]['like_count']) ? $interactive_count_lists[$prev_article->id]['like_count'] : 0;
                $prev_article->hate_count = isset($interactive_count_lists[$prev_article->id]['hate_count']) ? $interactive_count_lists[$prev_article->id]['hate_count'] : 0;
            }
            $prev_article->read_count    = (!empty($read_count_lists) && isset($read_count_lists[$prev_article->id])) ? $read_count_lists[$prev_article->id] : 0;
            $prev_article->comment_count = (!empty($comment_count_lists) && isset($comment_count_lists[$prev_article->id])) ? $comment_count_lists[$prev_article->id] : 0;
        }

        if (!empty($next_article)) {
            if (isset($interactive_count_lists[$next_article->id])) {
                $next_article->like_count = isset($interactive_count_lists[$next_article->id]['like_count']) ? $interactive_count_lists[$next_article->id]['like_count'] : 0;
                $next_article->hate_count = isset($interactive_count_lists[$next_article->id]['hate_count']) ? $interactive_count_lists[$next_article->id]['hate_count'] : 0;
            }
            $next_article->read_count    = (!empty($read_count_lists) && isset($read_count_lists[$next_article->id])) ? $read_count_lists[$next_article->id] : 0;
            $next_article->comment_count = (!empty($comment_count_lists) && isset($comment_count_lists[$next_article->id])) ? $comment_count_lists[$next_article->id] : 0;
        }

        $result['prev_article'] = !empty($prev_article) ? $prev_article : '';
        $result['next_article'] = !empty($next_article) ? $next_article : '';

        // 文章标签
        if (!empty($result['list']->tag_ids)) {
            $tag_id_arr   = explode(',', $result['list']->tag_ids);
            $tag_lists    = DB::table('tags')->whereIn('id', $tag_id_arr)->where('status', 1)->get();
            $temp_tag_arr = [];
            if (!empty($tag_lists)) {
                foreach ($tag_lists as $key => $item) {
                    $temp_tag_arr[] = [
                        'id'       => $item->id,
                        'tag_name' => $item->tag_name,
                    ];
                }
                $result['list']->tag_lists = $temp_tag_arr;
            }
        }
        return $this->responseResult(true, $result);
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
        $type_text = '';
        swtich($type) {
            case 'like':
                $type_text = '点赞';
            break;
            case 'hate':
                $type_text = '反对';
            break;
            case 'collect':
                $type_text = '收藏';
            break;
        }
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
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '操作失败，文章id或内容为空',
            ];
        }
        $show_status_value = DictRepository::getInstance()->getValueByCodeAndTextEn('article_status', 'show');

        $list               = Article::where('id', $article_id)->where('status', $show_status_value)->first();
        if (empty($list)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }
        $dict_key_value_lists        = DictRepository::getInstance()->getKeyValueByCode('audit');
        $comment_is_audit = DictRepository::getInstance()->getValueByCodeAndTextEn('system', 'article_comment_audit');

        // 表示回复
        if ($comment_id) {
            $commentList = ArticleComment::where('id', $comment_id)->where('status', 1)->where('is_audit', $dict_key_value_lists['pass'])->first();
            if (empty($commentList)) {
                return [
                    'status'  => Parent::ERROR_STATUS,
                    'data'    => [],
                    'message' => '回复失败，comment_id is null',
                ];
            }
        }
        $user_id      = Auth::guard('web')->id();
        $createResult = ArticleComment::create([
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
            'text'   => $createResult ? ($comment_id ? '回复成功' : '评论成功') : ($comment_id ? '回复失败，未知错误' : '评论失败，未知错误'),
            'status' => !!$createResult,
        ]);

        if (!$createResult) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => $comment_id ? '回复失败，未知错误' : '评论失败，未知错误',
            ];
        }
        // 评论成功
        if (!$comment_is_audit) {
            $comment_lists                = ArticleComment::where('id', $createResult->id)->with('user')->first();
            $comment_lists->response      = [];
            $comment_lists->show_response = true;
        }
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => [
                'list' => $comment_is_audit ? [] : $comment_lists,
            ],
            'message' => $comment_id ? '回复成功' : '评论成功',
        ];
    }

    /**
     * 获取点赞 or 反对 or 收藏详情
     * @param  Array $input [type]
     * @param  int $article_id
     * @return Array
     */
    public function interactiveDetail($input, $article_id)
    {
        $type = isset($input['type']) ? strval($input['type']) : '';
        if (!$article_id || !$type) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '发生未知错误',
            ];
        }
        $articleList = Article::where('id', $article_id)->where('status', 1)->first();
        if (empty($articleList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }
        $result['lists'] = Interact::where('article_id', $article_id)->where($type, 1)->where('status', 1)->user()->get();
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $result,
            'message' => '数据获取成功',
        ];
    }

    /**
     * 获取 like hate collect 分别的总数
     * @param  Array $type_arr       [like, hate, collect] 获取类别
     * @param  Array OR String $article_ids 文章id，数组或字符串
     * @return Array                 [article_id => [like => ,hate=> ,collect=>]]
     */
    public function interactiveCount($type_arr, $article_ids)
    {
        $result     = [];
        $type_arr[] = 'article_id';
        $query      = Interact::select($type_arr);
        if (is_array($article_ids)) {
            $query = $query->whereIn('article_id', $article_ids);
        } else {
            $query = $query->where('article_id', $article_ids);
        }
        $count_lists = $query->get();
        if (empty($count_lists)) {
            return $result;
        }
        foreach ($count_lists as $index => $item) {
            foreach ($type_arr as $type) {
                if ($item->$type) {
                    if (!isset($result[$item->article_id])) {
                        $result[$item->article_id] = [];
                    }
                    if (isset($result[$item->article_id][$type . '_count'])) {
                        $result[$item->article_id][$type . '_count'] += 1;
                    } else {
                        $result[$item->article_id][$type . '_count'] = 1;
                    }
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * 文章阅读总数
     * @param  Array OR String $article_ids 文章的id，可传数组或字符串
     * @return Array              [[article_id => count], [], []]
     */
    public function readCount($article_ids)
    {
        $result = [];
        $query  = ArticleRead::select(['article_id']);
        if (is_array($article_ids)) {
            $query = $query->whereIn('article_id', $article_ids);
        } else {
            $query = $query->where('article_id', $article_ids);
        }
        $count_lists = $query->get();
        if (empty($count_lists)) {
            return $result;
        }
        foreach ($count_lists as $index => $item) {
            if (isset($result[$item->article_id])) {
                $result[$item->article_id] += 1;
            } else {
                $result[$item->article_id] = 1;
            }
        }
        return $result;
    }

    /**
     * 文章评论总数
     * @param  Array OR String $article_ids 文章的id，可传数组或字符串
     * @return Array              [[article_id => count], [], []]
     */
    public function commentCount($article_ids)
    {
        $result         = [];
        $audit_pass_value = DictRepository::getInstance()->getValueByCodeAndTextEn('audit', 'pass');
        $show_status_value = DictRepository::getInstance()->getValueByCodeAndTextEn('article_status', 'show');
        $query          = ArticleComment::select(['article_id'])->where('is_audit', $audit_pass_value)->where('status', 1)->where('parent_id', 0);
        if (is_array($article_ids)) {
            $query = $query->whereIn('article_id', $article_ids);
        } else {
            $query = $query->where('article_id', $article_ids);
        }
        $count_lists = $query->get();
        if (empty($count_lists)) {
            return $result;
        }
        foreach ($count_lists as $index => $item) {
            if (isset($result[$item->article_id])) {
                $result[$item->article_id] += 1;
            } else {
                $result[$item->article_id] = 1;
            }
        }
        return $result;
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
    public function hotLists($page_size)
    {
        $articleLists = Article::withCount('interactives', function ($query) {
            $query->where('status', 1);
        })->sortBy('interactives_count')->paginate($page_size);
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
        })->sortBy('interactives_count')->paginate($page_size);
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
        })->sortBy('interactives_count')->paginate($page_size);
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
        })->sortBy('comments_count')->paginate($page_size);
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
        })->sortBy('reads_count')->paginate($page_size);
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

        $page_size           = DB::table('dicts')->where('text_en', 'article_page_size')->value('value');
        $result['lists'] = $query->paginate($page_size);

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

    /**
     * 获取菜单列表
     * @return Object
     */
    public function categoryLists()
    {
        $result['lists'] = CategoryRepository::getInstance()->getArticleCategories();
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $result,
            'message' => '数据获取成功',
        ];
    }
}
