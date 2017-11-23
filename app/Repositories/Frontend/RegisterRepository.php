<?php
namespace App\Repositories\Frontend;

use App\Models\Dict;
use App\Models\EmailRecord;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterRepository extends BaseRepository
{

    /**
     * 注册用户
     * @param  Array $input [username, email, password, face]
     * @return Array
     */
    public function createUser($input)
    {
        $username = isset($input['username']) ? strval($input['username']) : '';
        $email    = isset($input['email']) ? strval($input['email']) : '';
        $face     = isset($input['face']) ? strval($input['face']) : '';
        $password = isset($input['password']) ? Hash::make(strval($input['password'])) : '';

        if (!$username || !$email || !$password) {
            return $this->responseResult(false, [], '注册失败，必填信息不得为空');
        }

        $unique_list = User::where('username', $username)->whereOr('email', $email)->first();
        if (!empty($unique_list)) {
            $error_text = $unique_list->username == $username ? '注册失败，用户名已被注册' : '注册失败，邮箱已被注册';
            return $this->responseResult(false, [], $error_text);
        }

        $result = User::create([
            'username' => $username,
            'email'    => $email,
            'face'     => $face,
            'password' => $password,
        ]);

        $dicts = $this->getRedisDictLists(['email_type' => 'register_active']);
        $email_record = EmailRecord::create([
            'type_id'     => $dicts['email_type']['register_active'],
            'user_id'     => $result->id,
            'email_title' => '账户激活邮件',
            'text'        => '用户注册',
        ]);

        // 发送邮件
        sendEmail([
            'mail_id'  => $email_record->id,
            'user_id'  => $result->user_id,
            'to'       => $result->email,
            'username' => $result->username,
        ], 'register');

        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => [
                'id'       => base64_encode($insertResult->id),
                'username' => $insertResult->username,
                'email'    => $insertResult->email,
            ],
            'message' => '注册成功，请于一小时内激活账号',
        ];
    }

    /**
     * 激活用户
     * @param  Array $code 加密字符
     * @return Array
     */
    public function activeUser($input)
    {
        $mail_id = authcode($input['mail_id'], 'decrypt');
        $user_id = authcode($input['user_id'], 'decrypt');
        if (!$mail_data || !$user_id) {
            return $this->responseResult(false, [], '地址不存在或邮件已经失效');
        }

        $list = User::where('id', $user_id)->first();
        if (empty($list) ) {
            return $this->responseResult(false, [], '不存在此用户');
        }

        if ($list->active) {
            return $this->responseResult(false, [], '账户已经激活，请不要重复操作');
        }

        $list->active = 1;
        $result       = $list->save();
        return $this->responseResult(true, $result, '恭喜您，账户激活成功');
    }
}
