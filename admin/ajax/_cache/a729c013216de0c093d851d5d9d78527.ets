ETSa:2:{s:7:"0reduce";i:2;s:4:"main";a:49:{s:4:"2:85";s:59:"

<SCRIPT language="JavaScript" type="text/JavaScript">



";s:5:"2:175";s:110:"nfunctionsTrClick(n)s{ntids=sn.id.substr(2);ntn.style.backgroundColors=s"#e7e7e7";n

	";s:8:"2048:179";a:2:{s:4:"test";a:2:{s:17:"4:190:tree_count:";s:0:"";s:5:"2:195";s:4:" > 0";}s:4:"true";a:4:{s:5:"2:216";s:20:"
		for (i = 0; i <= ";s:17:"4:227:tree_count:";s:0:"";s:5:"2:238";s:10:"; i++) 
		";s:5:"2:363";s:179:"ntt{tntttifs(document.selparent.parent[i].values==sid)s{nttttdocument.selparent.parent[i].checkeds=strue;nttt};ntt};ntt
	";}}s:5:"2:385";s:17:"


	if ((id != 0)";s:17:"4:396:scaneditbl:";s:0:"";s:5:"2:644";s:247:") {
		document.all('aEditbl').style.color = "#000000";
		document.getElementById('iEditbl').className = "";
	} else {
		document.all('aEditbl').style.color = "#808080";
		document.getElementById('iEditbl').className = "none_active";
	};

	if (true";s:14:"4:652:scandel:";s:0:"";s:5:"2:888";s:235:") {
		document.all('aDel').style.color = "#000000";
		document.getElementById('iDel').className = "";
	} else {
		document.all('aDel').style.color = "#808080";
		document.getElementById('iDel').className = "none_active";
	};

	if (true";s:17:"4:899:scanaddcat:";s:0:"";s:6:"2:1140";s:240:") {
		document.all('aAdd').style.color = "#000000";
		document.getElementById('iAdd').className = "";
	} else {
		document.all('aAdd').style.color = "#808080";
		document.getElementById('iAdd').className = "none_active";
	};

	if ((id != 0)";s:16:"4:1149:scanedit:";s:0:"";s:6:"2:1484";s:334:") {
		document.all('aEdit').style.color = "#000000";
		document.getElementById('iEdit').className = "";

		//document.all('iEdit').src = imEdit.src;
	} else {
		document.all('aEdit').style.color = "#808080";
		document.getElementById('iEdit').className = "none_active";
		//document.all('iEdit').src = imEditS.src;
	};

	if ((id != 0)";s:16:"4:1493:scanhide:";s:0:"";s:6:"2:1743";s:249:") {
		document.all('aHide').style.color = "#000000";
		document.getElementById('iHide').className = "";
	} else {
		document.all('aHide').style.color = "#808080";
		document.getElementById('iHide').className = "none_active";
	};


	/*
	if ((id != 0)";s:16:"4:1752:scancopy:";s:0:"";s:19:"4:1765:scancopy_22:";s:0:"";s:6:"2:2058";s:292:") {
		document.all('aCopy').style.color = "#000000";
		document.getElementById('iCopy').className = "";
		document.all('iCopy').src = imCopy.src;
	} else {
		document.all('aCopy').style.color = "#808080";
		document.getElementById('iCopy').className = "none_active";
	};
	*/



	if ((id != 0)";s:16:"4:2067:scanmove:";s:0:"";s:19:"4:2080:scanmove_22:";s:0:"";s:6:"2:2593";s:512:") {
		document.all('aMove').style.color = "#000000";
		document.getElementById('iMove').className = "";
	} else {
		document.all('aMove').style.color = "#808080";
		document.getElementById('iMove').className = "none_active";
	};
}

function view_span(id)		{
	//document.getElementById(id).style.display = 'inline-block';
}
function noview_span(id)		{
	//document.getElementById(id).style.display = 'none';
}


function onclickEditbl(s) {
	if ((bpressed == '') && (document.selparent.parent2.value.substr(2) != 0)";s:8:"512:2598";a:3:{s:6:"2:2662";s:64:"str_replace("id", "document.selparent.parent2.value.substr(2)", ";s:18:"4:2673:scaneditbl:";s:0:"";s:6:"2:2675";s:1:")";}s:6:"2:2688";s:7:") {

		";s:9:"2048:2692";a:2:{s:4:"test";a:2:{s:18:"4:2703:tree_count:";s:0:"";s:6:"2:2708";s:4:" > 0";}s:4:"true";a:4:{s:6:"2:2730";s:21:"
			for (i = 0; i <= ";s:18:"4:2741:tree_count:";s:0:"";s:6:"2:2753";s:11:"; i++) 
			";s:6:"2:2913";s:202:"nttt{nttttifs(document.selparent.parent[i].values==sdocument.selparent.parent2.value.substr(2))s{document.selparent.parent[i].checkeds=strue;};nttt};nttt
		";}}s:6:"2:3227";s:309:"


		document.selparent.blocks.value=1;

		//alert (document.selparent.blocks.value);

		document.selparent.action='/admin/cat_edit.php'; 
		document.selparent.target='parent_window';

		document.selparent.submit();
	};
	if (bpressed != '') {
		TrRightClick(s);
	}
	return false;
};
</SCRIPT>



						
						";s:14:"32:3238:error:";a:3:{s:6:"2:3260";s:22:"<font color="#800000">";s:15:"4:3268:prerror:";s:0:"";s:6:"2:3284";s:15:"</font><br><br>";}s:6:"2:3737";s:447:"

						<TABLE  width="100%" border="0" cellpadding="2" cellspacing="0">
							<form name="selparent" action="cat_edit.php" method="get">
							<input type="hidden" name="id" value="0">
							<input type="hidden" name="blocks" value="0">
							<input type="hidden" name="del" value="0">
							<input type="hidden" name="toup" value="0">
							<input type="hidden" name="todown" value="0">
							<input type="hidden" name="parent2" value="tr";s:14:"4:3744:parent:";s:0:"";s:6:"2:4012";s:267:"">
							<input type="hidden" name="copy" value="0">
							<input type="hidden" name="move" value="0">
							<input type="hidden" name="copymove" value="0">
							<input type="hidden" name="hide" value="0">
							<input type="hidden" name="enabled" value="0">


";s:17:"64:4027:user_is:1";a:1:{s:6:"2:4317";s:290:"
							<TR onMouseMove="javascript:TrMove(this);" onMouseOut="javascript:TrOut(this);" onMouseOver="javascript:TrOver(this);" oncontextmenu="TrRightClick(this);return false;" onDrag="return false;" onDragStart="return false;" onSelectStart="return false;" style="cursor:pointer" id="tr0">
";}s:6:"2:4324";s:1:"
";s:18:"256:4339:user_is:1";a:1:{s:6:"2:4362";s:23:"
							<TR  id="tr0">
";}s:6:"2:4651";s:283:"

								<TD width="18px"><IMG border=0 src="<!--base_url//-->img/ico-site.gif" width="16" height="16"></TD>
								<TD colspan="100">Корень сайта</TD>
								<TD><input style="height:11px;width=11px;visibility:hidden" class="input" type="radio" name="parent" value="0"";s:16:"64:4665:parent:0";a:1:{s:6:"2:4673";s:8:" checked";}s:6:"2:4709";s:30:"></TD>
							</TR>



							";s:13:"16:4720:item:";a:48:{s:6:"2:4744";s:24:"
							<!--
							<TR ";s:6:"2:4865";s:121:"sonMouseMove="javascript:TrMove(this);"s onMouseOut="javascript:TrOut(this); if (bpressed == '') noview_span('spantr_";s:13:"4:4871:r[id]:";s:0:"";s:6:"2:4954";s:82:"');" onMouseOver="javascript:TrOver(this);  if (bpressed == '') view_span('spantr_";s:13:"4:4960:r[id]:";s:0:"";s:6:"2:5078";s:117:"'); " onDrag="return false;" onDragStart="return false;" onSelectStart="return false;" style="cursor:pointer"  id="tr";s:13:"4:5084:r[id]:";s:0:"";s:6:"2:5215";s:130:"" onclick="if (bpressed != '') { onclickEditbl(this); };" oncontextmenu="TrRightClick(this);return false;">
							-->
							<TR ";s:6:"2:5336";s:121:"sonMouseMove="javascript:TrMove(this);"s onMouseOut="javascript:TrOut(this); if (bpressed == '') noview_span('spantr_";s:13:"4:5342:r[id]:";s:0:"";s:6:"2:5425";s:82:"');" onMouseOver="javascript:TrOver(this);  if (bpressed == '') view_span('spantr_";s:13:"4:5431:r[id]:";s:0:"";s:6:"2:5549";s:117:"'); " onDrag="return false;" onDragStart="return false;" onSelectStart="return false;" style="cursor:pointer"  id="tr";s:13:"4:5555:r[id]:";s:0:"";s:6:"2:5558";s:2:"" ";s:21:"128:5575:r[disabled]:";a:1:{s:6:"2:5679";s:104:"onclick="if (bpressed != '') { onclickEditbl(this); };" oncontextmenu="TrRightClick(this);return false;"";}s:20:"32:5702:r[disabled]:";a:1:{s:6:"2:5731";s:29:"oncontextmenu="return false;"";}s:6:"2:5751";s:14:">



									";s:14:"16:5763:item2:";a:21:{s:6:"2:5796";s:33:"
									<TD width="18px" id="td";s:8:"512:5801";a:1:{s:10:"4:5804:td:";s:0:"";}s:6:"2:5869";s:58:"" >
									
									<script>document.getElementById('td";s:10:"4:5872:td:";s:0:"";s:6:"2:5907";s:34:"').style.background = 'url('+ imTD";s:9:"4:5909:l:";s:0:"";s:18:"32:5925:show_plus:";a:1:{s:6:"2:5930";s:5:"_plus";}s:19:"32:5952:show_minus:";a:1:{s:6:"2:5958";s:6:"_minus";}s:6:"2:6040";s:76:".src + ')';
									//+ ') no-repeat 0 0';
									
									//alert (imTD";s:9:"4:6042:l:";s:0:"";s:18:"32:6058:show_plus:";a:1:{s:6:"2:6063";s:5:"_plus";}s:19:"32:6085:show_minus:";a:1:{s:6:"2:6091";s:6:"_minus";}s:6:"2:6119";s:22:".src);
									//imTD";s:9:"4:6121:l:";s:0:"";s:18:"32:6137:show_plus:";a:1:{s:6:"2:6142";s:5:"_plus";}s:19:"32:6164:show_minus:";a:1:{s:6:"2:6170";s:6:"_minus";}s:6:"2:6211";s:35:".src;
									</script>

									";s:24:"32:6232:show_plus_minus:";a:4:{s:6:"2:6297";s:65:"
									<a style="text-decoration:none" href="index.php?unhide=";s:19:"32:6313:show_minus:";a:1:{s:6:"2:6314";s:1:"-";}s:13:"4:6326:r[id]:";s:0:"";s:6:"2:6369";s:42:"">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									";}s:6:"2:6385";s:10:"
									";s:25:"128:6406:show_plus_minus:";a:1:{s:6:"2:6412";s:6:"&nbsp;";}s:6:"2:6433";s:15:"</TD>
									";}s:6:"2:6468";s:28:"

									<TD width=1%><IMG";s:9:"2048:6472";a:2:{s:4:"test";a:7:{s:6:"2:6473";s:1:" ";s:18:"4:6484:r[visible]:";s:0:"";s:6:"2:6492";s:7:"==0 || ";s:19:"4:6504:r[disabled]:";s:0:"";s:6:"2:6509";s:4:" || ";s:18:"4:6520:r[enabled]:";s:0:"";s:6:"2:6524";s:3:"==0";}s:4:"true";a:1:{s:6:"2:6564";s:39:" style="filter:alpha(opacity=20) gray;"";}}s:6:"2:6605";s:36:" border=0 src="<!--base_url//-->img/";s:14:"4:6612:r2_lev:";s:0:"";s:6:"2:6684";s:71:".gif" width="16" height="16"></TD>

									<TD width="100%" colspan="";s:15:"4:6692:colspan:";s:0:"";s:6:"2:6702";s:9:"" nowrap ";s:9:"2048:6706";a:2:{s:4:"test";a:7:{s:6:"2:6707";s:1:" ";s:18:"4:6718:r[visible]:";s:0:"";s:6:"2:6728";s:9:" == 0 || ";s:19:"4:6740:r[disabled]:";s:0:"";s:6:"2:6745";s:4:" || ";s:18:"4:6756:r[enabled]:";s:0:"";s:6:"2:6762";s:5:" == 0";}s:4:"true";a:1:{s:6:"2:6784";s:21:"style="color:#b0b0b0"";}}s:6:"2:6801";s:12:" >
									";s:9:"2048:6805";a:2:{s:4:"test";a:2:{s:10:"4:6808:cl:";s:0:"";s:6:"2:6817";s:8:" == 't1'";}s:4:"true";a:1:{s:6:"2:6823";s:5:" <b> ";}}s:6:"2:6829";s:1:" ";s:9:"2048:6833";a:2:{s:4:"test";a:3:{s:6:"2:6834";s:1:" ";s:18:"4:6845:r[enabled]:";s:0:"";s:6:"2:6851";s:5:" == 0";}s:4:"true";a:1:{s:6:"2:6855";s:3:"<s>";}}s:6:"2:6870";s:10:"
									";s:8:"512:6875";a:3:{s:6:"2:6886";s:11:"strip_tags(";s:15:"4:6894:r[name]:";s:0:"";s:6:"2:6896";s:1:")";}s:6:"2:6913";s:11:" 
									";s:9:"2048:6917";a:2:{s:4:"test";a:2:{s:10:"4:6920:cl:";s:0:"";s:6:"2:6929";s:8:" == 't1'";}s:4:"true";a:1:{s:6:"2:6936";s:6:" </b> ";}}s:6:"2:6951";s:10:"
									";s:9:"2048:6955";a:2:{s:4:"test";a:3:{s:6:"2:6956";s:1:" ";s:18:"4:6967:r[enabled]:";s:0:"";s:6:"2:6973";s:5:" == 0";}s:4:"true";a:1:{s:6:"2:6978";s:4:"</s>";}}s:6:"2:6995";s:12:"
	
									";s:22:"32:7014:user_is_super:";a:7:{s:6:"2:7015";s:1:" ";s:8:"512:7020";a:3:{s:6:"2:7037";s:17:"htmlspecialchars(";s:14:"4:7044:r[key]:";s:0:"";s:6:"2:7046";s:1:")";}s:6:"2:7056";s:4:"  - ";s:19:"4:7068:r[template]:";s:0:"";s:6:"2:7072";s:3:" - ";s:13:"4:7078:r[id]:";s:0:"";s:6:"2:7080";s:1:" ";}s:6:"2:7179";s:93:"
									
									<!--
										кнопка для оперы 
									<span id="spantr_";s:13:"4:7185:r[id]:";s:0:"";s:6:"2:7295";s:109:"" style="display:none; background-color:#eee " onClick="javascript:  TrRightClick(document.getElementById('tr";s:13:"4:7301:r[id]:";s:0:"";s:6:"2:7484";s:182:"')); return false;">(меню)</span>
									//-->
									</TD>

									<TD><input style="height:11px;width=11px; display:none" class="input" type="radio" name="parent" value="";s:13:"4:7490:r[id]:";s:0:"";s:6:"2:7493";s:2:"" ";s:9:"2048:7497";a:2:{s:4:"test";a:4:{s:14:"4:7504:parent:";s:0:"";s:6:"2:7509";s:4:" == ";s:13:"4:7515:r[id]:";s:0:"";s:6:"2:7517";s:1:" ";}s:4:"true";a:1:{s:6:"2:7528";s:10:"  checked ";}}s:6:"2:7561";s:28:"></TD>
								</TR>
							";}s:6:"2:8013";s:445:"


							<table><tr><td><table><tr><td><input style="display:none" type="radio" name="parent" value="-1"></td></tr></table></td></tr></table>
							</form>
						</TABLE>
					</TD>
				</TR>
			</TABLE>




			<div oncontextmenu="javascript:divHide();return false;" onmouseout="javascript:divOut();" onmouseover="javascript:divOver();" style="position:absolute;visibility:hidden;width=135px;left:10px;top:10px" id="popup" class="service">

		";s:6:"2:9053";s:1398:"nttt<scriptslanguage="JavaScript"stype="text/javascript">ntttdivInvs=s0;ntttfunctionspTrOver(n)s{nttttn.style.backgroundColors=s'0A246A';nttttwascolors=sn.style.color;nttttn.style.colors=s'#FFFFFF';nttt}ntttfunctionspTrOut(n)s{nttttn.style.backgroundColors=s'';nttttn.style.colors=swascolor;nttt}ntttfunctionsdivOver()s{nttttifs(divInvs>s0)s{clearInterval(divInv);sdivInv=0;};nttt}ntttfunctionsdivOut()s{nttttifs(divInvs==s0)s{ntttttdivInvs=ssetInterval("divHide()",s300);ntttt};nttt}ntttfunctionsdivHide()s{nttttclearInterval(divInv);nttttdivInvs=s0;nttttpopup.style.visibilitys=s'hidden';nttttpopup.style.lefts=s'10px';nttttpopup.style.tops=s'10px';nttttifs(bpresseds==s'')sdocument.all(document.selparent.parent2.value).style.backgroundColors=s"";nttt}nttt</script>ntt


                                        
                                            <div class="bg">
                                                <div class="bg2">
                                                    <div id="aAdd" onclick="javascipt:if (true";s:8:"512:9058";a:3:{s:6:"2:9122";s:64:"str_replace("id", "document.selparent.parent2.value.substr(2)", ";s:18:"4:9133:scanaddcat:";s:0:"";s:6:"2:9135";s:1:")";}s:6:"2:9599";s:458:") { 
							document.selparent.action='/admin/cat_edit.php'; document.selparent.target='parent_window'; document.selparent.submit(); };return false;" onMouseOver="javascript:pTrOver(this);" onMouseOut="javascript:pTrOut(this);"><a id="iAdd" href="#" title="" onClick="return false;">Создать новую</a></div>
                                                    <div id="aEdit" onclick="javascript:if ((document.selparent.parent[0].checked == false)";s:8:"512:9604";a:3:{s:6:"2:9668";s:64:"str_replace("id", "document.selparent.parent2.value.substr(2)", ";s:16:"4:9677:scanedit:";s:0:"";s:6:"2:9679";s:1:")";}s:7:"2:10389";s:704:")   { document.selparent.id.value=1; document.selparent.action='/admin/cat_edit.php'; document.selparent.target='parent_window'; document.selparent.submit(); };return false;" onMouseOver="javascript:pTrOver(this);" onMouseOut="javascript:pTrOut(this);"><a href="#"  id="iEdit"  onClick="return false;">Редактировать</a></div>

                                                    <div id="aEditbl" onclick="javascript:return onclickEditbl('');" onMouseOver="javascript:pTrOver(this);" onMouseOut="javascript:pTrOut(this);"><a href="#" title="" id="iEditbl" onClick="return false;">Блоки</a></div>

                                                    <div  id="aMove" onclick="javascript:";s:10:"2048:10393";a:2:{s:4:"test";a:2:{s:19:"4:10404:tree_count:";s:0:"";s:7:"2:10409";s:4:" > 1";}s:4:"true";a:3:{s:7:"2:10419";s:9:" if (true";s:9:"512:10424";a:3:{s:7:"2:10488";s:64:"str_replace("id", "document.selparent.parent2.value.substr(2)", ";s:17:"4:10497:scanmove:";s:0:"";s:7:"2:10499";s:1:")";}s:7:"2:10528";s:23:") { bpress('move'); }; ";}}s:7:"2:10856";s:323:" return false;" onMouseOver="javascript:pTrOver(this);" onMouseOut="javascript:pTrOut(this);"><a href="#" title="" id="iMove" onClick="return false;">Переместить</a></div>

                                                    <div id="aHide" onclick="javascript:if ((document.selparent.parent[0].checked == false)";s:9:"512:10861";a:3:{s:7:"2:10925";s:64:"str_replace("id", "document.selparent.parent2.value.substr(2)", ";s:17:"4:10934:scanhide:";s:0:"";s:7:"2:10936";s:1:")";}s:7:"2:11302";s:360:") { document.selparent.hide.value=1;document.selparent.submit(); };return false;" onMouseOver="javascript:pTrOver(this);"><a href="#" title="" id="iHide" onClick="return false;">Скрыть/показать</a></div>
                                                    <div id="aEnabled" onclick="javascript:if ((document.selparent.parent[0].checked == false)";s:9:"512:11307";a:3:{s:7:"2:11371";s:64:"str_replace("id", "document.selparent.parent2.value.substr(2)", ";s:20:"4:11383:scanenabled:";s:0:"";s:7:"2:11385";s:1:")";}s:7:"2:11671";s:280:") { document.selparent.enabled.value=1;document.selparent.submit(); };return false;" onMouseOver="javascript:pTrOver(this);"><a href="#" title="" id="iEnabled" onClick="return false;">Вкл/Выкл</a></div>

													<div class="last" id="aDel" onclick="javascript:if (true";s:9:"512:11676";a:3:{s:7:"2:11740";s:64:"str_replace("id", "document.selparent.parent2.value.substr(2)", ";s:16:"4:11748:scandel:";s:0:"";s:7:"2:11750";s:1:")";}s:7:"2:12775";s:1019:") { if (confirm('Вы действительно хотите удалить папку?')) { document.selparent.del.value=1; document.selparent.submit(); }; };return false;" onMouseOver="javascript:pTrOver(this);" onMouseOut="javascript:pTrOut(this);"><a href="#" title="" id="iDel" onClick="return false;">Удалить</a></div>
                                                </div>
                                            </div>
                                        



			</div>

			
			<div style="position:absolute;visibility:hidden;" id="divcursor">
			<img onclick="javascript:TrMove(document.all(document.selparent.parent2.value));" id="im" border=0 style="cursor:hand;filter:alpha(opacity=100)" src="<!--base_url//-->img/spacer.gif"></div>


<script>

document.onmousemove = function(event) {
      var event = event || window.event;
	  XY = mousePageXY(event);
      //document.body.innerHTML = "x = " + mousePageXY(event).x + ", y = " + mousePageXY(event).y;
}

</script>


	<table >
		<tr><td>
	
";}}