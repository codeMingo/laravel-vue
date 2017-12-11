<?php
namespace App\Servers\Common;

abstract class BaseServer
{
    protected $adminRepository;
    protected $articleRepository;
    protected $categoryRepository;
    protected $leaveRepository;
    protected $tagRepository;
    protected $registerRepository;
    protected $loginRepository;
    protected $userRepository;
    protected $videoRepository;

    // 获取当前用户id
    abstract protected function getCurrentId();
}
