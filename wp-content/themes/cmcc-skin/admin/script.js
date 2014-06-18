jQuery(function($){
	
	$('.picture-list')
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
					var pictures = $.parseJSON($('input#pictures').val()) || {};
					var position = thisItem.attr('id');
					pictures[position] = attachment.id;
					$('input#pictures').val(JSON.stringify(pictures));
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
			console.log(pictures);
			$('input#pictures').val(JSON.stringify(pictures));
		});
	
});