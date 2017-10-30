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

    public $table_name = 'articles';

    /**
     * 文章列表
     * @param  Array $input [search_form]
     * @return Array
     */
    public function lists($input)
    {
        $resultData['lists'] = $this->getArticleLists($input['search_form']);
        if (!empty($resultData['lists'])) {
            $article_id_arr = [];
            foreach ($resultData['lists']['data'] as $index => $article) {
                $article_id_arr[] = $article['id'];
            }

            // 获取 like hate collect 各自的总数
            $interactive_key_count = $this->interactiveCount(['like', 'hate', 'collect'], $article_id_arr);

            $comment_key_value_lists = $read_key_value_lists = [];
            // 获取评论总数
            $comment_lists = ArticleComment::whereIn('article_id', $article_id_arr)->get();
            if (!empty($comment_lists)) {
                foreach ($comment_lists as $index => $comment) {
                    if (isset($comment_key_value_lists[$comment->article_id])) {
                        $comment_key_value_lists[$comment->article_id]++;
                    } else {
                        $comment_key_value_lists[$comment->article_id] = 1;
                    }
                }
            }

            // 获取阅读总数
            $read_count_lists = $this->readCount($article_id_arr);

            foreach ($resultData['lists']['data'] as $index => $article) {
                $article['like_count']               = isset($interactive_key_count[$article['id']]) && isset($interactive_key_count[$article['id']]['like_count']) ? $interactive_key_count[$article['id']]['like_count'] : 0;
                $article['hate_count']               = isset($interactive_key_count[$article['id']]) && isset($interactive_key_count[$article['id']]['hate_count']) ? $interactive_key_count[$article['id']]['hate_count'] : 0;
                $article['collect_count']            = isset($interactive_key_count[$article['id']]) && isset($interactive_key_count[$article['id']]['collect_count']) ? $interactive_key_count[$article['id']]['collect_count'] : 0;
                $article['comment_count']            = isset($comment_key_value_lists[$article['id']]) ? $comment_key_value_lists[$article['id']] : 0;
                $article['read_count']               = isset($read_count_lists[$article['id']]) ? $read_count_lists[$article['id']] : 0;
                $resultData['lists']['data'][$index] = $article;
            }
        }
        $resultData['options']['categories'] = CategoryRepository::getInstance()->getListsByDictText('article_category');
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '数据获取成功',
        ];
    }

    /**
     * 文章详情
     * @param  int $article_id
     * @return Array
     */
    public function detail($article_id)
    {
        $dictListsValue     = DictRepository::getInstance()->getDictListsByTextEnArr(['article_is_show', 'audit_pass']);
        $resultData['list'] = Article::where('id', $article_id)->where('status', $dictListsValue['article_is_show'])->with('category')->first();
        if (empty($resultData['list'])) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }

        // 文章阅读量 +1
        $user_id = Auth::guard('web')->id();
        ArticleRead::create([
            'user_id'    => !empty($user_id) ? $user_id : 0,
            'article_id' => $article_id,
            'ip_address' => getClientIp(),
        ]);

        // 上一篇文章
        $prev_article = Article::where('id', '<', $article_id)->where('status', $dictListsValue['article_is_show'])->orderBy('id', 'desc')->first();
        // 下一篇文章
        $next_article = Article::where('id', '>', $article_id)->where('status', $dictListsValue['article_is_show'])->orderBy('id', 'asc')->first();

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
            $resultData['list']->like_count = isset($interactive_count_lists[$article_id]['like_count']) ? $interactive_count_lists[$article_id]['like_count'] : 0;
            $resultData['list']->hate_count = isset($interactive_count_lists[$article_id]['hate_count']) ? $interactive_count_lists[$article_id]['hate_count'] : 0;
        }
        $resultData['list']->read_count = (!empty($read_count_lists) && isset($read_count_lists[$article_id])) ? $read_count_lists[$article_id] : 0;

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

        $resultData['prev_article'] = !empty($prev_article) ? $prev_article : '';
        $resultData['next_article'] = !empty($next_article) ? $next_article : '';

        // 文章标签
        if (!empty($resultData['list']->tag_ids)) {
            $tag_id_arr   = explode(',', $resultData['list']->tag_ids);
            $tag_lists    = DB::table('tags')->whereIn('id', $tag_id_arr)->where('status', 1)->get();
            $temp_tag_arr = [];
            if (!empty($tag_lists)) {
                foreach ($tag_lists as $key => $item) {
                    $temp_tag_arr[] = [
                        'id'       => $item->id,
                        'tag_name' => $item->tag_name,
                    ];
                }
                $resultData['list']->tag_lists = $temp_tag_arr;
            }
        }

        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '数据获取成功',
        ];
    }

    /**
     * 获取文章评论列表
     * @param  int $article_id 文章id
     * @return Object
     */
    public function commentLists($article_id)
    {
        $dictListsValue      = DictRepository::getInstance()->getDictListsByTextEnArr(['article_is_show', 'audit_pass']);
        $resultData['lists'] = ArticleComment::where('article_id', $article_id)->where('is_audit', $dictListsValue['audit_pass'])->where('status', 1)->where('parent_id', 0)->with('user')->paginate(10);
        if ($resultData['lists']->isEmpty()) {
            return [
                'status'  => Parent::SUCCESS_STATUS,
                'data'    => $resultData,
                'message' => '数据获取成功',
            ];
        }
        $comment_ids = [];
        foreach ($resultData['lists'] as $index => $comment) {
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

                foreach ($resultData['lists'] as $index => $comment) {
                    $resultData['lists'][$index]['response'] = isset($response_temp[$comment->id]) ? $response_temp[$comment->id] : [];
                }
            }
        }
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '数据获取成功',
        ];
    }

    /**
     * 点赞 or 反对 or 收藏
     * @param  Array $input [article_id, type]
     * @return Array
     */
    public function interactive($input, $article_id)
    {
        $type = isset($input['type']) ? strval($input['type']) : '';
        if (!$article_id || !$type) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '操作失败，发生未知错误',
            ];
        }

        $dictListsValue = DictRepository::getInstance()->getDictListsByTextEnArr(['article_is_show', 'audit_pass']);
        $articleList    = Article::where('id', $article_id)->where('status', $dictListsValue['article_is_show'])->first();
        if (empty($articleList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '操作失败，不存在这篇文章',
            ];
        }
        $user_id  = Auth::guard('web')->id();
        $dataList = Interact::where('article_id', $article_id)->where('user_id', $user_id)->where($type, 1)->first();
        if (!empty($dataList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '操作失败，不可重复操作',
            ];
        }
        $result = Interact::create([
            'user_id'    => $user_id,
            'article_id' => $article_id,
            $type        => 1,
        ]);

        // 记录操作日志
        Parent::saveUserOperateRecord([
            'action' => 'Article/interactive',
            'params' => $input,
            'text'   => $result ? '操作成功' : '操作失败',
            'status' => !!$result,
        ]);

        return [
            'status'  => $result ? Parent::SUCCESS_STATUS : Parent::ERROR_STATUS,
            'data'    => [],
            'message' => $result ? '操作成功' : '操作失败，发生未知错误',
        ];
    }

    /**
     * 评论 or 回复
     * @param  Array $input [article_id, comment_id, content]
     * @return Array
     */
    public function comment($input, int $article_id)
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
        $article_show_status_value = DB::table('dicts')->where('code', 'article_status')->where('text_en', 'article_is_show')->value('value');
        $articleList               = Article::where('id', $article_id)->where('status', $article_show_status_value)->first();
        if (empty($articleList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }
        $article_comment_audit = DB::table('dicts')->where('code', 'system')->where('text_en', 'article_comment_audit')->value('value');
        $dictListsValue        = DictRepository::getInstance()->getDictListsByTextEnArr(['audit_loading', 'audit_pass']);

        // 表示回复
        if ($comment_id) {
            $commentList = ArticleComment::where('id', $comment_id)->where('status', 1)->where('is_audit', $dictListsValue['audit_pass'])->first();
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
            'is_audit'   => $article_comment_audit ? $dictListsValue['audit_loading'] : $dictListsValue['audit_pass'],
            'status'     => 1,
        ]);

        // 记录操作日志
        Parent::saveUserOperateRecord([
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
        if (!$article_comment_audit) {
            $comment_lists                = ArticleComment::where('id', $createResult->id)->with('user')->first();
            $comment_lists->response      = [];
            $comment_lists->show_response = true;
        }
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => [
                'list' => $article_comment_audit ? [] : $comment_lists,
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
        $resultData['lists'] = Interact::where('article_id', $article_id)->where($type, 1)->where('status', 1)->user()->get();
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
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
        $dictListsValue = DictRepository::getInstance()->getDictListsByTextEnArr(['audit_pass']);
        $query          = ArticleComment::select(['article_id'])->where('is_audit', $dictListsValue['audit_pass'])->where('status', 1)->where('parent_id', 0);
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
        $resultData = [];
        if (empty($input)) {
            $resultData['lists'] = Article::where('recommend', 1)->where('status', 1)->get();
        } else {
            $type = isset($input['type']) ? strval($input['type']) : '';
            switch ($type) {
                case 'hot':
                    $resultData['lists'] = $this->hotLists();
                    break;
                case 'most-like':
                    $resultData['lists'] = $this->mostLikeLists();
                    break;
                case 'most-collect':
                    $resultData['lists'] = $this->mostCollectLists();
                    break;
                case 'most-comment':
                    $resultData['lists'] = $this->mostCommentLists();
                    break;
                case 'most-read':
                    $resultData['lists'] = $this->mostReadLists();
                    break;
                default:
                    $resultData['lists'] = $this->hotLists();
            }
        }
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
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
        $resultData['lists'] = $query->paginate($page_size);

        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
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
        $dictKeyValue          = DictRepository::getInstance()->getDictListsByTextEnArr(['article_is_show', 'article_page_size']);
        $search_form['status'] = $dictKeyValue['article_is_show'];
        $where_params          = $this->parseParams($search_form);
        $page_size             = $dictKeyValue['article_page_size'];
        return Article::parseWheres($where_params)->paginate($page_size)->toArray();
    }

    /**
     * 获取菜单列表
     * @return Object
     */
    public function categoryLists()
    {
        $resultData['lists'] = CategoryRepository::getInstance()->getListsByDictText('article_category');
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '数据获取成功',
        ];
    }
}
