<template>
    <div class="app-container">
        <table-header-component v-on:create="create" v-on:getList="getList">
            <el-input v-model="searchForm.title" placeholder="请输入文章标题" style="width: 200px;"></el-input>
            <el-input v-model="searchForm.auther" placeholder="请输入作者" style="width: 200px;"></el-input>
            <el-select v-model="searchForm.category_id" placeholder="请选择文章类别">
                <el-option label="全部" value=""></el-option>
                <el-option v-for="item in options.categories" :key="item.id" :label="item.category_name" :value="item.id"></el-option>
            </el-select>
            <el-select v-model="searchForm.status" placeholder="请选择状态">
                <el-option label="全部" value=""></el-option>
                <el-option v-for="item in options.status" :key="item.value" :label="item.text" :value="item.value"></el-option>
            </el-select>
        </table-header-component>
        <el-table :data="tableData" border style="width: 100%">
            <el-table-column prop="title" label="标题"></el-table-column>
            <el-table-column label="类别">
                <template scope="scope">
                    {{scope.row.category_id | formatByOptions(options.categories, 'id', 'category_name')}}
                </template>
            </el-table-column>
            <el-table-column prop="auther" label="作者"></el-table-column>
            <el-table-column prop="created_at" label="发表时间"></el-table-column>
            <el-table-column prop="status" label="状态" :formatter="formatStatus"></el-table-column>
            <el-table-column align="center" label="操作" width="250">
                <template scope="scope">
                    <el-button size="small" type="info" @click="toLink('/article/show/' + scope.row.id)">查看详情</el-button>
                    <el-button size="small" type="success" @click="toLink('/article/update/' + scope.row.id)">编辑</el-button>
                    <el-button size="small" type="danger" @click="trashed(scope.row.id)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <pagination-component ref="pagination" v-on:getList="getList"></pagination-component>
    </div>
</template>
<script>
import PaginationComponent from '../common/pagination-component.vue';
import TableHeaderComponent from '../common/table-header-component.vue';
export default {
    components: {
        'pagination-component': PaginationComponent,
        'table-header-component': TableHeaderComponent
    },
    data() {
        return {
            formTitle: '',
            tableData: [],
            searchForm: {
                title: '',
                auther: '',
                category_id: '',
                status: '',
            },
            options: {
                status: [],
                categories: []
            }
        }
    },
    mounted() {
        this.getList();
    },
    methods: {
        getList() {
            let _this = this;
            let paramsData = { 'data': { 'searchForm': _this.searchForm } };
            axios.get('/backend/articles?page=' + _this.$refs.pagination.pageData.current_page, { params: paramsData }).then(response => {
                let { status, data, message } = response.data;
                _this.tableData = data.lists.data;
                _this.options = data.options;
                _this.$refs.pagination.pageData.per_page = parseInt(data.lists.per_page);
                _this.$refs.pagination.pageData.current_page = parseInt(data.lists.current_page);
                _this.$refs.pagination.pageData.total = parseInt(data.lists.total);
            })
        },
        trashed(id) {
            let _this = this;
            _this.$confirm('确定删除这篇文章吗').then(() => {
                axios.delete('/backend/articles/' + id).then(response => {
                    _this.$message.success(response.message);
                    Vue.removeOneData(_this.tableData, id);
                });
            });
        },
        formatStatus(row) {
            let text = '-';
            this.options.status.forEach(function(item) {
                if (row.status == item.value) {
                    return text = item.text;
                }
            });
            return text;
        },
        formatCategory(row) {
            let text = '-';
            this.options.categories.forEach(function(item) {
                if (row.category_id == item.id) {
                    return text = item.name;
                }
            });
            return text;
        },
        create() {
            this.$router.push({ path: '/article/create' });
        },
        toLink(url) {
            this.$router.push({ path: url });
        }
    }
}
</script>