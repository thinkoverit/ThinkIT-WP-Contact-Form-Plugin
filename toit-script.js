jQuery(document).ready(function() {
	try {

		jQuery(".toit-form-submit-button").click(function() {
			
			var formObj =jQuery(this).parents("form");
			
			toitcf7CleanResults();
			jQuery('img.toitcf-ajax-loader', jQuery(formObj)).css({ visibility: 'visible' });
			jQuery(formObj).append('<input type="hidden" name="toitcf_ajax_call" value="1" />');
			
			var str = jQuery(formObj).serialize();

			jQuery.ajax({
				type: "POST",
				dataType:'json',
				url: jQuery(formObj).attr("action"),
				data: 'action=contact_form&'+str,
				success: function(data) {
					toitcfProcessJson(data);
				}
			});
			return false;  
		});
	}catch (e) {
	}
});
function toitcfBeforeSubmit(formData, jqForm, options) {
	toitcf7CleanResults();
	jQuery('img.toitcf-ajax-loader', jqForm[0]).css({ visibility: 'visible' });

	formData.push({name: 'toitcf_is_ajax_call', value: 1});
	jQuery(jqForm[0]).append('<input type="hidden" name="toitcf_is_ajax_call" value="1" />');

	return true;
}



function toitcfProcessJson(data) {
	var toitcfResultObj = jQuery("body").find('div.toitcf-ajax-result');

	toitcfShowMessage( data);
	if (data.success == "0") {
		toitcfResultObj.addClass('toitcf-validation-errors');
	}else{
		jQuery("div.toitcf-form > form").resetForm().clearForm();
		toitcfResultObj.addClass('toitcf-subscribed');
	}
	jQuery('div.toitcf-ajax-result').show();
}

function toitcfShowMessage(data) {

	if (data.success == "0") {
		var total = data.message.length;
		for(var i=0;i<total;i++){
			jQuery(".toit-"+data.message[i]['name']).append(data.message[i]['error']);
		}
		
	}else{
		var toitcfResultObj = jQuery("body").find('div.toitcf-ajax-result');
		toitcfResultObj.append(data.message);
	}

}

function toitcf7CleanResults() {
	jQuery('div.toitcf-ajax-result').hide().empty().removeClass('toitcf-validation-errors toitcf-subscribed');
	jQuery('img.toitcf-ajax-loader').css({ visibility: 'hidden' });
	jQuery('.toit-error').remove();
	
}