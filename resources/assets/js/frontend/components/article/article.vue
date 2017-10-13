<template>
    <div class="content-container article-container">
        <el-row :gutter="10">
            <el-col :xs="24" :sm="24" :md="16" :lg="16">
                <div class="breadcrumb">
                    <el-breadcrumb separator="/">
                        <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
                        <el-breadcrumb-item>技术篇</el-breadcrumb-item>
                    </el-breadcrumb>
                </div>
                <div class="content-box article-box">

                    <div class="article-detail" v-for="(item, index) in articleData">
                        <div class='article-picture'><img :src="item.thumbnail"></div>
                        <div class="article-word">
                            <h2 class='article-title'>
                                <router-link :to="{ path: '/article/detail/' + item.id }" target="_blank">{{index}}、{{item.title}}</router-link>
                            </h2>
                            <div class='article-right'>
                                <p>
                                    <span>作者：{{item.author}}</span>
                                    <span>发表时间：{{item.created_at}}</span>
                                    <span>类别：{{item.category_id | formatByOptions(articleOptions.categories, 'id', 'category_name')}}</span>
                                </p>
                            </div>
                            <div class='article-intro'>
                                <p>{{item.content | subString(0, 200)}}</p>
                            </div>
                            <div class='article-interactive'>
                                <p>
                                    <a href="javascript:;"><i class='fa fa-thumbs-o-up'></i><span>{{item.likeCount}}</span></a>
                                    <a href="javascript:;"><i class='fa fa-commenting-o'></i><span>{{item.commentCount}}</span></a>
                                    <a href="javascript:;"><i class='fa fa-eye'></i><span>{{item.readCount}}</span></a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="page-box">
                        <el-pagination @current-change="changeCurrentPage" :current-page.sync="articlePagination.currentPage" :page-size="articlePagination.pageSize" layout="total, prev, pager, next" :total="articlePagination.total">
                        </el-pagination>
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
.article-container {
    .article-box {
        padding-right: 20px;
        margin-right: 10px;
        border-right: 1px solid #eee;
        .article-detail {
            padding: 5px 8px;
            border: 1px solid #eee;
            box-shadow: 3px 5px 3px #fafafa;
            border-radius: 3px;
            float: left;
            margin-bottom: 20px;
            .article-picture {
                width: 20%;
                float: left;
                img {
                    width: 100%;
                    border-radius: 3px;
                }
            }
            .article-word {
                float: left;
                width: 80%;
                box-sizing: border-box;
                padding-left: 5px;
                .article-title {
                    margin-bottom: 5px;
                    a {
                        font-size: 16px;
                        color: #333;
                    }
                    a:hover {
                        color: red;
                        text-decoration: underline;
                    }
                }
                .article-right {
                    margin-bottom: 5px;
                    font-size: 12px;
                }
                .article-intro {
                    text-indent: 20px;
                    font-size: #666;
                    line-height: 180%;
                    margin-bottom: 5px;
                }
                .article-interactive {
                    text-align: right;
                    a {
                        color: #999;
                        font-size: 13px;
                        margin-right: 10px;
                    }
                }
            }
        }
    }
}
</style>
<script type="text/javascript">
export default {
    data() {
        return {
            articleData: [],
            articleOptions: {
                categories: [],
            }
            articlePagination: {
                currentPage: 0,
                total: 0,
                pageSize: 0,
            },
            searchForm: []
        };
    },
    mounted() {
        this.getLists();
    },
    methods: {
        getLists() {
            let _this = this;
            let paramsData = { 'data': { 'searchForm': _this.searchForm } };
            axios.get('/article/lists?page=' + _this.articlePagination.currentPage, { params: paramsData }).then(response => {
                let { status, data, message } = response.data;
                _this.articleData = data.lists.data;
                _this.articleOptions = data.options;
                _this.articlePagination.per_page = parseInt(data.lists.per_page);
                _this.articlePagination.current_page = parseInt(data.lists.current_page);
                _this.articlePagination.total = parseInt(data.lists.total);
            })
        },
        changeCurrentPage(val) {
            this.articlePagination.currentPage = val;
            this.getLists();
        }
    }
}
</script>