<?php
namespace Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('dicts')->truncate();
        $data = [
            // 系统参数
            ['code' => 'system', 'code_name' => '系统参数', 'value' => 20, 'text_en' => 'article_page_size', 'text' => '文章分页'],
            ['code' => 'system', 'code_name' => '系统参数', 'value' => 20, 'text_en' => 'video_page_size', 'text' => '视频分页'],
            ['code' => 'system', 'code_name' => '系统参数', 'value' => 30, 'text_en' => 'leave_page_size', 'text' => '留言分页'],
            ['code' => 'system', 'code_name' => '系统参数', 'value' => 5, 'text_en' => 'article_recommend_page_size', 'text' => '文章推荐展示数'],
            ['code' => 'system', 'code_name' => '系统参数', 'value' => 5, 'text_en' => 'video_recommend_page_size', 'text' => '视频推荐展示数'],
            ['code' => 'system', 'code_name' => '系统参数', 'value' => 5, 'text_en' => 'leave_recommend_page_size', 'text' => '留言推荐展示数'],
            ['code' => 'system', 'code_name' => '系统参数', 'value' => 0, 'text_en' => 'article_comment_audit', 'text' => '评论审核'],
            ['code' => 'system', 'code_name' => '系统参数', 'value' => 0, 'text_en' => 'leave_audit', 'text' => '留言审核'],
            ['code' => 'system', 'code_name' => '系统参数', 'value' => 10, 'text_en' => 'login_limit_time', 'text' => '登录限制时间，秒'],
            ['code' => 'system', 'code_name' => '系统参数', 'value' => 10, 'text_en' => 'backend_login_limit_time', 'text' => '后台登录限制时间，秒'],
            ['code' => 'system', 'code_name' => '系统参数', 'value' => 10, 'text_en' => 'repeat_limit_times', 'text' => '频繁操作次数'],
            ['code' => 'system', 'code_name' => '系统参数', 'value' => 10, 'text_en' => 'repeat_limit_time', 'text' => '频繁操作限制时间。秒'],
            // 性别
            ['code' => 'gender', 'code_name' => '性别', 'value' => 0, 'text_en' => 'unknown', 'text' => '未知'],
            ['code' => 'gender', 'code_name' => '性别', 'value' => 10, 'text_en' => 'male', 'text' => '男'],
            ['code' => 'gender', 'code_name' => '性别', 'value' => 20, 'text_en' => 'female', 'text' => '女'],
            // 菜单类型
            ['code' => 'category', 'code_name' => '菜单类型', 'value' => 10, 'text_en' => 'article', 'text' => '文章菜单'],
            ['code' => 'category', 'code_name' => '菜单类型', 'value' => 20, 'text_en' => 'video', 'text' => '视频菜单'],
            // 审核
            ['code' => 'audit', 'code_name' => '审核结果', 'value' => 0, 'text_en' => 'loading', 'text' => '审核中'],
            ['code' => 'audit', 'code_name' => '审核结果', 'value' => 10, 'text_en' => 'refuse', 'text' => '拒绝'],
            ['code' => 'audit', 'code_name' => '审核结果', 'value' => 20, 'text_en' => 'pass', 'text' => '通过'],
            // 邮件类型
            ['code' => 'email_type', 'code_name' => '邮件类型', 'value' => 10, 'text_en' => 'active', 'text' => '注册'],
            ['code' => 'email_type', 'code_name' => '邮件类型', 'value' => 20, 'text_en' => 'repassword', 'text' => '重置密码'],
            // 文章状态
            ['code' => 'article_status', 'code_name' => '文章状态', 'value' => 0, 'text_en' => 'not_show', 'text' => '未显示'],
            ['code' => 'article_status', 'code_name' => '文章状态', 'value' => -10, 'text_en' => 'freezing', 'text' => '冻结'],
            ['code' => 'article_status', 'code_name' => '文章状态', 'value' => 10, 'text_en' => 'audit_loading', 'text' => '审核中'],
            ['code' => 'article_status', 'code_name' => '文章状态', 'value' => 20, 'text_en' => 'show', 'text' => '正常'],
            // 视频状态
            ['code' => 'video_status', 'code_name' => '视频状态', 'value' => 0, 'text_en' => 'not_show', 'text' => '未显示'],
            ['code' => 'video_status', 'code_name' => '视频状态', 'value' => -10, 'text_en' => 'freezing', 'text' => '冻结'],
            ['code' => 'video_status', 'code_name' => '视频状态', 'value' => 10, 'text_en' => 'audit_loading', 'text' => '审核中'],
            ['code' => 'video_status', 'code_name' => '视频状态', 'value' => 20, 'text_en' => 'show', 'text' => '正常'],
        ];
        \App\Models\Dict::insert($data);
    }
}
