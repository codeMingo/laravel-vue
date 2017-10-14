<template>
    <div class="content-container article-detail-container">
        <el-row :gutter="10">
            <el-col :xs="24" :sm="24" :md="16" :lg="16">
                <div class="breadcrumb">
                    <el-breadcrumb separator="/">
                        <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
                        <el-breadcrumb-item :to="{ path: '/article/index' }">技术篇</el-breadcrumb-item>
                        <el-breadcrumb-item>每周推送 Laravel 最新资讯</el-breadcrumb-item>
                    </el-breadcrumb>
                </div>
                <div class="content-box article-detail-box">
                    <h2 class="article-title">每周推送 Laravel 最新资讯</h2>
                    <p class="article-right">
                        <span>作者：<strong>林联敏</strong></span>
                        <span>发表时间：<strong>2017-08-21 15:26：11</strong></span>
                        <span>阅读量：<strong>329</strong></span>
                        <span>评论：<strong>19</strong></span>
                        <span>点赞：<strong>31</strong></span>
                    </p>
                    <div class="article-content">前面我们实现了使用PC端上位机串口发送图像数据到VGA显示，通过MATLAB处理的图像数据直接是灰度图像，后面我们在此基础上修改，从而实现，基于FPGA的动态图片的Sobel边缘检测、中值滤波、Canny算子边缘检测、腐蚀和膨胀等。那么这篇文章我们将来实现基于FPGA的Sobel边缘检测。 图像边缘：简言之，边缘就是图像灰度值突变的地方，亦即图像在该部分的像素值变化速度非常之快，这就好比在坐标轴上一条曲线有刚开始的平滑突然来个大转弯，在变化出的导数非常大。 　　Sobel算子主要用作边缘检测，在技术上，它是一离散型差分算子，用来计算图像亮度函数灰度之近似值。在图像的恩和一点使用此算子，将会产生对应的灰度矢量或是其法矢量。 　　边缘是指其周围像素灰度急剧变化的那些像素的集合。边缘存在于目标、背景和区域之间，所以，他是图像分割所以来的最重要的依据。由于边缘是位置的标志，对灰度的变化不敏感，因此，边缘也是图像匹配的重要的特征。 Sobel边缘检测的核心在于像素矩阵的卷积，卷积对于数字图像处理非常重要，很多图像处理算法都是做卷积来实现的。卷积运算的本质就是对制定的图像区域的像素值进行加权求和的过程，其计算过程为图像区域中的每个像素值分别与卷积模板的每个元素对应相乘，将卷积的结果作求和运算，运算到的和就是卷积运算的结果。
                    </div>
                    <div class="article-interactive">
                        <div class="article-more">
                            <div class="article-prev">
                                <p><a href="javascript:;"><i class="fa fa-chevron-left"></i>上一篇：Sobel算子边缘检测</a></p>
                                <p>@2017-08-31 阅读(301) 赞(19) 评论(25)</p>
                            </div>
                            <div class="article-next">
                                <p><a href="javascript:;">下一篇：Laravel 5.4 中文文档<i class="fa fa-chevron-right"></i></a></p>
                                <p>@2017-08-31 阅读(301) 赞(19) 评论(25)</p>
                            </div>
                        </div>
                        <div class="article-advertise">
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
                        <!-- <div class="comment-now-content" contenteditable="true"><span># 高山流水</span></div> -->
                        <quill-edit class="interactive-now-content" v-model="comment.content" :options="editorOption"></quill-edit>
                        <div class="interactive-now-submit">
                            <el-button type="primary" @click="commentSubmit">提　交</el-button>
                        </div>
                    </div>
                </div>
            </el-col>
            <el-col :xs="0" :sm="0" :md="8" :lg="8">
                <div class='right-recommend'>
                    <div class="recommend-box hot-article">
                        <h3>热门文章<a href="javascript:;">更多推荐 ++</a></h3>
                        <ul>
                            <li><a href="javascript:;">1.Laravel 5.4 中文文档</a></li>
                            <li><a href="javascript:;">2.一小时同步一次，更多信息请查阅 文档导读</a></li>
                            <li><a href="javascript:;">3.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">4.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">5.一小时同步一次，更多信息请查阅 文档导读</a></li>
                            <li><a href="javascript:;">6.一小时同步一次，更多信息请查阅 文档导读</a></li>
                            <li><a href="javascript:;">7.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">8.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">9.一小时同步一次，更多信息请查阅 文档导读</a></li>
                            <li><a href="javascript:;">10.每周推送 Laravel 最新资讯</a></li>
                        </ul>
                    </div>
                    <div class="recommend-box hot-video">
                        <h3>热门视频<a href="javascript:;">更多视频 ++</a></h3>
                        <ul>
                            <li><a href="javascript:;">1.Laravel 5.4 中文文档</a></li>
                            <li><a href="javascript:;">2.一小时同步一次，更多信息请查阅 文档导读</a></li>
                            <li><a href="javascript:;">3.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">4.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">5.一小时同步一次，更多信息请查阅 文档导读</a></li>
                        </ul>
                    </div>
                    <div class="recommend-box hot-comment">
                        <h3>精彩评论<a href="javascript:;">更多评论 ++</a></h3>
                        <ul>
                            <li><a href="javascript:;">1.Laravel 5.4 中文文档</a></li>
                            <li><a href="javascript:;">2.一小时同步一次，更多信息请查阅 文档导读</a></li>
                            <li><a href="javascript:;">3.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">4.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">5.一小时同步一次，更多信息请查阅 文档导读</a></li>
                        </ul>
                    </div>
                    <div class="recommend-box hot-leave">
                        <h3>精彩留言<a href="javascript:;">更多留言 ++</a></h3>
                        <ul>
                            <li><a href="javascript:;">1.Laravel 5.4 中文文档</a></li>
                            <li><a href="javascript:;">2.一小时同步一次，更多信息请查阅 文档导读</a></li>
                            <li><a href="javascript:;">3.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">4.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">5.一小时同步一次，更多信息请查阅 文档导读</a></li>
                        </ul>
                    </div>
                    <div class="recommend-box hot-vote">
                        <h3>当前投票<a href="javascript:;">更多投票 ++</a></h3>
                        <ul>
                            <li><a href="javascript:;">1.Laravel 5.4 中文文档</a></li>
                            <li><a href="javascript:;">2.一小时同步一次，更多信息请查阅 文档导读</a></li>
                            <li><a href="javascript:;">3.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">4.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">5.一小时同步一次，更多信息请查阅 文档导读</a></li>
                        </ul>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
