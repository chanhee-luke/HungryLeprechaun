<!doctype html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

	<style> #map { width: 100%; height: 50vh; background-color: grey; } ul.list-unstyled img { height:100%; width: 8vw; } .template { display:none; }</style>

	<title>Hello, world!</title>
</head>
<body>
	<h1>Hello, world!</h1>
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-6">
				<h3>Filters</h3>
				<form id="filter">
					<div class="form-group row">
						<label for="colFormLabel" class="col-sm-2 col-form-label">Name</label>
						<div class="col-sm-10">
							<input type="email" class="form-control" id="colFormLabel" placeholder="Type a name to find only similar">
						</div>
					</div>
					<div class="form-group row">
						<label for="colFormLabel" class="col-sm-2 col-form-label">Cost</label>
						<div class="col-sm-10">
							<select multiple class="form-control" id="exampleFormControlSelect2" style="overflow: hidden;">
								<option>$</option>
								<option>$$</option>
								<option>$$$</option>
								<option>$$$$</option>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="colFormLabel" class="col-sm-2 col-form-label">Distance</label>
						<div class="col-sm-10">
							<select class="custom-select">
								<option selected>Any</option>
								<option value="2">&lt;2 Miles</option>
								<option value="1">&lt;1 Mile</option>
								<option value=".5">&lt;.5 Miles</option>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="colFormLabel" class="col-sm-2 col-form-label">Number of Results</label>
						<div class="col-sm-10">
							<select class="custom-select">
								<option selected>All</option>
								<option value="10">Ten</option>
								<option value="5">Five</option>
								<option value="2">Two</option>
							</select>
						</div>
					</div>
				</form>
				<div id="map">
				</div>
			</div>
			<div id="resultList" class="col-lg-6">
				<h3>Results</h3>
				<ul class="list-unstyled">
					<li class="media template">
						<img class="mr-3 img" src="http://via.placeholder.com/64x64" alt="Generic placeholder image">
						<div class="media-body">
							<h5 class="mt-0 mb-1"><span class="name">Name</span> <span class="dist">(.5 mi)</span></h5>
							<span class="desc">Description</span>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<script src="https://maps.googleapis.com/maps/api/js?key=<?php require "key.txt" ?>&callback=initMap" async defer></script>
	<script type="text/javascript">
		var loc = {lat: 41.699170, lng: -86.238754};
		var map;
		var markers = [];
		function initMap() {
			var locMarker;
			var hasBeenDragged = false;
			map = new google.maps.Map(document.getElementById('map'), {
				zoom: 16,
				center: loc
			});
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(function(position) {
					var pos = {
						lat: position.coords.latitude,
						lng: position.coords.longitude
					};
					if(!hasBeenDragged){
						map.setCenter(pos);
						locMarker.setPosition(pos);
					}
				});
			}
			update();
			locMarker = new google.maps.Marker({
				position: loc,
				map: map,
				//label: 'A',
				draggable:true
			});
			locMarker.addListener('dragend', function() {
				hasBeenDragged = true;
				map.setCenter(locMarker.getPosition());
				update();
				loc = locMarker.getPosition();
				//recalculate positions
			});

			/*var marker = new google.maps.Marker({
				position: uluru,
				map: map
			});*/
		}
		var decimals = Math.pow(10, 2);
		function update(){
			$.getJSON("locations.php", { "long" : loc.lng, "lat" : loc.lat }, function(result){
				$.each(markers, function(index, elem){
					console.log("null");
					elem.setMap(null);
				});
				markers = {};
				$("#resultList li").not(".template").remove();
				$.each(result, function(index, elem){
					var newElem = $("#resultList .template").clone().appendTo("#resultList ul").removeClass("template");
					markers[index] = new google.maps.Marker({
						position: { "lng" : elem.long, "lat" : elem.lat },
						map: map,
						label: index,
						draggable: false
					});
					newElem.find(".name").text(elem.name || "---");
					newElem.find(".desc").text(elem.desc || "---");
					newElem.find(".dist").text("(" + Math.round(getDistance(elem.lat, elem.long, loc.lat, loc.lng) * decimals) / decimals+ " mi)");
					if(elem.img) newElem.find(".img").attr("src", elem.img);
				});
			});
		}
		$("#filter input,select").change(update);
		function getDistance(lat1,long1,lat2,long2) {
			var R = 3959; // radius of earth in miles
			var dLat = deg2rad(lat2-lat1);
			var dLon = deg2rad(long2-long1);
			var a =
				Math.sin(dLat/2) * Math.sin(dLat/2) +
				Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
				Math.sin(dLon/2) * Math.sin(dLon/2);
			var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
			var d = R * c;
			return d;
		}

		function deg2rad(deg) {
			return deg * (Math.PI/180)
		}
	</script>
</body>
</html>
