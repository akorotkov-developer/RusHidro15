(function($){
	var _jQuery = $;

	function dropzoneSize() {
		var dropzoneHeight = $('.dropzone').height();
		var dropzoneWidth = $('.dropzone').width();
		var dropzoneMargin = ($('.dropzone').height() / 2 ) - 12;
		$('#dropzone').width(dropzoneWidth);
		$('#dropzone').height(dropzoneHeight);
		$('#dropzone span').css('margin-top', dropzoneMargin + 'px');
	}

	$( document ).ready(function() {
		if ($('#dropzone').length > 0)
			dropzoneSize();
	});

	$(document).ready(function(){
		//var $ = _jQuery;
		var _dz = $('.dropzone');
		_dz[0].ondragover = function(){
			$('#dropzone').show();
		};
		var dz = $('#dropzone');
		//if (typeof(window.FileReader) == 'undefined') {
			// браузер не поддерживает drag&drop
		//}
		dz[0].ondragleave = function() {
			dz.hide();
			return false;
		};
		dz[0].ondragover = function(){
			return false;
		};
		dz[0].ondrop = function(event) {
			$('.bottomText').show();
			//console.log('dropped');
			event.preventDefault();
			event.stopPropagation();
			event.stopImmediatePropagation();
			dz.hide();

			var container = $('#packet-tpl').parent();
			var tpl = $('#packet-tpl').clone(true).removeAttr('id');

			var photos = 0, photos_loaded = 0,
                packet_sort = parseInt($('#packet_sort').val());

			for (var i = 0; i < event.dataTransfer.files.length; i++) {
				var file = event.dataTransfer.files[i];
				if (!/image/.test(file.type)) {
					continue;
				}
				photos++;
				(function(file){
					var data = new FormData();
					
					//console.log('packet_config', packet_config);
					
					data.append(packet_config.field, file);
					var _tpl = $(tpl).clone(true);
					_tpl.find('[name]').each(function(){
						var name = $(this).attr('name');
						var _name = name.split('[')[1].split(']')[0];
						var val = '';
						if (packet_config.def[_name]) {
							val = packet_config.def[_name];
						}
						if (_name == packet_config.increment) val += ' ' + photos;
                        if (_name == 'sort') val = packet_sort + photos;
                        
						$(this).val(val);
						data.append(name, val);
					});
					_tpl.appendTo(container).show();
					var href = location.href.replace('parent=', 'parent=' + $('#parent').val());
					
					//console.log('href',href);
					
					
					_jQuery.ajax(href, {processData: false, type: 'post', contentType: false, data: data}).done(function(data){
																												
						//console.log('data1',data);																							 
																														 
						data = JSON.parse(data);
						photos_loaded++;
						$('.pf_count').text(photos_loaded);
						_tpl.find('img.preview').attr('src', '/' + data.img);
                        _tpl.find('.id').text(data.id);
						_tpl.removeClass('load').attr('id', data.id).attr('onclick','press(this.id)');
						_tpl.find('[disabled]').removeProp('disabled');
						_tpl.find('a').html('удалить').click(function(){
							$.ajax(href + '&action=del&blockid=' + data.id).done(function(){
								_tpl.remove();
								resize();
								dropzoneSize();
							});
						});
						_tpl.find('input').change(function(){
							var _data = {};
							_data[$(this).attr('name')] = $(this).val();
							_data.id = data.id;
							$.ajax(href + '&edit=true', {data: _data, type: 'post'}).done(function(){
							});
						})
					});
					dz.removeClass('drop');
				})(file);
				resize();
				dropzoneSize();
			}
            $('#packet_sort').val(packet_sort + photos);
			$('.pf_amount').text(photos);
		};
	});
})($);