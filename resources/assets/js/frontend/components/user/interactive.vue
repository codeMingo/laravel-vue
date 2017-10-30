<template>
    <div class="user-detail user-interactive-container">
        <el-row :gutter="60">
            <el-col :sm="14" :md="14" :lg="14">
                <h2 class="user-title">我的收藏(18)</h2>
                <div class="interactive-box">
                    <div class="interactive-detail">
                        <p>
                            <span>2017-09-17 15:21</span>
                            收藏了文章
                            <a href="javascript:;">《Laravel基础语法解析》</a>
                        </p>
                        <p>
                            <i class="fa fa-paper-plane-o"></i>福建-大田(198.168.21.202)
                        </p>
                    </div>
                    <div class="interactive-detail">
                        <p>
                            <span>2017-09-17 15:21</span>
                            收藏了文章
                            <a href="javascript:;">《pull-left 可以轻易构造出引用的特殊效果》</a>
                        </p>
                        <p>
                            <i class="fa fa-paper-plane-o"></i>福建-大田(198.168.21.202)
                        </p>
                    </div>
                    <div class="interactive-detail">
                        <p>
                            <span>2017-09-17 15:21</span>
                            收藏了视频
                            <a href="javascript:;">《Yii基础视频教程》</a>
                        </p>
                        <p>
                            <i class="fa fa-paper-plane-o"></i>上海-闵行区(198.168.21.202)
                        </p>
                    </div>
                    <div class="interactive-detail">
                        <p>
                            <span>2017-09-17 15:21</span>
                            收藏了视频
                            <a href="javascript:;">《pull-left 可以轻易构造出引用的特殊效果》</a>
                        </p>
                        <p>
                            <i class="fa fa-paper-plane-o"></i>福建-龙岩(198.168.21.202)
                        </p>
                    </div>
                    <div class="interactive-detail">
                        <p>
                            <span>2017-09-17 15:21</span>
                            收藏了
                            <a href="javascript:;">《pull-left 可以轻易构造出引用的特殊效果》</a>
                        </p>
                        <p>
                            <i class="fa fa-paper-plane-o"></i>福建-大田(198.168.21.202)
                        </p>
                    </div>
                    <div class="interactive-detail">
                        <p>
                            <span>2017-09-17 15:21</span>
                            收藏了
                            <a href="javascript:;">《pull-left 可以轻易构造出引用的特殊效果》</a>
                        </p>
                        <p>
                            <i class="fa fa-paper-plane-o"></i>福建-大田(198.168.21.202)
                        </p>
                    </div>
                </div>
            </el-col>
            <el-col :sm="10" :md="10" :lg="10"></el-col>
        </el-row>
    </div>
</template>
<style type="text/scss" lang="scss" scoped>
    .interactive-box {
        .interactive-detail {
            margin-bottom: 15px;
            p {
                line-height: 180%;
                a {
                    color: #EC7171;
                    text-decoration: underline;
                }
                i {
                    margin-right: 5px;
                }
            }
        }
    }
</style>
<script type="text/javascript">
    export default {
        data() {
            return {};
        },
        mounted() {},
        methods: {

        }
    }
</script>