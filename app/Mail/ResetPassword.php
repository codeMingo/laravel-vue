<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data;)
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
            'title'    => env('website_name') . '-账户重置密码确认邮件',
            'url'      => config('ububs.website_url') . '/reset-password/check?code=' . authcode($this->data['mail_id'], 'encrypt', 3600),
            'username' => $this->data['username'],
        ])->subject(env('website_name') . '-账户重置密码确认邮件');
    }
}
