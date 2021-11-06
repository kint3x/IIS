

function add_to_cart(id,btn){

	var formData = {
		'cart_action' : 'add_to_cart',
		'item_id' : id
	};

	$.ajax({
            type: "POST",
            url: "/ajax/cart_action.php",
            data: formData,
            dataType: "json",
            encode: true,
          }).done(function (data) {
            if (!data.error) {
            	add_to_cart_animation(btn);
            } else {
             	alert("Nepodarilo sa pridať do košíka.");
            }
            
    	});

}

function add_to_cart_animation(btn){

	$(btn).fadeOut(200);
	$(btn).fadeIn(200);


}