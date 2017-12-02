<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Repositories\Frontend\CommonRepository;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use App\Models\EmailRecord;
class TestController extends Controller
{
    // 测试
    public function index()
    {
        $email_record = EmailRecord::create([
            'type_id'     => 10,
            'user_id'     => 2,
            'email_title' => '账户激活邮件',
            'text'        => '用户注册',
        ]);
    	sendEmail([
            'mail_id'  => $email_record->id,
            'user_id'  => 2,
            'to'       => '292304400@qq.com',
            'username' => '高山流水',
        ], 'register');
    }
}
