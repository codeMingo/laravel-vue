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
                            <el-option v-for="item in options.categories" :key="item.id" :label="item.title" :value="item.id + ''"></el-option>
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
                    <el-form-item label="状态" prop="status">
                        <el-select v-model="form.status" placeholder="请选择文章状态">
                            <el-option v-for="item in options.status" :key="item.value" :label="item.text" :value="item.value"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="是否推荐" prop="recommend">
                        <el-select v-model="form.recommend" placeholder="请选择文章是否推荐">
                            <el-option v-for="item in options.recommends" :key="item.value" :label="item.text" :value="item.value + ''"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row :gutter="24">
                <el-col :span="12">
                    <el-form-item label="来源" prop="source">
                        <el-input v-model="form.source" placeholder="请输入文章来源"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-form-item label="缩略图" prop="thumbnail">
                <el-upload class="upload-demo" action="/api/upload-image" :on-preview="previewThumbnail" :on-remove="removeThumbnail" :file-list="thumbnailFileList" list-type="picture" :on-success="uploadThumbnailSuccess" :before-upload="beforeUploadThumbnail" :on-change="thumbnailChange" :headers="uploadHeaders">
                    <el-button size="small" type="primary">点击上传</el-button>
                </el-upload>
            </el-form-item>
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
                id: this.$route.params.id,
                category_id: '',
                title: '',
                auther: '',
                source: '',
                thumbnail: '',
                recommend: '',
                content: '',
                status: ''
            },
            options: {
                categories: [],
                recommends: [],
                status: []
            },
            thumbnailFileList: [],
            rules: {
                category_id: [
                    { required: true, message: '请选择文章类别', trigger: 'blur' }
                ],
                title: [
                    { max: 50, message: '长度不得超过 50 个字符', trigger: 'blur' }
                ],
                auther: [
                    { max: 25, message: '长度不得超过 25 个字符', trigger: 'blur' }
                ],
                source: [
                    { max: 255, message: '长度不得超过 255 个字符', trigger: 'blur' }
                ],
                reading: [
                    { type: 'number', message: '阅读量必须为数字' }
                ],
                content: [
                    { required: true, message: '请输入文章内容', trigger: 'blur' }
                ]
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
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'font': [] }],
                        [{ 'align': [] }],
                        ['clean'],
                        ['link', 'image', 'video']
                    ]
                }
            },
            uploadHeaders: {
                'X-CSRF-TOKEN': window.laravelCsrfToken
            },
        }
    },
    mounted() {
        this.getList();
    },
    methods: {
        getList() {
            let _this = this;
            axios.get('/backend/article/' + _this.form.id + '/edit').then(response => {
                let { status, data, message } = response.data;
                _this.form = data.data;
                _this.options = data.options;
            });
        },
        submit(formName) {
            let _this = this;
            _this.$refs[formName].validate((valid) => {
                if (valid) {
                    axios.put('/backend/article/' + _this.form.id, { 'data': _this.form }).then(response => {
                        let { status, data, message } = response.data;
                        if (!status) {
                            _this.$message.error(message);
                            return false;
                        }
                        _this.$message.success(message);
                        _this.$router.push({ path: '/article/index' });
                    });
                }
            });
        },
        removeThumbnail(file, fileList) {
            //console.log(file, fileList);
        },
        previewThumbnail(file) {
            //console.log(file);
        },
        uploadThumbnailSuccess(res, file) {
            this.form.thumbnail = res.data.imageUrl;
        },
        beforeUploadThumbnail(file) {
            let _this = this,
                fileType = file.type,
                fileSize = file.size / 1024,
                truePictureType = ['image/jpeg', 'image/jpg', 'image/png', 'image/x-png'];
            if (truePictureType.indexOf(fileType) == -1) {
                _this.$message.error('请上传正确的图片的格式（jpg/png）');
                return false;
            }
            if (fileSize > 500) {
                _this.$message.error('上传的图片大小不能超过 500KB');
                return false;
            }
            return true;
        },
        thumbnailChange(file, fileList) {
            this.thumbnailFileList = fileList.slice(-1);
        }
    }
}
</script>