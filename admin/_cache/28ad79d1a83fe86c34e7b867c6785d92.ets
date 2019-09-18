ETSa:3:{s:7:"0reduce";i:2;s:4:"main";a:59:{s:5:"2:784";s:758:"
<script language="javascript">
<!--

var divInv='';


function checked_all_number()		{
	var k = 0;
	var arr = document.getElementsByTagName('input');
	for (i = 0; i < arr.length; i++)		{
		var a = arr[i];
		if (a.id.substr(0, 10) == 'massblock_')	{
			if (a.checked)	{
				k++;
			}
		}
	}

	if (k > 0)		{
		$('opergroupdiv').style.display = 'block';
	}	else	{
		$('opergroupdiv').style.display = 'none';
	}
	$('number_selected_checkbox').innerHTML = 'Выделено '+k;
}


function checked_all()		{
	var elem = $('checked_all_checkbox');

	var arr = document.getElementsByTagName('input');
	for (i = 0; i < arr.length; i++)		{
		var a = arr[i];
		if (a.id.substr(0, 10) == 'massblock_')	{
			a.checked = elem.checked;
		}
	}

	checked_all_number();
}

";s:6:"2:3338";s:3420:"nXYs=s{x:0,sy:0};nnfunctionsgetElementPosition(elemId)n{nnssssvarselem_1s=sdocument.getElementById(elemId);nnt//alerts(elem_1);nt//return;nnssssvarsw_1s=selem_1.offsetWidth;nssssvarsh_1s=selem_1.offsetHeight;ntnssssvarsl_1s=s0;nssssvarst_1s=s0;ntnsssswhiles(elem_1)nssss{nssssssssl_1s+=selem_1.offsetLeft;nsssssssst_1s+=selem_1.offsetTop;nsssssssselem_1s=selem_1.offsetParent;nssss}nnssssreturns{"left":l_1,s"offsetY":t_1,s"width":sw_1,s"height":h_1};n}nnnnfunctionsmousePageXY(e)n{nssvarsxs=s0,sys=s0;nnssifs(!e)ses=swindow.event;nnssifs(e.pageXs||se.pageY)nss{nssssxs=se.pageX;nssssys=se.pageY;nss}nsselsesifs(e.clientXs||se.clientY)nss{nssssxs=se.clientXs+s(document.documentElement.scrollLefts||sdocument.body.scrollLeft)s-sdocument.documentElement.clientLeft;nssssys=se.clientYs+s(document.documentElement.scrollTops||sdocument.body.scrollTop)s-sdocument.documentElement.clientTop;nt//ys=sys+ssdocument.body.scrollTop;nss}nnt//offYs=se.offsetY;nnssreturns{"x":x,s"y":y};n}nnnnfunctionstmove(n)n{ntoffXYs=sgetElementPosition(n.id);nnts$('divcursor').style.tops=sXY.y;s//s+sdocument.body.scrollTops-soffXY.offsetY;ntsxxs=sXY.xs-s27;ntsifs(xxs<s15)sxxs=s15;nntyys=sXY.ys-soffXY.offsetY;nntsifs(yys<s7)t{s$('im').src=s'img/bg-top.gif';s$('divcursor').style.tops=sXY.ys-s2;s}ntsifs(yys>s15)t{s$('im').src=s'img/bg-bottom.gif';s$('divcursor').style.tops=sXY.ys-s10;ss}nts$('divcursor').style.lefts=xx;n}nnnnfunctionsmove()n{nsscontexthide();nss$('divcursor').style.displays=s'';nss$('mcparent').values=s$('blockid').value;tttn}n//s---------------------------------------n//sСтилевыеsфункции.n//s---------------------------------------nfunctionspTrOver(n)n{ntn.style.backgroundColors=s'#f7f7f7';ntwascolors=sn.style.color;ntn.style.colors=s'000000';n}nfunctionspTrOut(n)n{ntn.style.backgroundColors=s'';ntn.style.colors=swascolor;n}n//s---------------------------------------n//sКонтекстноеsменю.n//s---------------------------------------tttnfunctionscontext(id)n{nss$('blockid').values=s$(id).id;s//sВыдаёмsнаsредактированиеsблокsidnss$('popup').style.lefts=sXY.x-10;//s+sdocument.body.scrollTop;nss$('popup').style.tops=sXY.y-10;nss$('popup').style.displays=s'';nn}nfunctionscontexthide()n{nss$('popup').style.displays=s'none';n}nnnnn//s---------------------------------------n//sСозданиеsновогоsблока.n//s---------------------------------------tnn
function add_new()
{
location.href='block.php?cparent='+$('cparent').value+'&btmplate='+$('btmplate').value+'&actions=add'+ '";s:12:"16:3357.1://";a:1:{s:16:"16:3357.2:filter";a:1:{s:17:"4:3357:urlfilter:";s:0:"";}}s:6:"2:3517";s:159:"';
}
function edit()
{
  location.href='block.php?cparent='+$('cparent').value+'&btmplate='+$('btmplate').value+'&actions=edit&blockid='+$('blockid').value + '";s:12:"16:3536.1://";a:1:{s:16:"16:3536.2:filter";a:1:{s:17:"4:3536:urlfilter:";s:0:"";}}s:6:"2:3857";s:320:"';
}

function copy()
{
  location.href='block.php?cparent='+$('cparent').value+'&btmplate='+$('btmplate').value+'&actions=copy&blockid='+$('blockid').value
}



function hide_block()
{
location.href='block.php?cparent='+$('cparent').value+'&btmplate='+$('btmplate').value+'&actions=hide&blockid='+$('blockid').value + '";s:12:"16:3876.1://";a:1:{s:16:"16:3876.2:filter";a:1:{s:17:"4:3876:urlfilter:";s:0:"";}}s:6:"2:4124";s:247:"';
}
function del()
{
 if(window.confirm('Вы действительно хотите удалить блок?'))location.href='block.php?cparent='+$('cparent').value+'&btmplate='+$('btmplate').value+'&actions=del&blockid='+$('blockid').value + '";s:12:"16:4143.1://";a:1:{s:16:"16:4143.2:filter";a:1:{s:17:"4:4143:urlfilter:";s:0:"";}}s:6:"2:4643";s:499:"';
}

function press(id)
{

	//n = document.getElementById(id);
	offXY = getElementPosition(id);
	yy = XY.y - offXY.offsetY;

//	alert (yy);

 $('blockid').value = $(id).id; // Выдаём на редактирование блок id
 if($('divcursor').style.display=='' && $('mcparent').value!==id)
 {
	
  	 if (yy < 7)location.href='block.php?cparent='+$('cparent').value+'&btmplate='+$('btmplate').value+'&actions=mup&copymove=0&mcparent='+$('mcparent').value+'&blockid='+$('blockid').value + '";s:12:"16:4662.1://";a:1:{s:16:"16:4662.2:filter";a:1:{s:17:"4:4662:urlfilter:";s:0:"";}}s:6:"2:4860";s:197:"';
	 if (yy > 15) location.href='block.php?cparent='+$('cparent').value+'&copymove=1&btmplate='+$('btmplate').value+'&actions=mdown&mcparent='+$('mcparent').value+'&blockid='+$('blockid').value + '";s:12:"16:4879.1://";a:1:{s:16:"16:4879.2:filter";a:1:{s:17:"4:4879:urlfilter:";s:0:"";}}s:6:"2:4954";s:74:"';
 }
  $('mcparent').value='';
  $('divcursor').style.display='none';
}

";s:6:"2:5927";s:1127:"n//s-------------------------------------------------------------------]n//sМеняемsшаблонsблока.nfunctionschangebtempl(btempl)n{nslocation.href='block.php?cparent='+$('cparent').value+'&btmplate='+btempl;n}nfunctionschangesort(filterkey)n{nslocation.href='block.php?cparent='+$('cparent').value+'&btmplate='+$('btmplate').value+'&filterkey='+filterkey;n}nfunctionschangesortby(sortby)n{nslocation.href='block.php?cparent='+$('cparent').value+'&btmplate='+$('btmplate').value+'&filterby='+sortby;n}nnfunctionsfind()n{nslocation.href='block.php?cparent='+$('cparent').value+'&btmplate='+$('btmplate').value+'&findmy='+$('findmy').value+'&findin='+$('filterkey').value;n}nnnnnntfunctionsvis_csv()tt{nttifs(s$('csv_form').style.displays==s'block')tt{nttt$('csv_form').style.displays=s'none';ntt}telset{nttt$('csv_form').style.displays=s'block';ntt}nt}nnnnnnn
//-->

</script>
<!-------------------------------------------------------------------------->


";s:12:"16:5943.1://";a:1:{s:17:"32:5943:redirect:";a:9:{s:6:"2:5964";s:21:"
<script>
	//alert ('";s:12:"16:5975.1://";a:1:{s:16:"4:5975:redirect:";s:0:"";}s:6:"2:5979";s:3:"'+'";s:12:"16:5998.1://";a:1:{s:16:"16:5998.2:filter";a:1:{s:17:"4:5998:urlfilter:";s:0:"";}}s:6:"2:6026";s:27:"');
	window.location.href='";s:12:"16:6037.1://";a:1:{s:16:"4:6037:redirect:";s:0:"";}s:6:"2:6041";s:3:"'+'";s:12:"16:6060.1://";a:1:{s:16:"16:6060.2:filter";a:1:{s:17:"4:6060:urlfilter:";s:0:"";}}s:6:"2:6074";s:13:"';
</script>
";}}s:6:"2:6735";s:655:"

        	<div class="options">
				<div class="bg_b">
					<div class="bg_l">
						<div class="bg_r">
							<div class="bg_tr">
								<div class="bg_tl">

									<div class="bg_bl">
										<div class="bg_br">
											<div>
												<h1  style="width:60%; "><img src="img/ico_file.gif" width="16" height="20" alt="" />Редактирование&nbsp;блоков</h1>
												<h2>&nbsp;</h2>
												<div class="nav">
													

												</div>
												<span class="clear"></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
            </div>

";s:9:"2048:6739";a:2:{s:4:"test";a:2:{s:12:"16:6770.1://";a:1:{s:19:"16:6770.2:cat_filtr";a:1:{s:26:"4:6770:unvis_option_block:";s:0:"";}}s:6:"2:6776";s:5:" != 1";}s:4:"true";a:3:{s:6:"2:6864";s:87:"
<table style="width:400px" cellpadding="0" cellspacing="0"	>
<tr>
<td >
</td>



<td>
";s:9:"2048:6868";a:2:{s:4:"test";a:2:{s:13:"16:6875.1:csv";a:1:{s:10:"4:6875:is:";s:0:"";}s:6:"2:6879";s:3:"==1";}s:4:"true";a:1:{s:6:"2:7029";s:149:"
			<div class="csv_block" onClick=""><a href="#" title="Загрузить csv" onClick="vis_csv(); return false;">Загрузить csv</a></div>
";}}s:6:"2:7058";s:24:"
</td>


</tr>
</table>
";}}s:6:"2:7068";s:5:"




";s:9:"2048:7072";a:2:{s:4:"test";a:2:{s:13:"16:7079.1:csv";a:1:{s:10:"4:7079:is:";s:0:"";}s:6:"2:7083";s:3:"==1";}s:4:"true";a:13:{s:6:"2:7112";s:28:"

<div id="csv_form" style="";s:9:"2048:7116";a:2:{s:4:"test";a:2:{s:13:"16:7127.1:csv";a:1:{s:14:"4:7127:upload:";s:0:"";}s:6:"2:7131";s:3:"!=1";}s:4:"true";a:1:{s:6:"2:7144";s:12:"display:none";}}s:6:"2:7171";s:22:"" class="csv_div">
	
	";s:12:"16:7181:csv:";a:3:{s:6:"2:7184";s:3:"
		";s:15:"32:7196:errors:";a:3:{s:6:"2:7267";s:71:"
			<div style="padding-left:20px; padding-bottom:10px; color:red">
			";s:15:"16:7280:errors:";a:3:{s:6:"2:7285";s:5:"
				";s:13:"4:7291:error:";s:0:"";s:6:"2:7296";s:4:"
			";}s:6:"2:7316";s:13:"
			</div>
		";}s:6:"2:7324";s:2:"
	";}s:6:"2:7428";s:97:"


<form name="import_csv" method="post" enctype="multipart/form-data" action="block.php?cparent=";s:15:"4:7436:cparent:";s:0:"";s:6:"2:7683";s:246:"">
<input type="hidden" name="import_csv_true" value="true" />
<table cellspacing="10">
<tr>
<td>Файл:</td>
<td><input type="file" name="csv_file" /></td>
</tr>
<tr>
<td>Режим:</td>
<td>
	<select name="csv_regime">
		<option value="add" ";s:9:"2048:7687";a:2:{s:4:"test";a:2:{s:13:"16:7698.1:csv";a:1:{s:14:"4:7698:regime:";s:0:"";}s:6:"2:7706";s:7:"=='add'";}s:4:"true";a:1:{s:6:"2:7715";s:8:"selected";}}s:6:"2:7775";s:55:">добавление</option>
		<option value="merge" ";s:9:"2048:7779";a:2:{s:4:"test";a:2:{s:13:"16:7790.1:csv";a:1:{s:14:"4:7790:regime:";s:0:"";}s:6:"2:7800";s:9:"=='merge'";}s:4:"true";a:1:{s:6:"2:7809";s:8:"selected";}}s:6:"2:7883";s:69:">только обновление</option>
		<option value="delete" ";s:9:"2048:7887";a:2:{s:4:"test";a:2:{s:13:"16:7898.1:csv";a:1:{s:14:"4:7898:regime:";s:0:"";}s:6:"2:7909";s:10:"=='delete'";}s:4:"true";a:1:{s:6:"2:7918";s:8:"selected";}}s:6:"2:8097";s:174:">удалить все данные</option>
	</select>
</td>
</tr>
<tr>
<td></td>
<td><input type="submit" value="Загрузить" ></td>
</tr>
</table>
</form>


</div>
";}}s:6:"2:8103";s:1:"
";s:9:"2048:8107";a:2:{s:4:"test";a:1:{s:12:"16:8117.1://";a:1:{s:15:"4:8117:canmove:";s:0:"";}}s:4:"true";a:1:{s:6:"2:9675";s:1556:"
<script type="text/javascript">
	_jQuery(document).ready(function($) {

		/*$('#divtable tbody').sortable({ axis: "y", cancel: '.id, .name, .block-options, .check',
			update: function(event,ui) {
				var id = ui.item.data('id');
				var mcparent = id;
				_jQuery('#blockid').val(ui.item.prev().length ? ui.item.prev().data('id') : ui.item.next().data('id'));
				data = _jQuery(document.blockform).serialize();
				if (ui.item.prev().length) {
					actions = 'mdown';
					data += '&copymove=1';
				} else {
					data += '&copymove=0';
					actions = 'mup';
				}
				data = data.replace('actions=', 'actions=' + actions);
				data += '&mcparent=' + mcparent;
				_jQuery.get('block.php', data);
			},
			activate: function(event, ui) {
				$(ui.placeholder).html(ui.item.html());
			}

		});*/
		/*stop - функция для аякса перезагрузки списка*/

		$('#divtable .item input:checked').each(function(){ $(this).parents('tr').toggleClass('checked')  })
		$('#divtable .item input:checkbox').click(function(){ $(this).parents('tr').toggleClass('checked') })

		$('#checked_all_checkbox').click(function(){
			if( $(this).is(':checked')) { $(this).parents('table').next('.list-wrapper').find('tr').addClass('checked') }
			else { $(this).parents('table').next('.list-wrapper').find('tr').removeClass('checked') }
		})
	});
</script>
<style>
	.ui-sortable-helper {
		position:initial !important;
		display:none;
	}
	.ui-sortable-placeholder {
		visibility: visible !important;
	}
	td.move > span {
		cursor:move;
	}
</style>
";}}s:6:"2:9744";s:66:"


<form method="post" name="blockform" action="block.php?cparent=";s:15:"4:9752:cparent:";s:0:"";s:6:"2:9763";s:10:"&btmplate=";s:16:"4:9772:btmplate:";s:0:"";s:7:"2:10116";s:343:"">
<table class="in_cont" cellpadding="0" cellspacing="0" style="width:100%"  >
            	<tr>
                	<td align="center" style="width:80%" valign="top">
                        <div class="with_page">
				<!--выбор типов блоков-->
				<div class="type-list">
					<div class="title">Тип блоков</div>
					";s:16:"16:10129:blocks:";a:1:{s:10:"2048:10133";a:2:{s:4:"test";a:3:{s:13:"16:10146.1://";a:1:{s:19:"4:10146:superadmin:";s:0:"";}s:7:"2:10152";s:5:" || !";s:19:"4:10163:super_only:";s:0:"";}s:4:"true";a:13:{s:7:"2:10206";s:41:"
					<a href="#" onclick="changebtempl('";s:15:"4:10213:btempl:";s:0:"";s:7:"2:10225";s:11:"')" class="";s:10:"2048:10229";a:2:{s:4:"test";a:7:{s:13:"16:10242.1://";a:1:{s:19:"4:10242:superadmin:";s:0:"";}s:7:"2:10247";s:4:" && ";s:19:"4:10258:super_only:";s:0:"";s:7:"2:10263";s:4:" && ";s:15:"4:10270:btempl:";s:0:"";s:7:"2:10273";s:2:"!=";s:13:"16:10284.1://";a:1:{s:17:"4:10284:btmplate:";s:0:"";}}s:4:"true";a:1:{s:7:"2:10290";s:4:"zero";}}s:10:"2048:10297";a:2:{s:4:"test";a:3:{s:15:"4:10304:btempl:";s:0:"";s:7:"2:10307";s:2:"!=";s:13:"16:10318.1://";a:1:{s:17:"4:10318:btmplate:";s:0:"";}}s:4:"true";a:0:{}}s:10:"2048:10327";a:2:{s:4:"test";a:3:{s:15:"4:10334:btempl:";s:0:"";s:7:"2:10337";s:2:"==";s:13:"16:10348.1://";a:1:{s:17:"4:10348:btmplate:";s:0:"";}}s:4:"true";a:1:{s:7:"2:10356";s:6:"active";}}s:7:"2:10369";s:8:""><span>";s:19:"4:10380:btemplname:";s:0:"";s:7:"2:10390";s:9:"</span> (";s:14:"4:10396:count:";s:0:"";s:7:"2:10399";s:2:") ";s:10:"2048:10403";a:2:{s:4:"test";a:1:{s:13:"16:10416.1://";a:1:{s:19:"4:10416:superadmin:";s:0:"";}}s:4:"true";a:3:{s:7:"2:10420";s:2:" (";s:15:"4:10427:btempl:";s:0:"";s:7:"2:10429";s:1:")";}}s:7:"2:10442";s:10:"</a>
					";}}}s:7:"2:10920";s:468:"
				</div>
                            <table cellpadding="0" cellspacing="0">
                                <tr>
						<th style="padding: 0">
							<div class="block-new"><a href="#" onclick="add_new(); return false;" title="Новый блок"><span>Новый блок</span></a></div>
						</th>
						<th style="width:80%; text-align:right; padding: 0 10px 0 0; vertical-align: bottom">
									Выводить на страницу по:&nbsp;
									";s:16:"16:10933:filter:";a:3:{s:7:"2:10944";s:11:"
										";s:19:"16:10960:limit_arr:";a:7:{s:7:"2:10972";s:12:"
											";s:10:"2048:10976";a:2:{s:4:"test";a:5:{s:7:"2:10977";s:1:" ";s:13:"16:10992.1://";a:1:{s:17:"16:10992.2:filter";a:1:{s:14:"4:10992:limit:";s:0:"";}}s:7:"2:10997";s:4:" == ";s:14:"4:11003:limit:";s:0:"";s:7:"2:11005";s:1:" ";}s:4:"true";a:3:{s:7:"2:11027";s:21:"
												<strong>";s:14:"4:11033:limit:";s:0:"";s:7:"2:11063";s:29:"</stong></strong>
											";}}s:7:"2:11080";s:12:"
											";s:10:"2048:11084";a:2:{s:4:"test";a:5:{s:7:"2:11085";s:1:" ";s:13:"16:11100.1://";a:1:{s:17:"16:11100.2:filter";a:1:{s:14:"4:11100:limit:";s:0:"";}}s:7:"2:11105";s:4:" != ";s:14:"4:11111:limit:";s:0:"";s:7:"2:11113";s:1:" ";}s:4:"true";a:8:{s:7:"2:11136";s:22:"
												<a href="";s:13:"16:11152.1://";a:1:{s:17:"16:11152.2:filter";a:1:{s:15:"4:11152:preurl:";s:0:"";}}s:9:"512:11158";a:3:{s:7:"2:11226";s:68:"str_replace('f_page', 'f_page1', str_replace('f_limit', 'f_limit1', ";s:13:"16:11245.1://";a:1:{s:17:"16:11245.2:filter";a:1:{s:18:"4:11245:urlfilter:";s:0:"";}}s:7:"2:11249";s:3:") )";}s:7:"2:11264";s:9:"&f_limit=";s:14:"4:11270:limit:";s:0:"";s:7:"2:11273";s:2:"">";s:14:"4:11279:limit:";s:0:"";s:7:"2:11296";s:16:"</a>
											";}}s:7:"2:11313";s:12:"
											";s:16:"128:11324:_last:";a:1:{s:7:"2:11337";s:13:"&nbsp;|&nbsp;";}s:7:"2:11354";s:11:"
										";}s:7:"2:11371";s:10:"
									";}s:7:"2:11505";s:127:"
								</th>
				                                </tr>	    
<tr>
						<td colspan="2" style="border:none; text-align:left">
";s:14:"32:11515:page:";a:9:{s:7:"2:11563";s:48:"
<p class="pad">Показано записей ";s:14:"4:11569:start:";s:0:"";s:7:"2:11572";s:2:"-
";s:10:"2048:11576";a:2:{s:4:"test";a:3:{s:15:"4:11583:numall:";s:0:"";s:7:"2:11588";s:4:" >= ";s:13:"4:11593:stop:";s:0:"";}s:4:"true";a:3:{s:7:"2:11597";s:2:"
	";s:13:"4:11602:stop:";s:0:"";s:7:"2:11604";s:1:"
";}}s:7:"2:11610";s:1:"
";s:10:"2048:11614";a:2:{s:4:"test";a:3:{s:15:"4:11621:numall:";s:0:"";s:7:"2:11625";s:3:" < ";s:13:"4:11630:stop:";s:0:"";}s:4:"true";a:3:{s:7:"2:11634";s:2:"
	";s:15:"4:11641:numall:";s:0:"";s:7:"2:11643";s:1:"
";}}s:7:"2:11662";s:14:"
 (всего ";s:15:"4:11669:numall:";s:0:"";s:7:"2:11676";s:6:")</p>
";}s:7:"2:11782";s:100:"
</td>
</tr>
</table>
<!-------------------------------------------------------------------------->
";s:15:"32:11793:alert:";a:3:{s:7:"2:11822";s:29:"<font color="red"><br><br><b>";s:14:"4:11828:alert:";s:0:"";s:7:"2:11840";s:11:"</b></font>";}s:7:"2:11951";s:105:"
<input type="hidden" name="blockid" id="blockid">
<input type="hidden" name="parent" id="parent" value="";s:15:"4:11958:parent:";s:0:"";s:7:"2:12020";s:61:"">
<input type="hidden" name="btmplate" id="btmplate" value="";s:17:"4:12029:btmplate:";s:0:"";s:7:"2:12089";s:59:"">
<input type="hidden" name="cparent" id="cparent" value="";s:16:"4:12097:cparent:";s:0:"";s:7:"2:12278";s:180:"">
<input type="hidden" name="mcparent" id="mcparent" >
<input type="hidden" name="actions" value="">
				<div id="divtable" class="list-wrapper">
					<table  class="table-list">
";s:19:"32:12293:blocklist:";a:7:{s:7:"2:12349";s:56:"
						<thead><tr>
							<th class="id">ID</th>
							";s:10:"2048:12353";a:2:{s:4:"test";a:2:{s:13:"16:12384.1://";a:1:{s:20:"16:12384.2:cat_filtr";a:1:{s:27:"4:12384:unvis_option_block:";s:0:"";}}s:7:"2:12390";s:5:" != 1";}s:4:"true";a:1:{s:18:"16:12406:table_hr:";a:3:{s:7:"2:12431";s:25:"
							<th class="name">";s:14:"4:12437:title:";s:0:"";s:7:"2:12451";s:13:"</th>
							";}}}s:7:"2:12471";s:8:"
							";s:10:"2048:12475";a:2:{s:4:"test";a:2:{s:13:"16:12506.1://";a:1:{s:20:"16:12506.2:cat_filtr";a:1:{s:27:"4:12506:unvis_option_block:";s:0:"";}}s:7:"2:12512";s:5:" == 1";}s:4:"true";a:2:{s:18:"16:12528:table_hr:";a:3:{s:7:"2:12598";s:70:"
							<th nowrap="nowrap" style="background: #e7e7e7;" class="name">";s:14:"4:12604:title:";s:0:"";s:7:"2:12612";s:7:"</th>
	";}s:7:"2:12713";s:94:"
							<th nowrap="nowrap" style="background: #e7e7e7; width:90%" class="name">путь</th>
";}}s:7:"2:12992";s:274:"
							<th class="block-options"><label for="checked_all_checkbox" style="cursor:pointer;">Выделить&nbsp;все</label></th>
							<th class="check"><input type="checkbox" id="checked_all_checkbox" onclick="checked_all();" /></th>
						</tr></thead>
						<tbody>";s:19:"16:13008:blocklist:";a:29:{s:7:"2:13023";s:15:"
						<tr id="";s:11:"4:13026:id:";s:0:"";s:7:"2:13038";s:11:"" data-id="";s:11:"4:13041:id:";s:0:"";s:7:"2:13057";s:15:"" class="item" ";s:10:"2048:13061";a:2:{s:4:"test";a:1:{s:13:"16:13071.1://";a:1:{s:16:"4:13071:canedit:";s:0:"";}}s:4:"true";a:3:{s:7:"2:13074";s:1:" ";s:10:"2048:13078";a:2:{s:4:"test";a:2:{s:13:"16:13109.1://";a:1:{s:20:"16:13109.2:cat_filtr";a:1:{s:27:"4:13109:unvis_option_block:";s:0:"";}}s:7:"2:13115";s:5:" != 1";}s:4:"true";a:1:{s:7:"2:13162";s:46:"oncontextmenu="context(this.id); return false"";}}s:7:"2:13289";s:122:" onmousemove="tmove(this)"  onMouseOver="pTrOver(this); contexthide()" onMouseOut="pTrOut(this);" onclick="press(this.id)"";}}s:7:"2:13317";s:23:">
							<td class="id ";s:10:"2048:13321";a:2:{s:4:"test";a:2:{s:16:"4:13329:visible:";s:0:"";s:7:"2:13333";s:3:"!=1";}s:4:"true";a:1:{s:7:"2:13338";s:4:"gray";}}s:7:"2:13345";s:2:"">";s:11:"4:13348:id:";s:0:"";s:7:"2:13355";s:6:"</td>
";s:20:"16:13372:table_name:";a:5:{s:7:"2:13421";s:49:"
							<td nowrap="nowrap" class="name">
							";s:14:"32:13431:data:";a:13:{s:7:"2:13449";s:18:"<a href="?cparent=";s:13:"16:13459.1://";a:1:{s:16:"4:13459:cparent:";s:0:"";}s:7:"2:13470";s:10:"&btmplate=";s:13:"16:13481.1://";a:1:{s:17:"4:13481:btmplate:";s:0:"";}s:7:"2:13504";s:22:"&actions=edit&blockid=";s:13:"16:13510.1:..";a:1:{s:11:"4:13510:id:";s:0:"";}s:7:"2:13551";s:40:"" style="text-decoration:none" onclick="";s:10:"2048:13555";a:2:{s:4:"test";a:1:{s:13:"16:13565.1://";a:1:{s:16:"4:13565:canedit:";s:0:"";}}s:4:"true";a:3:{s:7:"2:13589";s:22:"$('blockid').value = '";s:13:"16:13595.1:..";a:1:{s:11:"4:13595:id:";s:0:"";}s:7:"2:13606";s:10:"'; edit();";}}s:7:"2:13634";s:23:" return false;" class="";s:10:"2048:13638";a:2:{s:4:"test";a:2:{s:13:"16:13649.1:..";a:1:{s:16:"4:13649:visible:";s:0:"";}s:7:"2:13653";s:3:"!=1";}s:4:"true";a:1:{s:7:"2:13658";s:4:"gray";}}s:7:"2:13665";s:2:"">";s:13:"4:13670:data:";s:0:"";s:7:"2:13675";s:4:"</a>";}s:7:"2:13680";s:2:"
	";s:15:"128:13690:data:";a:1:{s:7:"2:13696";s:6:"&nbsp;";}s:7:"2:13710";s:8:"
	</td>
";}s:7:"2:13760";s:43:"
							<td class="block-options">
								";s:10:"2048:13764";a:2:{s:4:"test";a:1:{s:13:"16:13774.1://";a:1:{s:16:"4:13774:canedit:";s:0:"";}}s:4:"true";a:3:{s:7:"2:13859";s:83:"<a class="redact" title="редактировать" onclick="$('blockid').value = ";s:11:"4:13862:id:";s:0:"";s:7:"2:13878";s:15:"; edit();"></a>";}}s:7:"2:13890";s:9:"
								";s:10:"2048:13894";a:2:{s:4:"test";a:1:{s:13:"16:13904.1://";a:1:{s:16:"4:13904:canhide:";s:0:"";}}s:4:"true";a:4:{s:7:"2:13921";s:15:"<a class="hide ";s:7:"2:14019";s:70:"" title="скрыть/показать" onclick="$('blockid').value = ";s:11:"4:14022:id:";s:0:"";s:7:"2:14044";s:21:"; hide_block();"></a>";}}s:7:"2:14056";s:9:"
								";s:10:"2048:14060";a:2:{s:4:"test";a:1:{s:13:"16:14070.1://";a:1:{s:16:"4:14070:cancopy:";s:0:"";}}s:4:"true";a:3:{s:7:"2:14147";s:75:"<a class="copy" title="копировать" onclick="$('blockid').value = ";s:11:"4:14150:id:";s:0:"";s:7:"2:14166";s:15:"; copy();"></a>";}}s:7:"2:14178";s:9:"
								";s:10:"2048:14182";a:2:{s:4:"test";a:1:{s:13:"16:14191.1://";a:1:{s:15:"4:14191:candel:";s:0:"";}}s:4:"true";a:3:{s:7:"2:14264";s:71:"<a class="delete" title="удалить" onclick="$('blockid').value = ";s:11:"4:14267:id:";s:0:"";s:7:"2:14282";s:14:"; del();"></a>";}}s:7:"2:14306";s:21:"
							</td>
							";s:10:"2048:14310";a:2:{s:4:"test";a:2:{s:13:"16:14341.1://";a:1:{s:20:"16:14341.2:cat_filtr";a:1:{s:27:"4:14341:unvis_option_block:";s:0:"";}}s:7:"2:14347";s:5:" == 1";}s:4:"true";a:3:{s:7:"2:14359";s:11:"<td><small>";s:13:"4:14364:path:";s:0:"";s:7:"2:14378";s:13:"</small></td>";}}s:7:"2:14446";s:63:"
							<td class="check"><input type="checkbox" id="massblock_";s:11:"4:14449:id:";s:0:"";s:7:"2:14468";s:18:"" name="massblock[";s:11:"4:14471:id:";s:0:"";s:7:"2:14482";s:10:"]" value="";s:11:"4:14485:id:";s:0:"";s:7:"2:14540";s:54:"" onclick="checked_all_number();" /></td>
</tr>
						";}s:7:"2:14561";s:14:"</tbody>
					";}s:7:"2:14593";s:26:"
					</table>
				</div>
";s:14:"32:14603:page:";a:3:{s:7:"2:14798";s:195:"
						<table style="paddin:0px; margin:0px">
								<tr >
									<td style="border: none; font-size: 11px;">Страницы:</td>
						<td style="width:99%; border: none; font-size: 11px;">";s:14:"16:14809:page:";a:5:{s:7:"2:14817";s:8:"
							";s:17:"32:14830:current:";a:1:{s:14:"4:14836:title:";s:0:"";}s:7:"2:14851";s:8:"
							";s:18:"128:14864:current:";a:6:{s:7:"2:14873";s:9:"<a href="";s:12:"4:14877:url:";s:0:"";s:9:"512:14883";a:3:{s:7:"2:14916";s:33:"str_replace('f_page', 'f_page1', ";s:13:"16:14935.1://";a:1:{s:17:"16:14935.2:filter";a:1:{s:18:"4:14935:urlfilter:";s:0:"";}}s:7:"2:14937";s:1:")";}s:7:"2:14945";s:2:"">";s:14:"4:14951:title:";s:0:"";s:7:"2:14956";s:4:"</a>";}s:7:"2:14988";s:26:"
											&nbsp; 
						";}s:7:"2:15030";s:35:"</td>
								</tr>
						</table>
";}s:7:"2:16525";s:1489:"
		</div>
		</td>
                        <td class="r_cont" style="width:30%">
							<script>
									function open_param()		{
										if ($('form_open_param').style.display == 'none')		{
											$('form_open_param').style.display = 'block';
											$('f_openparam').value = '1';
										}	else	{
											$('form_open_param').style.display = 'none';
											$('f_openparam').value = '0';
										}
									}
									function set_value(id, value)	{
										$(id).value = value;
									}
									function put_cat_filtr_catid()		{
											var string = '';
											var clear_tree = false;
											for (i = 0; i < $('cat_filtr_cat').options.length; i++)		{
												var opt = $('cat_filtr_cat').options[i];
												if ( (i == 0) &&  (opt.selected == true) )	{
													clear_tree = true;
												}
												if ( ( i > 0) && (clear_tree == true) ) 	{
													opt.selected = false;
												}
												if (opt.disabled == true)	{
													opt.selected = false;
												}
											}
											for (i = 0; i < $('cat_filtr_cat').options.length; i++)		{
												var opt = $('cat_filtr_cat').options[i];

												if (opt.disabled == false)	{
													if (opt.selected == true)	{
														string += opt.value + ';';
													}
												}
											}
											$('f_cat_filtr_catid').value = string;
									}
							</script>
							<input type="hidden" name="f_openparam" id="f_openparam" value="";s:13:"16:16546.1://";a:1:{s:17:"16:16546.2:filter";a:1:{s:20:"4:16546:s_openparam:";s:0:"";}}s:7:"2:16559";s:12:"" />
							";s:16:"16:16572:filter:";a:11:{s:7:"2:16815";s:243:"
                            <div class="rblock block_gray roll" >
								<div class="first" ><a href="#" class="roller" onClick="open_param(); return false;">Фильтр списка</a></div>
								<div class="body" id="form_open_param" ";s:10:"2048:16819";a:2:{s:4:"test";a:2:{s:13:"16:16840.1://";a:1:{s:17:"16:16840.2:filter";a:1:{s:20:"4:16840:s_openparam:";s:0:"";}}s:7:"2:16848";s:7:" == '0'";}s:4:"true";a:1:{s:7:"2:16869";s:20:"style="display:none"";}}s:7:"2:16875";s:1:" ";s:10:"2048:16879";a:2:{s:4:"test";a:2:{s:13:"16:16900.1://";a:1:{s:17:"16:16900.2:filter";a:1:{s:20:"4:16900:s_openparam:";s:0:"";}}s:7:"2:16908";s:7:" == '1'";}s:4:"true";a:1:{s:7:"2:16930";s:21:"style="display:block"";}}s:7:"2:16995";s:60:" >
								<div style="padding: 0; margin: 10px;" >
								";s:10:"2048:16999";a:2:{s:4:"test";a:2:{s:13:"16:17014.1://";a:1:{s:20:"16:17014.2:cat_filtr";a:1:{s:11:"4:17014:is:";s:0:"";}}s:7:"2:17018";s:3:"==1";}s:4:"true";a:11:{s:7:"2:17153";s:134:"
									<h4>Искать в папках:</h4>
									<input type="hidden" name="f_cat_filtr_catid" id="f_cat_filtr_catid" value="";s:13:"16:17178.1://";a:1:{s:17:"16:17178.2:filter";a:1:{s:24:"4:17178:cat_filtr_catid:";s:0:"";}}s:7:"2:17247";s:68:"" />
									<select id="cat_filtr_cat" name="cat_filtr_cat" size="";s:13:"16:17271.1://";a:1:{s:20:"16:17271.2:cat_filtr";a:1:{s:20:"4:17271:select_size:";s:0:"";}}s:7:"2:17304";s:32:"" style="width:200px; font-size:";s:13:"16:17326.1://";a:1:{s:20:"16:17326.2:cat_filtr";a:1:{s:18:"4:17326:font_size:";s:0:"";}}s:7:"2:17400";s:73:"" multiple onClick="put_cat_filtr_catid();">
										<option value="0" ";s:10:"2048:17404";a:2:{s:4:"test";a:2:{s:13:"16:17429.1://";a:1:{s:17:"16:17429.2:filter";a:1:{s:24:"4:17429:cat_filtr_catid:";s:0:"";}}s:7:"2:17436";s:6:"=='0;'";}s:4:"true";a:1:{s:7:"2:17445";s:8:"selected";}}s:7:"2:17578";s:128:">во всех папках</option>
										<option value="-" disabled>- - - - - - - - - - - - - - - - - -</option>
									";s:13:"16:17596.1://";a:1:{s:19:"16:17596:cat_filtr:";a:3:{s:7:"2:17606";s:10:"
									";s:15:"16:17618:items:";a:3:{s:7:"2:17629";s:11:"
										";s:13:"4:17634:item:";s:0:"";s:7:"2:17645";s:10:"
									";}s:7:"2:17662";s:10:"
									";}}s:7:"2:17708";s:39:"
									

									</select>
								";}}s:7:"2:17722";s:9:"
								";s:17:"32:17735:s_field:";a:11:{s:7:"2:17744";s:9:"
								";s:17:"16:17758:s_field:";a:37:{s:7:"2:17764";s:6:"
					";s:21:"64:17782:datakey:date";a:3:{s:7:"2:17786";s:4:"<h4>";s:14:"4:17792:title:";s:0:"";s:7:"2:17799";s:6:":</h4>";}s:7:"2:17811";s:6:"
					";s:20:"64:17828:datakey:int";a:3:{s:7:"2:17832";s:4:"<h4>";s:14:"4:17838:title:";s:0:"";s:7:"2:17845";s:6:":</h4>";}s:7:"2:17857";s:6:"
					";s:23:"64:17877:datakey:double";a:3:{s:7:"2:17881";s:4:"<h4>";s:14:"4:17887:title:";s:0:"";s:7:"2:17894";s:6:":</h4>";}s:7:"2:17906";s:6:"
					";s:21:"64:17924:datakey:text";a:3:{s:7:"2:17928";s:4:"<h4>";s:14:"4:17934:title:";s:0:"";s:7:"2:17941";s:6:":</h4>";}s:7:"2:17953";s:6:"
					";s:25:"64:17975:datakey:textarea";a:3:{s:7:"2:17979";s:4:"<h4>";s:14:"4:17985:title:";s:0:"";s:7:"2:17992";s:6:":</h4>";}s:7:"2:18004";s:6:"
					";s:21:"64:18022:datakey:html";a:3:{s:7:"2:18026";s:4:"<h4>";s:14:"4:18032:title:";s:0:"";s:7:"2:18039";s:6:":</h4>";}s:7:"2:18051";s:6:"
					";s:23:"64:18071:datakey:select";a:3:{s:7:"2:18075";s:4:"<h4>";s:14:"4:18081:title:";s:0:"";s:7:"2:18088";s:6:":</h4>";}s:7:"2:18100";s:6:"
					";s:25:"64:18122:datakey:checkbox";a:3:{s:7:"2:18126";s:4:"<h4>";s:14:"4:18132:title:";s:0:"";s:7:"2:18139";s:6:":</h4>";}s:7:"2:18151";s:6:"
					";s:25:"64:18173:datakey:idnumber";a:3:{s:7:"2:18177";s:4:"<h4>";s:14:"4:18183:title:";s:0:"";s:7:"2:18190";s:6:":</h4>";}s:7:"2:18206";s:10:"
									";s:20:"64:18223:datakey:int";a:17:{s:7:"2:18374";s:151:"
									<table border="0" class="simple">
									<tr>
										<td>от:</td>
										<td class="w100"><input type="text" size="10" name="f_sea_";s:12:"4:18378:key:";s:0:"";s:7:"2:18391";s:12:"_1"  value="";s:16:"4:18399:value_1:";s:0:"";s:7:"2:18407";s:7:"" id="a";s:14:"4:18413:_rank:";s:0:"";s:7:"2:18426";s:12:"_1" >&nbsp;(";s:18:"4:18436:value_min:";s:0:"";s:7:"2:18572";s:135:")</td>
										<td></td>
									</tr>
									<tr>
										<td>до:&nbsp;</td>
										<td><input type="text" size="10" id="a";s:14:"4:18578:_rank:";s:0:"";s:7:"2:18595";s:16:"_2" name="f_sea_";s:12:"4:18599:key:";s:0:"";s:7:"2:18611";s:11:"_2" value="";s:16:"4:18619:value_2:";s:0:"";s:7:"2:18631";s:11:""  >&nbsp;(";s:18:"4:18641:value_max:";s:0:"";s:7:"2:18711";s:69:")</td>
										<td></td>
									</tr>
									</table>
									";}s:7:"2:18727";s:10:"
									";s:23:"64:18747:datakey:double";a:17:{s:7:"2:18898";s:151:"
									<table border="0" class="simple">
									<tr>
										<td>от:</td>
										<td class="w100"><input type="text" size="10" name="f_sea_";s:12:"4:18902:key:";s:0:"";s:7:"2:18915";s:12:"_1"  value="";s:16:"4:18923:value_1:";s:0:"";s:7:"2:18931";s:7:"" id="a";s:14:"4:18937:_rank:";s:0:"";s:7:"2:18950";s:12:"_1" >&nbsp;(";s:18:"4:18960:value_min:";s:0:"";s:7:"2:19096";s:135:")</td>
										<td></td>
									</tr>
									<tr>
										<td>до:&nbsp;</td>
										<td><input type="text" size="10" id="a";s:14:"4:19102:_rank:";s:0:"";s:7:"2:19119";s:16:"_2" name="f_sea_";s:12:"4:19123:key:";s:0:"";s:7:"2:19135";s:11:"_2" value="";s:16:"4:19143:value_2:";s:0:"";s:7:"2:19155";s:11:""  >&nbsp;(";s:18:"4:19165:value_max:";s:0:"";s:7:"2:19235";s:69:")</td>
										<td></td>
									</tr>
									</table>
									";}s:7:"2:19251";s:10:"
									";s:21:"64:19269:datakey:date";a:19:{s:7:"2:19421";s:152:"
									<table border="0" class="simple">
									<tr>
										<td>с:</td>
										<td class="w100"><input type="text" class="w100" name="f_sea_";s:12:"4:19425:key:";s:0:"";s:7:"2:19438";s:12:"_1"  value="";s:16:"4:19446:value_1:";s:0:"";s:7:"2:19454";s:7:"" id="a";s:14:"4:19460:_rank:";s:0:"";s:7:"2:19601";s:140:"_1" readonly></td>
										<td><img src="img/calendar.gif" alt="" class="calendar" onclick="popUpCalendar(this, document.getElementById('a";s:14:"4:19607:_rank:";s:0:"";s:7:"2:19813";s:205:"_1'), 'dd.mm.yyyy','left');return false;" height="20" width="34" style="cursor:pointer"></td>
									</tr>
									<tr>
										<td>по:&nbsp;</td>
										<td><input type="text" class="w100" id="a";s:14:"4:19819:_rank:";s:0:"";s:7:"2:19836";s:16:"_2" name="f_sea_";s:12:"4:19840:key:";s:0:"";s:7:"2:19852";s:11:"_2" value="";s:16:"4:19860:value_2:";s:0:"";s:7:"2:20000";s:139:""  readonly></td>
										<td><img src="img/calendar.gif" alt="" class="calendar" onclick="popUpCalendar(this, document.getElementById('a";s:14:"4:20006:_rank:";s:0:"";s:7:"2:20143";s:136:"_2'), 'dd.mm.yyyy','left');return false;" height="20" width="34" style="cursor:pointer"></td>
									</tr>
									</table>
									";s:14:"32:20153:year:";a:5:{s:7:"2:20191";s:38:"
									<div class="roll" id="f_sea_";s:12:"4:20195:key:";s:0:"";s:7:"2:20468";s:272:"_advanced" onClick="if (this.className == 'rollout')	{ this.className = 'roll' } else { this.className = 'rollout' }; ">
										<div class="arrow"><a class="false" href="#" onclick="return false;">По месяцам</a></div>
										<div class="hidden">
											";s:14:"16:20479:year:";a:5:{s:7:"2:20495";s:16:"
											<h5>";s:13:"4:20500:year:";s:0:"";s:7:"2:20534";s:33:"</h5>
											<p>
												";s:15:"16:20546:month:";a:13:{s:7:"2:20594";s:48:"
														<a href="#" onClick="set_value('a";s:13:"16:20606.1:..";a:1:{s:13:"16:20606.2:..";a:1:{s:14:"4:20606:_rank:";s:0:"";}}s:7:"2:20613";s:6:"_1', '";s:16:"4:20621:dayleft:";s:0:"";s:7:"2:20638";s:16:"'); set_value('a";s:13:"16:20650.1:..";a:1:{s:13:"16:20650.2:..";a:1:{s:14:"4:20650:_rank:";s:0:"";}}s:7:"2:20657";s:6:"_2', '";s:17:"4:20666:dayright:";s:0:"";s:7:"2:20686";s:19:"'); return false;">";s:14:"4:20692:title:";s:0:"";s:7:"2:20697";s:4:"</a>";s:16:"128:20708:_last:";a:1:{s:7:"2:20709";s:1:",";}s:7:"2:20728";s:13:"
												";}s:7:"2:20763";s:28:"
											</p>
											";}s:7:"2:20831";s:61:"
											<hr />
										</div>
									</div>
									";}s:7:"2:20847";s:10:"
									";}s:7:"2:20859";s:6:"
					";s:21:"64:20877:datakey:text";a:5:{s:7:"2:20921";s:44:"<input type="text" class="w100" name="f_sea_";s:12:"4:20925:key:";s:0:"";s:7:"2:20935";s:9:"" value="";s:9:"512:20940";a:3:{s:7:"2:20957";s:17:"htmlspecialchars(";s:14:"4:20963:value:";s:0:"";s:7:"2:20965";s:1:")";}s:7:"2:20973";s:2:"">";}s:7:"2:20985";s:6:"
					";s:25:"64:21007:datakey:textarea";a:5:{s:7:"2:21051";s:44:"<input type="text" class="w100" name="f_sea_";s:12:"4:21055:key:";s:0:"";s:7:"2:21065";s:9:"" value="";s:14:"4:21071:value:";s:0:"";s:7:"2:21074";s:2:"">";}s:7:"2:21086";s:6:"
					";s:21:"64:21104:datakey:html";a:5:{s:7:"2:21148";s:44:"<input type="text" class="w100" name="f_sea_";s:12:"4:21152:key:";s:0:"";s:7:"2:21162";s:9:"" value="";s:14:"4:21168:value:";s:0:"";s:7:"2:21171";s:2:"">";}s:7:"2:21183";s:6:"
					";s:23:"64:21203:datakey:select";a:1:{s:18:"4:21213:html_form:";s:0:"";}s:7:"2:21226";s:6:"
					";s:25:"64:21248:datakey:checkbox";a:1:{s:18:"4:21258:html_form:";s:0:"";}s:7:"2:21271";s:6:"
					";s:25:"64:21293:datakey:idnumber";a:5:{s:7:"2:21337";s:44:"<input type="text" class="w100" name="f_sea_";s:12:"4:21341:key:";s:0:"";s:7:"2:21351";s:9:"" value="";s:14:"4:21357:value:";s:0:"";s:7:"2:21360";s:2:"">";}s:7:"2:21375";s:9:"
								";}s:7:"2:21554";s:172:"
								<table border="0" class="simple">
								<tr>
									<td>Показывать:</td>
									<td>
										<select name="f_vis">
											<option value="-1" ";s:10:"2048:21558";a:2:{s:4:"test";a:3:{s:7:"2:21559";s:1:" ";s:13:"16:21572.1://";a:1:{s:17:"16:21572.2:filter";a:1:{s:12:"4:21572:vis:";s:0:"";}}s:7:"2:21582";s:9:" == '-1' ";}s:4:"true";a:1:{s:7:"2:21591";s:8:"selected";}}s:7:"2:21644";s:48:">все</option>
											<option value="-10" ";s:10:"2048:21648";a:2:{s:4:"test";a:3:{s:7:"2:21649";s:1:" ";s:13:"16:21662.1://";a:1:{s:17:"16:21662.2:filter";a:1:{s:12:"4:21662:vis:";s:0:"";}}s:7:"2:21671";s:8:" == '0' ";}s:4:"true";a:1:{s:7:"2:21680";s:8:"selected";}}s:7:"2:21739";s:54:">скрытые</option>
											<option value="1" ";s:10:"2048:21743";a:2:{s:4:"test";a:3:{s:7:"2:21744";s:1:" ";s:13:"16:21757.1://";a:1:{s:17:"16:21757.2:filter";a:1:{s:12:"4:21757:vis:";s:0:"";}}s:7:"2:21766";s:8:" == '1' ";}s:4:"true";a:1:{s:7:"2:21775";s:8:"selected";}}s:7:"2:21969";s:189:">активные</option>
										</select>
									</td>
								</tr>
								<tr>
									<td>Выводить по:</td>
									<td>
										<select name="f_limit">
											";s:19:"16:21985:limit_arr:";a:7:{s:7:"2:22012";s:27:"
											<option value="";s:14:"4:22018:limit:";s:0:"";s:7:"2:22021";s:2:"" ";s:10:"2048:22025";a:2:{s:4:"test";a:4:{s:7:"2:22026";s:1:" ";s:13:"16:22041.1://";a:1:{s:17:"16:22041.2:filter";a:1:{s:14:"4:22041:limit:";s:0:"";}}s:7:"2:22046";s:4:" == ";s:14:"4:22052:limit:";s:0:"";}s:4:"true";a:1:{s:7:"2:22062";s:8:"selected";}}s:7:"2:22068";s:1:">";s:14:"4:22074:limit:";s:0:"";s:7:"2:22095";s:20:"</option>
										";}s:7:"2:22185";s:83:"
										</select>
									</td>
								</tr>
								</table>								
								";}s:7:"2:22408";s:217:"
								<hr />
								<div style="width: 100%;">
									<div class="save"><input type="submit" value="Найти"></div> <div class="cancel"><input type="button" value="Cброс" onClick="document.location.href='";s:15:"4:22415:preurl:";s:0:"";s:7:"2:22616";s:200:"';"></div>
								</div>
								</div>
								</div>
                                <div class="last">&nbsp;</div>
                            </div>
							<div style="height:20px"></div>
							";}s:7:"2:23954";s:1331:"
                            <div class="func" id="opergroupdiv" style="display:none">
                                <div class="first"><a class="green" href="#" title="" onClick="document.blockform.actions.value='groupvis'; document.blockform.submit(); return false;">показать</a><br /><a href="#" title="" onClick="document.blockform.actions.value='grouphid'; document.blockform.submit(); return false;">скрыть</a><a class="red" href="#" title="" onClick="if (window.confirm('Вы действительно хотите удалить группу блоков?'))	{ document.blockform.actions.value='groupdel'; document.blockform.submit(); } return false;">удалить</a></div><hr />
                                <div class="last" id="number_selected_checkbox">Выделено 0</div>
                            </div>
                        </td>
		</tr>
		</table>
 </form>
					<div style="position:absolute;display:none;" id="divcursor"><img id="im" border=0 style="cursor:hand;filter:alpha(opacity=100)" src="img/bg-top.gif"></div>

					 <div style="position:absolute;display:none;width=135px;left:10px;top:10px" id="popup" class="service">

                                             <div class="bg">
                                                <div class="bg2">
												
												";s:10:"2048:23958";a:2:{s:4:"test";a:1:{s:16:"4:23966:canedit:";s:0:"";}s:4:"true";a:1:{s:7:"2:24192";s:224:"
                                                    <div onclick="edit();" onMouseOver="pTrOver(this)" onMouseOut="pTrOut(this);"><a href="" title="" onClick="return false;">Редактировать</a></div>
												";}}s:7:"2:24210";s:13:"
												";s:10:"2048:24214";a:2:{s:4:"test";a:1:{s:16:"4:24222:canmove:";s:0:"";}s:4:"true";a:1:{s:7:"2:24418";s:194:"
													<div onclick="move();return false;" onMouseOver="pTrOver(this)" onMouseOut="pTrOut(this);"><a href="" title="" onClick="return false;">Переместить</a></div>
												";}}s:7:"2:24436";s:13:"
												";s:10:"2048:24440";a:2:{s:4:"test";a:1:{s:16:"4:24448:cancopy:";s:0:"";}s:4:"true";a:1:{s:7:"2:24686";s:236:"
                                                    <div onclick="copy();return false;" onMouseOver="pTrOver(this)" onMouseOut="pTrOut(this);"><a href="" title="" onClick="return false;">Создать копию</a></div>
												";}}s:7:"2:24704";s:13:"
												";s:10:"2048:24708";a:2:{s:4:"test";a:1:{s:16:"4:24716:canhide:";s:0:"";}s:4:"true";a:1:{s:7:"2:24988";s:270:"
                                                    <div onclick="hide_block();" onMouseOver="pTrOver(this)" onMouseOut="pTrOut(this);"><a href="#" title="" onClick="return false;">Скрыть/показать</a></div>
                                                ";}}s:7:"2:25006";s:13:"
												";s:10:"2048:25010";a:2:{s:4:"test";a:1:{s:15:"4:25017:candel:";s:0:"";}s:4:"true";a:1:{s:7:"2:25245";s:226:"
                                                    <div class="last"  onclick="del();"  onMouseOver="pTrOver(this)" onMouseOut="pTrOut(this);"><a href="" title="" onClick="return false;">Удалить</a></div>
												";}}s:7:"2:25609";s:359:"
                                                </div>
                                            </div>


			
			</div>
			
<script>

document.onmousemove = function(event) {
      var event = event || window.event;
	  XY = mousePageXY(event);
      //document.body.innerHTML = "x = " + mousePageXY(event).x + ", y = " + mousePageXY(event).y;
}

</script>
";}s:4:"item";a:12:{s:7:"2:25648";s:19:"
			<option value="";s:11:"4:25651:id:";s:0:"";s:7:"2:25654";s:2:"" ";s:15:"128:25664:flag:";a:1:{s:7:"2:25691";s:27:"disabled style="color:#999"";}s:7:"2:25698";s:1:" ";s:18:"32:25712:selected:";a:1:{s:7:"2:25720";s:8:"selected";}s:7:"2:25727";s:1:">";s:13:"4194304:25735";a:2:{s:5:"loops";a:4:{s:14:"4:25741:level:";s:0:"";s:7:"2:25743";s:1:"-";s:13:"16:25761.1://";a:1:{s:20:"16:25761.2:cat_filtr";a:1:{s:14:"4:25761:level:";s:0:"";}}s:7:"2:25764";s:2:"-1";}s:8:"repeated";a:1:{s:7:"2:25783";s:18:"&nbsp;&nbsp;&nbsp;";}}s:13:"4:25797:name:";s:0:"";s:7:"2:25811";s:13:"</option>
			";s:13:"4:25816:item:";s:0:"";s:7:"2:25819";s:2:"
	";}}