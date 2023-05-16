// 自定义表单校验规则
//正则表达式校验。patt为正则表达式（自己封装的校验函数）
function regex(rule, value, callback, obj, patt) {
    // console.log('校验对象：', obj);
    // console.log(rule, value, callback, obj);
    if (!value) {
        callback();
    }
    if (!patt.test(value)) {
        return callback(new Error());
    }
    callback();
}

// 手机号验证规则
export let checkPhoneStrict = (rule, value, callback, obj) => {
    // 十一位手机号,2019年工信部要求
    var patt = /^(?:(?:\+|00)86)?1(?:(?:3[\d])|(?:4[5-79])|(?:5[0-35-9])|(?:6[5-7])|(?:7[0-8])|(?:8[\d])|(?:9[1589]))\d{8}$/;
    regex(rule, value, callback, obj, patt);
}

// qq号检测
export let checkQQStrict = (rule, value, callback, obj) => {
    // qq号
    var patt = /^[1-9][0-9]{4,10}$/;
    regex(rule, value, callback, obj, patt);
}

// 密码检测
export let checkPwdStrict = (rule, value, callback, obj) => {
    // 密码校验，大写字母，小写字母，数字，特殊符号
    var patt = /^(?![a-zA-Z]+$)(?![A-Z0-9]+$)(?![A-Z\W_!@#$%^&*`~()-+=]+$)(?![a-z0-9]+$)(?![a-z\W_!@#$%^&*`~()-+=]+$)(?![0-9\W_!@#$%^&*`~()-+=]+$)[a-zA-Z0-9\W_!@#$%^&*`~()-+=]/;
    regex(rule, value, callback, obj, patt);
}

// 账号
export let checkAccStrict = (rule, value, callback, obj) => {
    // 用户名校验，4到16位（字母，数字，下划线，减号）
    var patt = /^[\w-]{4,16}$/;
    regex(rule, value, callback, obj, patt);
}

// 用户组名
export let checkGroupNameStrict = (rule, value, callback, obj) => {
    // 用户名校验，中文和数字
    var patt = /^((?:[\u3400-\u4DB5\u4E00-\u9FEA\uFA0E\uFA0F\uFA11\uFA13\uFA14\uFA1F\uFA21\uFA23\uFA24\uFA27-\uFA29]|[\uD840-\uD868\uD86A-\uD86C\uD86F-\uD872\uD874-\uD879][\uDC00-\uDFFF]|\uD869[\uDC00-\uDED6\uDF00-\uDFFF]|\uD86D[\uDC00-\uDF34\uDF40-\uDFFF]|\uD86E[\uDC00-\uDC1D\uDC20-\uDFFF]|\uD873[\uDC00-\uDEA1\uDEB0-\uDFFF]|\uD87A[\uDC00-\uDFE0])|(\d))+$/;
    regex(rule, value, callback, obj, patt);
}