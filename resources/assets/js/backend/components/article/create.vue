<template>
    <div class="app-container">
        <el-form :model="form" :rules="rules" ref="form" label-width="100px">
            <el-form-item label="标题" prop="title">
                <el-input v-model="form.title" placeholder="请输入文章标题"></el-input>
            </el-form-item>
            <el-row :gutter="24">
                <el-col :span="12">
                    <el-form-item label="类别" prop="category_id">
                        <el-select v-model="form.category_id" placeholder="请选择文章类别">
                            <el-option v-for="item in options.categories" :key="item.id" :label="item.name" :value="item.id + ''"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="作者" prop="auther">
                        <el-input v-model="form.auther" placeholder="请输入文章作者"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row :gutter="24">
                <el-col :span="12">
                    <el-form-item label="来源" prop="source">
                        <el-input v-model="form.source" placeholder="请输入文章来源"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="阅读量" prop="reading">
                        <el-input v-model.number="form.reading" placeholder="请输入文章阅读量"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row :gutter="24">
                <el-col :span="12">
                    <el-form-item label="状态" prop="status">
                        <el-select v-model="form.status" placeholder="请选择文章状态">
                            <el-option v-for="item in options.status" :key="item.value" :label="item.text" :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-form-item label="内容" prop="content">
                <quill-edit class="interactive-now-content" v-model="form.content" :options="editorOption"></quill-edit>
            </el-form-item>
            <el-form-item>
                <!-- <el-button type="primary" @click="toLink('/article/index')">返回</el-button> -->
                <el-button type="primary" @click="submit('form')">保存</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<script type="text/javascript">
import { quillEditor } from 'vue-quill-editor';
export default {
    components: {
        'quill-edit': quillEditor
    },
    data() {
        return {
            form: {
                category_id: '',
                title: '',
                auther: '',
                source: '',
                reading: 0,
                content: '',
                status: ''
            },
            options: {
                categories: [],
                status: []
            },
            rules: {
                category_id: [
                    { required: true, message: '请选择文章类别', trigger: 'blur' }
                ],
                title: [
                    { required: true, message: '请输入文章标题', trigger: 'blur' }
                ],
                auther: [
                    { required: true, message: '请输入文章作者', trigger: 'blur' }
                ],
                source: [
                    { required: true, message: '请输入文章来源', trigger: 'blur' }
                ],
                reading: [
                    { type: 'number', message: '阅读量必须为数字' }
                ],
                content: [
                    { required: true, message: '请输入文章内容', trigger: 'blur' }
                ],
                status: [
                    { required: true, message: '请选择文章状态', trigger: 'blur' }
                ],
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
        }
    },
    mounted() {
        this.getOptions();
    },
    methods: {
        getOptions() {
            let _this = this;
            axios.get('/backend/article/options').then(response => {
                let { status, data, message } = response.data;
                _this.options = data.options;
            });
        },
        submit(formName) {
            let _this = this;
            _this.$refs[formName].validate((valid) => {
                if (valid) {
                    axios.post('/backend/articles', { 'data': _this.form }).then(response => {
                        _this.$store.state.submitLoading = false;
                        if (!response.status) {
                            _this.$message.error(response.message);
                            return false;
                        }
                        _this.$message.success(response.message);
                        _this.formVisible = false;
                        _this.toLink('/article/index');
                    });
                }
            });
        }
    }
}
</script>