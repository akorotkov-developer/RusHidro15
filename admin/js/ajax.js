
function ajaxSend(url, string, div_id, text)
{

	if (text)	{}
	else	{
		text = 'Подождите, идет загрузка...';
	}

  var ajaxObj=null;
  if(window.XMLHttpRequest)
  {
    ajaxObj=new XMLHttpRequest();
  }
  else if(window.ActiveXObject)
  {
    ajaxObj=new ActiveXObject("Microsoft.XMLHTTP");
  }
  else
  {
    return;
  }
 
  document.getElementById(div_id).innerHTML='<div class="ajax"><div>'+text+'</div></div>';

/*
			<script language="JavaScript" type="text/javascript">
				for (i = 0; (document.body.getElementsByTagName("select").item(i)); i++) {	
				document.body.getElementsByTagName("select").item(i).style.visibility = 'hidden';
				}
			</script>
*/


  ajaxObj.onreadystatechange = function()
  {
    if(ajaxObj.readyState==4)
    {
      document.getElementById(div_id).innerHTML=ajaxObj.responseText;

    }
  }
  ajaxObj.open('POST', url, true);
  ajaxObj.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;');
  ajaxObj.send(string);

}

