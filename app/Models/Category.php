<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Backend\DictRepository;

class Category extends Model
{

    public $timestamps = false;

    protected $fillable = ['name'];

    public static function lists($type_text)
    {
        $category_type = DictRepository::getInstance()->getDictValueByTextEn($type_text);
        return Category::where('category_type', $category_type)->get();
    }
}
