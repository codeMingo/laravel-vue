<?php
namespace App\Servers\Frontend;

use App\Repositories\Frontend\CategoryRepository;

class CategoryServer extends CommonServer
{

    public function __construct(
        CategoryRepository $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * 文章菜单列表
     * @return Array
     */
    public function getArticleCategoryLists()
    {
        $result['lists'] = $this->categoryRepository->getArticleCategoryLists();
        return returnSuccess($result);
    }

    /**
     * 视频菜单列表
     * @return Array
     */
    public function getVideoCategoryLists()
    {
        $result['lists'] = $this->categoryRepository->getVideoCategoryLists();
        return returnSuccess($result);
    }
}
