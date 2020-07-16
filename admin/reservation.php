<?php
	include ("../config/session_admin.php");
	include ("../config/db.php");
	include ("../includes/menu.php");
	include ("../includes/html.php");
	
	$getreservationdate = "";
	if(isset($_GET['reservationdate'])) {
		$getreservationdate = $_GET['reservationdate'];
	}
	$gettime1 = date('H');
	if(isset($_GET['time1'])) {
		$gettime1 = $_GET['time1'];
	}
	$gettime2 = date('H') + 1;
	if(isset($_GET['time2'])) {
		$gettime2 = $_GET['time2'];
	}
	$getroomid = 0;
	if(isset($_GET['roomid'])) {
		$getroomid = $_GET['roomid'];
	}
	$counthours = $gettime2 - $gettime1;
	if (isset($_GET['block'])) {
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("INSERT INTO blocks (seatid,blockedseatid) VALUES (?,?)");
		if ($statement->execute(array($_GET['seatid'],$_GET['blockedseatid']))) {
			$html = $html."seat block added";
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
	}
	if (isset($_GET['unblock'])) {
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("DELETE FROM blocks WHERE blockedseatid = ? AND seatid = ?");
		if ($statement->execute(array($_GET['blockedseatid'],$_GET['seatid']))) {
			echo "seat block removed";
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
	}
	
	if (isset($_GET['delete'])) {
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("DELETE FROM reservation WHERE reservationid = ?");
		if ($statement->execute(array($_GET['id']))) {
			$getroomid = $_GET['roomid'];
			$getreservationdate = $_GET['reservationdate'];
			$gettime1 = $_GET['time1'];
			$gettime2 = $_GET['time2'];
			header("Location: reservation.php?roomid=$getroomid&reservationdate=$getreservationdate&time1=$gettime1&time2=$gettime2");
			die();
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
	}
	
	function display_view($dbserver, $dbuser, $dbpw, $roomid, $reservationdate, $time1, $time2) 
	{
		$html = "<div class='container'>Select seat to edit";
		$html = $html."<table class='table table-bordered'><tbody>";

		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT seat.seatid, seat.row, seat.orientation, seat.type from seat WHERE seat.roomid = ? ORDER BY seat.row, seat.col");
		if ($statement->execute(array($roomid))) {
			$gridrow = 0;
			while($row = $statement->fetch()) {
				if ($row['row'] > $gridrow) {
					$gridrow = $row['row'];
					$html = $html."<tr>";
				}
				$seatid = $row['seatid'];
				$orientation = $row['orientation'];

				if ($row['type'] == 1) {
					$html = $html.img_with_title($orientation, $roomid, $seatid, $reservationdate, $time1, $time2, $dbserver, $dbuser, $dbpw);
				} else if ($row['type'] == 2) {
					if ($row['orientation'] == 1) {
						$html = $html."<td width='1'><img src='/img/udoor.png'></td>";
					} else if ($row['orientation'] == 2) {
						$html = $html."<td width='1'><img src='/img/rdoor.png'></td>";
					} else if($row['orientation'] == 3) {
						$html = $html."<td width='1'><img src='/img/ddoor.png'></td>";
					} else if($row['orientation'] == 4) {
						$html = $html."<td width='1'><img src='/img/ldoor.png'></td>";
					}
				} else if ($row['type'] == 3) {
					if ($row['orientation'] == 1) {
						$html = $html."<td width='1'><img src='/img/hwindow.png'></td>";
					} else if ($row['orientation'] == 2) {
						$html = $html."<td width='1'><img src='/img/vwindow.png'></td>";
					} else if($row['orientation'] == 3) {
						$html = $html."<td width='1'><img src='/img/hwindow.png'></td>";
					} else if($row['orientation'] == 4) {
						$html = $html."<td width='1'><img src='/img/vwindow.png'></td>";
					}
				} else if ($row['type'] == 4) {
					$html = $html."<td width='1'><img src='/img/blank.png'></td>";
				}
			}
			$html = $html."</tr>";
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}

		$html = $html."</tbody></table></div>";	
		return $html;
	}
	
	function img_with_title($orientation, $roomid, $seatid, $date, $time1, $time2, $dbserver, $dbuser, $dbpw)
	{
		$hour = $time1;
		$title = "";
		$hours=$time2-$time1;
		$count_blocks=0;
		$html = "";

		while($hour < $time2) {
			
			$reserved = false;
			$blocked = false;
			
			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("SELECT reservation.userid,user.fullname from reservation LEFT JOIN user ON reservation.userid = user.userid WHERE reservation.seatid = ? AND reservation.reservationdate = ? AND reservation.time = ? LIMIT 1");
			//$statement = $pdo->prepare("SELECT reservation.userid,user.fullname from reservation LEFT JOIN user ON reservation.userid = user.userid LEFT JOIN blocks ON reservation.seatid = blocks.seatid WHERE blocks.blockedseatid = ? AND reservation.reservationdate = ? AND reservation.time = ? LIMIT 1");
			$statement->execute(array($seatid,$date,$hour));
			$row = $statement->fetch();
			$userid = $row['userid'];
			$fullname = $row['fullname'];
			if ($userid != 0)
			{
				$reserved=true;
				if ($title == "")
				{
					$title = $hour.":00-".($hour + 1).":00 reserved by ".$fullname;
				} else {
					$title = $title."&#10;".$hour.":00-".($hour + 1).":00 reserved by ".$fullname;
				}
			} else {
				$pdo = new PDO($dbserver, $dbuser, $dbpw);
				//$statement = $pdo->prepare("SELECT reservation.userid,user.fullname from reservation LEFT JOIN user ON reservation.userid = user.userid WHERE reservation.seatid = ? AND reservation.reservationdate = ? AND reservation.time = ? LIMIT 1");
				$statement = $pdo->prepare("SELECT reservation.userid,user.fullname from reservation LEFT JOIN user ON reservation.userid = user.userid LEFT JOIN blocks ON reservation.seatid = blocks.seatid WHERE blocks.blockedseatid = ? AND reservation.reservationdate = ? AND reservation.time = ? LIMIT 1");
				$statement->execute(array($seatid,$date,$hour));
				$row = $statement->fetch();
				$userid = $row['userid'];
				$fullname = $row['fullname'];
				if ($userid != 0)
				{
					$blocked=true;
					if ($title == "")
					{
						$title = $hour.":00-".($hour + 1).":00 blocked by ".$fullname;
					} else {
						$title = $title."&#10;".$hour.":00-".($hour + 1).":00 blocked by ".$fullname;
					}
				}
			}
			
			if ($reserved == false && $blocked == false) {
					if ($title == "")
					{
						$title = $hour.":00-".($hour + 1).":00 not blocked";
					} else {
						$title = $title."&#10;".$hour.":00-".($hour + 1).":00 not blocked";
					}
				
			} else {
				//found a reservation or block, add one to counter to calc the color
				$count_blocks = $count_blocks + 1;
			}
			
			$hour = $hour+1;
		}
		$img = "";
		if ($orientation == 1) {
			$img = "u";
		} else if ($orientation == 2) {
			$img = "r";
		} else if ($orientation == 3) {
			$img = "d";
		} else if ($orientation == 4) {
			$img = "l";
		}
		
		$link = "";
		
		if ($count_blocks == $hours) {
			$img = $img."r.png";
			$link = "reservation.php?roomid=$roomid&reservationdate=$date&time1=$time1&time2=$time2";
		} else if ($count_blocks > 0) {
			$img = $img."o.png";
			$link = "reservation.php?roomid=$roomid&reservationdate=$date&time1=$time1&time2=$time2";
			//$link = "reservation.php?roomid=$roomid&reservationdate=$date&time1=$time1&time2=$time2&seatid=$seatid";
		} else {
			$img = $img."g.png";
			$link = "addreservation.php?reservationdate=$date&time1=$time1&time2=$time2&seatid=$seatid";
		}
		$html = $html."<td width='1'><a href='$link'><img src='/img/$img' title='$title'></a></td>";
		
		return $html;

	}

	function display_list($dbserver, $dbuser, $dbpw, $froomid, $freservationdate, $time1, $time2)
	{
		$html = "<h2>Reservation list</h2>";
		$html = $html."<div class='table-responsive'>";
		$html = $html."<table class='table table-striped table-sm'>";
		$html = $html."<thead>";
		$html = $html."<tr>";
		$html = $html."<th>Reservation ID</th>";
		$html = $html."<th>Room Name</th>";
		$html = $html."<th>Seat ID</th>";
		$html = $html."<th>Seat Info</th>";
		$html = $html."<th>Date</th>";
		$html = $html."<th>Time</th>";
		$html = $html."<th>User</th>";
		$html = $html."<th>Edit / Delete</th>";
		$html = $html."</tr>";
		$html = $html."</thead>";
		$html = $html."<tbody>";

		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT reservation.*, room.roomname, user.username, seat.description FROM reservation LEFT JOIN seat ON reservation.seatid = seat.seatid LEFT JOIN room ON seat.roomid = room.roomid LEFT JOIN user ON reservation.userid = user.userid WHERE room.roomid = ? and reservation.reservationdate = ? and reservation.time >= ? AND reservation.time <= ? ORDER BY room.roomname, reservation.reservationdate, reservation.time");

		if ($statement->execute(array($froomid,$freservationdate,$time1,$time2-1))) {
			while($row = $statement->fetch()) {
				$id = $row['reservationid'];
				$time = $row['time'];
				$time2 = $row['time'] + 1;
				$html = $html."<tr>";
				$html = $html."<td>".$row['reservationid']."</td>";
				$html = $html."<td>".$row['roomname']."</td>";
				$html = $html."<td>".$row['seatid']."</td>";
				$html = $html."<td>".$row['description']."</td>";
				$html = $html."<td>".$row['reservationdate']."</td>";
				$html = $html."<td>".$row['time'].":00-$time2:00</td>";
				$html = $html."<td>".$row['username']."</td>";
				$html = $html."<td>";
				$html = $html."<a class='btn btn-sm btn-outline-secondary' href='addreservation.php?&id=$id' role='button'>Edit</a>";
				$html = $html."<a class='btn btn-sm btn-outline-secondary' href='reservation.php?&roomid=$froomid&reservationdate=$freservationdate&time1=$time1&time2=$time2&id=$id&delete=1' role='button'>Delete</a>";
				$html = $html."</td>";
				$html = $html."</tr>";
			}
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
		$html = $html."</tbody>";
		$html = $html."</table>";
		$html = $html."</div>";
		return $html;
	}
	
	function display_selection($dbserver, $dbuser, $dbpw, $froomid, $freservationdate, $time1, $time2,$username)
	{
		
		$html = "<h2>Reservation view</h2>";
		$html = $html."<form action='reservation.php' method='GET'>";
		$html = $html."<select id='roomid' name='roomid'>";

		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT room.roomid, room.roomname FROM room INNER JOIN roomtogroup ON room.roomid = roomtogroup.roomid INNER JOIN usertogroup ON roomtogroup.groupid = usertogroup.groupid INNER JOIN user ON usertogroup.userid = user.userid WHERE user.username = ? ORDER BY room.roomname");
		if ($statement->execute(array($username))) {
			while($row = $statement->fetch()) {
				if ($froomid == $row['roomid']) {
					$html = $html."<option value='".$row['roomid']."' selected>".$row['roomname']."</option>";
				} else {
					$html = $html."<option value='".$row['roomid']."'>".$row['roomname']."</option>";
				}
			}
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}

		$html = $html."</select>";
		$html = $html."<input id='reservationdate' type='date' name='reservationdate' size='50'"; 
		if($freservationdate != "") { 
			$html = $html."value='$freservationdate'"; 
		} else { 
			$html = $html."value='".date('Y-m-d')."'"; 
		}
		$html = $html."/>";
		$html = $html."<input id='time1' type='number' name='time1' size='1' step='1' min='0' max='24' ";
		$html = $html."value='$time1'"; 
		$html = $html."/>:00 - "; 
		$html = $html."<input id='time2' type='number' name='time2' size='1' step='1' min='0' max='24' ";
		$html = $html."value='$time2'"; 
		$html = $html."/>:00 ";
		$html = $html."<input type='submit' value='Submit' />";
		$html = $html."</form>";
		
		return $html;
	}
	
	echo head("Seat Reservation - Admin");
	echo body_top("Seat Reservation - Admin",$username);
	echo display_admin_menu("reservation", $isadmin);
	echo body_title_top("Admin - Seat Reservation");
	echo add_button("Add Reservation", "addreservation.php");
	echo body_title_bottom();
	echo display_selection($dbserver, $dbuser, $dbpw, $getroomid, $getreservationdate, $gettime1, $gettime2,$username);
	echo display_view($dbserver, $dbuser, $dbpw, $getroomid, $getreservationdate, $gettime1, $gettime2);
	echo display_list($dbserver, $dbuser, $dbpw, $getroomid, $getreservationdate, $gettime1, $gettime2);
	echo body_bottom();
?>	 
