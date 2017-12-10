<?php
namespace App\Servers\Frontend;

use App\Repositories\Frontend\VideoRepository;

class VideoServer extends CommonServer
{

    public function __construct(
        VideoRepository $videoRepository
    ) {
        $this->videoRepository = $videoRepository;
    }
}
