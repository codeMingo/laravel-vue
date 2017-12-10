<?php
namespace App\Http\Controllers\Frontend;

use App\Servers\Frontend\VideoServer;
use Illuminate\Http\Request;

class VideoController extends BaseController
{

    public function __construct(VideoServer $videoServer)
    {
        parent::__construct();
        $this->server = $videoServer;
    }
}
