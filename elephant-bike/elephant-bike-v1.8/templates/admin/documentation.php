<!DOCTYPE HTML>
<!DOCTYPE html>
<html>
<head>
	<title>Elephant Bike Documentation</title>
	<style type="text/css">
		div.masterdi{
			width:50%;
			padding:0px 0px 20px; 
			margin:30px auto;
			background:white;
			box-shadow:5px 5px 5px #e7e7e7;
			border:2px solid white;
		}

		p{
			font-size:1.2em;
			width:90%;
			margin:20px auto;
		}

		code#bike{
			margin-left:30px;
		}

		h1,h3{
			text-align:center;
			color:orange;
			background: #fafafa;
			margin-bottom: 30px;
		}

		h2{
			padding-left:5%;
			font-weight:600;
			margin-top:40px;
		}
		ol{
			margin-left:10%;
		}

		@media screen and (max-width:700px){
			div.masterdiv{
				width:75%;
			}
		}
	</style>
</head>
<body>
	<div class="wrap masterdiv">
		<h1 style="padding:20px 0px">ELEPHANT BIKE DOCUMENTATION</h1>
		<h2>Introduction</h2>
		<p>This documentation covers the basic usage of the Elephant Bike plugin, so as to help make the efficient use of this plugin.</p>

		<h2>Initial Setup</h2>
		<p>
			To make <b>Elephant Bike</b> visible on the website: Create a page wherefrom you want to show the bike, and in there, using any text editor, e.g Elementor, WPBakery, Block Editor etc, 
			<br>paste anywhere the following <b>Code</b>:
				<br>
		</p>
		<p>
					<code id="bike">
						[elephant_bike]
					</code>

				
		</p>
		<p>
			This will let the plugin and wordpress know where exactly you want your bike to appear within your website.<br>

			Now, navigate to the page where you placed the above short code, and voilla! there comes the bike. 
		</p>
		<p>


			<h2>Admin Settings</h2>
			<p>
				Now that we have our bike running on the website, 
				the admin can configure and change the following:<br>
				<ol>
					<li>Bike Name</li>
					<li>Bike SLUG (Path/Url)</li>
					<li>Bike SKU (Stock Keep Unit)</li>
					<li>Bike Default Color</li>
				</ol>
			</p>
			<p>
				To have this configurations, once logged in to your admin Dashboard, 
				<br>
				hover the mouse on the <b>Elephant Bike</b>
				admin menu, and click on <b>Add Settings</b>.
				
				Fill the settings fields and <b>Save Changes</b>.
				<br>
				<br>
				<b>N.B:</b> Stock Keep Unit (SKU)is a special variable that helps to track the items in the store/stock.<br>
				If you change the Bike's woocommerce SKU, then you should also update it in this plugin.<br>
				The <b>SLUG</b> will determine where the Plugin should redirect when a Bike Product is clicked on the shop.<br>
				The default Bike SLUG Name is <code>elephant-bike</code>, which means <code>http://sitename.com/elephant-bike</code>, 
				<br>is the default path for the Bike Project. 
				</p>
			<p>
				If the slug changes, then the default Elephant Bike project will be: 
				<code>http://sitename.com/slug</code><br>
				If the given SLUG does not exist, then the default Product URL will interpret to:
				<br>
				<code>http://sitename.com/product/slug</code> <br>
				and the default product Page will be displayed not this project's page.<br>
			</p>

			<p>
				Therefore, you need to replace this with the URL path of the page where you inserted the Shortcode on Step 1.

				Now go to the Elephant Bike site and see your changes apply.
			</p>
			
			<h2>Client Side Usage</h2>
			<p>
				Once at the User Side, We can now change the Bike Color as we please our bike to look, and once we are done, we can add the bike to the Cart by clicking the button.<br>
				The page won't reload, but the message that the item has been added to the Cart will appear.<br>
				You can follow the link that comes with the message to view our Cart.
			</p>
			<p>The Above Are the basic Usage of the Elephant Bike Plugin</p>
			<p style="text-align:center;text-shadow:0px 2px 2px lightgrey">Plugin Documentation By: <a href="http://codenug.com/webmaster">Amin Matola</a></p>
	</div>

</body>
</html>
