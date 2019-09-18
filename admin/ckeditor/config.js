/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	config.toolbarGroups = [
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'forms', groups: [ 'forms' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		'/',
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'others', groups: [ 'others' ] },
		{ name: 'about', groups: [ 'about' ] }
	];

	config.removeButtons = 'Save,NewPage,Preview,Print,Templates,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,CreateDiv,BidiLtr,BidiRtl,Language,Smiley,SpecialChar,PageBreak,Styles,Font,FontSize,TextColor,BGColor,Maximize,About';
    config.filebrowserBrowseUrl = 'elfinder/elfinder.html';
	config.filebrowserWindowHeight = 500;
	config.filebrowserWindowWidth = 800;
	config.filebrowserWindowFeatures = 'location=no,menubar=no,toolbar=no,dependent=no,minimizable=no,modal=no,alwaysRaised=no,resizable=no,scrollbars=no';
	config.disableNativeSpellChecker = false;
	config.browserContextMenuOnCtrl = true;
	config.height = 400;
};

CKEDITOR.on('dialogDefinition', function(event) {
	var editor = event.editor;
	var dialogDefinition = event.data.definition;
	//console.log(event.editor);
	var dialogName = event.data.name;

	var tabCount = dialogDefinition.contents.length;
	for (var i = 0; i < tabCount; i++) {
		var browseButton = dialogDefinition.contents[i].get('browse');

		if (browseButton !== null) {
			browseButton.hidden = false;
			browseButton.onClick = function(dialog, i) {
				editor._.filebrowserSe = this;
				var _dialog = _jQuery('<div id="filemanager-dialog"\>').each(function() {
						var _elfinder = _jQuery(this).dialogelfinder({
							url: "elfinder/php/connector.php",
							getFileCallback: function(url) {
								CKEDITOR.tools.callFunction(editor._.filebrowserFn, '/' + url.path);
								_elfinder.destroy();
								_jQuery('#filemanager-dialog').remove();
							},
							title: 'Файловый менеджер 2.0',
							resizable: true,
							lang: 'ru',
							height: '500px',
							width: '960px',
							destroyOnClose: true,
							uiOptions: {
								toolbar : [
									['back', 'forward'],
									//['netmount'],
									 ['reload'],
									 ['home', 'up'],
									['mkdir', 'mkfile', 'upload'],
									['open', 'download', 'getfile'],
									['info', 'chmod'],
									['quicklook'],
									['copy', 'cut', 'paste'],
									['rm'],
									['duplicate', 'rename', 'edit', 'resize'],
									['extract', 'archive'],
									['search'],
									['view', 'sort']
								]
							}
						}).elfinder('instance');
						_jQuery('#filemanager-dialog').css('zIndex', 90000);
					}
				);
			}
		}
	}
});