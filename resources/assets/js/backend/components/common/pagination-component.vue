<template>
    <div class="pagination-container">
        <el-pagination
          @current-change="currentPageChange"
          :current-page.sync="pageData.current_page"
          :page-size="pageData.per_page"
          layout="total, prev, pager, next"
          :total="pageData.total">
        </el-pagination>
    </div>
</template>
<style type="text/scss" scoped="scope">
    .pagination-container {
        padding: 15px 0;
    }
</style>
<script type="text/javascript">
    export default {
        data() {
            return {
                pageData: {
                  'per_page': 0,
                  'current_page': 1,
                  'total': 0
                },
            }
        },
        methods: {
            currentPageChange(val) {
                this.pageData.current_page = val;
                this.$emit('getLists');
            },
        }
    }
</script>