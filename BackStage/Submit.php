<!doctype html>
<html>
<head>
<style>
#header {
    background-color:$CCC;
    color:black;
    text-align:center;
    padding:5px;
}
#section {
    width:800px;
    float:left;
    padding:10px;	 	 
}
</style>
<meta charset="utf-8">
<title>输入信息-ing</title>
<script language="javascript" src="src/Submit.js"></script>
</head>

<body>
<script>
	<?php
		$user = $_GET['id'];
		$Mindname = $_GET['mind'];
		$action = $_GET['action'];
		
		echo "var user = '$user';
			  var Mindname = '$Mindname';
			  var movement = '$action';		
			";
	?>


//设置提交地址
function subs(){
	var a=document.getElementById("info").value;
	window.location.href="Operation.php?id="+user+"&mind="+Mindname+"&action="+movement+"&users="+a;
}
</script>
<div id="header">
	<h1>Shared-Settings</h1>
</div>

<div id="section">

	<h3>Please input the users you want to share the Mind<?php echo "<< $Mindname >>"; ?> with~</h3>
	<p>
	The available users are as follow:     
	</p>
    <?php
		error_reporting(E_ALL ^ E_DEPRECATED);
		//查询当前所有用户名
		mysql_connect('localhost', 'root', '08');
		mysql_select_db('our-mind');
		
		$sql = "SELECT `UserID` FROM `user` WHERE 1";
		$result = mysql_query($sql);
	
		while($row = mysql_fetch_array($result,MYSQL_ASSOC))
		{
      		$users[]=$row;
		}
		
		for($i=0;$i<count($users);$i++)
		{
			echo 'ID : '.$users[$i]['UserID'].'<br />';			
		}		
	?>
    <p>Please enter the UserID, separated by commas! <input type="button" onclick="Submit()" value="Input the users">
    </p>
</div>
</body>
</html>