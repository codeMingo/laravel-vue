<?php
namespace App\Repositories\Frontend;

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ArticleRead;
use App\Models\Interact;
use App\Repositories\Frontend\CategoryRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArticleRepository extends CommonRepository
{

    public $categoryRepository;

    public function __construct(Article $article, CategoryRepository $categoryRepository)
    {
        parent::__construct($article);
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * 文章列表
     * @param  Array $input [search]
     * @return Array
     */
    public function lists($input)
    {
        $search                        = isset($input['search']) ? (array) $input['search'] : [];
        $result['lists']               = $this->getArticleLists($search);
        $result['options']['category'] = $this->categoryRepository->getCategoryLists(['type' => 'article']);

        return responseResult(true, $result);
    }

    /**
     * 文章详情
     * @param  int $id 文章id
     * @return Array
     */
    public function detail($id)
    {
        $dicts          = $this->getRedisDictLists(['audit' => ['pass'], 'article_status' => ['show']]);
        $result['list'] = $this->getDetail($id, [
            'status'             => $dicts['article_status']['show'],
            'is_audit'           => $dicts['audit']['pass'],
            '__not_select__'     => ['admin_id', 'user_id', 'deleted_at', 'updated_at', 'is_audit', 'recommend'],
            '__relation_table__' => ['interact', 'category'],
        ]);
        if (empty($result['list'])) {
            return responseResult(false, $result, '获取失败，文章不存在或已被删除');
        }

        // 文章阅读量 +1
        $this->read($id);
        // 获取上一篇文章
        $result['prev_article'] = $this->getPrevlist($id);
        // 获取下一篇文章
        $result['next_article'] = $this->getNextlist($id);

        // 文章标签
        if (!empty($result['list']->tag_ids)) {
            $tag_id_arr                = explode(',', $result['list']->tag_ids);
            $result['list']->tag_lists = DB::table('tags')->whereIn('id', $tag_id_arr)->where('status', 1)->get();
        }
        return responseResult(true, $result);
    }

    // 阅读数 + 1
    public function read($id)
    {
        ArticleRead::create([
            'user_id'    => $this->getCurrentId(),
            'article_id' => $id,
            'ip_address' => getClientIp(),
        ]);
        return true;
    }

    // 获取上一篇文章
    public function getPrevlist($id)
    {
        $dicts = $this->getRedisDictLists(['audit' => ['pass'], 'article_status' => ['show']]);
        return $this->getDetail([

        ]);
        return $this->model->where('status', $dicts['article_status']['show'])->where('is_audit', $dicts['audit']['pass'])->where('id', '<', $id)->with('read')->with('interact')->with('comment')->orderBy('id', 'desc')->first();
    }

    // 获取下一篇文章
    public function getNextlist($id)
    {
        $dicts = $this->getRedisDictLists(['audit' => ['pass'], 'article_status' => ['show']]);
        return $this->model->where('status', $dicts['article_status']['show'])->where('is_audit', $dicts['audit']['pass'])->where('id', '>', $id)->with('read')->with('interact')->with('comment')->orderBy('id', 'asc')->first();
    }

    /**
     * 获取文章评论列表
     * @param  int $id 文章id
     * @return Object
     */
    public function commentLists($id)
    {
        $dicts = $this->getRedisDictLists(['audit' => ['pass'], 'article_status' => ['show']]);
        $list  = $this->model->where('id', $id)->where('is_audit', $dicts['audit']['pass'])->where('status', $dicts['article_status']['show'])->first();
        if (empty($list)) {
            return responseResult(false, [], $type_text . '失败，文章不存在或已被删除');
        }

        $result['lists'] = ArticleComment::where('article_id', $id)->where('is_audit', $dicts['audit']['pass'])->where('status', 1)->where('parent_id', 0)->with('user')->paginate();
        if ($result['lists']->isEmpty()) {
            return responseResult(true, $result);
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
        return responseResult(true, $result);
    }

    /**
     * 点赞 or 反对 or 收藏
     * @param  Array $input [id, type]
     * @return Array
     */
    public function interactive($input, $id)
    {
        $type      = isset($input['type']) ? strval($input['type']) : '';
        $type_text = !$type ? '' : ($type == 'like' ? '点赞' : ($type == 'hate' ? '反对' : ($type == 'collect' ? '收藏' : '')));
        if (!$id || !$type || !$type_text) {
            return responseResult(false, [], $type_text . '失败，发生未知错误');
        }

        $dicts = $this->getRedisDictLists(['audit' => ['pass'], 'article_status' => ['show']]);

        $list = $this->model->where('id', $id)->where('is_audit', $dicts['audit']['pass'])->where('status', $dicts['article_status']['show'])->first();
        if (empty($list)) {
            return responseResult(false, [], $type_text . '失败，文章不存在或已被删除');
        }
        $user_id  = $this->getCurrentId();
        $dataList = Interact::where('article_id', $id)->where('user_id', $user_id)->where($type, 1)->first();
        if (!empty($dataList)) {
            return responseResult(false, [], $type_text . '失败，您已经操作过了');
        }
        $result = Interact::create([
            'user_id'    => $user_id,
            'article_id' => $id,
            $type        => 1,
        ]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Article/interactive',
            'params' => [
                'input' => $input,
            ],
            'text'   => $type_text . '成功',
        ]);
        return responseResult(true, $result, $type_text . '成功');
    }

    /**
     * 评论 or 回复
     * @param  Array $input [id, comment_id, content]
     * @return Array
     */
    public function comment($input, $id)
    {
        $comment_id = isset($input['comment_id']) ? intval($input['comment_id']) : '';
        $content    = isset($input['content']) ? strval($input['content']) : '';
        if (!$id || !$content) {
            return responseResult(false, [], '操作失败，参数错误，请刷新后重试');
        }
        $dicts = $this->getRedisDictLists([
            'audit'          => ['loading', 'pass'],
            'article_status' => ['show'],
            'system'         => ['article_comment_audit'],
        ]);

        $list = $this->model->where('id', $id)->where('status', $dicts['article_status']['show'])->first();
        if (empty($list)) {
            return responseResult(false, [], '操作失败，参数错误，请刷新后重试');
        }

        // 表示回复
        if ($comment_id) {
            $comment_list = ArticleComment::where('id', $comment_id)->where('status', 1)->where('is_audit', $dicts['audit']['pass'])->first();
            if (empty($comment_list)) {
                return responseResult(false, [], '操作失败，参数错误，请刷新后重试');
            }
        }
        $user_id        = Auth::guard('web')->id();
        $result['list'] = ArticleComment::create([
            'user_id'    => $user_id,
            'parent_id'  => $comment_id ? $comment_id : 0,
            'article_id' => $id,
            'content'    => $content,
            'is_audit'   => $dicts['system']['article_comment_audit'] ? $dicts['audit']['loading'] : $dicts['audit']['pass'],
            'status'     => 1,
        ]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Article/comment',
            'params' => $input,
            'text'   => $comment_id ? '回复成功' : '评论成功',
        ]);
        $result['list']['response'] = [];
        $result['list']['user']     = DB::table('users')->where('id', $this->getCurrentId())->first();

        return responseResult(true, $result, $comment_id ? '回复成功' : '评论成功');
    }

    /**
     * 获取点赞 or 反对 or 收藏详情
     * @param  Array $input [type]
     * @param  int $id 文章id
     * @return Array
     */
    public function interactiveDetail($input, $id)
    {
        $list      = $this->model->where('id', $id)->where('status', 1)->first();
        $type      = isset($input['type']) ? strval($input['type']) : '';
        $type_text = !$type ? '' : ($type == 'like' ? '点赞' : ($type == 'hate' ? '反对' : ($type == 'collect' ? '收藏' : '')));
        if (!$id || !$type_text) {
            return responseResult(false, [], '操作失败，参数错误，请刷新后重试');
        }

        $result['lists'] = Interact::where('article_id', $id)->where($type, 1)->where('status', 1)->with('user')->get();

        return responseResult(true, $result);
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
            $result['lists'] = $this->model->where('recommend', 1)->where('status', 1)->get();
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
                    $result['lists'] = $this->mostcomment_lists();
                    break;
                case 'most-read':
                    $result['lists'] = $this->mostReadLists();
                    break;
                default:
                    $result['lists'] = $this->hotLists();
            }
        }
        return responseResult(true, $result);
    }

    /**
     * 热门文章
     * 点赞 + 反对 + 收藏 最多
     * @return Obejct
     */
    public function hotLists()
    {
        $lists = $this->model->withCount('interactives', function ($query) {
            $query->where('status', 1);
        })->sortBy('interactives_count')->paginate();
        return $lists;
    }

    /**
     * 热最多人点赞文章
     * @return Obejct
     */
    public function mostLikeLists()
    {
        $lists = $this->model->withCount('interactives', function ($query) {
            $query->where('like', 1)->where('status', 1);
        })->sortBy('interactives_count')->paginate();
        return $lists;
    }

    /**
     * 最多人收藏文章
     * @return Obejct
     */
    public function mostCollectLists()
    {
        $lists = $this->model->withCount('interactives', function ($query) {
            $query->where('collect', 1)->where('status', 1);
        })->sortBy('interactives_count')->paginate();
        return $lists;
    }

    /**
     * 最多人评论文章
     * @return Obejct
     */
    public function mostcomment_lists()
    {
        $lists = $this->model->withCount('comments', function ($query) {
            $query->where('status', 1);
        })->sortBy('comments_count')->paginate();
        return $lists;
    }

    /**
     * 最多人浏览文章
     * @return Obejct
     */
    public function mostReadLists()
    {
        $lists = $this->model->withCount('reads', function ($query) {
            $query->where('status', 1);
        })->sortBy('reads_count')->paginate();
        return $lists;
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
            return responseResult(false, [], '获取数据失败，参数错误，请刷新后重试');
        }
        $query = Interact::where('user_id', $this->getCurrentId())->where('status', 1);

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

        return responseResult(true, $result);
    }

    /**
     * 获取文章数据
     * @param  Array $search [所有可搜索字段]
     * @return Object
     */
    public function getArticleLists($search)
    {
        $dicts                        = $this->getRedisDictLists(['audit' => ['pass'], 'article_status' => ['show']]);
        $search['status']             = $dicts['article_status']['show'];
        $search['is_audit']           = $dicts['audit']['pass'];
        $search['__not_select__']     = ['deleted_at', 'updated_at', 'tag_ids', 'source', 'is_audit', 'admin_id', 'user_id'];
        $search['__relation_table__'] = ['comment', 'read', 'interact'];
        return $this->getPaginateLists($search);
    }
}
