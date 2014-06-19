jQuery(function($){
	
	var pictureMetaBox = $('#pictures')
		.on('click', '.add-picture>a', function(event){
			event.preventDefault();
			
			var thisItem = $(this).closest('li');
			var lastItem = $(this).closest('ul').find('li:last');
			
			$(this).hide();
			thisItem.find('.picture-detail').show();
			lastItem.clone().appendTo($(this).closest('ul'));
			lastItem.show();
		})
		.on('change', '.picture-detail>.title>input', function(){
			var thisItem = $(this).closest('li');
			if($.inArray($(this).val(), $('.picture-list>li').map(function(){return $(this).attr('id');})) !== -1){
				$(this).css('border-color', '#B00');
				alert('该位置已经存在');
				return;
			}
			$(this).css('border-color', '#0B0');
			thisItem.attr('id', $(this).val());
		})
		.on('click', '.set-picture>a', function(event){
			event.preventDefault();
	
			var thisItem = $(this).closest('li');
			var position = thisItem.attr('id');
			
			if(!position){
				alert('请先输入位置，才能选择图片');
				return;
			}
			wp.media.frames[position] = wp.media({
				title: '设置' + position + '的画面',
				button: {
					text: '设置',
				},
				states : [
					new wp.media.controller.Library({
						title: '设置' + position + '的画面',
						filterable : 'all',
						multiple: false
					})
				]
			})
			.on('select', function(){
				var selection = wp.media.frames[position].state().get('selection');
				selection.map(function(attachment) {
					thisItem.find('.set-picture a').html($('<img/>', {src: attachment.attributes.url}));
					thisItem.find('.remove-picture').show();
					var pictures = $.parseJSON(pictureMetaBox.find('input#pictures').val()) || {};
					var position = thisItem.attr('id');
					pictures[position] = attachment.id;
					pictureMetaBox.find('input#pictures').val(JSON.stringify(pictures));
				});
			})
			.open();
		})
		.on('click', '.remove-picture>a', function(event){
			event.preventDefault();
			var thisItem = $(this).closest('li');
			var position = thisItem.attr('id');
			
			thisItem.remove();
			var pictures = $.parseJSON($('input#pictures').val()) || {};
			delete pictures[position];
			pictureMetaBox.find('input#pictures').val(JSON.stringify(pictures));
		});
	
	var framePictureSheetMetaBox = $('#frame-picture-sheet')
		.on('click', '.add-sheet>a', function(event){
			event.preventDefault();
			
			var sheets = $.parseJSON(framePictureSheetMetaBox.find('input#sheets').val()) || {};
			
			wp.media.frames.frameSheet = wp.media({
				title: '上传营业厅物料画面表格文件',
				button: {
					text: '加入导入队列',
				},
				states : [
					new wp.media.controller.Library({
						title: '上传营业厅物料画面表格文件',
						filterable : 'all',
						multiple: true
					})
				]
			})
			.on('select', function(){
				
				var list = framePictureSheetMetaBox.find('.sheet-list');
				var selection = wp.media.frames.frameSheet.state().get('selection');
				
				selection.map(function(attachment) {
					var lastItem = list.children('li:last');
					lastItem.clone().appendTo(list);
					lastItem.children('.sheet-file').text(attachment.attributes.filename);
					lastItem.find('a.download').attr('href', attachment.attributes.url);
					list.show();
					lastItem.attr('id', attachment.id).show();
					sheets[attachment.id] = 'queued';
				});
				console.log(JSON.stringify(sheets));
				framePictureSheetMetaBox.find('input#sheets').val(JSON.stringify(sheets));
				
			})
			.open();
		})
		.on('click', '.sheet-actions>a.remove', function(event){
			event.preventDefault();
			var sheets = $.parseJSON(framePictureSheetMetaBox.find('input#sheets').val());
			item = $(this).closest('li');
			delete sheets[item.attr('id')];
			framePictureSheetMetaBox.find('input#sheets').val(JSON.stringify(sheets));
			item.remove();
		});
	
});