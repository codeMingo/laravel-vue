<?php
namespace App\Repositories\Frontend;

use App\Mail\RegisterOrder;
use App\Models\Dict;
use App\Models\EmailRecord;
use App\Models\User;
use App\Repositories\Frontend\CommonRepository;
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
        $email = isset($input['email']) ? strval($input['email']) : '';
        $face = isset($input['face']) ? strval($input['face']) : '';
        $password = isset($input['password']) ? Hash::make(strval($input['password'])) : '';

        if (!$username || !$email || !$password) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '必填信息不得为空',
            ];
        }
        $usernameList = User::where('username', $username)->first();
        if (!empty($usernameList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '用户名已经存在',
            ];
        }
        $emailList = User::where('email', $email)->first();
        if (!empty($emailList)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '邮箱已经存在',
            ];
        }
        $insertResult = User::create([
            'username' => $username,
            'email'    => $email,
            'face'     => $face,
            'password' => $password,
            'active'   => 0,
            'status'   => 1,
        ]);
        if (!$insertResult) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '注册失败，未知错误',
            ];
        }
        $insertEmailResult = EmailRecord::create([
            'type_id'     => Dict::getDictValueByTextEn('register_active'),
            'user_id'     => $insertResult->id,
            'email_title' => '账户激活邮件',
            'text'        => '用户首次注册',
            'status'      => 1,
        ]);
        $mailData = [
            'view'  => 'register',
            'to'    => $insertResult->email,
            'title' => '账户激活邮件',
            'name'  => $insertResult->username,
            'url'   => env('APP_URL') . '/active?mail_id=' . $insertEmailResult->id . '&user_id=' . base64_encode($insertResult->id),
        ];
        CommonRepository::getInstance()->sendEmail($mailData);
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
     * @param  Array $input [email_id, user_id]
     * @return Array
     */
    public function activeUser($input)
    {
        $emailId = isset($input['email_id']) && !empty($input['email_id']) ? intval($input['email_id']) : '';
        $userId  = isset($input['user_id']) && !empty($input['user_id']) ? intval(base64_decode($input['user_id'])) : '';
        if (!$emailId || !$userId) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这个链接地址或邮件已经过期',
            ];
        }
        // 判断邮件是否过期
        $emailList = EmailRecord::where([
            ['id', '=', $emailId],
            ['user_id', '=', $userId],
        ])->first();
        if (empty($emailList) || time() - config('APP_EMAIL_REGISTER_TIME') < strtotime($emailList->create_at)) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在这个链接地址或邮件已经过期',
            ];
        }
        $userList = User::where('id', $userId)->first();
        if (empty($userList) || $userList->active) {
            return [
                'status'  => Parent::ERROR_STATUS,
                'data'    => [],
                'message' => '不存在此用户',
            ];
        }
        // 激活
        $userList->active = 1;
        $saveResult       = $userList->save();
        return [
            'status'  => Parent::SUCCESS_STATUS,
            'data'    => [],
            'message' => '用户成功激活',
        ];
    }
}
