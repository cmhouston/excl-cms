jQuery(document).ready(function($){
	$('.box-body').slideUp();
	$('body').on('click', '.box-head', function(){
		var sibling_body = $(this).siblings('.box-body').first();
		$('.box-body').each(function(){
			if(this != sibling_body.get(0))
				$(this).slideUp();
		});
		sibling_body.slideToggle();
	});
	$('.delete-notification').click(function(e){
		var clicked = $(this);
		var form = clicked.closest('form');
		e.preventDefault();
		var confirmation = confirm("Are you sure?");
		if(confirmation){
			clicked.closest('.wpscn-notif-form-box').remove();
		}
		$('#save-notifications').click();
	});
	$('#wpscn-notif-adder-btn').click(function(e){
		$('#wpscn-notif-adder .loading-img').show();
		var _status_from = $('#wpscn-notif-adder .status-from').val();
		var _status_to = $('#wpscn-notif-adder .status-to').val();
		if(_status_to == _status_from){
			$('#wpscn-notif-adder .loading-img').hide();
			alert('Old status and new status can not be the same');
			return;
		}
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 		'wpscn_get_notification_fields',
				post_type: 		$('#wpscn-notif-adder .post-type').val(),
				status_from: 	_status_from,
				status_to: 		_status_to,
				recipient: 		$('#wpscn-notif-adder .recipient').val(),
				format: 		$('#wpscn-notif-adder .format').val()
			},
			success:function(data, textStatus, XMLHttpRequest){
				$('.box-body').slideUp();
				$('#wpscn-notif-adder .loading-img').hide();
				$('#wpscn-notif-section-container').prepend(data);
			},
			error: function(MLHttpRequest, textStatus, errorThrown){
				alert(errorThrown);
			}
		});
	});
});