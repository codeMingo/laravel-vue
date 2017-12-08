## 前言 ##
- 本项目属业余时间开发（目前上海就职），纯属为了交流与学习，代码质量与性能等优化持续进行
- 目前属于开发阶段，更新的比较频繁，项目大体功能已实现，如果这个项目对你有帮助，请给个star，谢谢！（2017-08-20）
- 前台地址：**xxx.com**
- 后台地址：**xxx.com/backend**
- QQ：**292304400**，微信：**Ruizhenger**，邮箱：**linlm1994@gmail.com**，欢迎交流
- 持续更新ing

## 项目简介 ##
> 本项目同时开发前后台，主要使用laravel + vue + elementui搭建，项目集合了用户、文章、视频管理等完整的博客功能，代码严格遵守psr-2规范，如果不合理的地方，烦请告知，可通过github issue、QQ、email联系我，感谢支持！

## Main module ##
- 用户和管理员注册、登录、退出、改密等
- 文章管理、点赞、评论、回复、收藏、分享朋友圈空间等
- 视频管理、抢购、添加活动、在线观看、收藏、限时观看等
- Markdown编辑器、echarts图表、excel导入导出
- 日志管理、权限控制
- redis缓存、队列服务
- 公共错误401、404页面
- QQ、微信登录
- 邮件发送队列
- 数据库一键备份，还原
- 网站在线人数统计
- 留言板、投票管理、公告模块
- 网站维护关闭
- 整站搜索
- 后台推送通知，即时响应

## Technology application ##
- laravel5.4 + vue2 + vuex + vue-router + webpack + ES6/7 + elementui + 七牛云存储 + redis + sass

## Requirements ##
- PHP 5.6 or later（PHP 7 is best）
- mysql 5.6 or later
- composer （download link：[https://getcomposer.org/download/](https://getcomposer.org/download/ "composer下载地址")）
- nodejs （download link：[http://nodejs.cn/download/](http://nodejs.cn/download/ "nodejs下载地址")）
- npm （New version of the nedejs has include it）

## Install ##
#### 1. Clone the source code or create new project. ####
> git clone https://github.com/linlianmin/laravel-vue.git

#### 2. Set the basic config ####
> cp .env.example .env

#### 3. Create laravel app_key and create database  ####
> php artisan key:generate

> php artisan migrate:refresh --seed （must be set database config）
#### 4. Install the extended package dependency ####
> composer install

> npm install

if npm speeds slower，can do command（npm install -g cnpm --registry=https://registry.npm.taobao.org）,if do this, npm should be change cnpm

#### 5. Install yarn and run it ####
> npm install -g yarn

> yarn run dev

Now we can visit it, thank you for your reading!

## Preview ##
The future update, thank you!