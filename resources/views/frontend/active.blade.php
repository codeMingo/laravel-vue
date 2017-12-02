<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>账户激活</title>
  </head>
  <body>
    <div class="active-box">
    	<p class="tip">{{ $message }}</p>
    	<p><span id="countdown">5</span>秒之后自动跳转<a href="/#/login">登录页面</a></p>
    </div>

  </body>
  <style>
  	.active-box {

  	}
  	.active-box span {
  		color: red;
  		text-decoration: underline;
  		margin: 0 3px;
  	}
  	.active-box a {
  		color: red;
  		margin-left: 5px;
  	}
  </style>
  <script type="text/javascript">
    let obj = document.getElementById('countdown');
    let time = obj.innerHTML;
    setInterval(function() {
      obj.innerHTML = time --;
      if (time == 0) {
        window.location.href = '/#/login'; 
      }
    }, 1000);    
  </script>
</html>
