

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


$( document ).ready(function() {
	$("#cartModal").on('shown.bs.modal', function () {

		reload_cart();

	});
});


function reload_cart(){
		var formData = {
		'cart_action' : 'get_cart',
	};

	$("#cartModal").find(".cart").html('<img class="img-fluid" style="margin-left:90px;" src="/img/loading-buffering.gif"/>');

	var request = $.ajax({
    type: "POST",
    url: "/ajax/cart_action.php",
    data: formData,
    dataType: "json",
    encode: true,
  }).done(function (data) {
    	$("#cartModal").find(".cart").html("");
	    jQuery.each(data,function(i,val){
	    		
		    		var html = '<div class="row border-top border-bottom">';
		    		html += '<div class="row main align-items-center">';
		    		html += '<div class="col-2"><img class="img-fluid" src="'+val.image+'">';
		    		html += '</div><div class="col"><div class="row text-muted">'+val.name+'</div>';
		    		html += '<div class="row">Ako být sexi</div></div><div class="col">';
		    		html += '<span class="h5" style="margin-left:4px;margin-right:4px;"><a href="#" onclick="cart_action('+val.id+',\'decrease_item\')">-</a>'+val.count+'<a href="#" onclick="cart_action('+val.id+',\'increase_item\')">+</a> </span></div>';
		    		html += '<div class="col">'+val.count*val.price+'€<span class="close" style="cursor:pointer;" onclick="cart_action('+val.id+',\'remove_from_cart\')">✕</span></div>';
						html += '</div></div>';
						console.log(val.count,val.price);
		    		$("#cartModal").find(".cart").append(html);

	    });

   
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
