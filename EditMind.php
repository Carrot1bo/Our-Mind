<!doctype html>
<?php 
	session_start();
?>
<html>
<head>
<meta charset="utf-8">
<title>Editing</title>

<link type="text/css" rel="stylesheet" href="css/jsmind.css" />

<script type="text/javascript" src="src/jsmind.js"></script>
<script type="text/javascript" src="src/jquery-3.0.0.js"></script>

</head>

<body>
<div id="jsmind_container" style="width:1500px;height:1500px">
</div>


<?php
//载入思维导图信息
	$ID = $_GET['id'];
	$name = $_GET['mind'];
	$Author = $_GET['author'];
	$action = $_GET['action'];
	if($action == 'create')
	{
		$mind = $_SESSION['NewMind'];
	}
	if($action == 'show')
	{
		$mind = $_SESSION['data'];
	}

	echo "<script type='text/javascript'>
	
		var mind = $mind;
		var id = '$ID';
		var Mindname = '$name';
		var author_id = '$Author';
		</script>
		"
		?>
<script type='text/javascript'>
		var options = {                   
			container:'jsmind_container', 
			editable:true,                
			theme:'orange'                
		};
		var jm = new jsMind(options);
		
		jm.show(mind);
		
		setInterval("display()",5000);
		setInterval("Upload()",5000);
		
		function display()
		{
			
			$.post("Display.php",
					{user:id,author:author_id,name:Mindname},
					function(data){
						
						var i = 0;
					//	alert(data[i]['NodeID']);
						for(i=0;i<data.length;i++)
						{
							if(data[i]['NodeID'] == '')
							{
								continue;
							}
							else if(data[i]['NodeLevel'] == '0')
							{
								jm.update_node(data[i]['NodeID'], data[i]['NodeText']);
							}
							else{
								jm.remove_node(data[i]['NodeID']);
								jm.add_node(data[i]['ParentID'], data[i]['NodeID'], data[i]['NodeText'],'');
						    }
						}
					},
					"json");		
					
		}
		
		function Upload()
		{
			var UP ='[';
			$.post("Upload.php",
				    {user:id,author:author_id,name:Mindname,action:'request',data:''},
					function(data){
						
						var i = 0;
						var j = 0;
						//拼接json字符串过程
						//遍历用户拥有权限的所有节点信息
						for(i=0;i<data.length;i++)
						{
							var node = jm.get_node(data[i]['NodeID']);
							if(!node['isroot'])
							{
								var info = '{"NodeID":"'+node['id']+'","NodeText":"'+node['topic']+'","ParentID":"'+node['parent']['id']+'"},';
							}
							else{
								var info = '{"NodeID":"'+node['id']+'","NodeText":"'+node['topic']+'","ParentID":"NULL"},'
							}
							UP = UP + info;
							
						//	UP.push(info);
							
							var child = node['children'];
							for(j=0;j<child.length;j++)
							{
								var newInfo = '{"NodeID":"'+child[j]['id']+'","NodeText":"'+child[j]['topic']+'","ParentID":"'+child[j]['parent']['id']+'"},';
								//UP.push(JSON.parse(newInfo));	
								//UP.push(newInfo);
								UP = UP + newInfo ;
								
								if(child[j]['children'])
								{
									var temple1 = child[j]['children'];
									for(var k = 0;k<temple1.length;k++)
									{
										var newInfo = '{"NodeID":"'+temple1[k]['id']+'","NodeText":"'+temple1[k]['topic']+'","ParentID":"'+temple1[k]['parent']['id']+'"},';	
										UP = UP + newInfo ;
										
										if(temple1[k]['children'])
										{
											var temple2 = temple1[k]['children'];
											for(var l = 0;l<temple2.length;l++)
											{
												var newInfo = '{"NodeID":"'+temple2[l]['id']+'","NodeText":"'+temple2[l]['topic']+'","ParentID":"'+temple2[l]['parent']['id']+'"},';	
												UP = UP + newInfo ;
												
												if(temple2[l]['children'])
												{
													var temple3 = temple2[l]['children'];
													for(var m = 0;m<temple3.length;m++)
													{
														var newInfo = '{"NodeID":"'+temple3[m]['id']+'","NodeText":"'+temple3[m]['topic']+'","ParentID":"'+temple3[m]['parent']['id']+'"},';	
														UP = UP + newInfo ;
													}
												}
											}
											
										}
										
									}
								}
								
							}
						}
						UP = UP + ']';	
						UP=UP.replace(',]',']');
						
						//如果需要上传的数据不为空，则上传数据
						if(UP != '[]')
						{
							
							$.post("Upload.php",
									{user:id,author:author_id,name:Mindname,action:'upload',data:UP}
							);	
						}
						
					},
					"json"
			);
			
		}
			
		document.onkeydown = function(ev)
		{
			var oEvent = ev || event;
			 
			//如果F4按下，发生的事件处理
			if(oEvent.keyCode == 115)
			{
				var selected = jm.get_selected_node();
				
				var selected_id = selected.id;
				
				if(!selected_id){
					alert('please select a node first.');
					return;
				}

				$.post("Authorize.php",
					{user:id,name:Mindname,author:author_id,node:selected_id},
					function(data){
						var reply = data;
						alert(data);
						if(reply == 1)
						{
							<?php
							echo "jm.enable_edit();
								  jm.set_node_color(selected_id, '#CCC', null);";
							?>
						}
						else if(reply == 2)
						{
							<?php
							echo "jm.set_node_color(selected_id, '#f1c40f', null);";
							?>
						}
						else{
							alert("当前节点正在被编辑，请求失败！");
						}
					});

			}
		}
	</script>
</body>
</html>