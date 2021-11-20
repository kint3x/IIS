

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
            if (data.success) {
            	add_to_cart_animation(btn);
            } else {
             	alert(data.error);
            }
    	});

}

function add_to_cart_animation(btn){

	$(btn).fadeOut(200);
	$(btn).fadeIn(200);


}

$( document ).ready(function() {
	$("#cartModal").on('shown.bs.modal', function () {

		reload_cart();

	});
});


function reload_cart(){
		var formData = {
		'cart_action' : 'get_cart',
	};

	$("#cartModal").find(".cart").html('<div class="d-flex row justify-content-center mt-2"><img class="img-fluid" style="height: 3rem" src="/img/loading-buffering.gif"/></div>');

	var request = $.ajax({
    type: "POST",
    url: "/ajax/cart_action.php",
    data: formData,
    dataType: "json",
    encode: true,
  }).done(function (data) {
    	$("#cartModal").find(".cart").html("");
		var length = data.length;
	    jQuery.each(data,function(i, val){
					var html = ''

					if (i === length - 1) {
						html += '<div class="row align-items-center">';
					} else {
						html += '<div class="row border-bottom align-items-center">';
					}
		    		html += '<div class="col-sm-5">'+val.name+'</div>';
		    		html += '<div class="col-sm-1 offset-sm-1"><a href="#" onclick="cart_action('+val.id+',\'decrease_item\')">-</a></div>';
					html += '<div class="col-sm-1">' + val.count + '</div>';
					html += '<div class="col-sm-1"><a href="#" onclick="cart_action('+val.id+',\'increase_item\')">+</a></div>';
		    		html += '<div class="col-sm-2">'+val.count*val.price+'&euro;</div>';
					html += '<div class="col-sm-1"><span class="close font-weight-light" style="cursor:pointer;" onclick="cart_action('+val.id+',\'remove_from_cart\')">&times;</span></div>';
						html += '</div></div>';
		    		$("#cartModal").find(".cart").append(html);


	    });
	    if(data.length == 0){
	    	$(".item-num").html("Váš košík je prázdny");
	    }
	    else if(data.length == 1){
	    	$(".item-num").html(data.length+" položka");
	    }
	    else if(data.length > 1 && data.length < 5){
	    	$(".item-num").html(data.length+" položky");
	    }
	    else{
	    	$(".item-num").html(data.length+" položiek");
	    }
	    

   
		});
}


function cart_action(id,action){
	var formData = {
		'cart_action' : action,
		'id' : id
	};

	$.ajax({
            type: "POST",
            url: "/ajax/cart_action.php",
            data: formData,
            dataType: "json",
            encode: true,
          }).done(function (data) {
            if (!data.error) {
            	 setTimeout(function(){
						   	reload_cart();
						   },100);
            } else {
             	alert(data.error);
            }
            
    	});

  
   

}
