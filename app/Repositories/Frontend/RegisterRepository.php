<?php
namespace App\Repositories\Frontend;

use App\Models\EmailRecord;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class RegisterRepository extends CommonRepository
{

    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * 注册用户
     * @param  Array $input [username, email, password, face]
     * @return Array
     */
    public function register($username, $email, $face, $password)
    {
        $unique_list = $this->model->where('username', $username)->orWhere('email', $email)->first();
        if (!empty($unique_list)) {
            return ['flag' => false, 'message' => $unique_list->username == $username ? '注册失败，用户名已被注册' : '注册失败，邮箱已被注册'];
        }

        $result = $this->model->create([
            'username' => $username,
            'email'    => $email,
            'face'     => $face,
            'password' => $password,
        ]);

        $dicts        = $this->getRedisDictLists(['email_type' => ['register_active']]);
        $email_record = EmailRecord::create([
            'type_id'     => $dicts['email_type']['register_active'],
            'user_id'     => $result->id,
            'email_title' => '账户激活邮件',
            'text'        => '用户注册',
        ]);

        // 发送邮件
        sendEmail([
            'user_id'  => $result->id,
            'to'       => $result->email,
            'username' => $result->username,
        ], 'register');
        return $result;
    }

    /**
     * 激活用户
     * @param  Int $user_id 用户id
     * @return Array
     */
    public function active($user_id)
    {
        $list = $this->model->where('id', $user_id)->first();
        if (empty($list)) {
            return ['flag' => false, 'message' => '激活失败，不存在此用户'];
        }

        if ($list->active) {
            return ['flag' => false, 'message' => '激活失败，账户已经激活，请不要重复操作'];
        }

        $list->active = 1;
        $result       = $list->save();
        return true;
    }

    public function sendActiveEmail($input)
    {

    }
}
