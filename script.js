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
