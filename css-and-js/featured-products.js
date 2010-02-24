jQuery('.wpsc_featured_product_toggle').livequery(function(){
	jQuery(this).click(function(event){
		target_url = jQuery(this).attr('href');
		post_values = "ajax=true";
		jQuery.post(target_url, post_values, function(returned_data){
			eval(returned_data);
		});
		return false;
	});
}); 