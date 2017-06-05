<?php
	error_reporting(E_ALL ^ E_DEPRECATED);
	
	$user = $_POST['user'];
	$mind = $_POST['name'];
	$author = $_POST['author'];
	$action = $_POST['action'];
	$data = $_POST['data'];
	
	$json = json_decode($data);
	$data = $json;
	
	if($action == 'upload')
	{
		
		mysql_connect('localhost', 'root', '08');
		mysql_select_db('our-mind');
		//更新数据库中用户拥有权限的节点数据
		
		//统计当前用户可编辑的所有节				
		$sql = "SELECT NodeID FROM `editinfo` WHERE  EditerID = '$user' and MindName = '$mind' and AuthorID = '$author'";
		$res = mysql_query($sql);
		while($row = mysql_fetch_array($res,MYSQL_ASSOC))
		{
			$ALL[]=$row;
		}
		
		// 释放资源
   		mysql_free_result($res);
		
		//插入新数据之前，先记录isBegin=1的父节点信息（方便插入数据时确定节点的isBegin）
		$sql = "SELECT * FROM `editinfo` WHERE MindName = '$mind' and AuthorID = '$author' and EditerID = '$user' and isBegin = '1' ";
		$res = mysql_query($sql);
			
		while($row = mysql_fetch_array($res,MYSQL_ASSOC))
		{
			$result[]=$row;
		}
		
		// 释放资源
   		mysql_free_result($res);
		
		for($i=0;$i<count($result);$i++)
		{
			$sql = "SELECT ParentID FROM `nodeinfo` WHERE MindName = '$mind' and AuthorID = '$author' and NodeID = '{$result[$i]['NodeID']}'";
			$res_sql = mysql_query($sql);
			
			$res = mysql_fetch_array($res_sql);
			$isBegin_Parent[] = $res[0];
			
			// 释放资源
   			mysql_free_result($res_sql);			
		}
		
		//清除旧数据
		for($i=0;$i<count($ALL);$i++)
		{
			$sql = "DELETE FROM `nodeinfo` WHERE  MindName = '$mind' and AuthorID = '$author' and NodeID = '{$ALL[$i]['NodeID']}' ";	
			mysql_query($sql);
		}
		
		$sql = "DELETE FROM `editinfo` WHERE  EditerID = '$user' and MindName = '$mind' and AuthorID = '$author'";	
		mysql_query($sql);
		
		
		//插入新数据
		
		for($i=0;$i<count($data);$i++)
		{
			//计算当前节点的NodeLevel
			if($data[$i]->ParentID == 'NULL')
			{
				$level = 0;
			}
			else{
				
				$sql = "SELECT NodeLevel FROM `nodeinfo` WHERE  MindName = '$mind' and AuthorID = '$author' and NodeID = '{$data[$i]->ParentID}' ";
				$res = mysql_fetch_array(mysql_query($sql));
				$level = $res[0] + 1;
			}
			
			//计算当前节点的isBegin
			$Begin = '0';
			for($j=0;$j<count($isBegin_Parent);$j++)
			{
				if($data[$i]->ParentID == $isBegin_Parent[$j]){
					$Begin = '1';
				}
			}
			
			
			//开始向数据库同步
			$sql = "INSERT INTO `nodeinfo`(`AuthorID`, `MindName`, `NodeID`, `NodeLevel`,`ParentID`,`NodeText`,`isLock`,`Direction`) VALUES ('$author','$mind','{$data[$i]->NodeID}','$level','{$data[$i]->ParentID}','{$data[$i]->NodeText}','1','')";
			mysql_query($sql);
			
			$sql = "INSERT INTO `editinfo`(`AuthorID`, `EditerID`, `MindName`, `NodeID`,`isBegin`) VALUES ('$author','$user','$mind','{$data[$i]->NodeID}','$Begin')";
			mysql_query($sql);
			
			
			
		}
		
		// 关闭连接
		mysql_close();
			
	}
	
	//请求数据
	if($action == 'request')
	{
		
		mysql_connect('localhost', 'root', '08');
		mysql_select_db('our-mind');
		
		/*
			获取当前用户所拥有权限的所有节点的Begin
				isBegin:表示是否为用户开始申请权限的起始节点
			*/
		$sql = "SELECT NodeID FROM `editinfo` WHERE MindName = '$mind' and AuthorID = '$author' and EditerID = '$user' and isBegin = '1' ";
		$res = mysql_query($sql);
			
		while($row = mysql_fetch_array($res,MYSQL_ASSOC))
		{
			$result[]=$row;
		}
		
		// 释放资源
   		mysql_free_result($res);
		
		// 关闭连接
		mysql_close();
		
	//	var_dump($result);
		
		//编码并返回数据
		$json = json_encode($result);	
		echo $json;
	}
?>