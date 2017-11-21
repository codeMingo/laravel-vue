<template>
    <div id="shortcut" class="shortcut-container" v-show="shortcutDisplay">
        <h3><i class="el-icon-search"></i> 查看详情<a href="javascript:;" class="close-btn" @click="shortcutDisplay = false"><i class="el-icon-close"></i></a></h3>
        <div class="data-detail">
            <ul>
                <li>
                    <label>用户名：</label>
                    <span>{{dataDetail.username}}</span>
                </li>
                <li>
                    <label>管理权限：</label>
                    <router-link to="/home">{{dataDetail.permission_text}}</router-link>
                </li>
                <li>
                    <label>电子邮箱：</label>
                    <span>{{dataDetail.email}}</span>
                </li>
                <li>
                    <label>状态：</label>
                    <span>{{dataDetail.status_text}}</span>
                </li>
                <li>
                    <label>上次ip：</label>
                    <span>{{dataDetail.last_login_ip | defaultValue('未登录')}}</span>
                </li>
                <li>
                    <label>上次登录：</label>
                    <span>{{dataDetail.last_login_time | defaultValue('未登录')}}</span>
                </li>
            </ul>
        </div>
        <div class="footer-detail clear-float">
            <el-tag type="info">
                <router-link to="/home">查看详情</router-link>
            </el-tag>
            <el-tag type="info">
                <router-link to="/home">发送邮件</router-link>
            </el-tag>
            <el-tag type="info">
                <router-link to="/home">查看登陆日志</router-link>
            </el-tag>
            <el-tag type="info">
                <router-link to="/home">查看操作日志</router-link>
            </el-tag>
        </div>
    </div>
</template>
<style rel="stylesheet/scss" lang="scss" scoped>
.shortcut-container {
    position: fixed;
    width: 500px;
    height: 300px;
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: 2px 2px 2px #eee;
    background: #FFFFFF;
    overflow: hidden;
    z-index: 9998;
    h3 {
        padding: 10px;
        background: #eee;
        font-size: 13px;
        a {
            color: #ED4747;
            font-size: 12px;
            display: inline-block;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            position: absolute;
            right: 5px;
            top: 5px;
            text-align: center;
            line-height: 15px;
        }
    }
    .data-detail {
        padding: 10px;
        float: left;
        ul {
            width: 100%;
            float: left;
            li {
                float: left;
                width: 50%;
                font-size: 13px;
                line-height: 250%;
                label {
                    color: #9B9999;
                    width: 70px;
                    display: inline-block;
                    text-align: right;
                }
                p {
                    color: #5F5E5E;
                }
                a {
                    color: #409EFF;
                }
                a:hover {
                    text-decoration: underline;
                }
            }
        }
    }
    .footer-detail {
        border-top: 1px solid #eee;
        padding: 10px;
        position: absolute;
        width: 100%;
        bottom: 0px;
        a {
            color: #fafafa;
        }
        a:hover {
            text-decoration: underline;
        }
    }
}
</style>
<script type="text/javascript">
export default {
    props: ['dataDetail'],
    data() {
        return {
            shortcutDisplay: true,
            shortcutDataDetail: {
                username: '',
                permission_text: '',
                email: '',
                status: '',
                last_login_ip: '',
                last_login_time: ''
            }
        }
    },
    mounted() {

    },
    methods: {

    },
}
</script>