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
    public function articleCategoryLists()
    {
        $result['lists'] = $this->categoryRepository->articleCategoryLists();

        return ['获取成功', $result];
    }

    /**
     * 视频菜单列表
     * @return Array
     */
    public function videoCategoryLists()
    {
        $result['lists'] = $this->categoryRepository->videoCategoryLists();

        return ['获取成功', $result];
    }
}
