<!DOCTYPE HTML>
<html>
<head>
	<style>
		form#cog-form{
			margin-top:70px; 
			margin-left:22%; 
			width:56%;
			padding:15px;
			border:0px solid lightgrey;
			padding-bottom:30px;
			border-radius:5px;
			border-right: 1px solid #f6f6f6;
			border-bottom: 1px solid #f6f6f6;
			box-shadow: -3px -3px 3px #f6f6f6;
			margin-bottom:50px;

			<?php 
				if(empty($_GET["initial"])):
			?>
			    /*display:none*/
			<?php endif ?>
			}
			span.bike-controller{
				height:10px;
				margin-top:100px;
				box-sizing: border-box;

			}
			.dashicons-arrow-down-alt2:before,
			.dashicons-arrow-up-alt2:before{
				line-height: 1.5 !important;
			}

			form#cog-form input{
				border: 1px solid #e8e8e8;
			}

			#toggler{
				cursor:pointer;
				background:#fafafa;
				height:70px;
				padding-top:20px;
				box-sizing: border-box;
			}

			h1, h2{
				text-align:center;
			}

			div.form-row{
				margin-top:30px;
			}
	</style>
</head>
<body>
	
	<div class="wrap alignCenter" style="background:white;width:50%;margin:40px auto;">

	<!--h1 style="color:orange;text-align:center" onclick="toggle()" id="toggler"-->
	<h1 style="color:orange;text-align:center" onclick="toggle()" id="toggler">

		<span class="bike-controller">
			Add Bike Settings Below
		</span>
		<span class="bike-controller">
			<i class="dashicons-before dashicons-arrow-up-alt2">
			</i>
		</span>
	</h1>

	<?php if(array_key_exists("initial", $_GET) && !empty($_GET["initial"])): ?>
		<h3 style="text-align:center;font-style: italic">
			
			<b>
				<?= $_GET["initial"] ?>
			</b>
		</h3>
	<?php endif; ?>
	
	<form method="POST" id="cog-form" action="">

		<div class="form-row" style="width:100%">
			<h3>BIKE NAME</h3>
			<input type="text" class="form-control" 
			placeholder="Bike display name" name="bike_name" style="width:100%">
			<br>
		</div>

		<div class="form-row" style="width:100%">
			<h3>BIKE SLUG NAME</h3>
			<input type="text" class="form-control" 
			placeholder="Bike path" name="bike_slug" style="width:100%">
		</div>

		<div class="form-row" style="width:100%">
			<h3>BIKE SKU (Default: elephant_bike)</h3>
			<input type="text" class="form-control" 
			placeholder="Bike stock keep unit eg elephant_bike" name="bike_sku" style="width:100%">
		</div>

		<div class="form-row" style="width:100%">
			<h3>INITIAL COLOR</h3>
			<select class="form-control" name="initial_color" style="width:100%;max-width:100%; min-width:100%">
			    <option value="">Select Initial Bike Color</option>
				<option value="#F3E03B">Zinc Yellow</option>
				<option value="#F0CA00">Traffic Yellow</option>
				<option value="#DD7907">Yellow Orange</option>
				<option value="#E75B12">Pure Orange</option>
				<option value="#E1A6AD">Light Pink</option>
				<option value="#5E2028">Wine Red</option>
				<option value="#D15B8F">Heather Violet</option>
				<option value="#E9E5CE">Oyster White</option>
				<option value="#FDF4E3">Cream</option>
				<option value="#FFFFFF">Pure White</option>
				<option value="#83639D">Blue Lilac</option>
				<option value="#384C70">Violet Blue</option>
				<option value="#13447C">Gentian Blue</option>
				<option value="#3481B8">Light Blue</option>
				<option value="#26392F">Fir Green</option>
				<option value="#48A43F">Yellow Green</option>
				<option value="#008754">Traffic Grey</option>
				<option value="#596163">Basalt Grey</option>
				<option value="#6F4F28">Olive Brown</option>
				<option value="#633A34">Chesnut Brown</option>
			</select>
			<br>
			<br>
			<button type="submit" class="button button-primary button-large pull-right alingLeft">
				SAVE CHANGES
			</button>
		</div>
		<h4 style="text-align:center">&copy;Cycle Of Good <?= date("Y") ?></h4>

	</form>
	</div>
</body>
<script type="text/javascript">
	function toggle(){

		var form, target, classes, t_class;

		form 		= document.querySelector('#cog-form');

		target 		= document.querySelector(".bike-controller i");

		classes 	= target.getAttribute("class").split(" ");

		t_class 	= classes.indexOf("dashicons-arrow-down-alt2") >= 0? "dashicons-arrow-up-alt2":"dashicons-arrow-down-alt2";
		t_index 	= t_class=="dashicons-arrow-down-alt2"? 
					classes.indexOf("dashicons-arrow-up-alt2"):classes.indexOf("dashicons-arrow-down-alt2")

		classes[t_index] = t_class;


		target.setAttribute("class", classes.join(" "));
		if(t_class=="dashicons-arrow-down-alt2"){
			form.style.display = "none";
		}else{
			form.style.display = "block";
		}
	}
</script>
</html>

