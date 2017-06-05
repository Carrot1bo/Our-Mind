<?php
	session_start();
	error_reporting(E_ALL ^ E_DEPRECATED);
	
	$ID = $_GET['id'];
	$name = $_GET['mind'];
	$Author = $_GET['author'];
	
	mysql_connect('localhost', 'root', '08');
	mysql_select_db('our-mind');
	
	
	// 创建json字符串过程
	
		$mind = array();
		$mind['meta'] = array();
		$mind['meta']['name'] = "$name";
		$mind['meta']['author'] = "$Author";
		$mind['meta']['version'] = "1.0";
	
		$mind['format'] = 'node_array';
	
		$mind['data'] = array();
		
		//首先获取根节点root的信息
		$sql = "SELECT * FROM `nodeinfo` WHERE AuthorID = '$Author' and MindName = '$name' and NodeLevel ='0'";
		$result = mysql_query($sql);
		
		$res = mysql_fetch_array($result);
		$temp = array();
		$temp['id'] = $res['NodeID'];
		$temp['isroot'] = true;
		$temp['topic'] = $res['NodeText'];
		$mind['data'][] = $temp;
		
		 // 释放资源
   		 mysql_free_result($result);
		
		//首先Level=1的节点信息
		$sql = "SELECT * FROM `nodeinfo` WHERE AuthorID = '$Author' and MindName = '$name' and NodeLevel ='1'";
		$res = mysql_query($sql);
		
		if($res != FALSE)
		{
			while($row = mysql_fetch_array($res,MYSQL_ASSOC))
			{
				$node[]=$row;
			}
			
			 // 释放资源
   			 mysql_free_result($res);
			 
			for($i=0;$i<count($node);$i++)
			{
				$temp = array();
				$temp['id'] = $node[$i]['NodeID'];
				$temp['parentid'] = $node[$i]['ParentID'];
				$temp['topic'] = $node[$i]['NodeText'];
				$temp['direction'] = $node[$i]['Direction'];
				$mind['data'][] = $temp;
			};
		}
		
		//获取其他节点的信息
		$sql = "SELECT * FROM `nodeinfo` WHERE AuthorID = '$Author' and MindName = '$name' and NodeLevel <> '0' and NodeLevel <> '1'";
		$res = mysql_query($sql);
		
		if($res != FALSE)
		{
			while($row = mysql_fetch_array($res,MYSQL_ASSOC))
			{
				$node[]=$row;
			}
			
			// 释放资源
   			mysql_free_result($res);
			
			for($i=0;$i<count($node);$i++)
			{
				$temp = array();
				$temp['id'] = $node[$i]['NodeID'];
				$temp['parentid'] = $node[$i]['ParentID'];
				$temp['topic'] = $node[$i]['NodeText'];
				$mind['data'][] = $temp;
			};
		}
		
		// 关闭连接
		mysql_close();
		
		$json = json_encode($mind);	
		$_SESSION['data'] = $json;
		
		header("Location:EditMind.php?id=$ID&mind=$name&author=$Author&action=show"); 
	?>