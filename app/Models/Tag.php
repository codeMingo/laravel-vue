<?php

namespace App\Models;

class Tag extends Base
{

    protected $fillable = [
        'admin_id', 'title', 'category_type', 'status',
    ];

}
