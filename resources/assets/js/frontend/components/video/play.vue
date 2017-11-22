<template>
    <div class="content-container video-play-container">
        <div class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
                <el-breadcrumb-item :to="{ path: '/video/detail' }">视频列表</el-breadcrumb-item>
                <el-breadcrumb-item>在线观看：1.Yii基础视频讲解</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="content-box video-play-box">
            <div class="video-play">
                <div class="video-tip">
                    <p>Quickzz编程，《Yii课程实战》，正在学习：<strong>4</strong>课时 / <strong>19</strong>课时
                        <router-link to="/">免费观看</router-link>
                    </p>
                </div>
                <div class="video-show">
                    <video id="my-video" class="video-js vjs-big-play-centered" playsinline controls preload="auto" poster="/images/focus_weixin.png" data-setup="{}" style="min-height: 500px;">
                        <source src="/videos/test.mp4" type='video/mp4'>
                        <p class="vjs-no-js">
                            To view this video please enable JavaScript, and consider upgrading to a web browser that
                        </p>
                    </video>
                </div>
            </div>
            <div class="video-intro">
                <div class="video-describe">
                    <p>Font Awesome为您提供可缩放的矢量图标，您可以使用CSS所提供的所有特性对它们进行更改，包括：大小、颜色、阴影或者其它任何支持的效果。每周推送 Laravel 最新资讯、精华文章、开发技巧、推荐扩展包、最新 Laravel 职位信息以及 「Laravel China 社区」上的精华讨论。每周推送 Laravel 最新资讯、精华文章、开发技巧、推荐扩展包、最新 Laravel 职位信息以及 「Laravel China 社区」上的精华讨论。每周推送 Laravel 最新资讯、精华文章、开发技巧、推荐扩展包、最新 Laravel 职位信息以及 「Laravel China 社区」上的精华讨论。</p>
                </div>
                <div class="video-page">
                    <h2 class="sidebar-title">视频选择 （<span>34</span>课时）</h2>
                    <ul class="video-menu">
                        <li><a href="javascript:;">1.yii安装</a></li>
                        <li><a href="javascript:;">2.yii基础配置</a></li>
                        <li><a href="javascript:;">3.yii视频页面</a></li>
                        <li><a href="javascript:;">4.变量初始化、传递</a></li>
                        <li><a href="javascript:;">1.yii安装</a></li>
                        <li><a href="javascript:;">2.yii基础配置</a></li>
                        <li><a href="javascript:;">3.yii视频页面</a></li>
                        <li><a href="javascript:;" class="active">4.变量初始化、传递</a></li>
                        <li><a href="javascript:;">1.yii安装</a></li>
                        <li><a href="javascript:;">2.yii基础配置</a></li>
                        <li><a href="javascript:;">3.yii视频页面</a></li>
                        <li><a href="javascript:;">4.变量初始化、传递</a></li>
                        <li><a href="javascript:;">1.yii安装</a></li>
                        <li><a href="javascript:;">2.yii基础配置</a></li>
                        <li><a href="javascript:;">3.yii视频页面</a></li>
                        <li><a href="javascript:;">4.变量初始化、传递</a></li>
                        <li><a href="javascript:;">1.yii安装</a></li>
                        <li><a href="javascript:;">2.yii基础配置</a></li>
                        <li><a href="javascript:;">3.yii视频页面</a></li>
                        <li><a href="javascript:;">4.变量初始化、传递</a></li>
                    </ul>
                </div>
            </div>
            <div class="interactive-box comment-list">
                <h2 class="sidebar-title">视频评论 （<span>56</span>条）</h2>
                <div class="interactive-list">
                    <div class="interactive-detail">
                        <div class="user-face"><a href="javascript:;"><img src="/images/focus_weixin.png" /></a></div>
                        <div class="interactive-word">
                            <p class="user-name">高山流水<span>2017-07-12 16:12:31</span></p>
                            <p class="interactive-content">运用laravel+vue+elementui，从零搭建一个技术博客！</p>
                        </div>
                        <div class="interactive-response">
                            <div class="interactive-detail">
                                <div class="user-face"><a href="javascript:;"><img src="/images/focus_weixin.png" /></a></div>
                                <div class="interactive-word">
                                    <p class="user-name">高山流水 #<span>amgogo先生</span><span>2017-07-12 16:12:31</span></p>
                                    <p class="interactive-content">老哥，稳！</p>
                                </div>
                            </div>
                        </div>
                        <div class="interactive-response">
                            <div class="interactive-detail">
                                <div class="user-face"><a href="javascript:;"><img src="/images/focus_weixin.png" /></a></div>
                                <div class="interactive-word">
                                    <p class="user-name">amgogo先生 #<span>高山流水</span><span>2017-07-12 16:12:31</span></p>
                                    <p class="interactive-content">本人从事php开发工作，一直奋战在一线，处于水深火热之中，工作之余看看知乎、逛逛github、打打游戏！</p>
                                </div>
                            </div>
                        </div>
                        <div class="interactive-response">
                            <div class="interactive-detail">
                                <div class="user-face"><a href="javascript:;"><img src="/images/focus_weixin.png" /></a></div>
                                <div class="interactive-word">
                                    <p class="user-name">高山流水 #<span>amgogo先生</span><span>2017-07-12 16:12:31</span></p>
                                    <p class="interactive-content">老哥，稳！</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="interactive-detail">
                        <div class="user-face"><a href="javascript:;"><img src="/images/focus_weixin.png" /></a></div>
                        <div class="interactive-word">
                            <p class="user-name">高山流水<span>2017-07-12 16:12:31</span></p>
                            <p class="interactive-content">老哥，稳！</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="interactive-now">
                <h2 class="sidebar-title">我要评论</h2>
                <quill-edit class="interactive-now-content" v-model="comment.content" :options="editorOption"></quill-edit>
                <div class="commnet-now-submit">
                    <el-button type="primary" @click="commentSubmit">提　交</el-button>
                </div>
            </div>
        </div>
        <remote-css href="http://vjs.zencdn.net/5.19.2/video-js.css"></remote-css>
        <remote-js src="http://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></remote-js>
        <remote-js src="http://vjs.zencdn.net/5.19.2/video.js"></remote-js>
    </div>
