<!doctype html>
<html>
<head>
	<title>What Diocese am I In?</title>
	<script src="//code.jquery.com/jquery-2.1.0.min.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;libraries=places,visualization&amp;key=AIzaSyCwtBQNdYu0GcDm98BL6f82uXHVTxI6MLs"></script>
	<script>
	$( window ).resize(function() {
		$("#site").css("height", $("#sitedata").css("height"));
	//	$("#site").css("width", $("#sitedata").css("width"));

	});
	$( document ).ready(function() {

		<?php
		if (isset($_GET['zip'])) {
		echo 'zipcode = "'.str_pad((int)$_GET['zip'], 5, "0", STR_PAD_LEFT).'";
		$.get("getinfo.php?zip="+zipcode, function( diocese ) {
			console.log(zipcode + "zip dioce"+ diocese);
			$("div#info").html(diocese);
		});';
	} else {
		?>
		//event.preventDefault();
		console.log('Determining address...');

		navigator.geolocation.getCurrentPosition(function (position) {
			console.log('New geocoder...');
			var geocoder = new google.maps.Geocoder();
			console.log('Getting latLng...');
			var latLng   = new google.maps.LatLng(
			position.coords.latitude, position.coords.longitude);
			geocoder.geocode({
				'latLng': latLng
			}, function (results, status) {
				console.log('Address results...' + status);
				badcountry = false;
				console.log(results);

				// figure out the country first
				for (var i = 0; i < results[0].address_components.length; i++) {
					var address = results[0].address_components[i];
					console.log("Looking for country...")
					if(address.types[0] == "country")
					{
						console.log('Country discovered... '+address.short_name);
						if(address.short_name != "US")
						{
							$("div#info").html("Sorry, this service is only available in the United States and our location detection seems to think you're not in the US of A.");
							badcountry = true;
						}
					}
				}

				// If it's not an invalid country, go looking
				if(!badcountry)
				{
					for (var j = 0; j < results[0].address_components.length; j++) {
						var zipAddress = results[0].address_components[j];
						console.log(j + " " + zipAddress.types[0]);
						if (zipAddress.types[0] == "postal_code") {
							console.log("Found postal code..." + j)
							zipcode = zipAddress.long_name;
							<?php if (isset($_GET['zip'])) {echo "zipcode = ".str_pad((int)$_GET['zip'], 5, "0", STR_PAD_LEFT).";";} ?>
							$.get("getinfo.php?zip="+zipcode, function( diocese ) {
								console.log(zipcode + "zip dioce"+ diocese);
								$("div#info").html(diocese);
								/*$.ajax({
									url: './coa/'+diocese.parent+'.png', //or your url
									success: function(data){
										$("#site").css("background-image", 'url("./coa/'+diocese.parent+'.png")');
									},
									error: function(data){
										// does not have a coat of arms on file
									},
								});*/
							});
						}
					}
				}

			});
		}, function (err) {$("div#info").html(`An error occurred: ${err.message}`)});
		return false;
	<?php } ?>
});
</script>
<link href="newstyle.css" rel="stylesheet" type="text/css" media="all">
<link href='//fonts.googleapis.com/css?family=Josefin+Slab' rel='stylesheet' type='text/css'>
<meta name="viewport" content="width=400, initial-scale=1">
</head>
<body>
	<div id="outer">
		<div id="sitedata">
			<div id="info">
				Loading... <br /><br />
			</div>
				<br />
			<div>
				Manually enter your zip code: <form action="index.php" method="get"><input id="zip" name="zip" type="text" pattern="[0-9]*"><input class="button" type="submit" value="Submit"></form>
			</div>
				<br />
			<div id="credits">
				Buy me ☕: <a href="https://paypal.me/PaulHedman" target="new">PayPal</a> - <a href="https://cash.me/$PaulHedman" target="new">CashApp</a> - <a href="https://venmo.com/penguinpaul" target="new">Venmo</a><br />
				📧: <a href="mailto:webmaster@diocese.in">webmaster@diocese.in</a>
			</div>
		</div>
	</div>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-57204706-1', 'auto');
	  ga('send', 'pageview');
	</script>
</body>
</html>
