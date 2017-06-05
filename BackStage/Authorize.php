
<?php

	error_reporting(E_ALL ^ E_DEPRECATED);
	
	$user = $_POST['user'];
	$author = $_POST['author'];
	$mind = $_POST['name'];
	$request = $_POST['node'];
	$reply = '';
	
	/*
		$reply = '0' : 当前节点正在被编辑或者当前节点下的子树有用户正在编辑，申请权限失败；
		$reply = '1' : 申请节点编辑权限成功，用户获得当前节点及其子树的编辑权限；
		$reply = '2' : 释放节点权限操作成功。
		*/
	
	$sql = "SELECT * FROM `nodeinfo` WHERE  MindName = '$mind' and AuthorID = '$author' and NodeID = '$request' ";
	
	mysql_connect('localhost', 'root', '08');
	mysql_select_db('our-mind');
	
	$node=  mysql_fetch_array(mysql_query($sql)); 
	
	if($node['isLock'] == 0)
	{
		//计算当前申请权限的节点 - 所有的子节点
		$sql = "SELECT MAX(NodeLevel) FROM `nodeinfo` WHERE MindName = '$mind' and AuthorID = '$author' ";
		$res = mysql_fetch_array(mysql_query($sql));
		$lvMAX = $res[0];
		
		$sql = "SELECT * FROM `nodeinfo` WHERE  MindName = '$mind' and AuthorID = '$author' and ParentID = '{$node['NodeID']}' ";
		
		$res = mysql_query($sql);
		
		$row = mysql_fetch_array($res,MYSQL_ASSOC);
		
		//当前节点下无子节点
		if($row == FALSE)
		{
			//当前节点，请求成功
			 $sql = "INSERT INTO `editinfo`(`AuthorID`, `EditerID`, `MindName`, `NodeID`,`isBegin`) VALUES ('{$node['AuthorID']}','$user','$mind','$request','1')";
	
			mysql_query($sql);
			
			$sql = "UPDATE `nodeinfo` SET isLock = 1 WHERE NodeID = '$request' and MindName = '$mind' and AuthorID = '$author' ";
			mysql_query($sql);
			
			$reply = '1';
		}
		else{//当前节点存在子节点
			
				$temple[]=$row;
				$result[]=$row;
				
			while($row = mysql_fetch_array($res,MYSQL_ASSOC))
			{
				$temple[]=$row;
				$result[]=$row;
			}
			
			// 释放资源
   			mysql_free_result($res);
			
			for($i=$node['NodeLevel']+1;$i<$lvMAX;$i++)
			{
				
				for($j=0;$j<count($temple);$j++)
				{
					$sql = "SELECT * FROM `nodeinfo` WHERE  MindName = '$mind' and AuthorID = '$author' and ParentID = '{$temple[$j]['NodeID']}' ";
					$res = mysql_query($sql);
					
					$row = mysql_fetch_array($res,MYSQL_ASSOC);
					
					if($row == FALSE) continue; //该子节点无child 跳过
					
						$tem[]=$row;
						$result[]=$row;
					while($row = mysql_fetch_array($res,MYSQL_ASSOC))
					{
						$tem[]=$row;
						$result[]=$row;
					}
				}
				if($row == FALSE) continue;
				
				// 释放资源
   				mysql_free_result($res);
				
				$temple = $tem;
			}
	
			//判断这些子节点是否已经被申请，若是没有被占用，则返回申请成功；否则，返回失败。
				for($i=0;$i<count($result);$i++)
				{
					if($result[$i]['isLock'] == 1)
					{
						$reply = '0';	
					}
				}
			//当前节点及其所有子节点均处于可申请状态（即申请成功）
			if($reply != '0')
			{
				
			
				//当前节点，请求成功
				 $sql = "INSERT INTO `editinfo`(`AuthorID`, `EditerID`, `MindName`, `NodeID`,`isBegin`) VALUES ('{$node['AuthorID']}','$user','$mind','$request','1')";
		
				mysql_query($sql);
				
				$sql = "UPDATE `nodeinfo` SET isLock = 1 WHERE NodeID = '$request' and MindName = '$mind' and AuthorID = '$author' ";
				mysql_query($sql);
				
				
				//处理当前的节点的子节点
					for($i=0;$i<count($result);$i++)
					{
						$sql = "INSERT INTO `editinfo`(`AuthorID`, `EditerID`, `MindName`, `NodeID`,`isBegin`) VALUES ('{$result[$i]['AuthorID']}','$user','$mind','{$result[$i]['NodeID']}','0')";
						mysql_query($sql);
						
						$sql = "UPDATE `nodeinfo` SET isLock = 1 WHERE NodeID = '{$result[$i]['NodeID']}' and MindName = '$mind' and AuthorID = '$author'";
						mysql_query($sql);
					}
				$reply = '1';
			}
			/*
			//统计当前用户可编辑的所有节点。
			$sql = "SELECT NodeID FROM `editinfo` WHERE  EditerID = '$user' and MindName = '$mind'";
			
			$res = mysql_query($sql);
			while($row = mysql_fetch_array($res,MYSQL_ASSOC))
			{
				$result[]=$row;
			}
			
			//将二维数组转化为一维
			$authorize = array();
			
			for($i=0;$i<count($result);$i++)
			{
				$authorize[$i] = $result[$i]['NodeID'];
			}
			*/
		
		}
		
		
	}
	else{ //请求的节点被锁
		
		$sql = "SELECT * FROM `editinfo` WHERE  MindName = '$mind' and NodeID = '$request' and EditerID = '$user'";
		$res = mysql_fetch_array(mysql_query($sql));
		
		//当前节点被其他用户编辑
		if($res == FALSE) 
		{
			$reply = '0';
		}
		else{//释放节点编辑权限过程
			
			//释放当前节点
			$sql = " DELETE FROM `editinfo` WHERE EditerID = '$user' and MindName = '$mind' and NodeID = '$request' ";
			mysql_query($sql);
			
			$sql = "UPDATE `nodeinfo` SET isLock = 0 WHERE NodeID = '$request' and MindName = '$mind' and AuthorID = '$author' ";
			mysql_query($sql);
			
		//释放当前节点的所有子节点
			//计算当前要释放权限的节点 - 所有的子节点
			$sql = "SELECT MAX(NodeLevel) FROM `nodeinfo` WHERE MindName = '$mind' and AuthorID = '$author'";
			$res = mysql_fetch_array(mysql_query($sql));
			$lvMAX = $res[0];
			
			$sql = "SELECT * FROM `nodeinfo` WHERE  MindName = '$mind' and AuthorID = '$author' and ParentID = '{$node['NodeID']}' ";
			
			$res = mysql_query($sql);
			$row = mysql_fetch_array($res,MYSQL_ASSOC);
			
			//该节点下无子节点
			if($row == FALSE)
			{
				$reply = '2';
			}
			else{//该节点下存在子节点
				
					$temple[]=$row;
					$result[]=$row;
				while($row = mysql_fetch_array($res,MYSQL_ASSOC))
				{
					$temple[]=$row;
					$result[]=$row;
				}
				
				// 释放资源
   				mysql_free_result($res);
				
				for($i=$node['NodeLevel']+1;$i<$lvMAX;$i++)
				{
					
					for($j=0;$j<count($temple);$j++)
					{
						$sql = "SELECT * FROM `nodeinfo` WHERE  MindName = '$mind' and AuthorID = '$author' and ParentID = '{$temple[$j]['NodeID']}' ";
						$res = mysql_query($sql);
						
						$row = mysql_fetch_array($res,MYSQL_ASSOC);
					
						if($row == FALSE) continue; //该子节点无child 跳过
						
							$temple2[]=$row;
							$result[]=$row;
						while($row = mysql_fetch_array($res,MYSQL_ASSOC))
						{
							$temple2[]=$row;
							$result[]=$row;
						}
						
					}
					
					if($row == FALSE) continue;
					
					// 释放资源
   					mysql_free_result($res);
					
					$temple = $temple2;
				}
				
				//开始释放所有子节点的权限
				for($i=0;$i<count($result);$i++)
				{
					$sql="DELETE FROM `editinfo` WHERE EditerID='$user' and MindName='$mind' and NodeID='{$result[$i]['NodeID']}' ";
					mysql_query($sql);
				
					$sql = "UPDATE `nodeinfo` SET isLock = 0 WHERE NodeID = '{$result[$i]['NodeID']}' and MindName = '$mind' and AuthorID = '$author'";
					mysql_query($sql);
				}
				
				$reply = '2';
			}
		}
		
	}
	
	// 关闭连接
	mysql_close();
	
	echo $reply;			//返回 回应信息
?>