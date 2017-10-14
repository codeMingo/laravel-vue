<template>
    <div class="content-container video-container">
        <div class="breadcrumb">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
                <el-breadcrumb-item>视频列表</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="content-box video-box">
            <el-row :gutter="20">
                <el-col :span="6" v-for="(o, index) in 10" :key="o" class="video-detail">
                    <el-card :body-style="{ padding: '0px' }">
                        <router-link to="/video/detail">
                            <div class="video-thumb">
                                <img src="/images/focus_weixin.png" class="image video-image">
                                <div class="video-mask"><span><i class="fa fa-play"></i></span></div>
                            </div>
                            <div class="video-intro">
                                <h4>laravel基础教程（<strong>53</strong>课时）</h4>
                                <div class="bottom clearfix">
                                    <time class="time">2017-07-21</time>
                                    <el-button type="info" size="small" class="button">免费观看</el-button>
                                </div>
                            </div>
                        </router-link>
                    </el-card>
                </el-col>
            </el-row>
        </div>
    </div>
</template>
<style rel="stylesheet/scss" lang="scss" scoped>
.el-row {
    margin-bottom: 20px;
    &:last-child {
        margin-bottom: 0;
    }
}

.video-container {
    .video-box {
        .video-detail {
            margin-bottom: 20px;
            .video-thumb {
                position: relative;
                cursor: pointer;
                .video-image {
                    width: 100%;
                }
                .video-mask {
                    position: absolute;
                    left: 0;
                    top: 0;
                    background: rgba(0, 0, 0, 0.5);
                    width: 100%;
                    height: 100%;
                    display: none;
                    span {
                        width: 100%;
                        height: 100%;
                        display: table;
                        i {
                            font-size: 4rem;
                            display: table-cell;
                            vertical-align: middle;
                            text-align: center;
                            color: #eee;
                        }
                    }
                }
            }
            .video-thumb:hover .video-mask {
                display: block;
            }
            .video-intro {
                padding: 14px;
                color: #777;
                h4 {
                    font-weight: normal;
                    padding-bottom: 3px;
                    span {
                        color: #D9534F;
                        font-size: 14px;
                    }
                }
                .bottom {
                    button {
                        float: right;
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

        }
    },
    mounted() {
    }
}
</script>