</template>
<style rel="stylesheet/scss" lang="scss" scoped>
.article-detail-container {
    .article-detail-box {
        padding-right: 20px;
        margin-right: 10px;
        border-right: 1px solid #eee;
        .article-title {
            font-size: 20px;
            text-align: center;
            margin: 10px 0;
            color: #666;
        }
        .article-right {
            text-align: center;
            font-size: 12px;
            color: #999;
            margin-bottom: 10px;
            span {
                margin: 0 5px;
            }
        }
        .article-content {
            line-height: 180%;
            overflow: hidden;
            img {
                max-width: 100%;
            }
        }
        .article-interactive {
            border-top: 1px solid #eee;
            padding-top: 10px;
            margin-top: 10px;
            .article-more {
                font-size: 12px;
                margin: 10px 0;
                .article-prev {
                    float: left;
                    i {
                        margin-right: 5px;
                    }
                }
                .article-next {
                    float: right;
                    text-align: right;
                    i {
                        margin-left: 5px;
                    }
                }
                .article-prev,
                .article-next {
                    p {
                        margin: 5px;
                        a {
                            color: #20A0FF;
                        }
                    }
                }
            }
            .article-advertise {
                clear: both;
                margin: 10px 0;
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
            article_id: this.$route.params.id,
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
    mounted() {
        this.getList();
    },
    methods: {
        getList() {
            let _this = this;
            axios.get('/article/detail/' + _this.article_id).then(response => {
                let { status, data, message } = response.data;
                _this.article_data = data.lists.data;
                _this.article_options = data.options;
                _this.article_pagination.per_page = parseInt(data.lists.per_page);
                _this.article_pagination.current_page = parseInt(data.lists.current_page);
                _this.article_pagination.total = parseInt(data.lists.total);
            });
        },
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