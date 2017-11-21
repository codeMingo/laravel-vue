<?php

namespace App\Models;

class Tag extends Base
{

    protected $fillable = [
        'admin_id', 'tag_name', 'category_type', 'status',
    ];

}
