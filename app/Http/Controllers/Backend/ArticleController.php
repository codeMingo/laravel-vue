<?php
namespace App\Http\Controllers\Backend;

use App\Servers\Backend\ArticleServer;
use Illuminate\Http\Request;

class ArticleController extends CommonController
{

    public function __construct(ArticleServer $articleServer)
    {
        parent::__construct();
        $this->server = $articleServer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input  = json_decode($request->input('data'), true);
        $result = $this->server->lists($input);
        return $this->responseResult($result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input  = $request->input('data');
        $result = $this->server->store($input);
        return $this->responseResult($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = $this->server->show($id);
        return $this->responseResult($result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result = $this->server->edit($id);
        return $this->responseResult($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input  = $request->input('data');
        $result = $this->server->update($id, $input);
        return $this->responseResult($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->server->destroy($id);
        return $this->responseResult($result);
    }

    public function options()
    {
        $result = $this->server->getOptions();
        return $this->responseResult($result);
    }
}
