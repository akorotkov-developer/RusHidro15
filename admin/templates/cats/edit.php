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
												<h1 style="width:60%; "><img src="img/ico_file.gif" width="16" height="20" alt="" /><? if ($_GTC->id > 0) {?>Редактирование<?} if ($_GTC->id < 1) {?>Добавление<? } ?>&nbsp;папки</h1>
												
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


			<? if (isset($_GTC->urlforid)) {?>
				<table style="width:400px; margin-top:3px"  ><tr><td style="width:5%; padding-left:5px; font-size: 11px;">Ссылка:</td><td style="width:55%; font-size: 11px;">/<?=$_GTC->urlforid?></td></tr></table>
			<? } ?>


			<? if ($_GTC->id > 0) {?>
				<? if ($_GTC->row['canaddbl'] == 1) {?>
				<div class="new_block" style="margin-top:5px"><a href="block.php?cparent=<?=$_GTC->id?>" title="Список блоков">Перейти&nbsp;к&nbsp;блокам</a></div>
				
				<? }?>
			<? } //if ?>

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


	<form action="cat_edit.php" method="post" enctype="multipart/form-data"  name="form1" onSubmit="return check_iframe();">
	<input type="hidden" name="parent" value="<?=$_GTC->parent?>">
	<input type="hidden" name="id" value="<?=$_GTC->id?>">


	<tr >
		<th><b>Название:</b></th>
		<td ><input class="input" size=70 type="text" id="cat_name" name="cat_name" maxlength=255 value="<?=htmlspecialchars($_GTC->cr['name']);?>" <?if(!(int)$_GTC->id):?>onkeyup="translit('cat_name', 'cat_key');" onblur="translit('cat_name', 'cat_key');"<?endif;?> <?if ($_GTC->row['canedit'] != 1) { ?> <? if ($_GTC->row['caneditname'] != 1) { ?> <? if ($_GTC->user_is_super == 0) {?> <? if ($_GTC->id > 0) {?> disabled <?} } } }?>></td>
	</tr>

	<tr >
		<th><b>ЧПУ:</b></th>
		<td >
            <input class="input" size=70 type="text" id="cat_key" name="cat_key" maxlength=255 value="<?=htmlspecialchars($_GTC->cr['key'])?>" >
            <div class="generate-alturl-button" title="Сгенерировать" onclick="translit('cat_name', 'cat_key');"></div>
        </td>
	</tr>

    <tr>
		<th><b>ЧПУ от корня:</b></th>
        <td ><input class="input" type="checkbox" name="cat_alt_url" maxlength=255 <? if (intval($_GTC->cr['alt_url'])) { ?> checked="checked" <? } ?> value="1" ></td>
	</tr>


	<tr  >
		<th><b>Шаблон папки:</b></td>
		<td >

		<!-- mis change_template //-->
		<? if ($_GTC->id > 0) {?>
			<? if (!$_GTC->user_is_super) {?>
				<input type="hidden" name="template" value="<?=htmlspecialchars($_GTC->cr['template']);?>">
				<select disabled class="input" style="width:300px" ><option><?=htmlspecialchars($_GTC->disabled_select)?></option></select>
			<? } ?>
		<? } ?>

	
	    <? if ($_GTC->change_template) {?>
			<select name="template" class="input" style="width:300px" >
				<? if (is_array($_GTC->mtempl))foreach($_GTC->mtempl as $item) {?>
				<option value="<?=htmlspecialchars($item->row['key'])?>" <? if ($item->cr['template'] == $item->row['key']) {?> selected <? } ?>>
				<?=strip_tags($item->row['name']);?>
				<? if ($_GTC->user_is_super) {?> (<?=htmlspecialchars($item->row['key']);?>) <? } ?></option>

				<? } ?>
			</select>
		<? } ?>
		</td>
	</tr>

	<input type="hidden" name="templateinc" value="<?=htmlspecialchars($_GTC->cr['templateinc']);?>">


	<? if ($_GTC->edit_fields) {?>
	    <? if (is_array($_GTC->field)) foreach($_GTC->field as $item) {?>
				  <tr>
					<th style="padding-left:0px"><nobr><?=htmlspecialchars($item->rowd['name']);?>:
					
					<? if ($item->readonly) {?><br><font color="#C00000"><b>только чтение</b></font><? } ?>
					<? if ($_GTC->user_is_super) {?>
						<div><b><font color="green"><?=$item->rowd['key']?></font></b></div>
					<? } ?>
					</th>

					<td valign=top >
						<?=$item->s?>
						<input type="hidden" name="data<?=$item->i?>key" value="<?=htmlspecialchars($item->rowd['key'])?>">
					</td>
				  </tr>
		<? } ?>

	<? } ?>


	</table>

<p></p>

                                <a name="bottom_page"></a>


					<? if (isset($_GTC->b_save)) { ?>
								<table align="center" style="width:100px">
									<tr>
										<td>
                                		<div class="save" ><input type="submit" disabled name="add" id="add_button_submit" value="<? if ($_GTC->id > 0) {?>Сохранить<? } ?><? if ($_GTC->id < 1) {?>Добавить<? } ?>" /></div>
										</td>
									</tr>
								</table>
					<? } ?>

</TD>
	
                    <td class="block" style="width:10%">
            		</td>

                </tr>
            </table>



                              
	</form>
