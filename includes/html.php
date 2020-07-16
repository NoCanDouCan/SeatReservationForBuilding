<?php

	function head($title)
	{
		$html = "<!doctype html>";
		$html = $html."<html lang='en'>";
		$html = $html."<head>";
		$html = $html."<meta charset='utf-8'>";
		$html = $html."<title>$title</title>";
		$html = $html."<!-- Bootstrap core CSS -->";
		$html = $html."<link href='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T' crossorigin='anonymous'>";
		$html = $html."<style>";
		$html = $html.".bd-placeholder-img {";
		$html = $html."font-size: 1.125rem;";
		$html = $html."text-anchor: middle;";
		$html = $html."-webkit-user-select: none;";
        $html = $html."-moz-user-select: none;";
		$html = $html."-ms-user-select: none;";
		$html = $html."user-select: none;";
		$html = $html."}";
		$html = $html."@media (min-width: 768px) {";
		$html = $html.".bd-placeholder-img-lg {";
		$html = $html."font-size: 3.5rem;";
		$html = $html."}";
		$html = $html."}";
        $html = $html."</style>";
        $html = $html."<!-- Custom styles for this template -->";
        $html = $html."<link href='https://getbootstrap.com/docs/4.3/examples/dashboard/dashboard.css' rel='stylesheet'>";
		$html = $html."</head>";
		return $html;
	}
	function body_top($header, $username, $link)
	{
		$html = "<body>";
		$html = $html."<nav class='navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow'>";
		$html = $html."<a class='navbar-brand col-sm-3 col-md-2 mr-0' href='".$link."'>".$header."</a>";

		$html = $html."<ul class='navbar-nav px-3'>";
		$html = $html."<li class='nav-item text-nowrap'>";
		$html = $html."<a class='nav-link' href='../logout.php'>";
		$html = $html.$username." Sign out";
		$html = $html."</a>";
		$html = $html."</li>";
		$html = $html."</ul>";
		$html = $html."</nav>";

		$html = $html."<div class='container-fluid'>";
		$html = $html."<div class='row'>";
		return $html;
	}
	
	function body_top_admin($header, $username)
	{
		$html = "<body>";
		$html = $html."<nav class='navbar navbar-light fixed-top flex-md-nowrap p-0 shadow' style='background-color: #ff5050;'>";
		$html = $html."<a class='navbar-brand col-sm-3 col-md-2 mr-0' href='/admin/index.php'>".$header."</a>";

		$html = $html."<ul class='navbar-nav px-3'>";
		$html = $html."<li class='nav-item text-nowrap'>";
		$html = $html."<a class='nav-link' href='../logout.php'>";
		$html = $html.$username." Sign out";
		$html = $html."</a>";
		$html = $html."</li>";
		$html = $html."</ul>";
		$html = $html."</nav>";

		$html = $html."<div class='container-fluid'>";
		$html = $html."<div class='row'>";
		return $html;
	}
	
	function body_title_top($title)
	{
		$html = "<main role='main' class='col-md-9 ml-sm-auto col-lg-10 px-4'>";
		$html = $html."<div class='d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom'>";
		$html = $html."<h1 class='h2'>$title</h1>";
		$html = $html."<div class='btn-toolbar mb-2 mb-md-0'>";
		$html = $html."<div class='btn-group mr-2'>";
		return $html;
	}
	
	function body_title_bottom()
	{
		$html = "</div></div></div>";
		return $html;
	}
	
	function body_bottom()
	{
		$html = "</main>";
		$html = $html."</div>";
		$html = $html."</div>";
		$html = $html."<script src='https://code.jquery.com/jquery-3.3.1.slim.min.js' integrity='sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo' crossorigin='anonymous'></script>";
		$html = $html."<script>window.jQuery || document.write('<script src='/docs/4.3/assets/js/vendor/jquery-slim.min.js'><\/script>')</script><script src='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/dist/js/bootstrap.bundle.min.js' integrity='sha384-xrRywqdh3PHs8keKZN+8zzc5TX0GRTLCcmivcbNJWm2rs5C8PRhcEn3czEjhAO9o' crossorigin='anonymous'></script>";
		$html = $html."<script src='https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.9.0/feather.min.js'></script>";
		$html = $html."<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js'></script>";
		$html = $html."<script src='https://getbootstrap.com/docs/4.3/examples/dashboard/dashboard.js'></script></body>";
		$html = $html."</html>";
		return $html;
	}
	
	function delete_button($id, $link)
	{
		$html = "<form action='$link' method='POST'>";
		$html = $html."<input id='id' type='hidden' name='id' value='$id'/>";
		$html = $html."<input id='delete' type='hidden' name='delete' value='1'/>";
		$html = $html."<input class='btn btn-sm btn-outline-secondary' type='submit' name='delete' value='Delete' />";
		$html = $html."</form>";
		return $html;
	}
	
	function delete_button_get($id, $link)
	{
		$html = "<form action='$link' method='GET'>";
		$html = $html."<input id='id' type='hidden' name='id' value='$id'/>";
		$html = $html."<input id='delete' type='hidden' name='delete' value='1'/>";
		$html = $html."<input class='btn btn-sm btn-outline-secondary' type='submit' name='delete' value='Delete' />";
		$html = $html."</form>";
		return $html;
	}
	
	function add_button($title, $link)
	{
		$html = "<a class='btn btn-sm btn-outline-secondary' href='$link' role='button'>$title</a>";
		return $html;
	}

?>