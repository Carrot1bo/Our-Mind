// JavaScript Document
function tabSwitch(id)
{
	var oDiv=document.getElementById(id);
	var aBtn=oDiv.getElementsByTagName('input');
	var aDiv=oDiv.getElementsByTagName('div');
	var i=0;
	
	for(i=0;i<aBtn.length;i++)
	{
		aBtn[i].index=i;
		aBtn[i].onclick=function ()
		{
			for(i=0;i<aBtn.length;i++)
			{
				aBtn[i].className='';
				aDiv[i].style.display='none';
			}
			this.className='active';
			aDiv[this.index].style.display='block';
		};
	}
}