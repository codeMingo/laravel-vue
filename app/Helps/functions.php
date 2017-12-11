<?php



/**
 * 响应返回失败信息
 * @param  array  $data    返回结果集
 * @param  string $message 消息提示
 * @return Array
 */
function returnError($message = '', $data = [])
{
    return [
        'status'  => 0,
        'data'    => $data,
        'message' => $message === '' ? (!$status ? '失败' : '成功') : $message,
    ];
}

/**
 * 响应返回成功信息
 * @param  array  $data    返回结果集
 * @param  string $message 消息提示
 * @return Array
 */
function returnSuccess($message = '', $data = [])
{
    return [
        'status'  => 1,
        'data'    => $data,
        'message' => $message === '' ? (!$status ? '失败' : '成功') : $message,
    ];
}

// 获取ip地址
function getClientIp()
{
    if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        // nginx 代理模式下，获取客户端真实IP
        $ip_address = $_SERVER['HTTP_X_REAL_IP'];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        // 客户端的ip
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // 浏览当前页面的用户计算机的网关
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos) {
            unset($arr[$pos]);
        }
        $ip_address = trim($arr[0]);
    } else {
        $ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }
    return $ip_address;
}

// 字符串切割，自动过滤html标签
function truncate_utf8_string($string, $length, $etc = '...')
{
    $result = '';
    $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
    $strlen = strlen($string);
    for ($i = 0; (($i < $strlen) && ($length > 0)); $i++) {
        if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
            if ($length < 1.0) {
                break;
            }
            $result .= substr($string, $i, $number);
            $length -= 1.0;
            $i += $number - 1;
        } else {
            $result .= substr($string, $i, 1);
            $length -= 0.5;
        }
    }
    $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
    if ($i < $strlen) {
        $result .= $etc;
    }
    return $result;
}

// 根据邮箱后缀得到邮箱登录地址
function getMailAddress($mailSuffix)
{
    switch ($mailSuffix) {
        case 'qq.com':
            $mailUrl = 'http://mail.qq.com/';
            break;
        case '163.com':
            $mailUrl = 'http://mail.163.com/';
            break;
        case 'foxmail.com':
            $mailUrl = 'http://mail.qq.com/';
            break;
        case 'gmail.com':
            $mailUrl = 'https://mail.google.com/';
            break;
        case '126.com':
            $mailUrl = 'http://mail.126.com/';
            break;
        case 'sina.com':
            $mailUrl = 'http://mail.sina.com/';
            break;
        case 'sohu.com':
            $mailUrl = 'http://mail.sohu.com/';
            break;
        case 'yahoo.com':
            $mailUrl = 'http://mail.yahoo.com/';
            break;
        case '139.com':
            $mailUrl = 'http://mail.139.com/';
            break;
        default:
            $mailUrl = 'javascript:;';
    }
    return $mailUrl;
}

// 根据ip地址获取所在地区
function getIpLookup($ip)
{
    $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
    if (empty($res)) {return false;}
    $jsonMatches = array();
    preg_match('#\{.+?\}#', $res, $jsonMatches);
    if (!isset($jsonMatches[0])) {return false;}
    $json = json_decode($jsonMatches[0], true);
    if (isset($json['ret']) && $json['ret'] == 1) {
        $json['ip'] = $ip;
        unset($json['ret']);
    } else {
        return false;
    }
    return $json;
}

// 判断是手机登录
function isMobile()
{
    if (isset($_SERVER['HTTP_VIA'])) {
        return true;
    }
    if (isset($_SERVER['HTTP_X_NOKIA_CONNECTION_MODE'])) {
        return true;
    }
    if (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID'])) {
        return true;
    }
    if (strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML") > 0) {
        // Check whether the browser/gateway says it accepts WML.
        $br = "WML";
    } else {
        $browser = isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '';
        if (empty($browser)) {
            return true;
        }
        $mobile_os_list    = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
        $mobile_token_list = array('Profile/MIDP', 'Configuration/CLDC-', '160×160', '176×220', '240×240', '240×320', '320×240', 'UP.Browser', 'UP.Link', 'SymbianOS', 'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry', 'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_', 'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo', 'iPhone', 'iPod');
        $found_mobile      = checkSubstrs($mobile_os_list, $browser) || checkSubstrs($mobile_token_list, $browser);
        if ($found_mobile) {
            return true;
        } else {
            return false;
        }
    }
}
function checkSubstrs($list, $str)
{
    $flag = false;
    for ($i = 0; $i < count($list); $i++) {
        if (strpos($str, $list[$i]) > 0) {
            $flag = true;
            break;
        }
    }
    return $flag;
}

/**
 * 发送邮件
 * @param  Array $mail_data 发送邮件的数据
 * @return bool
 */
function sendEmail($mail_data, $type)
{
    switch ($type) {
        case 'register':
            $mail = new App\Mail\Register($mail_data);
            break;
        case 'reset_password':
            $mail = new App\Mail\ResetPassword($mail_data);
            break;
    }
    Illuminate\Support\Facades\Mail::to($mail_data['to'])->queue($mail);
    return true;
}
/**
 * 加密 和 解密 算法
 * @param  String $string 需要加密的字符
 * @param  String $operation decrypt表示解密，其它表示加密
 * @param  Int $expiry 有效期，秒数
 * @return  String $result, 为空表示解密失败，不存在
 */
function authcode($string, $operation = '', $expiry = 0)
{
    // 密匙
    $key = md5(config('ububs.website_encrypt'));
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    $ckey_length = 4;

    // 密匙a会参与加解密
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文
    $keyc = $ckey_length ? ($operation == 'decrypt' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙
    $cryptkey   = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，
    // 解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
    $string        = $operation == 'decrypt' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result        = '';
    $box           = range(0, 255);
    $rndkey        = array();
    // 产生密匙簿
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
    for ($j = $i = 0; $i < 256; $i++) {
        $j       = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp     = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a       = ($a + 1) % 256;
        $j       = ($j + $box[$a]) % 256;
        $tmp     = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'decrypt') {
        // 验证数据有效性，请看未加密明文的格式
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
            substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}
