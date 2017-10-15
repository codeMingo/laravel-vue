<?php
namespace App\Repositories\Frontend;

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ArticleInteract;
use App\Models\ArticleRead;
use App\Repositories\Frontend\CategoryRepository;
use App\Repositories\Frontend\DictRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ArticleRepository extends BaseRepository
{

    /**
     * 文章列表
     * @param  Array $input [search_form]
     * @return Array
     */
    public function lists($input)
    {
        $resultData['lists']                 = $this->getArticleLists($input['search_form']);
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
        $article_show_status_value = DB::table('dicts')->where('code', 'article_status')->where('text_en', 'article_is_show')->value('value');
        $resultData['data'] = Article::find($article_id)->with('comments', 'comments.user')->where('status', $article_show_status_value)->first();
        if (empty($resultData['data'])) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }
        $resultData['data']['like_count'] = ArticleInteract::where('article_id', $article_id)->where('like', 1)->count();
        $resultData['data']['hate_count'] = ArticleInteract::where('article_id', $article_id)->where('hate', 1)->count();
        $resultData['data']['read_count'] = ArticleRead::where('article_id', $article_id)->count();
        // 上一篇文章
        $resultData['prev_data'] = Article::where('id', '<', $article_id)->where('status', 1)->orderBy('id', 'desc')->first();
        if (!empty($resultData['prev_data'])) {
            $resultData['prev_data']['like_count'] = ArticleInteract::where('article_id', $resultData['prev_data']->id)->where('like', 1)->count();
            $resultData['prev_data']['hate_count'] = ArticleInteract::where('article_id', $resultData['prev_data']->id)->where('hate', 1)->count();
            $resultData['prev_data']['read_count'] = ArticleRead::where('article_id', $resultData['prev_data']->id)->count();
        }
        // 下一篇文章
        $resultData['next_data'] = Article::where('id', '>', $article_id)->where('status', 1)->orderBy('id', 'asc')->first();
        if (!empty($resultData['next_data'])) {
            $resultData['next_data']['like_count'] = ArticleInteract::where('article_id', $resultData['next_data']->id)->where('like', 1)->count();
            $resultData['next_data']['hate_count'] = ArticleInteract::where('article_id', $resultData['next_data']->id)->where('hate', 1)->count();
            $resultData['next_data']['read_count'] = ArticleRead::where('article_id', $resultData['next_data']->id)->count();
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
        $dataList = ArticleInteract::where('article_id', $article_id)->where($type, 1)->first();
        if (empty($dataList)) {
            $user_id = Auth::guard('web')->id();
            $result  = ArticleInteract::create([
                'user_id'    => $user_id,
                'article_id' => $article_id,
                $type        => 1,
            ]);
        } else {
            $result = ArticleInteract::where('article_id', $article_id)->update($type, 0);
        }
        if (!$result) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '操作失败',
            ];
        }
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => [],
            'message' => '操作成功',
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
        $articleList = Article::where('id', $article_id)->where('status', $article_show_status_value)->first();
        if (empty($articleList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这篇文章',
            ];
        }
        $article_comment_audit = DB::table('dicts')->where('code', 'system')->where('text_en', 'article_comment_audit')->value('value');
        $dictListsValue = DictRepository::getInstance()->getDictListsByTextEnArr(['audit_loading', 'audit_pass']);

        // 表示回复
        if ($comment_id) {
            $commentList = ArticleComment::where('id', $comment_id)->where('status', 1)->where('is_audit', $dictListsValue['audit_pass'])->first();
            if (empty($commentList)) {
                return [
                    'status'  => Parent::ERROR_STATUS,
                    'data'    => [],
                    'message' => '未知错误，comment_id is null',
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

        if (!$createResult) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => $comment_id ? '回复失败，未知错误' : '评论失败，未知错误',
            ];
        }
        // 评论成功

        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => [
                'data' => [
                    'comment_id' => $createResult->parent_id,
                    'content'    => $createResult->content,
                    'create_at'  => $createResult->create_at,
                ],
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
        $resultData['lists'] = ArticleInteract::where('article_id', $article_id)->where($type, 1)->where('status', 1)->user()->get();
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => $resultData,
            'message' => '数据获取成功',
        ];
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
        $query   = ArticleInteract::where('user_id', $user_id)->where('status', 1);

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
        $dictKeyValue = DictRepository::getInstance()->getDictListsByTextEnArr(['article_is_show', 'article_page_size']);

        $where_params['status'] = $dictKeyValue['article_is_show'];
        $page_size              = $dictKeyValue['article_page_size'];

        if (empty($search_form)) {
            return Article::where($where_params)->paginate($page_size);
        }

        if (isset($search_form['status'])) {
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
