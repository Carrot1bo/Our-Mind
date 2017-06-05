// JavaScript Document
function Submit(){
		var div=document.createElement("div");
		div.style.position="absolute";
		div.style.left="200px";
		div.style.top="500px";
		div.style.width="1200px";
		div.style.height="300px";
		div.style.backgroundColor="black";
		div.style.filter="alpha(opacity=40)";
		div.style.opacity=.4;
		div.setAttribute("id","submit");
		var div2=document.createElement("div");
		var input1=document.createElement("input");
		input1.type="text";
		input1.value="";
		input1.setAttribute("id","info");
		div2.appendChild(input1);
		var input2=document.createElement("input");
		input2.type="button";
		input2.value="提交";
		input2.onclick=subs;
		div2.appendChild(input2);
		var input3=document.createElement("input");
		input3.type="button";
		input3.value="取消";
		input3.onclick=cancel;
		div2.appendChild(input3);
		var c=document.createElement("center");
		c.appendChild(div2);
		div.appendChild(c);
		document.body.appendChild(div);
}
/*
function subs(){
	var a=document.getElementById("info").value;
	window.location.href="http://www.baidu.com/s?wd="+a;
}
*/
function cancel(){
	var p=document.getElementById("submit");
	document.body.removeChild(p);
}