</template>
<style rel="stylesheet/scss" lang="scss" scoped>
.video-play-container {
    .video-play-box {
        .video-play {
            background: #010001;
            overflow: hidden;
            position: relative;
            min-height: 500px;
            max-height: 1000px;
            border: 1px solid #eee;
            clear: both;
            .video-tip {
                background: rgba(0, 0, 0, 0.6);
                padding: 0.6rem 2rem 0.6rem 1rem;
                color: #C6CCC0;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                z-index: 99;
                font-size: 1rem;
                opacity: 0;
                transition: opacity 1s;
                strong {
                    color: #2BB07D;
                }
                a {
                    color: #fff;
                    background-color: #13ce66;
                    border: 1px solid #13ce66;
                    padding: 5px 8px;
                    margin-left: 10px;
                    font-size: 13px;
                    border-radius: 3px;
                }
            }
            .vjs-paused .vjs-big-play-button,
            .vjs-paused.vjs-has-started .vjs-big-play-button {
                display: block;
            }

            .video-js.vjs-playing .vjs-tech {
                pointer-events: auto;
            }

            .vjs-volume-menu-button {
                float: right;
            }

            .video-js .vjs-current-time,
            .vjs-no-flex .vjs-current-time {
                display: block;
            }

            .vjs-time-divider {
                display: block;
            }

            .video-js .vjs-duration,
            .vjs-no-flex .vjs-duration {
                display: block;
            }

            .vjs-remaining-time {
                display: none;
            }

            .video-js .vjs-tech {
                position: relative;
            }

            .my-video-dimensions {
                width: 100%;
                background: #000;
            }

            .video-js .vjs-current-time,
            .vjs-no-flex .vjs-current-time,
            .vjs-time-divider,
            .video-js .vjs-duration,
            .vjs-no-flex .vjs-duration {
                display: block;
            }

            .vjs-time-divider {
                text-align: center;
            }

            .video-js .vjs-time-control {
                padding: 0;
            }
        }
        .video-play:hover .video-tip {
            opacity: 1;
            transition-duration: 1s;
        }
        .video-intro {
            .video-describe {
                color: #999;
                font-size: 12px;
                text-indent: 20px;
                padding: 10px;
                line-height: 150%;
            }
            .video-page {
                .video-menu {
                    padding: 0 20px 20px 20px;
                    float: left;
                    li {
                        float: left;
                        margin: 0 5px 10px 5px;
                        width: 10%;
                        text-align: center;
                        a {
                            border: 1px solid #eee;
                            border-radius: 3px;
                            font-size: 12px;
                            background: #fafafa;
                            color: #888;
                            display: block;
                            padding: 3px 5px;
                        }
                        a:hover,
                        a.active {
                            background: #58B7FF;
                            color: #fff;
                            border: 1px solid #20A0FF;
                        }
                    }
                }
            }
        }
    }
}
</style>
<script type="text/javascript">
import { quillEditor } from 'vue-quill-editor';
export default {
    components: {
        'remote-js': {
            render(createElement) {
                return createElement('script', { attrs: { type: 'text/javascript', src: this.src } });
            },
            props: {
                src: { type: String, required: true },
            },
        },
        'remote-css': {
            render(createElement) {
                return createElement('link', { attrs: { type: 'text/css', rel: 'stylesheet', href: this.href } });
            },
            props: {
                href: { type: String, required: true },
            },
        },
        'quill-edit': quillEditor
    },
    data() {
        return {
            currentPage1: 5,
            comment: {
                content: ''
            },
            editorOption: {
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'header': 1 }, { 'header': 2 }],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        [{ 'script': 'sub' }, { 'script': 'super' }],
                        [{ 'indent': '-1' }, { 'indent': '+1' }],
                        [{ 'direction': 'rtl' }],
                        //[{ 'size': ['small', false, 'large', 'huge'] }],
                        //[{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'color': [] }, { 'background': [] }],
                        //[{ 'font': [] }],
                        //[{ 'align': [] }],
                        ['clean'],
                        ['link']
                        //['link', 'image', 'video']
                    ]
                }
            }
        };
    },
    mounted() {},
    methods: {
        handleSizeChange(val) {
            console.log(`每页 ${val} 条`);
        },
        handleCurrentChange(val) {
            console.log(`当前页: ${val}`);
        },
        commentSubmit() {

        }
    }
}
</script>