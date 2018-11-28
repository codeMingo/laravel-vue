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
