## ububs Blog ##
- It's for learning
- Home page address：**xxx.com**
- admin page address：**xxx.com/backend**
- QQ：**292304400**，gmail：**linlm1994@gmail.com**，
- 项目更新频繁，时常报错，感谢支持！持续更新ing！（2017-08-20）

## Main module ##
- 注册、登录、注销、记住密码、改密、冻结
- 文章发布、置顶、推荐、修改、删除、评论、回复、点赞、阅读记录、分享朋友圈空间等
- 视频发布、置顶、添加活动、促销、购买、上架下架、删除、编辑、在线观看、收藏、限时免费观看
- Markdown编辑器、echarts图表、excel导入导出
- 日志管理
- 权限控制
- 七牛云存储、redis缓存、视频抢购并发、登录限制
- 公共错误401、404页面
- QQ、微信登录
- 邮件发送，redis队列
- 前台响应式布局
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
#### 3. create laravel app_key and  ####
> php artisan key:generate
#### 4. Install the extended package dependency ####
composer operation
> composer update

npm operation，if npm speeds slower，can do command（npm install -g cnpm --registry=https://registry.npm.taobao.org）
> npm install （cnpm install）
#### 5. install yarn ####
> npm install -g yarn
#### 6.run it and visit it ####
> yarn run dev