<?php
namespace App\Servers\Backend;

use App\Repositories\Common\ApiRepository;

class AdminServer extends CommonServer
{

    public function __construct(
        ApiRepository $apiRepository
    ) {
        $this->apiRepository = $apiRepository;
    }

    public function refreshCache()
    {
        $result = $this->apiRepository->refreshCache();

        return ['更新成功', $result];
    }
}
