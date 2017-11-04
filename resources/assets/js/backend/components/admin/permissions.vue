<template>
    <div class="app-container">
        <table-header-component v-on:create="create" v-on:getList="getList">
            <el-input v-model="searchForm.permissionName" placeholder="请输入权限名" style="width: 200px;"></el-input>
        </table-header-component>
        <el-table :data="tableData" border style="width: 100%">
            <el-table-column prop="text" label="权限等级" width="180"></el-table-column>
            <el-table-column prop="num" label="当前人数" width="180" :formatter="formatNum"></el-table-column>
            <el-table-column prop="include_menus" label="权限节点数" width="180" :formatter="formatMenus"></el-table-column>
            <el-table-column prop="status" label="状态" width="180" :formatter="formatStatus"></el-table-column>
            <el-table-column align="center" label="操作">
                <template slot-scope="scope">
                    <el-button size="small" type="info" @click="toLink('/admin/permissions/detail/' + scope.row.id)">查看详情</el-button>
                    <el-button size="small" type="success" @click="changeStatus(scope.row.id, 0)" v-if="scope.row.status == 1">禁用</el-button>
                    <el-button size="small" type="warning" @click="changeStatus(scope.row.id, 1)" v-else>启用</el-button>
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
        'table-header-component': TableHeaderComponent,
    },
    data() {
        return {
            formTitle: '',
            formVisible: false,
            tableData: [],
            options: {
                statusOptions: '',
                numOptions: '',
            },
            searchForm: {
                permissionName: ''
            }
        }
    },
    mounted() {
        //this.getList();
    },
    methods: {
        getList() {

        },
        trashed(id) {

        },
        changeStatus(id, status) {

        },
        formatNum(row) {

        },
        formatMenus(row) {

        },
        formatStatus(row) {

        },
        create() {

        }
    }
}
</script>