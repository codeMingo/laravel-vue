<?php
namespace App\Repositories\Frontend;

use App\Models\Tag;

class TagRepository extends CommonRepository
{

    public function __construct(Tag $tag)
    {
        parent::__construct($tag);
    }

    public function getArticleTags($tag_ids)
    {
        return $this->model->whereIn('id', $tag_ids)->where('status', 1)->get();
    }
}
