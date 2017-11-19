/*
 * 时间格式解析
 * */
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

/*
 *  根据对象内容获取某一个字段
 * */
export function formatByOptions(val, options, objKey, objValue, text = '-') {
    if (val == undefined) {
        return text;
    }
    options.forEach(function (item) {
        if (val === item[objKey]) {
            return text = item[objValue];
        }
    });
    return text;
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
