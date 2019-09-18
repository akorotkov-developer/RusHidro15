ETSa:2:{s:7:"0reduce";i:2;s:4:"main";a:36:{s:5:"2:698";s:672:"
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>

<HEAD>
<TITLE>Система администрирования сайта - SoftMajor - 1</TITLE>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
<LINK href="styles/main.css" rel="stylesheet" type="text/css">
<LINK href="styles/styles.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="styles/dropfile.css"/>
<script type="text/javascript" src="js/jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script>
<script type="text/javascript" src="js/scripts.js?v=2"></script>
<script language="javascript">
_jQuery = jQuery.noConflict(true);
<!--
";s:6:"2:5806";s:6338:"nvarsdivInv='';nnnXYs=s{x:0,sy:0};nnfunctionsgetElementPosition(elemId)n{nnssssvarselem_1s=sdocument.getElementById(elemId);nnt//alerts(elem_1);nt//return;nnssssvarsw_1s=selem_1.offsetWidth;nssssvarsh_1s=selem_1.offsetHeight;nnssssvarsl_1s=s0;nssssvarst_1s=s0;nnsssswhiles(elem_1)nssss{nssssssssl_1s+=selem_1.offsetLeft;nsssssssst_1s+=selem_1.offsetTop;nsssssssselem_1s=selem_1.offsetParent;nssss}nnssssreturns{"left":l_1,s"offsetY":t_1,s"width":sw_1,s"height":h_1};n}nnnnfunctionsmousePageXY(e)n{nssvarsxs=s0,sys=s0;nnssifs(!e)ses=swindow.event;nnssifs(e.pageXs||se.pageY)nss{nssssxs=se.pageX;nssssys=se.pageY;nss}nsselsesifs(e.clientXs||se.clientY)nss{nssssxs=se.clientXs+s(document.documentElement.scrollLefts||sdocument.body.scrollLeft)s-sdocument.documentElement.clientLeft;nssssys=se.clientYs+s(document.documentElement.scrollTops||sdocument.body.scrollTop)s-sdocument.documentElement.clientTop;nt//ys=sys+ssdocument.body.scrollTop;nss}nnt//offYs=se.offsetY;nnssreturns{"x":x,s"y":y};n}nnnnnfunctionstmove(n)n{nntoffXYs=sgetElementPosition(n.id);nnts$('divcursor').style.tops=sXY.y;s//s+sdocument.body.scrollTops-soffXY.offsetY;ntsxxs=sXY.xs-s27;ntsifs(xxs<s15)sxxs=s15;nntyys=sXY.ys-soffXY.offsetY;nntsifs(yys<s7)t{s$('im').src=s'img/bg-top.gif';s$('divcursor').style.tops=sXY.ys-s2;s}ntsifs(yys>s15)t{s$('im').src=s'img/bg-bottom.gif';s$('divcursor').style.tops=sXY.ys-s10;ss}nts$('divcursor').style.lefts=xx;nnt/*nts$('divcursor').style.tops=sevent.ys+sdocument.body.scrollTops-sevent.offsetY+3;ntsxxs=sevent.xs-s27;ntsifs(xxs<s15)sxxs=s15;ntsifs(event.offsetYs<s7)$('im').src=s'img/bg-top.gif';ntsifs(event.offsetYs>s7s&&sevent.offsetY<20)$('im').src=s'img/bg-bottom.gif';nts$('divcursor').style.lefts=xx;nts*/n}nnfunctionsmove()n{nsscontexthide();nss$('divcursor').style.displays=s'';nss$('mblockparent').values=s$('blockid').value;n}nnn//s---------------------------------------n//sЦентральнаяsфункцияsопределения.n//s---------------------------------------nfunctions$(id)n{nsssreturnsdocument.getElementById(id);n}nn//s---------------------------------------n//sСтилевыеsфункции.n//s---------------------------------------nfunctionspTrOver(n)n{ntn.style.backgroundColors=s'#f7f7f7';ntwascolors=sn.style.color;ntn.style.colors=s'000000';n}nfunctionspTrOut(n)n{ntn.style.backgroundColors=s'';ntn.style.colors=swascolor;n}n//s---------------------------------------n//sКонтекстноеsменю.n//s---------------------------------------nfunctionscontext(id)n{nss$('blockid').values=s$(id).id;s//sВыдаёмsнаsредактированиеsблокsidnss$('popup').style.lefts=sXY.x-10;//s+sdocument.body.scrollTop;nss$('popup').style.tops=sXY.y-10;nss$('popup').style.displays=s'';nss/*nss$('blockid').values=s$(id).id;s//sВыдаёмsнаsредактированиеsблокsidnss$('popup').style.lefts=sy-40;nss$('popup').style.tops=sx-10;nss$('popup').style.displays=s'';nss*/n}nfunctionscontexthide()n{nss$('popup').style.displays=s'none';n}n//s---------------------------------------n//sСозданиеsновогоsблока.n//s---------------------------------------nfunctionsadd_new()n{nnslocation.href='item_edit.php?blockparent='+$('blockparent').value+'&template='+$('template').value+'&action=add&blockid='+$('blockid').value+'&parent='+$('parent').valuen}nfunctionsedit()n{nslocation.href='item_edit.php?blockparent='+$('blockparent').value+'&template='+$('template').value+'&action=edit&blockid='+$('blockid').value+'&parent='+$('parent').valuen}nfunctionshide_block()n{nslocation.href='item_edit.php?blockparent='+$('blockparent').value+'&template='+$('template').value+'&action=hide&blockid='+$('blockid').value+'&parent='+$('parent').valuen}nfunctionsdel()n{nsif(window.confirm('Выsдействительноsхотитеsудалитьsблок?'))location.href='item_edit.php?blockparent='+$('blockparent').value+'&template='+$('template').value+'&action=del&blockid='+$('blockid').value+'&parent='+$('parent').valuen}nnfunctionspress(id)n{nntoffXYs=sgetElementPosition(id);ntyys=sXY.ys-soffXY.offsetY;nnns$('blockid').values=s$(id).id;s//sВыдаёмsнаsредактированиеsблокsidnsif($('divcursor').style.display==''s&&s$('mblockparent').value!==id)ns{nsstsifs(yys<s7)location.href='item_edit.php?blockparent='+$('blockparent').value+'&template='+$('template').value+'&action=mup&copymove=0&mblockparent='+$('mblockparent').value+'&blockid='+$('blockid').value+'&parent='+$('parent').valuentsifs(yys>s15)slocation.href='item_edit.php?blockparent='+$('blockparent').value+'&copymove=1&template='+$('template').value+'&action=mdown&mblockparent='+$('mblockparent').value+'&blockid='+$('blockid').value+'&parent='+$('parent').valuens}nnnnns$('mblockparent').value='';nss$('divcursor').style.display='none';n}n//-->n


document.onmousemove = function(event) {
      var event = event || window.event;
	  XY = mousePageXY(event);
      //document.body.innerHTML = "x = " + mousePageXY(event).x + ", y = " + mousePageXY(event).y;
}
function resize() {
	if (parent.document.getElementById('itemframe";s:16:"4:5815:template:";s:0:"";s:6:"2:5862";s:46:"'))
	parent.document.getElementById('itemframe";s:16:"4:5871:template:";s:0:"";s:6:"2:6370";s:498:"').height = $('divtable').offsetHeight+150;
}
</script>

</HEAD>
<body  onload="resize();" bgcolor="#e4e4e4" style="background: #fff url(../img/e.gif); width:100%; min-width:400px;">



<table class="center" cellpadding="0" cellspacing="0" border="0">
	<tr>
    	<td class="cont"  style="padding:5px">
			<div id="div_cont" style="padding:0px">

<table class="in_cont" cellpadding="0" cellspacing="0" border="0" style="width:100%; padding-top:0px; margin-top:0px"  >
<tr>
<td align="center" class="";s:12:"16:6384.1://";a:1:{s:15:"32:6384:packet:";a:1:{s:6:"2:6392";s:8:"dropzone";}}s:6:"2:6490";s:95:"" style="width:100%; padding:0px" valign="top">

<div class="with_page"  style="padding:0px" >
";s:12:"16:6504.1://";a:1:{s:15:"32:6504:packet:";a:1:{s:6:"2:6594";s:90:"<div id="dropzone"><span>перетащите изображения сюда</span></div>";}}s:6:"2:6740";s:143:"



<form method="get" id="itemsform">
<input type="hidden" name="blockid" id="blockid">
<input type="hidden" name="parent" id="parent" value="";s:14:"4:6747:parent:";s:0:"";s:6:"2:6809";s:61:"">
<input type="hidden" name="template" id="template" value="";s:16:"4:6818:template:";s:0:"";s:6:"2:6886";s:67:"">
<input type="hidden" name="blockparent" id="blockparent" value="";s:19:"4:6898:blockparent:";s:0:"";s:6:"2:6963";s:64:"">
<input type="hidden" name="mblockparent" id="mblockparent" >
";s:12:"16:6977.1://";a:1:{s:15:"32:6977:packet:";a:1:{s:6:"2:7026";s:49:"<input type="hidden" name="packet" value="true"/>";}}s:6:"2:7191";s:162:"

<div class="blue">

	<p style="text-align:left">
		<input type="button" value="Создать вложенный блок" onClick="add_new(); dropzoneSize();">";s:12:"16:7205.1://";a:1:{s:15:"32:7205:packet:";a:1:{s:6:"2:7265";s:60:" или перетащите изображения сюда";}}s:6:"2:7389";s:121:"
	</p>
<TABLE  style="width:100%; margin:0; padding:0" border="0" cellspacing="0" cellpadding="1" id="divtable">
<tr><td>";s:9:"2048:7393";a:2:{s:4:"test";a:2:{s:6:"2:7394";s:1:"!";s:19:"4:7406:blockparent:";s:0:"";}s:4:"true";a:1:{s:6:"2:7498";s:90:"Для создания вложений необходимо сохранить блок ";}}s:6:"2:7514";s:11:"</td></tr>
";s:9:"2048:7518";a:2:{s:4:"test";a:1:{s:19:"4:7530:blockparent:";s:0:"";}s:4:"true";a:5:{s:6:"2:7533";s:1:"
";s:18:"32:7548:blocklist:";a:7:{s:6:"2:7634";s:86:"
<tr>
	<th nowrap="nowrap" style="background: #e7e7e7; width: 30px !important"></th>
	";s:9:"2048:7638";a:2:{s:4:"test";a:1:{s:12:"16:7651.1://";a:1:{s:18:"4:7651:superadmin:";s:0:"";}}s:4:"true";a:1:{s:6:"2:7701";s:48:"
	<th style="background: #e7e7e7;">
	id
	</th>
	";}}s:6:"2:7707";s:1:"
";s:17:"16:7722:table_hr:";a:5:{s:6:"2:7771";s:49:"
<th nowrap="nowrap" style="background: #e7e7e7; ";s:14:"32:7782:_last:";a:1:{s:6:"2:7791";s:9:"width:90%";}s:6:"2:7800";s:3:"" >";s:13:"4:7806:title:";s:0:"";s:6:"2:7814";s:7:"
</th>
";}s:6:"2:7887";s:66:"
<th nowrap="nowrap" style="background:#e7e7e7">&nbsp;</th>
</tr>
";s:18:"16:7903:blocklist:";a:13:{s:6:"2:7912";s:9:"
<tr id="";s:10:"4:7915:id:";s:0:"";s:6:"2:7918";s:2:"" ";s:9:"2048:7922";a:2:{s:4:"test";a:1:{s:12:"16:7932.1://";a:1:{s:15:"4:7932:canedit:";s:0:"";}}s:4:"true";a:1:{s:6:"2:8006";s:72:" oncontextmenu="context(this.id); return false" onClick="press(this.id)"";}}s:6:"2:8159";s:148:" onMouseMove="tmove(this)"   onMouseOver="pTrOver(this); contexthide()" onMouseOut="pTrOut(this);" style="cursor:pointer">

	<td nowrap="nowrap">
		";s:9:"2048:8163";a:2:{s:4:"test";a:2:{s:15:"4:8171:visible:";s:0:"";s:6:"2:8177";s:5:"!="1"";}s:4:"true";a:3:{s:6:"2:8265";s:87:"
		<img src="img/ico_file_g.gif" align="center" width="16" height="20" alt="" onClick="";s:9:"2048:8269";a:2:{s:4:"test";a:1:{s:12:"16:8279.1://";a:1:{s:15:"4:8279:canedit:";s:0:"";}}s:4:"true";a:3:{s:6:"2:8303";s:22:"$('blockid').value = '";s:10:"4:8306:id:";s:0:"";s:6:"2:8317";s:10:"'; edit();";}}s:6:"2:8343";s:21:" return false;" />
		";}}s:6:"2:8351";s:3:"
		";s:9:"2048:8355";a:2:{s:4:"test";a:2:{s:15:"4:8363:visible:";s:0:"";s:6:"2:8369";s:5:"=="1"";}s:4:"true";a:3:{s:6:"2:8455";s:85:"
		<img src="img/ico_file.gif" align="center" width="16" height="20" alt="" onClick="";s:9:"2048:8459";a:2:{s:4:"test";a:1:{s:12:"16:8469.1://";a:1:{s:15:"4:8469:canedit:";s:0:"";}}s:4:"true";a:3:{s:6:"2:8493";s:22:"$('blockid').value = '";s:10:"4:8496:id:";s:0:"";s:6:"2:8507";s:10:"'; edit();";}}s:6:"2:8533";s:21:" return false;" />
		";}}s:6:"2:8548";s:10:"
	</td>

	";s:9:"2048:8552";a:2:{s:4:"test";a:1:{s:12:"16:8565.1://";a:1:{s:18:"4:8565:superadmin:";s:0:"";}}s:4:"true";a:3:{s:6:"2:8605";s:38:"
	<td nowrap="nowrap">
	<p class="id">";s:10:"4:8608:id:";s:0:"";s:6:"2:8622";s:13:"</p>
	</td>
	";}}s:6:"2:8629";s:2:"

";s:19:"16:8646:table_name:";a:7:{s:6:"2:8677";s:31:"
	<td  nowrap="nowrap">
	<p><a ";s:9:"2048:8681";a:2:{s:4:"test";a:2:{s:12:"16:8692.1:..";a:1:{s:15:"4:8692:visible:";s:0:"";}s:6:"2:8698";s:5:"!="1"";}s:4:"true";a:1:{s:6:"2:8711";s:12:"class="gray"";}}s:6:"2:8717";s:1:">";s:12:"4:8722:data:";s:0:"";s:6:"2:8729";s:6:"</a>
	";s:14:"128:8739:data:";a:1:{s:6:"2:8745";s:6:"&nbsp;";}s:6:"2:8765";s:14:"
	</p>
	</td>
";}s:6:"2:8796";s:24:"
	<td>&nbsp;</td>
</tr>
";}s:6:"2:8804";s:1:"
";}s:6:"2:8811";s:1:"
";s:9:"2048:8815";a:2:{s:4:"test";a:1:{s:12:"16:8824.1://";a:1:{s:14:"4:8824:packet:";s:0:"";}}s:4:"true";a:15:{s:6:"2:8933";s:107:"
<tr id="packet-tpl" class="load" style="cursor: pointer; display:none;">
    <td nowrap="nowrap">
        ";s:9:"2048:8937";a:2:{s:4:"test";a:2:{s:15:"4:8945:visible:";s:0:"";s:6:"2:8951";s:5:"!="1"";}s:4:"true";a:3:{s:6:"2:9045";s:93:"
        <img src="img/ico_file_g.gif" align="center" width="16" height="20" alt="" onClick="";s:9:"2048:9049";a:2:{s:4:"test";a:1:{s:12:"16:9059.1://";a:1:{s:15:"4:9059:canedit:";s:0:"";}}s:4:"true";a:3:{s:6:"2:9083";s:22:"$('blockid').value = '";s:10:"4:9086:id:";s:0:"";s:6:"2:9097";s:10:"'; edit();";}}s:6:"2:9129";s:27:" return false;" />
        ";}}s:6:"2:9143";s:9:"
        ";s:9:"2048:9147";a:2:{s:4:"test";a:2:{s:15:"4:9155:visible:";s:0:"";s:6:"2:9161";s:5:"=="1"";}s:4:"true";a:3:{s:6:"2:9253";s:91:"
        <img src="img/ico_file.gif" align="center" width="16" height="20" alt="" onClick="";s:9:"2048:9257";a:2:{s:4:"test";a:1:{s:12:"16:9267.1://";a:1:{s:15:"4:9267:canedit:";s:0:"";}}s:4:"true";a:3:{s:6:"2:9291";s:22:"$('blockid').value = '";s:10:"4:9294:id:";s:0:"";s:6:"2:9305";s:10:"'; edit();";}}s:6:"2:9337";s:27:" return false;" />
        ";}}s:6:"2:9358";s:16:"
    </td>

    ";s:9:"2048:9362";a:2:{s:4:"test";a:1:{s:12:"16:9375.1://";a:1:{s:18:"4:9375:superadmin:";s:0:"";}}s:4:"true";a:3:{s:6:"2:9425";s:48:"
    <td nowrap="nowrap">
        <p class="id">";s:10:"4:9428:id:";s:0:"";s:6:"2:9448";s:19:"</p>
    </td>
    ";}}s:6:"2:9458";s:5:"
    ";s:12:"16:9476.1:..";a:1:{s:17:"16:9476:table_hr:";a:5:{s:6:"2:9510";s:34:"
    <td nowrap="nowrap">
        ";s:9:"2048:9514";a:2:{s:4:"test";a:3:{s:11:"4:9518:key:";s:0:"";s:6:"2:9521";s:2:"==";s:12:"16:9536.1://";a:1:{s:20:"4:9536:packet_field:";s:0:"";}}s:4:"true";a:1:{s:6:"2:9595";s:57:"<img class="preview" src="img/noimg.jpg" align="center"/>";}}s:6:"2:9607";s:9:"
        ";s:9:"2048:9611";a:2:{s:4:"test";a:3:{s:11:"4:9615:key:";s:0:"";s:6:"2:9618";s:2:"!=";s:12:"16:9633.1://";a:1:{s:20:"4:9633:packet_field:";s:0:"";}}s:4:"true";a:3:{s:6:"2:9691";s:56:"
        <input disabled type="text" name="packet_field[";s:11:"4:9695:key:";s:0:"";s:6:"2:9740";s:44:"]" value="" class="dropzoneInput"/>
        ";}}s:6:"2:9758";s:15:"
    </td>
    ";}}s:6:"2:9996";s:231:"
    <input disabled type="hidden" name="packet_field[sort]" value="" class="dropzoneInput"/>
    <td><a href="#"><img src="img/preload.gif" style="outline: none;"></a></td>
</tr>
<script>
    var packet_config = {
        field: '";s:21:"4:10009:packet_field:";s:0:"";s:7:"2:10036";s:26:"',
        def: {
        ";s:24:"16:10057:packet_default:";a:7:{s:7:"2:10070";s:13:"
            ";s:12:"4:10074:key:";s:0:"";s:7:"2:10077";s:2:":'";s:9:"512:10082";a:3:{s:7:"2:10093";s:11:"addslashes(";s:12:"4:10097:val:";s:0:"";s:7:"2:10099";s:1:")";}s:7:"2:10103";s:1:"'";s:16:"128:10114:_last:";a:1:{s:7:"2:10115";s:1:",";}s:7:"2:10127";s:9:"
        ";}s:7:"2:10166";s:32:"
        },
        increment: '";s:25:"4:10183:packet_increment:";s:0:"";s:7:"2:10203";s:19:"'
    };
</script>
";}}s:7:"2:10207";s:1:"
";}}s:7:"2:10259";s:47:"
</TABLE>
<input disabled type="hidden" value="";s:20:"4:10271:packet_sort:";s:0:"";s:7:"2:10973";s:701:"" id="packet_sort" />
<p class="bottomText" style="display:none">Прикреплено: <span class="pf_count">0</span> <span class="pf_word">файла</span> из <span class="pf_amount">0</span></p>
</form>




</div>
</td>
<td>&nbsp;</td>
</tr>
</table>
</div>
</td>
</tr>
</table>

					<div style="position:absolute;display:none;" id="divcursor"><img id="im" border=0 style="cursor:hand;filter:alpha(opacity=100)" src="img/bg-top.gif"></div>

					 <div style="position:absolute;display:none;width=135px;left:10px;top:10px" id="popup" class="service">

                                             <div class="bg">
                                                <div class="bg2">

												";s:10:"2048:10977";a:2:{s:4:"test";a:1:{s:16:"4:10985:canedit:";s:0:"";}s:4:"true";a:1:{s:7:"2:11211";s:224:"
                                                    <div onClick="edit();" onMouseOver="pTrOver(this)" onMouseOut="pTrOut(this);"><a href="" title="" onClick="return false;">Редактировать</a></div>
												";}}s:7:"2:11229";s:13:"
												";s:10:"2048:11233";a:2:{s:4:"test";a:1:{s:16:"4:11241:canmove:";s:0:"";}s:4:"true";a:1:{s:7:"2:11437";s:194:"
													<div onClick="move();return false;" onMouseOver="pTrOver(this)" onMouseOut="pTrOut(this);"><a href="" title="" onClick="return false;">Переместить</a></div>
												";}}s:7:"2:11455";s:13:"
												";s:10:"2048:11459";a:2:{s:4:"test";a:1:{s:16:"4:11467:cancopy:";s:0:"";}s:4:"true";a:1:{s:7:"2:11705";s:236:"
                                                    <div onClick="copy();return false;" onMouseOver="pTrOver(this)" onMouseOut="pTrOut(this);"><a href="" title="" onClick="return false;">Создать копию</a></div>
												";}}s:7:"2:11723";s:13:"
												";s:10:"2048:11727";a:2:{s:4:"test";a:1:{s:16:"4:11735:canhide:";s:0:"";}s:4:"true";a:1:{s:7:"2:12007";s:270:"
                                                    <div onClick="hide_block();" onMouseOver="pTrOver(this)" onMouseOut="pTrOut(this);"><a href="#" title="" onClick="return false;">Скрыть/показать</a></div>
                                                ";}}s:7:"2:12025";s:13:"
												";s:10:"2048:12029";a:2:{s:4:"test";a:1:{s:15:"4:12036:candel:";s:0:"";}s:4:"true";a:1:{s:7:"2:12264";s:226:"
                                                    <div class="last"  onclick="del();"  onMouseOver="pTrOver(this)" onMouseOut="pTrOut(this);"><a href="" title="" onClick="return false;">Удалить</a></div>
												";}}s:7:"2:12408";s:139:"
                                                </div>
                                            </div>



			</div>
			</BODY>
</HTML>
";}}