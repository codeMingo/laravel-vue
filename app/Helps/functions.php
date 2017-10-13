<?php

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

// 判断是手机登录还是pc登录
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

// 判断手机访问， pc访问
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
