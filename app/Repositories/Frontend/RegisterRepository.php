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
        $result = $this->model->where('id', $user_id)->update(['active' => 1]);
        return $result;
    }

    public function sendActiveEmail($input)
    {

    }
}
