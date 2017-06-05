<?php
	error_reporting(E_ALL ^ E_DEPRECATED);
	$user = '123';//$_POST['user'];
	$mind = 'Mind3';//$_POST['name'];
	$author = '123';//$_POST['author'];
	
	mysql_connect('localhost', 'root', '08');
	mysql_select_db('our-mind');
	
	//取当前用户有权限的Level最小的节点（即最靠近根节点）
	/*$sql = "SELECT MIN(NodeLevel) FROM `nodeinfo` WHERE EXISTS (SELECT * FROM `editinfo` WHERE nodeinfo.AuthorID = editinfo.AuthorID and nodeinfo.MindName = editinfo.MindName and nodeinfo.NodeID = editinfo.NodeID and editinfo.EditerID = '$user')";*/
	$sql = "SELECT * FROM `editinfo` WHERE AuthorID = '$author' and MindName = '$mind' and EditerID = '$user' and isBegin = '1'";
	$res = mysql_fetch_array(mysql_query($sql));
	
	//当前用户没有任何节点的编辑权限
	if($res[0] == NULL)
	{
		$sql = "SELECT * FROM `nodeinfo` WHERE MindName = '$mind' and AuthorID = '$author' ORDER BY NodeLevel ASC";
		$res = mysql_query($sql);
		
		while($row = mysql_fetch_array($res,MYSQL_ASSOC))
		{
			$result[]=$row;
		}
		
		// 释放资源
   		mysql_free_result($res);
		
		$json = json_encode($result);	
		echo $json;
		
	}
	//当前用户有节点的编辑权限
	else{

		/*$sql ="SELECT * FROM `nodeinfo` WHERE EXISTS (SELECT * FROM `editinfo` WHERE nodeinfo.AuthorID = editinfo.AuthorID and nodeinfo.MindName = editinfo.MindName and nodeinfo.NodeID = editinfo.NodeID and editinfo.EditerID = '$user' and nodeinfo.NodeLevel = '$res[0]')";*/
		//取节点isBegin = 1
		$sql = "SELECT * FROM `editinfo` WHERE AuthorID = '$author' and MindName = '$mind' and EditerID = '$user' and isBegin = '1'";
		$res = mysql_query($sql);
		
		while($row = mysql_fetch_array($res,MYSQL_ASSOC))
		{
			$temple[]=$row;
		}
		
		// 释放资源
   		mysql_free_result($res);
			
		//x:取当前用户没有权限的所有节点
		
		$sql = "SELECT * FROM `nodeinfo` WHERE MindName = '$mind' and AuthorID = '$author' and NodeID NOT IN (SELECT NodeID FROM `editinfo` WHERE editinfo.AuthorID = '$author' and editinfo.MindName = '$mind' and editinfo.EditerID = '$user') ORDER BY NodeLevel ASC";
		$res = mysql_query($sql);
		
		while($row = mysql_fetch_array($res,MYSQL_ASSOC))
		{
			$result[]=$row;
		}

		// 释放资源
   		mysql_free_result($res);
		
		//y:遍历出用户没有权限并且其他用户也没有权限更改的节点。
		$j = 0;
		for($i=0;$i<count($temple);$i++)
		{
			$child = $temple[$i]['NodeID'];
			
			$sql="SELECT * FROM `nodeinfo` WHERE AuthorID = '$author' and MindName = '$mind' and NodeID = '$child'";		
			
			$res = mysql_query($sql);
			$child = mysql_fetch_array($res,MYSQL_ASSOC);
			
			$parent = $child;
			
			while($parent['NodeLevel'] != '0')
			{
				
				$sql="SELECT * FROM `nodeinfo` WHERE AuthorID = '$author' and MindName = '$mind' and NodeID = '{$parent['ParentID']}'";	
				
				$res = mysql_query($sql);
				$Just[$j] = mysql_fetch_array($res,MYSQL_ASSOC);
				
				// 释放资源
   				mysql_free_result($res);
				
				$parent = $Just[$j];
				$j = $j + 1;		
			}
		}
		
		//x-y
		for($i=0;$i<count($Just);$i++)
		{
			for($j=0;$j<count($result);$j++)
			{
				if($Just[$i]['NodeID'] == $result[$j]['NodeID'])
				{
					$result[$j]['NodeID'] = '';	
				}
			}
		}
		
		$json = json_encode($result);	
		echo $json;
	}
	
	// 关闭连接
	mysql_close();
?>
