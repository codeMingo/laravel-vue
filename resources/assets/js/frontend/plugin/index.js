import Vue from 'vue';

const plugins = {
    install: function(Vue, options) {
        Vue.resetForm = function(form) {
            for (let key in form) {
                if (form[key] instanceof Array) {
                    form[key] = [];
                } else if (form[key] instanceof Object) {
                    form[key] = {};
                } else {
                    form[key] = '';
                }
            }
        };
        Vue.copyObj = function(obj, newobj = {}) {
            for (var attr in obj) {
                if (obj[attr] instanceof Object) {
                    newobj = Vue.copyObj(obj[attr], newobj);
                } else {
                    newobj[attr] = '' + obj[attr];
                }
            }
            return newobj;
        };
        Vue.formatDate = function(time, format) {
            var t = new Date(time);
            var tf = function(i) {
                return (i < 10 ? '0' : '') + i
            };
            return format.replace(/yyyy|MM|dd|HH|mm|ss/g, function(a) {
                switch (a) {
                    case 'yyyy':
                        return tf(t.getFullYear());
                        break;
                    case 'MM':
                        return tf(t.getMonth() + 1);
                        break;
                    case 'mm':
                        return tf(t.getMinutes());
                        break;
                    case 'dd':
                        return tf(t.getDate());
                        break;
                    case 'HH':
                        return tf(t.getHours());
                        break;
                    case 'ss':
                        return tf(t.getSeconds());
                        break;
                }
            })
        };
        Vue.findOne = function(data, value, key = 'id') {
            let result = '';
            data.forEach(function(item) {
                if (item[key] === value) {
                    return result = item;
                }
            });
            return result;
        };
        Vue.removeOneData = function(data, value, key = 'id') {
            data.forEach(function(item, index) {
                if (item[key] === value) {
                    data.splice(index, 1);
                    return item;
                }
            });
        };
        Vue.mousePosition = function(evt) {
            evt = evt || window.event;
            return {
                x: evt.clientX,
                y: evt.clientY
            };
        };
        //获取X轴坐标
        Vue.getX = function(evt) {
            evt = evt || window.event;
            return Vue.mousePosition(evt).x;
        };
        //获取Y轴坐标
        Vue.getY = function(evt) {
            evt = evt || window.event;
            return Vue.mousePosition(evt).y;
        };
    }
};
Vue.use(plugins);
export default plugins;