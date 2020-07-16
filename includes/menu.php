<?php	
	function display_admin_menu($active, $isadmin)
	{
		$html = "<nav class='col-md-2 d-none d-md-block bg-light sidebar'>";
		$html = $html."<div class='sidebar-sticky'><ul class='nav flex-column'>";

		$html = $html."<li class='nav-item'>";
		if ($active == "home") {
			$html = $html."<a class='nav-link active' href='../index.php'>";
		} else {
			$html = $html."<a class='nav-link' href='../index.php'>";
		}
		$html = $html."<span data-feather='home'></span>";
		$html = $html." Home<span class='sr-only'>(current)</span></a></li>";
		
		$html = $html."<li class='nav-item'>";
		if ($active == "admin") {
			$html = $html."<a class='nav-link active' href='index.php'>";
		} else {
			$html = $html."<a class='nav-link' href='index.php'>";
		}
		$html = $html."<span data-feather='settings'></span>";
		$html = $html." Admin<span class='sr-only'>(current)</span></a></li>";
		
		if ($isadmin == 2) {
			$html = $html."<li class='nav-item'>";
			if ($active == "group") {
				$html = $html."<a class='nav-link active' href='group.php'>";
			} else {
				$html = $html."<a class='nav-link' href='group.php'>";
			}
			$html = $html."<span data-feather='settings'></span>";
			$html = $html." Group<span class='sr-only'>(current)</span></a></li>";
		}
		
		$html = $html."<li class='nav-item'>";
		if ($active == "floor") {
			$html = $html."<a class='nav-link active' href='floor.php'>";
		} else {
			$html = $html."<a class='nav-link' href='floor.php'>";
		}
		$html = $html."<span data-feather='settings'></span>";
		$html = $html." Floor<span class='sr-only'>(current)</span></a></li>";

		$html = $html."<li class='nav-item'>";
		if ($active == "room") {
			$html = $html."<a class='nav-link active' href='room.php'>";
		} else {
			$html = $html."<a class='nav-link' href='room.php'>";
		}
		$html = $html."<span data-feather='settings'></span>";
		$html = $html." Room</a></li>";
	
		$html = $html."<li class='nav-item'>";
		if ($active == "seat") {
			$html = $html."<a class='nav-link active' href='seat.php'>";
		} else {
			$html = $html."<a class='nav-link' href='seat.php'>";
		}
		$html = $html."<span data-feather='settings'></span>";
		$html = $html." Seat</a></li>";

		$html = $html."<li class='nav-item'>";
		if ($active == "user") {
			$html = $html."<a class='nav-link active' href='user.php'>";
		} else {	
			$html = $html."<a class='nav-link' href='user.php'>";
		}
		$html = $html."<span data-feather='settings'></span>";
		$html = $html." User</a></li>";
		
		if ($isadmin >= 1) {
			$html = $html."<li class='nav-item'>";
			if ($active == "usertoroom") {
				$html = $html."<a class='nav-link active' href='usertoroom.php'>";
			} else {	
				$html = $html."<a class='nav-link' href='usertoroom.php'>";
			}
			$html = $html."<span data-feather='settings'></span>";
			$html = $html." User To Room</a></li>";
		}
		
		if ($isadmin >= 1) {
			$html = $html."<li class='nav-item'>";
			if ($active == "usertoseat") {
				$html = $html."<a class='nav-link active' href='usertoseat.php'>";
			} else {	
				$html = $html."<a class='nav-link' href='usertoseat.php'>";
			}
			$html = $html."<span data-feather='settings'></span>";
			$html = $html." User To Seat</a></li>";
		}

		$html = $html."<li class='nav-item'>";
		if ($active == "block") {
			$html = $html."<a class='nav-link active' href='block.php'>";
		} else {
			$html = $html."<a class='nav-link' href='block.php'>";
		}
		$html = $html."<span data-feather='settings'></span>";
		$html = $html." Seat Block</a></li>";
	
		// $html = $html."<li class='nav-item'>";
		// if ($active == "reservation") {
			// $html = $html."<a class='nav-link active' href='reservation.php'>";
		// } else {
			// $html = $html."<a class='nav-link' href='reservation.php'>";
		// }
		// $html = $html."<span data-feather='settings'></span>";
		// $html = $html." Reservation</a></li>";
		
		if ($isadmin == 2) {
			$html = $html."<li class='nav-item'>";
			if ($active == "zombies") {
				$html = $html."<a class='nav-link active' href='zombies.php'>";
			} else {
				$html = $html."<a class='nav-link' href='zombies.php'>";
			}
			$html = $html."<span data-feather='settings'></span>";
			$html = $html." Zombies<span class='sr-only'>(current)</span></a></li>";
		}
		
		if ($isadmin >= 1) {
			$html = $html."<li class='nav-item'>";
			if ($active == "floormap") {
				$html = $html."<a class='nav-link active' href='floormap.php'>";
			} else {
				$html = $html."<a class='nav-link' href='floormap.php'>";
			}
			$html = $html."<span data-feather='settings'></span>";
			$html = $html." Floormap<span class='sr-only'>(current)</span></a></li>";
		}
		
		$html = $html."</ul></div></nav>";
		
		return $html;
	}

?>
