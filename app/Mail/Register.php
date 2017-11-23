<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Register extends Mailable
{
    use Queueable, SerializesModels;

    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.register')->with([
            'title'    => config('ububs.website_name') . '-账户注册激活邮件',
            'url'      => config('ububs.website_url') . '/register-active/check?mail_id=' . authcode($this->data['mail_id'], 'encrypt', 3600) . '&user_id=' authcode($this->data['user_id'], 'encrypt', 3600),
            'username' => $this->data['username'],
        ])->subject(config('ububs.website_name') . '-账户注册激活邮件');
    }
}
