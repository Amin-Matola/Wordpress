
/* *
 * This are the javascript functions that will handle the functionalities of the views
 * Plugin Name: 	Local Pickup Extender
 * By: 				Amin Matola - Cycle of Good Dev.
 * Last Modified: 	10/02/2020 12:55pm 
 */


/*************************************************************************************
*
* Get a specified html item
* */
function get_item(selector){
	return document.querySelector(selector);
}


/* *
 * Get the collection of items with a specified selector
 * */
 function get_items(selector){
 	return document.querySelectorAll(selector);
 }


/* *
* Check if an item has a said class 
* */
function has_class(what, item){
	if(item.getAttribute("class").trim() == "" ){ 
		return false;
	}
	var classes 		= item.getAttribute("class").toLowerCase().split(" ");
	for(i = 0; i < classes.length; i++){
		if(classes[i] == what) return true;
	} 

	return false;
}


/* *
 * Show a hidden item
 * */
 function show(item){
 	item.style 	= "display:block !important";
 	return true;
 }

/* *
 * Hide a specified item
 * */
function hide(item){
	item.style.display ="none";
	return true;
}


/* *
 * Change the color of a specified icon(s)
 * */
function change_icon_color(target){
	var targetItem 	  = target.getAttribute("id");
	var ship_icons 	  = get_items("#cog-btn-shipping .ship-icon");
	var pickup_icons  = get_items("#cog-btn-local-pickup .pickup-icon");

	if(targetItem == "cog-btn-shipping"){
		repaint_icons(ship_icons, "white");
		repaint_icons(pickup_icons, "#01c5b8");
	}
	else{
		repaint_icons(ship_icons, "#01c5b8");
		repaint_icons(pickup_icons, "white");
	}
}


/* *
 * Change the color of icon to a specified color
 * */
function repaint_icons(icons, color){
	for(i=0; i < icons.length; i++){
		icons[i].setAttribute("fill", color);
	}
}


function initialize_shipping_method(){
	parent 		= get_item("#shipping_method_0_field > span > #shipping_method");
	if(!parent){
		parent  = get_item("#shipping_method_0_field > span > #shipping_method_0");
	}

	if(!parent){
		return false;
	}

	if(!parent.classList.contains("shipping_method")){
		parent.classList.add("class", "shipping_method");
	}
	return true;
}


/* *
 * Change the background for the clicked item and color
 * */
function repaint(background, item, event){
	var children 		  = get_items(".cog-control-item");
	for(i = 0; i < children.length; i++){
		if(children[i].getAttribute("id").toLowerCase() != item.getAttribute("id").toLowerCase()){
			children[i].style.background = "inherit";
			children[i].style.color 	 = "#01c5b8";
		}
	}
	change_icon_color(item);
	item.style.background = background;
	item.style.color 	  = "white";

	toggle_shipping_forms(event);

}

/* *
 * Get an item based on the specified event
 * */
 function get_event_item_id(event){
 	return event.target.getAttribute("id");
 }



/* *
 * Toggle the methods to be used now
 * */
 function toggle_shipping_forms(event){

 	var target 			 		= get_item("."+get_event_item_id(event));

	 if(target.innerHTML.length < 1){
	 	window.location.reload(false);
	 	return true;
	 }

	 show(target);
	 	
	 if(has_class("cog-toggle-form-area", target)){
	 	var sorc 	  	= get_item("#customer_details > div.col-1 > div.woocommerce-shipping-fields select#shipping_method_0");
	 	var d;

	 	if(!sorc){
	 		sorc 		= get_item("#customer_details > div.col-1 > div.woocommerce-shipping-fields input#shipping_method");
	 		d    		= sorc.value;
	 	}
	 	else{
	 		d    		= sorc.options[sorc.selectedIndex].innerText;
	 	}

	 	var tgt 	  	= get_item("#order_review > table > tfoot > tr:nth-child(2) > td:nth-child(1) > h5");
	 	tgt.innerText 	= d; 
	 	hide(get_item("#customer_details > div.col-1.cog-toggle-form-area-2"));
	 	hide(get_item("#order_review > table > tfoot > tr:nth-child(2) > td:nth-child(2) > h5#free-local"));
	 	show(get_item("#order_review > table > tfoot > tr:nth-child(2) > td:nth-child(2) > h5.text-danger"));
	 	}else{
	 		var tgt 		= get_item("#order_review > table > tfoot > tr:nth-child(2) > td:nth-child(1) > h5");
	 		tgt.innerText	= "Select pickup location";
	 		show(get_item("#order_review > table > tfoot > tr:nth-child(2) > td:nth-child(2) > h5#free-local"));
	 		hide(get_item("#order_review > table > tfoot > tr:nth-child(2) > td:nth-child(2) > h5.text-danger"));
	 		

	 		hide(get_item("#customer_details > div.col-1.cog-toggle-form-area"));
	 		//get_item("h5.text-danger.pickup_location_current_fee").innerText = "FREE";
	 	}
	 }





