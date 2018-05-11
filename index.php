<!doctype html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

	<!-- Our CSS -->
	<link rel="stylesheet" href="styles.css">

	<title>Hungry Leprechaun</title>
</head>
<body background="background.jpg">
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<div class="container-fluid">
		<h1 class="text-center">Hungry Leprechaun</h1>
		<div class="row">
			<!-- Filters -->
			<div class="col-lg-6">
				<h3>Filters</h3>
				<form id="filter" href="#" onsubmit="update(); return false">
					<div class="form-group row">
						<label for="query" class="col-sm-2 col-form-label">Name</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="query" placeholder="Type a name to find only similar">
						</div>
					</div>
					<div class="form-group row">
						<label for="price" class="col-sm-2 col-form-label">Cost</label>
						<div class="col-sm-10">
							<select multiple class="form-control" id="price" style="overflow: hidden;">
								<option value="1">$</option>
								<option value="2">$$</option>
								<option value="4">$$$</option>
								<option value="8">$$$$</option>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="distance" class="col-sm-2 col-form-label">Distance</label>
						<div class="col-sm-10">
							<select id="distance" class="custom-select">
								<option value="-1" selected>Any</option>
								<option value=".5">&lt;.5 Miles</option>
								<option value=".25">&lt;.25 Miles</option>
								<option value=".1">&lt;.1 Miles</option>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="num" class="col-sm-2 col-form-label">Number of Results</label>
						<div class="col-sm-10">
							<select id="num" class="custom-select">
								<option value="-1" selected>All</option>
								<option value="10">Ten</option>
								<option value="5">Five</option>
								<option value="2">Two</option>
							</select>
						</div>
					</div>
				</form>
				<!-- Map -->
				<div id="map">
				</div>
				<p class="text-center">If you do not have GPS, drag the dot marker to set your position</p>
			</div>
			<!-- Results -->
			<div id="resultList" class="col-lg-6">
				<h3>Results</h3>
				<ul class="list-unstyled">
					<?php require "template.html"; ?>
				</ul>
			</div>
		</div>
	</div>
	<script src="script.js" type="text/javascript"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=<?php require "key.txt" ?>&callback=initMap" async defer></script>
</body>
</html>
