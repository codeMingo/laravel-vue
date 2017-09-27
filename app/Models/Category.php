<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    public $timestamps = false;

    protected $fillable = ['name'];

    public static function lists($type_text)
    {
        return Category::where('category_type', $type_text)->get();
    }
}
