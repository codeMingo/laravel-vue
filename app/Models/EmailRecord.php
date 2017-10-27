<?php

namespace App\Models;

class EmailRecord extends Base
{
    protected $fillable = [
        'type_id', 'admin_id', 'user_id', 'email_title', 'text', 'status',
    ];
}
