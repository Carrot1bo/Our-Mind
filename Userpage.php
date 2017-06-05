<!doctype html>
<?php session_start(); ?>
<html>
<head>
<style>
#div1 input {background:#CCC;}
#div1 .active {background:yellow;}
#div1 div {width:300px; height:700px; background:#CCC; display:none;}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户个人信息界面</title>
<script language="javascript" src="src/tabSwitch.js"></script>
<script>
window.onload=function ()
{
	tabSwitch('div1');
};


</script>
</head>

<body>
<div id="div1" >
	<input class="active" type="button" value="用户个人信息" />
	<input type="button" value="思维导图库" />
	<input type="button" value="共享空间" />
    
   <?php
   		error_reporting(E_ALL ^ E_DEPRECATED); 
		
		$ID = $_SESSION['id'];
		$Password = $_SESSION['key'];
		
		/*
		//2、清空session信息
		$_SESSION = array();
   
		//3、清楚客户端sessionid
		if(isset($_COOKIE[session_name()]))
		{
  			setCookie(session_name(),'',time()-3600,'/');
		}
		//4、彻底销毁session
		session_destroy();	
		*/
   		
		//向数据库中查询用户个人信息界面需要的所有数据
   		$sql_user="SELECT * FROM `user` WHERE UserID = '$ID' and Password = '$Password' ";
		$sql_mind="SELECT MindName FROM `mind` WHERE AuthorID = '$ID' ";
		$sql_share="SELECT * FROM `sharegroup` WHERE ShareID = '$ID' and AuthorID <> '$ID' ";
		$sql_2share="SELECT DISTINCT MindName FROM `sharegroup` WHERE AuthorID = '$ID' and ShareID <> '$ID' ";

		mysql_connect('localhost', 'root', '08');
		mysql_select_db('our-mind');
		
		$Info_user = mysql_fetch_array(mysql_query($sql_user));
		
		$res = mysql_query($sql_mind);
		while($row = mysql_fetch_array($res,MYSQL_ASSOC))
		{
      		$Info_mind[]=$row;
		}
		
		 // 释放资源
   		 mysql_free_result($res);
		 
		$res = mysql_query($sql_share);
		while($row = mysql_fetch_array($res,MYSQL_ASSOC))
		{
      		$Info_share[]=$row;
		}
		 // 释放资源
   		 mysql_free_result($res);
		
		$res = mysql_query($sql_2share);
		while($row = mysql_fetch_array($res,MYSQL_ASSOC))
		{
      		$Info_2share[]=$row;
		}
		 // 释放资源
   		 mysql_free_result($res);
		
	?>
    
	<div style="display:block;">
 	<?php
			echo '<br/>'.'用户名及密码:'.'<br/>';
			echo "UserID:  $Info_user[0]".'<br/>'."Password:  $Info_user[1]".'<br/>';
			
			echo '<br/>'.'思维导图:'.'<br/>';
			echo "拥有的思维导图:  $Info_user[2]".'<br/>'."其中共享的思维导图： $Info_user[3]".'<br/>';
			echo "参与共享的思维导图: $Info_user[4]";
	?>

 	</div>
	<div>
    <?php 
		 echo "$Info_user[0]的思维导图库：";
		 echo '<a href="Operation.php?id='.$Info_user[0].'&action=new">'.'New Mind'.'</a>'.'<br/>';
		 for($i=0;$i<$Info_user[2];$i++)
		 {
			echo "Mind: < ".$Info_mind[$i]['MindName'].' ><br/>';
			echo "Operation:";
			echo '<a href="Operation.php?id='.$Info_user[0].'&mind='.$Info_mind[$i]['MindName'].'&action=edit">'.'Edit'.'</a>';
			echo "||";
			echo '<a href="Submit.php?id='.$Info_user[0].'&mind='.$Info_mind[$i]['MindName'].'&action=share">'.'Share'.'</a>';
			echo "||";
			echo '<a href="Operation.php?id='.$Info_user[0].'&mind='.$Info_mind[$i]['MindName'].'&action=delete">'.'Delete'.'</a>'.'<br/>';
		 }
    ?>
    
    </div>
	<div>
    <?php
		echo "$Info_user[0]共享的思维导图:".'<br/>';
		
		for($i=0;$i<$Info_user[3];$i++)
		{
			echo "MindName: < ".$Info_2share[$i]['MindName'].' ><br/>';
			
			//清空记录数组
			$group = array();
			$sql = "SELECT ShareID FROM `sharegroup` WHERE MindName = '{$Info_2share[$i]['MindName']}' and AuthorID = '$Info_user[0]' ";
			
			$res = mysql_query($sql);
			while($row = mysql_fetch_array($res,MYSQL_ASSOC))
			{
      			$group[]=$row;
			}
			 // 释放资源
   		 	mysql_free_result($res);
			
			echo "Share-group:".'<a href="Operation.php?id='.$Info_user[0].'&mind='.$Info_mind[$i]['MindName'].'&request=all&action=del_share">'.'All-Delete'.'</a>'.'<br/>';
			
			for($j=0;$j<count($group);$j++)
			{
				echo $group[$j]['ShareID']."|";
				echo '<a href="Operation.php?id='.$Info_user[0].'&mind='.$Info_mind[$i]['MindName'].'&request='.$group[$j]['ShareID'].'&action=del_share">'.'Delete'.'</a>'.'<br/>';
			}
			
		}
		echo "$Info_user[0]参与共享的思维导图:".'<br/>';
		for($i=0;$i<$Info_user[4];$i++)
		{
			echo     '<a href="getData.php?id='.$Info_share[$i]['ShareID'].'&mind='.$Info_share[$i]['MindName'].'&author='.$Info_share[$i]['AuthorID'].'">'.$Info_share[$i]['MindName'].'</a>';
			echo "|Author ->";
			echo $Info_share[$i]['AuthorID'].'<br/>';
		}
		
		// 关闭连接
		 mysql_close(); 
	
	?>
    </div>
</div>


</body>
</html>
