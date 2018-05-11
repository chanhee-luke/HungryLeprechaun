var loc = {lat: 41.699170, lng: -86.238754}; var map; var markers = []; function initMap() {
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
		loc = locMarker.getPosition();
		update();
	});
		/*var marker = new google.maps.Marker({
		position: uluru,
		map: map
	});*/
}
var decimals = Math.pow(10, 2);
var alpha = "abcdefghijklmnopqrstuvwxyz".toUpperCase();
var alphaIndex;
function update(){
	var query = { "long" : loc.lng, "lat" : loc.lat };
	if($("#query").val()) query.query = $("#query").val();
	if($("#price").val().length) query.price = $("#price").val().reduce(function(sum, num){return parseInt(sum) + parseInt(num)});
	if($("#distance").val() != -1) query.rad = $("#distance").val();
	if($("#num").val() != -1) query.num = $("#num").val();
	$.getJSON("locations.php", query, function(result){
		$.each(markers, function(index, elem){
			elem.setMap(null);
		});
		markers = {};
		var alphaMapping = {};
		alphaIndex = 0;
		$("#resultList li,hr").not(".template").remove();
		$.each(result, function(index, elem){
			var newElem = $("#resultList .template").clone().appendTo("#resultList ul").removeClass("template");
			$("#resultList ul").append("<hr>");
			var position = { "lng" : elem.long, "lat" : elem.lat };
			var newLetter = 0;
			var strkey = elem.long + "," + elem.lat;
			if(!alphaMapping[strkey]){
				alphaMapping[strkey] = alpha[alphaIndex++];
				newLetter = 1;
			}
			markers[index] = new google.maps.Marker({
				position: position,
				map: map,
				label: alphaMapping[strkey],
				draggable: false
			});
			newElem.attr("id", "li" + index);
			newElem.find(".name").text(elem.name || "---");
			newElem.find(".desc").text(elem.desc || "---");
			newElem.find(".dist").text("(" + Math.round(getDistance(elem.lat, elem.long, f(loc.lat), f(loc.lng)) * decimals) / decimals + " mi)");
			if(elem.img) newElem.find("img.img").attr("src", elem.img);
			newElem.find(".link").click(function(){
				map.setCenter(position);
			}).find("div").text(alphaMapping[strkey]);
			if(newLetter){
				markers[index].addListener("click", function(){
					$("#resultList li").removeClass("focused");
					newElem.addClass("focused");
					window.location.hash = "li" + index;
				});
			}
		});
	});
}
function f(num){
	if(num instanceof Function) return num();
	return num;
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
