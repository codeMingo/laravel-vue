<?php
namespace App\Http\Controllers\Backend;

use App\Http\Servers\Backend\ApiServer;

class IndexController extends CommonController
{

    public function __construct(ApiServer $apiServer)
    {
        parent::__construct();
        $this->server = $apiServer;
    }

    public function index()
    {
        return view('backend.index');
    }

    public function refreshCache()
    {
        $result = $this->server->refreshCache();
        return $this->responseResult($result);
    }

}
