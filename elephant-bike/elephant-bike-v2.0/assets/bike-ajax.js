class Bike_Ajax{

	constructor(){
		this.id 		= null;
		this.variation_id 	= null;
		this.colors 	  	= {}
		this.set_target("div.bike-right select#hd");
		this.set_handler();
		this.set_handlers();
		this.init_color_map();
		this.set_id();
		this.set_variation_id();
		
	}

	/* *
	 * Set the this object/ constructor of this class
	 * */
	set_handler(){
		var _this = this;

		jQuery(document).ready(function(){

			/* *
			 * Check if the provided color is available
			 * */
			jQuery("#hd").change(function(e){
				_this.reset_id()
			})

			/* *
			 * Set callback to handle add to cart button
			 * */
			jQuery("#bikemaster").click(function(e){
				_this.do_ajax(_this)
			});
			
		});

	}

	/* *
	 * Set the target element of the options
	 * */
	set_target(target){
		this.target = document.querySelector(target);
	}

	/* *
	 * Set all handlers
	 * */
	 set_handlers(){
	 	this.handlers = document.querySelectorAll("div.bike-right select:not([name ^= 'attribute_colour'])");
	 }

	 /* *
	 * set the id of the bike
	 * */
	 set_id(){
	 	this.id = document.querySelector("div.bike-right input#product_id").value;
	 }

	 /* *
	 * set the id of the bike
	 * */
	 set_variation_id(){
	 	this.id = document.querySelector("div.bike-right input#variation_id").value;
	 }

	 /* *
    * Set the html for the display area
    * */
    set_display(text = ""){
    	jQuery("#jsresults").html( text );
    }

   /* *
	* Reset the id whenever the color is changed
	* */
	reset_id(){
		this.retrieve_id();
	}



   /* *
    * Get the target element for this class
    * */
    get_target(){
    	return this.target;
    }

   /* *
    * Retrieve color from the colors object
    * */
    retrieve_color(color){
    	return this.get_colors()[color];
    }

   /* *
	* Get the value of this handler
	* */
	get_target_color(){
	   let handler = this.get_target();
	   return this.retrieve_color(handler.value).trim();
	}

	/* *
	 * Get the id for the bike
	 * */
	 get_id(){
	 	return  this.id;
	 }

	 /* *
	 * Get the id for the bike
	 * */
	 get_variation_id(){
	 	return  this.variation_id;
	 }
	

	/* *
	 * Get all the selections
	 * */
	 get_handlers(){
	 		return this.handlers;
	 }

	/* *
	 * Paint all the items with the current chosen color
	 * */
	 repaint_handlers(){
		var _handlers = this.get_handlers(), _this = this;
		_handlers.forEach(function(i){
			i.style.background = _this.target.style.background;
			i.style.color      = _this.target.style.color;

		})
	}

	/* *
	 * Iterate through the entire list of data attributes and add them as 1 object;
	 * */
	 iterate_form_data(){
	 	 var _handlers  = this.get_handlers();
	 	 var  _data 	= {
	 	 	"attribute_colour":this.get_target_color(),
	 	 	"action":"add_bike",
	 	 	"quantity" 	  : 1,
			"add-to-cart" : this.get_id(),
			"product_id"  : this.get_id(),
			"variation_id": this.get_variation_id()
	 	 };
	 	 
	 	 _handlers.forEach(function(i){
	 	 	_data[i.getAttribute("name")] = i.value;
	 	 })

	 	 return _data;
	 }


   /* *
	* Set colors for this object
	* */
	init_color_map(){
		let tgt 		= this.get_target();

		if(!Boolean(tgt))
			return true;
		
		for(let i = 0; i < tgt.length; i++){
			this.add_color(tgt[i].value, tgt[i].innerText.trim());
		}
		
	}

   /* *
    * Add the color to the colors
    * */
	add_color(color, name){
		this.colors[color] = name;
	}

   
   /* *
    * Capitalize the word or sentence
    * */
	capitalize(text){
		return text.charAt(0).toUpperCase() + text.slice(1, text.length);
	}

	/* *
	 * Generate the valid color for this item
	 * */
	generate_valid_color(code){
		var colors, all, first_word, last_word;

		colors 		= this.get_colors();
		all    		= colors[code].split("_");

		first_word 	= this.capitalize(all[0]);
		last_word  	= this.capitalize(all[1]);

	}

   /* *
    * Get the colors object for this class
    * */
	get_colors(){
		return this.colors;
	}

   /* *
	* Retrieve the variation id on the real time
	* */
	retrieve_id(){
		var _this = this;
		jQuery.post(
			elephant_ajax.ajax_url,

			{
				"action":"add_bike",
				"bike_colour": this.get_target_color()
			},

			function(results, status){
		
				var val 			= JSON.parse(results.slice(0, -1));

				if(Boolean(val.bike_id)){
					_this.id 		= val.bike_id;
					
					if(!isNaN(parseInt(val.bike_id))) {
						_this.set_display(val.message);
						document.querySelector("div.bike-right input#variation_id").value = val.bike_id;
					}else{
						_this.set_display(val.bike_id);
						
					}

					_this.repaint_handlers();
				}else{
					_this.set_display("Sorry! " + _this.get_target_color() + " Bike is <b>not available</b> for purchase.");
				}
			})
	}

   /* *
    * Intercept and Process the ajax request and add the item to the cart
    * */
	do_ajax(master = this){
	    this.set_display("Adding "+this.get_target_color()+" to the cart...");
	    
		jQuery.post(
			elephant_ajax.ajax_url,

			this.iterate_form_data(),

			 function(results, status){
			 	master.process_ajax(results, status);
			 });
	}


   /* *
    * Finally process this ajax request
    * */
	process_ajax(results, status){
		
		if(status == "success"){
			results = JSON.parse(results.slice(0,-1));
			//window.location.reload(true);
			this.set_display(results.cart_message);
		}
		else{
			this.set_display("Sorry! Adding " + this.get_target_color() + " bike to cart failed.");
		}
	}

   /* *
    * Avoid javascript from executing on unnecessary pages
    * */
    static test_handler(item = "div.bike-right select#hd"){
		 	return document.querySelector(item);
	}
}

/* *
 * Make sure we are working with Bike Page
 * */
if(Bike_Ajax.test_handler())
	new Bike_Ajax();

