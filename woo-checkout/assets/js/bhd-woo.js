(function($){
	
	$('input:checkbox').click(function() {
        $('input:checkbox').not(this).prop('checked', false);
    });

    $("#bhd-ship").click(function(e){
        e.preventDefault();
        
        var payments = $(".shipping-holder").find($("input[name='shipping_method[0]']"));
        var _all = [];
        payments.each(function(i){
            if($(this).is(":checked")){
                _all.push($(this).val())
            }
        })

		if( !Boolean($("#billing_phone").val())){
			//alert("Shipping Phone is a required!");

			$("#billing_phone").focus();
			var item_1 = document.querySelector("#billing_phone_field > span > #billing_phone")
			if(item_1) item_1.style = "border:2px solid red !important";
			return false;
		}
        
        if( _all.length >= 1){
            
            $(".col-1").hide();
            $(".shipping-holder").css({"display":"block"}).addClass("col-1");
        }
    })
})(jQuery);