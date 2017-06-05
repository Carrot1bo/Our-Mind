<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>注册</title>
</head>
<body>
	<div
		style="text-align: center; background-color: silver; color: white; border: 1px solid gray; font-size: 45px">
		Our-Mind</div>
	<div
		style="text-align: center; background:url(src/background.jpg); background-size: 100%">
		<br /> <br /> <br /> <br />
	<form action="regcheck.php" method="post"> 
   			 用户名：<input type="text" name="username"/>  
   			 <br/>  
   			 密　码:<input type="password" name="password"/>  
   			 <br/>  
    		 确认密码：<input type="password" name="confirm"/>  
    		 <br/>  
    		<input type="Submit" name="Submit" value="注册"/>  
	</form>
    		 <a href="login.php">返回登录</a> 
    
		<br /> <br /> <br />
	</div>
	<div
		style="text-align: right; background-color: silver; color: darkcyan; border: 1px solid gray; margin-top: 2px; padding-left: 10px"
		id="clock">
		<script type="text/javascript">
			setInterval("document.getElementById('clock').innerHTML = 'GJX © ' + new Date().toLocaleString() + ' 星期' + '日一二三四五六'.charAt(new Date().getDay());", 100);
		</script>
	</div>
</body>
</html>