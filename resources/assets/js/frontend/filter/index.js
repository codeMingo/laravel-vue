/**
 * 时间格式解析
 * @param  {string} time    时间戳
 * @param  {string} cFormat 过滤格式
 * @return {string}
 */
export function parseTime(time, cFormat) {
    const format = cFormat || '{y}-{m}-{d} {h}:{i}:{s}';
    let date = new Date(time);
    const formatObj = {
        y: date.getFullYear(),
        m: date.getMonth() + 1,
        d: date.getDate(),
        h: date.getHours(),
        i: date.getMinutes(),
        s: date.getSeconds(),
        a: date.getDay()
    };
    const time_str = format.replace(/{(y|m|d|h|i|s|a)+}/g, (result, key) => {
        let value = formatObj[key];
        if (key === 'a') return ['一', '二', '三', '四', '五', '六', '日'][value - 1];
        if (result.length > 0 && value < 10) {
            value = '0' + value;
        }
        return value || 0;
    });
    return time_str;
}

/**
 * 根据对象内容获取某一个字段
 * @param  {string} val      value值
 * @param  {Object} options  对象
 * @param  {string} objKey   对象的key字段名称
 * @param  {string} objValue 对象的value字段名称
 * @param  {string} text     没有匹配到返回默认值
 * @return {string}
 */
export function formatByOptions(val, options, objKey, objValue, text = '-') {
    if (val == undefined) {
        return text;
    }
    options.forEach(function(item) {
        if (val == item[objKey]) {
            return text = item[objValue];
        }
    });
    return text;
}

/**
 * 截取字符串，过滤html标签
 * @param  {string} str 可能含html标签
 * @param  {int} start   开始截取位置
 * @param  {int} length  截取字符长度
 * @return {string}
 */
export function subString(str, sub_start, sub_length) {
    if (str === null || str === undefined) {
        return '';
    }
    str = str.replace(/<\/?[^>]*>/g, ''); //去除HTML tag
    str = str.replace(/[ | ]*\n/g, '\n'); //去除行尾空白
    str = str.replace(/\n[\s| | ]*\r/g, '\n'); //去除多余空行
    str = str.replace(/&nbsp;/ig, ''); //去掉&nbsp;
    return str.length > sub_length ? str.substring(sub_start, sub_length) + '...' : str;
}

/**
 * 获取总数
 * @param  {array} lists
 * @return {string}
 */
export function getCount(lists, key = '', value = 1) {
    let count = 0;
    if (lists == undefined) {
        return count;
    }
    if (Array.isArray(lists)) {
        if (key === '') {
            return lists.length;
        }
        lists.forEach(response => {
            if (response[key] == value) {
                count++;
            }
        });
        return count;
    }
    return getCount(Object.keys(lists), key, value);
}

/**
 * 默认值
 * @param  {string} value
 * @param  {string} default
 * @return {string}
 */
export function defaultValue(value, defaultString) {
    if (value == undefined || value === '') {
        return defaultString;
    }
    return value;
}