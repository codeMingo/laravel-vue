<template>
    <div class="user-detail">
        <el-row :gutter="60">
            <el-col :sm="14" :md="14" :lg="14">
                <h2 class="user-title">我的收藏({{collect_data | getCount}})</h2>
                <div class="collect-box">
                    <div class="collect-detail" v-for="item in collect_data">
                        <p v-if="item.article">
                            <span>{{item.created_at}}</span> 收藏了文章
                            <router-link :to="{ path: '/article/detail/' + item.article.id }">《{{item.article.title}}》</router-link>
                        </p>
                        <p v-else>
                            <span>{{item.created_at}}</span> 收藏了视频
                            <a href="javascript:;">《item.video.title》</a>
                        </p>
                        <p>
                            <i class="fa fa-paper-plane-o"></i>福建-大田({{item.ip_address}})
                        </p>
                    </div>
                    <div class="page-box">
                        <el-pagination @current-change="changeCurrentPage" :current-page.sync="collect_pagination.current_page" :page-size="collect_pagination.per_page" layout="total, prev, pager, next" :total="collect_pagination.total">
                        </el-pagination>
                    </div>
                </div>
            </el-col>
            <el-col :sm="10" :md="10" :lg="10"></el-col>
        </el-row>
    </div>
</template>
<style rel="stylesheet/scss" lang="scss" scoped>
.collect-box {
    .collect-detail {
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
        return {
            collect_data: {},
            collect_pagination: {
                current_page: 1,
                total: 0,
                per_page: 10,
            },
        };
    },
    mounted() {
        this.getCollectLists();
    },
    methods: {
        getCollectLists() {
            let _this = this;
            let paramsData = { 'data': { 'search_form': _this.search_form } };
            axios.get('/user/collect/lists?page=' + _this.collect_pagination.current_page, { params: paramsData }).then(response => {
                let { status, data, message } = response.data;
                _this.collect_data = data.lists.data;
                _this.collect_pagination.per_page = parseInt(data.lists.per_page);
                _this.collect_pagination.current_page = parseInt(data.lists.current_page);
                _this.collect_pagination.total = parseInt(data.lists.total);
            }).catch(response => {

            });
        },
        changeCurrentPage(val) {
            this.collect_pagination.current_page = val;
            this.getCollectLists();
        },
    }
}
</script>