/**
 * Check if an item is hidden
 */
function is_hidden(item){
	return item.style.display.toLowerCase() == "none";
}


/**
 * Toggle the checkout summary
 */
function show_summary(){
	var location = get_item("div.cog-order-review");
	var handler  = get_item(".cog-order-pointer");



	if(is_hidden(location)){
		handler.innerHTML = "Hide Order Summary <i class='fa fa-chevron-up'></i>";
		show(location);
	}else{
		handler.innerHTML = "Show Order Summary <i class='fa fa-chevron-down'></i>";
		hide(location);
	}
}


/* *
 * Set the event listener for a specified item
 * */
function set_event(event, item, callback_func){
	var target;
	if(item.tagName != undefined){
		target = item;
	}else if(item == "document"){
		document.body.onload = callback_func;
		return true;
	}
	else{
		target = get_item(item);
	}

	target.addEventListener(event, callback_func);

}



/* *
 * Get short month names to be used in input field, eg "10 - Feb - 2020"
 * */
function get_months(){
	return ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
}

function get_date_from_string(target){
	if(target.getAttribute("type") != "date"){
		var day, month, year, months, data;
		data 		= target.value.split(" ").join("").split("-");
		months 		= get_months();
		mon 		= months.indexOf(data[1])+1;
		month 		= mon.toString();

		day 		= data[0];
		month 		= month.trim().length < 2? "0"+month : month;
		year 		= data[2];

		return year+"-"+month+"-"+day;

		
	}
}





/* *
 * New Javascript from checkout form
 * */

function toggle_billing_form(e){
		var header, body, ship_h;
		header 			=   get_item("#bill")
		body 	   		= 	get_item("div.col-1.cog-toggle-form-area.cog-btn-shipping > div.woocommerce-billing-fields");
		ship_h 			= 	get_item("#customer_details > div.col-1.cog-toggle-form-area.cog-btn-shipping > h3:nth-child(7)");

		if(e.target.checked){
			hide( header );
			hide( body );
			show( ship_h );
			
		}
		else{
			show( header );
			show( body );
			hide( ship_h );
		}
}

/* *
 * Remove billing auto update totals on select billing country
 * */
function remove_class(class_name, item){
		var classes = item.getAttribute("class").split(" ");

		var results = classes.filter(function(v, i, a){ return v != "update_totals_on_change";});
		item.setAttribute("class", results.join(" "));

	}

/* *
 * Bypass woocommerce billing validations
 * */
function set_billing_country(country = ""){
		if(country.length < 1){
			country = get_item("#customer_details > div.col-1.cog-toggle-form-area.cog-btn-shipping > div.woocommerce-shipping-fields > div > div > #shipping_country_field > #shipping_country").value;
		}

		//alert("Resetting billing country to "+country);

		try{
			var billing_country = get_items("#billing_country_field > #billing_country");
			if(billing_country.length > 1){
				
				if(has_class("update_totals_on_change", billing_country[0].parentNode) || has_class("update_totals_on_change", billing_country[1].parentNode)){
					remove_class("update_totals_on_change", billing_country[0].parentNode);
					remove_class("update_totals_on_change", billing_country[1].parentNode);
				}
				billing_country[0].value = country;
				billing_country[1].value = country;

			}else{
				billing_country.value = country;
			}
		}catch(e){
			//alert(e.message);
		}

}

/* *
 * Resetting pickup location if shipping country changes
 * */
function reset_pickup_point(){
	pick_point = get_item("#customer_details > div.col-1.cog-toggle-form-area-2.cog-btn-local-pickup > #pickup_point_field > span > #pickup_point");
	if(pick_point != null && pick_point != undefined && pick_point.value.length > 1){
		pick_point.value = "";
		//alert("Now resetting pick_point")
	}
}


