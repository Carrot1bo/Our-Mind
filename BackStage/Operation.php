<?php
	error_reporting(E_ALL ^ E_DEPRECATED);
	session_start();
	$action = $_GET['action'];
			
	mysql_connect('localhost', 'root', '08');
	mysql_select_db('our-mind');
	
	//创建新的思维导图
	if($action == 'new')
	{
		//获取用户信息
		$user = $_GET['id'];
		$i = 0;
		
		//确定思维导图名称
		$res = TRUE;
		while($res != FALSE)
		{
			$i = $i + 1;
			$Mindname = 'Mind'.$i;
			
			$sql = "SELECT * FROM `mind` WHERE AuthorID = '$user' and Mindname = '$Mindname'";
			$res = mysql_fetch_array(mysql_query($sql));
		}
		
		 // 释放资源
    	mysql_free_result($res);
		
		//先将思维导图的初始数据写入数据库中
		
		$sql = "INSERT INTO `mind`(`AuthorID`, `MindName`, `Version`) VALUES ('$user','$Mindname','1.0')";
		mysql_query($sql);
		
		$sql = "INSERT INTO `nodeinfo`(`AuthorID`, `MindName`, `NodeID`, `NodeLevel`, `ParentID`, `NodeText`, `isLock`, `Direction`) VALUES ('$user','$Mindname','$Mindname','0','null','New Mind','0','')";
		mysql_query($sql);
		
		$sql = "UPDATE `user` SET `MindSum` = MindSum + 1 WHERE `UserID` = '$user'";
		mysql_query($sql);
		
		//生成思维导图数据
		
		$mind = array();
		$mind['meta'] = array();
		$mind['meta']['name'] = $Mindname;
		$mind['meta']['author'] = $user;
		$mind['meta']['version'] = "1.0";
	
		$mind['format'] = 'node_array';
		
		$temp = array();
		$temp['id'] = $Mindname;
		$temp['isroot'] = true;
		$temp['topic'] = 'New Mind';
		
		$mind['data'][] = $temp;
		
		$json = json_encode($mind);			
		$_SESSION['NewMind'] = $json;

		header("Location:EditMind.php?id=$user&mind=$Mindname&author=$user&action=create"); 		
			
	}	
	
	//删除思维导图
	if($action == 'delete')
	{
		$user = $_GET['id'];
		$Mindname = $_GET['mind'];
		
		//将该思维导图的数据从数据库中删除
		
		$sql = "DELETE FROM `mind` WHERE AuthorID = '$user' and MindName = '$Mindname' ";
		mysql_query($sql);
		
		$sql = "DELETE FROM `nodeinfo` WHERE AuthorID = '$user' and MindName = '$Mindname'";
		mysql_query($sql);
		
		$sql = "DELETE FROM `editinfo` WHERE AuthorID = '$user' and MindName = '$Mindname' ";
		mysql_query($sql);
		
		$sql = "UPDATE `user` SET `MindSum` = MindSum - 1 WHERE `UserID` = '$user'";
		mysql_query($sql);
		
		header("Location:Userpage.php"); 	
		
	}
	
	//移除共享
	if($action == 'del_share')
	{
		$user = $_GET['id'];
		$Mindname = $_GET['mind'];
		$request = $_GET['request'];
		
		//将共享移除
		if($request == 'all')
		{
			//获取当前思维导图的共享用户，更新相关信息
			$sql = "SELECT ShareID FORM `sharegroup` WHERE MindName = '$Mindname' and AuthorID = '$user'";
			$result = mysql_query($sql);
	
			while($row = mysql_fetch_array($result,MYSQL_ASSOC))
			{
      			$users[]=$row;
			}
			
			 // 释放资源
    		mysql_free_result($result);
			
			for($i=0;$i<count($users);$i++)
			{
				$sql = "UPDATE `user` SET `ShareNum` = ShareNum - 1 WHERE `UserID` = '$users[$i]'";
				mysql_query($sql);
			}

			$sql = "DELETE FROM `sharegroup` WHERE MindName = '$Mindname' and AuthorID = '$user' ";
			mysql_query($sql);
			
			$sql = "UPDATE `user` SET `toShareNum` = toShareNum - 1 WHERE `UserID` = '$user'";
			mysql_query($sql);
			
		}
		else{ //移除指定用户
			$sql = "DELETE FROM `sharegroup` WHERE MindName = '$Mindname' and AuthorID = '$user' and ShareID = '$request' ";
			mysql_query($sql);
			
			$sql = "UPDATE `user` SET `ShareNum` = ShareNum - 1 WHERE `UserID` = '$request'";
			mysql_query($sql);
			
			//判断当前思维导图是否还有共享用户
			$sql = "SELECT ShareID FROM `sharegroup` WHERE MindName = '$Mindname' and AuthorID = '$user' ";
			$result = mysql_fetch_array(mysql_query($sql));
		
			//不存在共享用户
			if($result == FALSE)
			{
				$sql = "UPDATE `user` SET `toShareNum` = toShareNum - 1 WHERE `UserID` = '$user'";
				mysql_query($sql);
			}
			
			 // 释放资源
    		mysql_free_result($result);
			
		}
		header("Location:Userpage.php");
		
	}
	
	//共享思维导图
	if($action == 'share')
	{
		$user = $_GET['id'];
		$Mindname = $_GET['mind'];
		$request = $_GET['users'];
		
		//用户设置的分享用户组
		$temple = explode(',',$request); 
		//遍历其中有效的用户设置（即存在的用户（非作者ID））
		$sql = "SELECT `UserID` FROM `user` WHERE UserID <> '$user'";
		$result = mysql_query($sql);
	
		while($row = mysql_fetch_array($result,MYSQL_ASSOC))
		{
      		$users[]=$row;
		}
		 
		 // 释放资源
    	mysql_free_result($result);
		
		for($i=0;$i<count($temple);$i++)
		{
		
			for($j=0;$j<count($users);$j++)
			{
				if($temple[$i] == $users[$j]['UserID'])
					$share[] = $temple[$i];
			}
			
		}
		
		//判断当前思维导图是否被共享过
		$sql = "SELECT MindName FROM `sharegroup` WHERE MindName = '$Mindname' and AuthorID = '$user'";
		$result = mysql_fetch_array(mysql_query($sql));
		
		//如果没有
		if($result == FALSE)
		{
			$sql = "UPDATE `user` SET `toShareNum` = toShareNum + 1 WHERE `UserID` = '$user'";
			mysql_query($sql);
		}
		
		 // 释放资源
    	mysql_free_result($result);
			
		for($i=0;$i<count($share);$i++)
		{
			//判断当前用户是否已经被设置共享
			$sql = "SELECT * FROM `sharegroup` WHERE MindName = '$Mindname' and ShareID = '$share[$i]' and AuthorID = '$user' ";
			$result = mysql_fetch_array(mysql_query($sql));
			
			//如果没有
			if($result == FALSE)
			{
				$sql = "INSERT INTO `sharegroup`(`MindName`, `AuthorID`, `ShareID`) VALUES ('$Mindname','$user','$share[$i]')";
				mysql_query($sql);
			
				$sql = "UPDATE `user` SET `ShareNum` = ShareNum + 1 WHERE `UserID` = '$share[$i]'";
				mysql_query($sql);
			}
			
			
		}	
		
		header("Location:Userpage.php"); 
		
	}
	
	//编辑思维导图
	if($action == 'edit')
	{
		$user = $_GET['id'];
		$Mindname = $_GET['mind'];
		
		header("Location:getData.php?id=$user&mind=$Mindname&author=$user"); 
		
	}
	
	// 关闭连接
	mysql_close(); 
?>