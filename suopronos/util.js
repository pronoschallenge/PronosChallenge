// trim function
function trim(str) {
   return str.replace(/^\s*|\s*$/g,"");
}

document.onmousemove = moveDiv;
var currentId;

function showHideDiv(id)
{
	currentId = id;
	if(document.getElementById(id).style.display == 'block')
	{
		document.getElementById(id).style.display = 'none';
		document.getElementById("img"+id).src = 'images/plus.gif';
	}
	else
	{
		document.getElementById(id).style.display = 'block';
		document.getElementById("img"+id).src = 'images/minus.gif';
	}
}

function showDiv(id)
{
	currentId = id;
	document.getElementById(id).style.display = 'block';
}

function moveDiv(e)
{
	if (navigator.appName=="Microsoft Internet Explorer")
	{
		var x = event.x + document.body.scrollLeft;	
		var y = event.y + document.body.scrollTop;
	}
	else
	{
		var x =  e.pageX;
		var y =  e.pageY;
	}
	if(document.getElementById(currentId)!=null)
	{
		document.getElementById(currentId).style.left = x+15; 
		document.getElementById(currentId).style.top  = y;
	}
}

function hideDiv(id)
{
	currentId = '';
	document.getElementById(id).style.display = 'none';
}
