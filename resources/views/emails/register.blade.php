<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{$title}}</title>
</head>
<body>
    <div class="mail-box">
        <h3>hello <strong>{{$username}}</strong>：</h3>
        <p>感谢您来到<strong>ububs.com</strong>，点击以下链接激活您的账户（有效期：<strong class="active-time">24小时</strong>）：</p>
        <p><a href="{{$url}}" class="active-url">{{$url}}</a></p>
        <p>谢谢！</p>
        <div class="intro-box">
            <h2>爱分享，爱编程，那就加入我们吧！</h2>
            <p>quickzz团队</p>
            <ul>
                <li>关注微信公众号：爱分享爱编程</li>
                <li>加入QQ交流群：118333876</li>
            </ul>
        </div>
    </div>
    <style type="text/css">
        .mail-box {

        }
        .active-time {
            color: red;
        }
        .active-url {
            text-decoration: underline;
        }
        .intro-box {
            border: 1px solid #666;
            padding: 20px;
            margin: 10px 0;
        }

    </style>
</body>
</html>