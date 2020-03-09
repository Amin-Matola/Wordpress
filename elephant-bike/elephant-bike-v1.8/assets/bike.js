/* *
 * This is the main javascript to handle the manipulations
 * */





class Bike_Handler{

		constructor(){
				this.body 		= null;
				this.target 	= null;
				this.choice 	= null;
				this.color 		= null;
				this.body_choice= null;
				this.carrier 	= null;
				this.options 	= [];
				this.colors 	= {};
				this.handler 	= null;
				this.item 		= this;

				/* Call all the bootstrapping functions for this item */
				this.init();
		}


		/* *
		 * A Bootstrapping function for the handler/select and the bike.
		 * */
		init(c = "hd"){
				this.set_handler("hd");
				this.set_options();
				this.initialize_paint();
				this.set_event_handlers();
				this.initialize_handler_background(elephant_initial.initial);
				this.repaint_bike(this.get_handler_color());
		}


		/* *
		 * Initialize the painting of the items
		 * */
		 initialize_paint(){

		 	var target, options, background, element;
		 	
		 	target			 	 		= this.get_handler();
			options 			 		= this.get_options();

			target.style.background 	= this.options[0].value;

			for(let i = 0; i < options.length; i++){
				element 				 = this.options[i]
				element.style.background = element.value;
		
				if( element.value != "#FFFFFF" && element.value != "#FDF4E3" ){
					element.style.color 	= "white";
				}else{
					element.style.color 	= "black";
				}
			}

		}

		/* *
		 * When a color is chosen, it is better painting the controller again,
		 * so that the user knows what he/she did
		 * */
		repaint_controller(element){
			if(element.value == "#FFFFFF" || element.value == "#FDF4E3" || element.value == "#E9E5CE"){
				element.style = "color: #000000; border-bottom: 1px solid grey !important";
			}else{
				element.style = "border-bottom: 0px solid white !important;color:#FFFFFF;";
			}
			element.style.background = element.value;
			
		}



		/* *
		 * Set the handler for the color changing
		 * */
		set_handler(handler = "hd"){
			this.handler = document.querySelector("div.bike-right select#" + handler);
		}

		/* *
		 * Set the handler for the color changing
		 * */
		set_options(handler = this.get_handler()){
			this.options = handler.options;
		}



		/* *
		 * Get the SVG Element for this bike
		 * */
		 get_svg(){
		 	let svg = document.querySelector("svg");
		 	return svg;
		 }

		/* *
		 * Get the handler for changing colors
		 * */
		get_handler(){
			return this.handler;
		}

		/* *
		 * Get the value of this handler
		 * */
		 get_handler_color(){
		     var c_item = this.options[this.handler.selectedIndex];
			 if(Boolean(c_item)){
				 return c_item.value;
			 }
			 return this.handler.value;
		 }

		 /* *
		  * Get available handler options
		  * */
		  get_options(){
		  	return this.options;
		  }

		 /* *
		  * Get the selected item
		  * */
		  get_choice(){
		  	return this.options[this.handler.selectedIndex];
		  }


		/* *
		 * Get the class for inside a function other than 'this' of a method.
		 * */
		 get_class(){
		 	return this.item;
		 }


		/* *
		 * Repaint Bike when a color is chosen
		 * */
		repaint_bike(color = ""){
			var cls   = ".cls-";

			if((color.length && color.length < 1) || color.hasAttribute != undefined){
			    color = this.get_handler_color();
			}

			for(let i=1; i < 91; i++){
					if(i != 70 && i != 71 && i != 90 && i != 3){
	
						var d = document.querySelectorAll(cls + i);
						for(let j = 0; j < d.length; j++){
							try{
								d[j].style.fill = color;
							}catch(e){
								/* console.log(d[j]); */
							}
					}
				}

			}

		}

		get_item(item){
			return document.querySelector(item);
		}


		/* *
		 * Change the color of this bike controller
		 * */
		change_color(e, class_object){

			class_object.choice = e.target.options[e.target.selectedIndex];

			class_object.repaint_controller(e.target);
			class_object.repaint_bike(class_object.choice.value);
		}
	
		get_option_index(option){
			var hd 	= this.get_handler(), found = false;
			if(Boolean(hd)){
				for(let i = 0; i < hd.options.length; i++){
					if(hd.options[i].getAttribute("value") == option){
						found = i;
						break;
					}
				}
			}
			return found;
		}

		initialize_handler_background(color = ""){
			if(color.trim(" ").length < 1){
				color 			= "#F3E03B";
			}
			var hd 				= document.querySelector("div.bike-right select#hd"), selected_index;
			if(!Boolean(hd)){
				return false;
			}
			
			selected_index 		= this.get_option_index(color);
			
			if(isNaN(parseInt(selected_index)))
				selected_index 	= 0;
			
		 	hd.value 			= color;
		 	hd.style.color 		= hd.options[selected_index].style.color
			hd.style.background = color;	
		}

		/* *
		 * Change the color of the button based on the selected color
		 * */
		 repaint_button(e, master){
		 	e.target.style.background = master.get_handler_color();
		 	e.target.style.color 	  = master.get_handler().style.color;
		 }


		/* *
		 * Set the events for the handler
		 * */
		 set_event_handlers(){

		    let _this		 = this;

		    /* *
		     * Event handler on bike itself
		     * */
		    this.get_svg().addEventListener("load", _this.repaint_bike);

		    /* *
		     * First event handler on button
		     * */
		    this.get_item("#bikemaster").addEventListener("mouseover", function(e){
		    	_this.repaint_button(e, _this);
		    });

		    /* *
		     * Second event handler on button
		     * */
		    this.get_item("#bikemaster").addEventListener("mouseout", function(e){
		    	this.style.background 	= "#e7e7e7";
		    	this.style.color 		= "orange";
		    });

		    /* *
		     * Event handler on handler selector
		     * */
		 	this.get_handler().addEventListener("change", function(e){
		 		_this.change_color(e, _this);
		 	});

		 }

		 static test_handler(item = "#hd"){
		 	return this.prototype.get_item(item);
		 }

}


/* *
 * Check if this is the bike site inorder to avoid Item not found erros
 * */
if(Boolean(Bike_Handler.test_handler()))
	new Bike_Handler();
