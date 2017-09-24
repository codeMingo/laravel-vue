<template>
    <div>
        <el-row :gutter="10">
            <el-col :xs="12" :sm="12" :md="12" :lg="12">
                <div id="userRegisterChart" class="box-chart"></div>
            </el-col>
            <el-col :xs="12" :sm="12" :md="12" :lg="12">
                <div id="userLoginChart" class="box-chart"></div>
            </el-col>
            <el-col :xs="12" :sm="12" :md="12" :lg="12">
                <div id="userInteractive" class="box-chart"></div>
            </el-col>
        </el-row>
    </div>
</template>
<style rel="stylesheet/scss" lang="scss" scoped>
.box-chart {
    height: 400px;
}
</style>
<script type="text/javascript">
// 引入基本模板
let echarts = require('echarts/lib/echarts');
// 引入柱状图组件
require('echarts/lib/chart/bar');
require('echarts/lib/chart/pie');
// 引入提示框和title组件
require('echarts/lib/component/tooltip');
require('echarts/lib/component/title');
export default {
    name: 'hello',
    data() {
        return {
            msg: 'Welcome to Your Vue.js App'
        }
    },
    mounted() {
        this.userRegisterChart();
        this.userLoginChart();
        this.userInteractive();
    },
    methods: {
        userRegisterChart() {
            // 基于准备好的dom，初始化echarts实例
            let myChart = echarts.init(document.getElementById('userRegisterChart'))
            // 绘制图表
            myChart.setOption({
                title: {
                    show: true, //显示策略，默认值true,可选为：true（显示） | false（隐藏）
                    text: '用户注册统计图', //主标题文本，'\n'指定换行
                    link: '', //主标题文本超链接,默认值true
                    target: null, //指定窗口打开主标题超链接，支持'self' | 'blank'，不指定等同为'blank'（新窗口）
                    subtext: '副标题', //副标题文本，'\n'指定换行
                    sublink: '', //副标题文本超链接
                    subtarget: null, //指定窗口打开副标题超链接，支持'self' | 'blank'，不指定等同为'blank'（新窗口）
                    x: 'center', //水平安放位置，默认为'left'，可选为：'center' | 'left' | 'right' | {number}（x坐标，单位px）
                    y: 'top', //垂直安放位置，默认为top，可选为：'top' | 'bottom' | 'center' | {number}（y坐标，单位px）
                    textAlign: null, //水平对齐方式，默认根据x设置自动调整，可选为： left' | 'right' | 'center
                    backgroundColor: 'rgba(0,0,0,0)', //标题背景颜色，默认'rgba(0,0,0,0)'透明
                    borderColor: '#ccc', //标题边框颜色,默认'#ccc'
                    borderWidth: 0, //标题边框线宽，单位px，默认为0（无边框）
                    padding: 5, //标题内边距，单位px，默认各方向内边距为5，接受数组分别设定上右下左边距
                },
                color: ['#3398DB'],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: { // 坐标轴指示器，坐标轴触发有效
                        type: 'shadow' // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [{
                    type: 'category',
                    data: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    axisTick: {
                        alignWithLabel: true
                    }
                }],
                yAxis: [{
                    type: 'value'
                }],
                series: [{
                    name: '直接访问',
                    type: 'bar',
                    barWidth: '60%',
                    data: [10, 52, 200, 334, 390, 330, 220, 10, 52, 200, 334, 390]
                }]
            });
        },
        userLoginChart() {
            // 基于准备好的dom，初始化echarts实例
            let myChart = echarts.init(document.getElementById('userLoginChart'))
            // 绘制图表
            myChart.setOption({
                title: {
                    show: true, //显示策略，默认值true,可选为：true（显示） | false（隐藏）
                    text: '用户登录统计图', //主标题文本，'\n'指定换行
                    link: '', //主标题文本超链接,默认值true
                    target: null, //指定窗口打开主标题超链接，支持'self' | 'blank'，不指定等同为'blank'（新窗口）
                    subtext: '副标题', //副标题文本，'\n'指定换行
                    sublink: '', //副标题文本超链接
                    subtarget: null, //指定窗口打开副标题超链接，支持'self' | 'blank'，不指定等同为'blank'（新窗口）
                    x: 'center', //水平安放位置，默认为'left'，可选为：'center' | 'left' | 'right' | {number}（x坐标，单位px）
                    y: 'top', //垂直安放位置，默认为top，可选为：'top' | 'bottom' | 'center' | {number}（y坐标，单位px）
                    textAlign: null, //水平对齐方式，默认根据x设置自动调整，可选为： left' | 'right' | 'center
                    backgroundColor: 'rgba(0,0,0,0)', //标题背景颜色，默认'rgba(0,0,0,0)'透明
                    borderColor: '#ccc', //标题边框颜色,默认'#ccc'
                    borderWidth: 0, //标题边框线宽，单位px，默认为0（无边框）
                    padding: 5, //标题内边距，单位px，默认各方向内边距为5，接受数组分别设定上右下左边距
                },
                color: ['#3398DB'],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: { // 坐标轴指示器，坐标轴触发有效
                        type: 'shadow' // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [{
                    type: 'category',
                    data: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    axisTick: {
                        alignWithLabel: true
                    }
                }],
                yAxis: [{
                    type: 'value'
                }],
                series: [{
                    name: '直接访问',
                    type: 'bar',
                    barWidth: '60%',
                    data: [200, 600, 3007, 4000, 1999, 2000, 600, 3007, 4000, 1999, 2000, 980]
                }]
            });
        },
        userInteractive() {
            // 基于准备好的dom，初始化echarts实例
            let myChart = echarts.init(document.getElementById('userInteractive'))
            // 绘制图表
            myChart.setOption({
                tooltip: {
                    trigger: 'item',
                    formatter: "{a} <br/>{b}: {c} ({d}%)"
                },
                legend: {
                    orient: 'vertical',
                    x: 'left',
                    data: ['直达', '营销广告', '搜索引擎', '邮件营销', '联盟广告', '视频广告', '百度', '谷歌', '必应', '其他']
                },
                series: [{
                        name: '访问来源',
                        type: 'pie',
                        selectedMode: 'single',
                        radius: [0, '30%'],

                        label: {
                            normal: {
                                position: 'inner'
                            }
                        },
                        labelLine: {
                            normal: {
                                show: false
                            }
                        },
                        data: [
                            { value: 335, name: '直达', selected: true },
                            { value: 679, name: '营销广告' },
                            { value: 1548, name: '搜索引擎' }
                        ]
                    },
                    {
                        name: '访问来源',
                        type: 'pie',
                        radius: ['40%', '55%'],
                        label: {
                            normal: {
                                formatter: '{a|{a}}{abg|}\n{hr|}\n  {b|{b}：}{c}  {per|{d}%}  ',
                                backgroundColor: '#eee',
                                borderColor: '#aaa',
                                borderWidth: 1,
                                borderRadius: 4,
                                // shadowBlur:3,
                                // shadowOffsetX: 2,
                                // shadowOffsetY: 2,
                                // shadowColor: '#999',
                                // padding: [0, 7],
                                rich: {
                                    a: {
                                        color: '#999',
                                        lineHeight: 22,
                                        align: 'center'
                                    },
                                    // abg: {
                                    //     backgroundColor: '#333',
                                    //     width: '100%',
                                    //     align: 'right',
                                    //     height: 22,
                                    //     borderRadius: [4, 4, 0, 0]
                                    // },
                                    hr: {
                                        borderColor: '#aaa',
                                        width: '100%',
                                        borderWidth: 0.5,
                                        height: 0
                                    },
                                    b: {
                                        fontSize: 16,
                                        lineHeight: 33
                                    },
                                    per: {
                                        color: '#eee',
                                        backgroundColor: '#334455',
                                        padding: [2, 4],
                                        borderRadius: 2
                                    }
                                }
                            }
                        },
                        data: [
                            { value: 335, name: '直达' },
                            { value: 310, name: '邮件营销' },
                            { value: 234, name: '联盟广告' },
                            { value: 135, name: '视频广告' },
                            { value: 1048, name: '百度' },
                            { value: 251, name: '谷歌' },
                            { value: 147, name: '必应' },
                            { value: 102, name: '其他' }
                        ]
                    }
                ]
            });
        }
    }
}
</script>