<?php
namespace App\Http\Controllers\Backend;

use App\Repositories\Backend\AdminRepository;
use Illuminate\Http\Request;

class AdminController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input  = json_decode($request->input('data'), true);
        $result = AdminRepository::getInstance()->lists($input);
        return response()->json($result);
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
        $result = AdminRepository::getInstance()->store($input);
        return response()->json($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = AdminRepository::getInstance()->show($id);
        return response()->json($result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $result = AdminRepository::getInstance()->update($input, $id);
        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = AdminRepository::getInstance()->destroy($id);
        return response()->json($result);
    }

    /*
     * 获取options
     */
    public function options()
    {
        $result = AdminRepository::getInstance()->getOptions();
        return response()->json($result);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function out(Request $request)
    {
        $input  = json_decode($request->input('data'), true);
        $result = AdminRepository::getInstance()->out($input);
        return response()->json($result);
    }

    /**
     * 改变状态
     */
    public function changeFieldValue($id, Request $request)
    {
        $input  = $request->input('data');
        $result = AdminRepository::getInstance()->changeFieldValue($id, $input);
        return response()->json($result);
    }
}
