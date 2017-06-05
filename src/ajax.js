function ajax(user, name, id,fnSucc)
{
	var url =  "http://localhost:8081/Our-Mind/Authorize.php?user="+user+"&name="+name+"&node="+id;

	//1.创建Ajax对象
	var oAjax=null;
	
	if(window.XMLHttpRequest)
	{
		oAjax=new XMLHttpRequest();
	}
	else
	{
		oAjax=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	//2.连接服务器
	oAjax.open('GET', url, false);
	
	//3.发送请求
	oAjax.send();
	
	//4.接收服务器的返回
	oAjax.onreadystatechange=function ()
	{
		if(oAjax.readyState == 4)	//完成
		{
			if(oAjax.status == 200)	//成功
			{
				fnSucc(oAjax.responseText);
			}
			else
			{
					return false;
			}
		}
	};
}