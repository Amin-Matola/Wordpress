/* *
 * Create input field on the run time that should be used for readonly
 * */
function create_input_field(placeholder = "My field"){
		var tfield;

		tfield = document.createElement("input");
    tfield.setAttribute("type", "text");
		tfield.setAttribute("disabled", "true");
		tfield.setAttribute("placeholder", placeholder);
		return tfield;
}

/* *
 * Get the shipping options for selections
 * */
function set_shipping_options(options = []){
	  var target, firstChild, parent, input;
		
    target 	= document.querySelector("#order_review > table > tfoot > tr:nth-child(2) > td:nth-child(1) > p > select");
		firstChild = target.options[target.selectedIndex].innerText;
		input 		 = create_input_field(firstChild);

		parent 	= document.querySelector("#shipping_method_0_field > span > #shipping_method");
		if(!parent){
				parent = document.querySelector("#shipping_method_0_field > span > #shipping_method_0");
			}

		//alert(jQuery("p").html());
		if(options > 1){
				if(parent.parentNode.childNodes.length > 1){
						parent.parentNode.removeChild(parent.parentNode.childNodes[1]);
					}
				parent.style     = "display: block";
			 	parent.innerHTML = target.innerHTML;
								
				}else{
					parent.style = "display: none";
					parent.parentNode.appendChild(input);
	}
}

set_shipping_options();
