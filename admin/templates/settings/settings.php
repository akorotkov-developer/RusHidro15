<? /* PHP-версия шаблона, т.к. в некотррых случаях ETS-версия глючит и каверкает JAVASCRIPT включения */ 
	global $_GTC;
?>
<script>

	function check_iframe()		{

		var arr = document.getElementsByTagName('iframe');
		for (i = 0; i < arr.length; i++)		{
			var id = arr[i].id;
			if (id.substr(0, 7) == 'ifrdata')	{
				var id2 = id + '_hid';				
				document.getElementById(id2).value = document.getElementById(id).contentWindow.GetContents();
				//alert (document.getElementById(id2).value);
			}
		}
		//alert (arr.length);

		//alert ('d');
		return true;
	}
</script>



        	<div class="options">
				<div class="bg_b">
					<div class="bg_l">
						<div class="bg_r">
							<div class="bg_tr">
								<div class="bg_tl">

									<div class="bg_bl">
										<div class="bg_br">
											<div >
												<h1 style="width:60%; "><img src="img/ico_file.gif" width="16" height="20" alt="" />Настройки сайта</h1>
												
												<h2></h2>
												
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


        	<table cellpadding="0" cellspacing="0">
            	<tr>
                	<td class="block_work">
                    	<div >
            				<div class="blue" >

                                <img class="blue_tl" src="img/blue_tl.gif" width="10" height="10" alt="" />
                                <img class="blue_tr" src="img/blue_tr.gif" width="10" height="10" alt="" />
                                <img class="blue_bl" src="img/blue_bl.gif" width="10" height="10" alt="" />
                                <img class="blue_br" src="img/blue_br.gif" width="10" height="10" alt="" />



	<table cellpadding="0" cellspacing="0" border="0">


	<form action="settings.php" method="post" enctype="multipart/form-data"  name="form1" onSubmit="return check_iframe();">


	<tr>
		<th><b>Файл robots.txt:</b></th>
		<td ><textarea name="DATA[robots]" cols="80" rows="10"><?=$_GTC->DATA['robots']?></textarea></td>
	</tr>

	<tr>
		<th><b>Временная блокировка сайта:</b></td>
		<td ><input type="checkbox" name="DATA[siteblock]" value="1" <? if ($_GTC->DATA['siteblock'] == 1) {?> checked="checked"<? } ?> /></td>
	</tr>

	<tr>
		<th><b>Текст страницы блокировки:</b></td>
		<td>
			 
		 <div id="idCTempdata0" style="display:none"><?=$_GTC->DATA['siteblock_stub']?></div>
			<?php
			echo input_html('data0', null, $_GTC->DATA['siteblock_stub']);
			?>
		</td>
	</tr>

	<tr>
		<th><b>Закачать файл в корень сайта (*.txt)</b></td>
		<td>
		<input type="file" name="txtfile" accept="text"> 
		</td>
	</tr>

	<tr>
		<th><b>Закачать файл иконки (favicon.ico)</b></td>
		<td>
		<input type="file" name="favicon" > 
		</td>
	</tr>


	</table>

<p></p>

                                <a name="bottom_page"></a>


								<table align="center" style="width:100px">
									<tr>
										<td>
                                		<div class="save" ><input type="submit" name="add" id="add_button_submit" value="Применить" /></div>
										</td>
									</tr>
								</table>

</TD>
	
                    <td class="block" style="width:10%">
            		</td>

                </tr>
            </table>



                              
	</form>