/**
 * Once the document has loaded, then bypass the date validation
 */
function reset_date(date = false){


	set_billing_country();

	var date_field  	= get_item("div.cog-toggle-form-area-2 .cog_arrival_date_field #arrival_date");
	var dt 			= date_field.value.toString().split(" ").join("");
	var months 		= get_months();

	var dt1 		= dt;
	if(!dt){
		dt 	= new Date();
	}else{
		dt = new Date(dt+"T00:00");
	}
			
	if(dt){
    	day = dt.getUTCDate().toString().length < 2? "0"+dt.getDate(): dt.getDate(),
        month = months[dt.getUTCMonth()],
        year = dt.getFullYear();

        var today = day + " - " + month + " - " + year;
        date_field.value = today;
    }
    
}

/* Event Handlers registration */
set_event("change",
		"#ship-to-different-address-checkbox",
		toggle_billing_form
		);


set_event("focus", 
	  "div.cog-toggle-form-area-2 .cog_arrival_date_field #arrival_date",
	  function(e){
		var months = get_months(), dt;
		dt 		   = this.value.toString().split(" ").join("");

	
		if(this.getAttribute("type") != "date" && this.value.split("-").length > 1){
			var dat   = dt.split("-");
			if(dat.length < 1){
				return true;
			}

			var m_num = dat.indexOf(dat[1])+1;
			var m_str = m_num.toString();
			var month = m_str.length < 2? "0"+m_str: m_str;
			this.setCustomValidity("");

			this.value= dat[0]+"-"+month+"-"+dat[0];

			this.setAttribute("type", "date");
		}
});


/**
 * Set the document event handling
 */
set_event("click", 
	  "#customer_details > div.col-1.cog-shipping-controls", 
	  function(e){

		var item, parent;
		item 		= e.target;
		parent 		= item.parentNode;

		if(!Boolean(parent) || !has_class("cog-shipping-controls", parent)){
			return false;
		}

		repaint("#01c5b8", item, e);
});

/* *
 * Get the pickup point value
 * */
 function get_pickup_point(){
 	return get_item("#customer_details > div.col-1.cog-toggle-form-area-2.cog-btn-local-pickup > #pickup_point_field > span > #pickup_point");
 }


 /* *
  * Get local pickup controller
  * */
  function get_pickup_controller(){
  	return get_item("#customer_details > div.col-1.cog-toggle-form-area-2.cog-btn-local-pickup");
  }


set_event("load",
	"document",
	reset_date);

set_event("focus", 
	  "div.cog-toggle-form-area-2 .cog_arrival_date_field #arrival_date",
	  function(e){
		this.setAttribute("type", "date");
});



set_event("mouseover", 
	  "div.cog-toggle-form-area-2 .cog_arrival_date_field #arrival_date",
	  function(e){
	
	  	var value 	= get_date_from_string(this);

		this.setAttribute("type", "date");
		if(value && value != this.value) this.value 	= value;
		
});

set_event("blur", 
	  "div.cog-toggle-form-area-2 .cog_arrival_date_field #arrival_date", 
	  function(){
    		this.setAttribute("type", "text");
    		reset_date();
})
	
set_event("change", 
	  "#customer_details > div.col-1.cog-toggle-form-area-2.cog-btn-local-pickup > #pickup_point_field > span > #pickup_point",
	  function(){
		var target 		= get_item("#order_review > table > tfoot > tr:nth-child(2) > td:nth-child(1) > h5");
		var item 		= get_item("div.col-1.cog-toggle-form-area-2.cog-btn-local-pickup #arrival_date_field.cog_arrival_date_field");
		show(item);
		target.innerText= this.options[this.selectedIndex].innerText;
})



var country = document.querySelector("#customer_details > div.col-1.cog-toggle-form-area.cog-btn-shipping > div.woocommerce-shipping-fields > div > div > #shipping_country_field > #shipping_country");

country.onchange = function(){
	try{
	 	reset_pickup_point();
	 	set_billing_country(this.value);
	 }catch(e){
	 	//alert(e.message);
	 }
	 	
}


/* *
 * Initialize the shipping method and its classes
 * */
 initialize_shipping_method();
 

/* ****** All the best javascripting and shipping ****** */

