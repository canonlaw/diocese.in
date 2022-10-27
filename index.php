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

				// Churn through all returned data for a country code
				for(i=0; i < results.length; i++){
					for(var j=0;j < results[i].address_components.length; j++){
						for(var k=0; k < results[i].address_components[j].types.length; k++){
							if(results[i].address_components[j].types[k] == "country"){
								country = results[i].address_components[j].short_name;
							}
						}
					}
				}

				if(country != "US")
				{
					// If you want me to make your country available,
					// send a database with the dioceses of your country
					// and the corresponding postal codes. Thank you
					if(country == "VA")
					{
						// Vatican Easter Egg
						$("div#info").html('<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/31/Coat_of_arms_Holy_See.svg/100px-Coat_of_arms_Holy_See.svg.png" align="right"><a href="./" style="text-decoration: none;">📍</a> You are in the...<br /><br /><strong>Diocese of Rome</strong><br />P.zza S. Giovanni in Laterano 6<br />00184 Roma<br /><a href="https://www.vatican.va">https://www.vatican.va</a><br /><br /><strong><em>Diocesan Bishop</strong></em><br />Francis<br /><em>...una cum famulo tuo Papa nostro Francisco...</em>');
					} else if(country == "GU") {
						// we can support Guam too, as a treat
						$.get("getinfo.php?zip=96910", function( diocese ) {
							console.log("ZIP = 96910");
							console.log(diocese);
							$("div#info").html(diocese);
						});
					} else {
						console.log(country);
						$("div#info").html("Sorry, this service is only available in the United States and Vatican City.");
						badcountry = true;
					}
				} else {
					// Churn through all returned data for a zip code
					for(i=0; i < results.length; i++){
						for(var j=0;j < results[i].address_components.length; j++){
							for(var k=0; k < results[i].address_components[j].types.length; k++){
								if(results[i].address_components[j].types[k] == "postal_code"){
									zipcode = results[i].address_components[j].short_name;
								}
							}
						}
					}

					$.get("getinfo.php?zip="+zipcode, function( diocese ) {
						console.log("ZIP = " + zipcode);
						console.log(diocese);
						$("div#info").html(diocese);
					});
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
				<form action="index.php" method="get"><label for="zip">Manual zip code: </label><input id="zip" name="zip" type="text" pattern="[0-9]*" maxlength="5" size="7"><input class="button" type="submit" value="Go"></form>
			</div>
				<br />
			<div id="credits">
				Buy ☕: <a href="https://paypal.me/PaulHedman" target="new">PayPal</a> - <a href="https://cash.me/$PaulHedman" target="new">CashApp</a> - <a href="https://venmo.com/penguinpaul" target="new">Venmo</a><br />
				Send 📧: <a href="mailto:webmaster@diocese.in">webmaster@diocese.in</a>
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
