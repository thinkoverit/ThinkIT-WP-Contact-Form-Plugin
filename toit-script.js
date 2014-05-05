jQuery(document).ready(function() {
	try {

		jQuery(".toit-form-submit-button").click(function() {
			
   jQuery(this).attr("disabled", true);
   jQuery("#toit-image-loader").show();
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


function getRandomNumber(){
 return Math.floor((Math.random()*10)+1); 
}
 
function toitcfProcessJson(data) {
	var toitcfResultObj = jQuery("body").find('div.toitcf-ajax-result');
	 jQuery("#toit-image-loader").hide();
	 jQuery(".toit-form-submit-button").attr("disabled", false);
 
	toitcfShowMessage( data);
	if (data.success == "0") {
		toitcfResultObj.addClass('toitcf-validation-errors');
	}else{
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