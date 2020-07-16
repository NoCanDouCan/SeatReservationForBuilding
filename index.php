<?php
session_start();
if (!isset($_SESSION['username'])) {
	header("Location: login.php");
	die();
}

include ("config/db.php");

$index_path = "index.php";

$sessionusername = $_SESSION['username'];
$isadmin = 0;
$userroomid = 0;
$userseatid = 0;
$userid = 0;
$getfloorid = 0;
$getrequest = 0;
$sendrequest = 0;
if (isset($_GET['floorid'])) {
	$getfloorid = $_GET['floorid'];
}
$getroomid = 0;
if (isset($_GET['roomid'])) {
	$getroomid = $_GET['roomid'];
}

$gettime1 = "-1";
if (isset($_GET['time1'])) {
	$gettime1 = $_GET['time1'];
}
$gettime2 = "-1";
if (isset($_GET['time2'])) {
	$gettime2 = $_GET['time2'];
}
$getseatid = "";
if (isset($_GET['seatid'])) {
	$getseatid = $_GET['seatid'];
}
if (isset($_GET['request'])) {
	$getrequest = $_GET['request'];
}
if (isset($_GET['sendrequest'])) {
	$sendrequest = $_GET['sendrequest'];
}

//add request message to db and reload
if ($sendrequest > 0) {
	$pdo = new PDO($dbserver, $dbuser, $dbpw);
	$statement = $pdo->prepare("INSERT INTO request (userid, groupid) VALUES ((SELECT user.userid from user where user.username = ?),?)");
	if ($statement->execute(array($sessionusername, $sendrequest))) {
		header("Location: ".$index_path);
		die();
	} else {
		echo "SQL Error <br />";
		echo $statement->queryString."<br />";
		echo $statement->errorInfo()[2];
	}
}

$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
$statement = $pdo->prepare("SELECT user.userid, user.isadmin,user.roomid,user.seatid FROM user where username = ? LIMIT 1");
if ($statement->execute(array($sessionusername))) {
	while($row = $statement->fetch()) {
		$isadmin = $row['isadmin'];
		$userroomid = $row['roomid'];
		$userseatid = $row['seatid'];
		$userid = $row['userid'];
	}
}

//check if date is valid or reload
$getrdate = "";
if (isset($_GET['rdate'])) {
	$getrdate = $_GET['rdate'];
	if ($isadmin <= 0) {
		
		$rdateint = strtotime($getrdate);
		$todayint = strtotime(date('Y-m-d'));
		$day = 60*60*24;
		$futureint = $todayint + ($day*7);
		if ($todayint <= $rdateint) {
			if ($rdateint > $futureint) {
				//set date to today + 7 when higher than +7
				$getrdate = date('Y-m-d', $futureint);
				header("Location: ".$index_path."?floorid=$getfloorid&roomid=$getroomid&rdate=$getrdate&time1=$gettime1&time2=$gettime2");
				die();
			}
		} else {
				//set date to today when lower than today
				$getrdate = date('Y-m-d');
				header("Location: ".$index_path."?floorid=$getfloorid&roomid=$getroomid&rdate=$getrdate&time1=$gettime1&time2=$gettime2");
				die();
		}	
	}
}


if (isset($_GET['delete'])) {
	$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
	$statement = $pdo->prepare("DELETE FROM reservation WHERE reservationid = ?");
	if ($statement->execute(array($_GET['id']))) {
		header("Location: ".$index_path."?floorid=$getfloorid&roomid=$getroomid&rdate=$getrdate&time1=$gettime1&time2=$gettime2");
        die();
	} else {
		echo "SQL Error <br />";
		echo $statement->queryString."<br />";
		echo $statement->errorInfo()[2];
	}
}



if (isset($_GET['insert'])) {

	$hour = $gettime1;
	$timestamp = strtotime($getrdate);
	while ($hour < $gettime2)
	{
		$timestamp2 = $timestamp + ($hour*60*60);
		$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
		$statement = $pdo->prepare("INSERT INTO reservation (seatid, reservationdate, time, userid, timestamp) VALUES (?,?,?,?,?)");
		if ($statement->execute(array($getseatid,$getrdate,$hour,$userid,$timestamp2))) {
			
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
		
		$hour = $hour + 1;
	}
	
	header("Location: ".$index_path."?floorid=$getfloorid&roomid=$getroomid&rdate=$getrdate&time1=$gettime1&time2=$gettime2");
	die();
}


function display_menu($isadmin, $floorid, $roomid, $request)
{
	global $index_path;
	
	$html = "<nav class='col-md-2 d-none d-md-block bg-light sidebar'>";
	$html = $html."<div class='sidebar-sticky'><ul class='nav flex-column'>";
	
	
	if ($roomid > 0) {
		$floorname = "";
		$roomname = "";
		$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
		$statement = $pdo->prepare("SELECT room.roomname, floor.floorname FROM room LEFT JOIN floor ON room.floorid = floor.floorid where room.roomid = ? LIMIT 1");
		if ($statement->execute(array($roomid))) {
			while($row = $statement->fetch()) {
				$roomname = $row['roomname'];
				$floorname = $row['floorname'];
			}
		}
		$html = $html."<li class='nav-item'><a class='nav-link' href='$index_path'><span data-feather='home'></span>Home <span class='sr-only'>(current)</span></a></li>";
		$html = $html."<li class='nav-item'><a class='nav-link' href='$index_path?floorid=$floorid'><span data-feather='layers'></span>$floorname <span class='sr-only'>(current)</span></a></li>";
		$html = $html."<li class='nav-item'><a class='nav-link active' href='$index_path?floorid=$floorid&roomid=$roomid'><span data-feather='box'></span>$roomname<span class='sr-only'>(current)</span></a></li>";
		$html = $html."<li class='nav-item'><a class='nav-link' href='$index_path?request=1'><span data-feather='plus'></span>Request <span class='sr-only'>(current)</span></a></li>";
	} else if ($floorid > 0) {
		$floorname = "";
		$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
		$statement = $pdo->prepare("SELECT floor.floorname FROM floor where floor.floorid = ? LIMIT 1");
		if ($statement->execute(array($floorid))) {
			while($row = $statement->fetch()) {
				$floorname = $row['floorname'];
			}
		}
		$html = $html."<li class='nav-item'><a class='nav-link' href='$index_path'><span data-feather='home'></span>Home <span class='sr-only'>(current)</span></a></li>";
		$html = $html."<li class='nav-item'><a class='nav-link active' href='$index_path?floorid=$floorid'><span data-feather='layers'></span>$floorname <span class='sr-only'>(current)</span></a></li>";
		$html = $html."<li class='nav-item'><a class='nav-link' href='$index_path?request=1'><span data-feather='plus'></span>Request <span class='sr-only'>(current)</span></a></li>";
	} else if ($request == 1) {
		$html = $html."<li class='nav-item'><a class='nav-link' href='$index_path'><span data-feather='home'></span>Home <span class='sr-only'>(current)</span></a></li>";
		$html = $html."<li class='nav-item'><a class='nav-link active' href='$index_path?request=1'><span data-feather='plus'></span>Request <span class='sr-only'>(current)</span></a></li>";
	} else {
		$html = $html."<li class='nav-item'><a class='nav-link active' href='$index_path'><span data-feather='home'></span>Home <span class='sr-only'>(current)</span></a></li>";
		$html = $html."<li class='nav-item'><a class='nav-link' href='$index_path?request=1'><span data-feather='plus'></span>Request <span class='sr-only'>(current)</span></a></li>";
	}
	
	if ($isadmin > 0) {
		$html = $html."<li class='nav-item'><a class='nav-link' href='/admin/index.php'><span data-feather='settings'></span>Admin <span class='sr-only'>(current)</span></a></li>";
	}

	$html = $html."</ul></div></nav>";
	
	return $html;
}

function list_all_floor($username)
{
	global $index_path;
	
	$table = "<h2>Select your floor</h2><div class='table-responsive'><table class='table table-striped table-sm'>";
	$table = $table."<thead><tr><th>Floor</th><th>Department</th><th></th></tr></thead><tbody>";
	
	$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
	$statement = $pdo->prepare("SELECT floor.floorid, floor.floorname, floor.department FROM floor INNER JOIN floortogroup ON floor.floorid = floortogroup.floorid INNER JOIN usertogroup ON floortogroup.groupid = usertogroup.groupid INNER JOIN user ON usertogroup.userid = user.userid WHERE user.username = ? ORDER BY floor.floorname");
	if ($statement->execute(array($username))) {
		while($row = $statement->fetch()) {
			$floorid = $row['floorid'];
			$floorname = $row['floorname'];
			$department = $row['department'];
			
			$table = $table."<tr><td>$floorname</td><td>$department</td>";
			$table = $table."<td><a class='btn btn-sm btn-outline-secondary' href='$index_path?floorid=$floorid' role='button'>Select</a></td></tr>";
			
		}
	}
      
    $table = $table."</tbody></table></div>";
	return $table;
}

function list_all_rooms($floorid, $userid)
{
	global $index_path;
	$table = "<h2>Select your room</h2><div class='table-responsive'><table class='table table-striped table-sm'>";
	$table = $table."<thead><tr><th>Room</th><th>Floor</th><th>Department</th><th></th></tr></thead><tbody>";
	
	$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
	$statement = $pdo->prepare("SELECT DISTINCT room.roomname, room.roomid,floor.floorid, floor.floorname, floor.department FROM room LEFT JOIN usertoroom ON room.roomid = usertoroom.roomid LEFT JOIN floor ON room.floorid = floor.floorid WHERE (room.floorid = ? AND usertoroom.userid = ?) OR (room.permission = 0 AND room.floorid = ?) OR (room.permission IS NULL AND room.floorid = ?) ORDER BY room.roomname");
	if ($statement->execute(array($floorid, $userid, $floorid, $floorid))) {
		while($row = $statement->fetch()) {
			$roomid = $row['roomid'];
			$roomname = $row['roomname'];
			$floorid = $row['floorid'];
			$floorname = $row['floorname'];
			$department = $row['department'];
			
			$table = $table."<tr><td>$roomname</td><td>$floorname</td><td>$department</td>";
			$table = $table."<td><a class='btn btn-sm btn-outline-secondary' href='$index_path?floorid=$floorid&roomid=$roomid' role='button'>Select</a></td></tr>";
			
		}
	}
      
    $table = $table."</tbody></table></div>";
	return $table;
}

function view_selection($roomid, $floorid, $rdate, $time1, $time2)
{
	global $index_path;
	$html = "<h2>Reservation view</h2>";
	$html = $html."<form action='$index_path' method='GET'>";
	$html = $html."<input id='floorid' type='text' name='floorid' value='$floorid' hidden />";
	$html = $html."<input id='roomid' type='text' name='roomid' value='$roomid' hidden />";
	$html = $html."<label for='source'>Date:&nbsp;</label>";
	$html = $html."<input id='rdate' type='date' name='rdate' size='50' value='";
	if ($rdate == "") {
		$html = $html.date('Y-m-d');
	} else {
		$html = $html.$rdate;
	}
	$html = $html."'/>";
	$html = $html."<label for='source'>&nbsp;Time:&nbsp;</label>";
	$html = $html."<input id='time1' type='number' name='time1' size='1' step='1' min='0' max='24' value='";
	if ($time1 < 0) {
		//$html = $html."0";
		$html = $html.date('H');
	} else {
		$html = $html.$time1;
	}
	$html = $html."' />:00 -&nbsp;";
	$html = $html."<input id='time2' type='number' name='time2' size='1' step='1' min='0' max='24' value='";
	if ($time2 < 0) {
		//$html = $html."24";
		$html = $html.(date('H') + 1);
	} else {
		$html = $html.$time2;
	}
	$html = $html."' />:00&nbsp;";
	$html = $html."<input type='submit' value='Select' />";
	$html = $html."</form>";
	return $html;
}

function img_with_title($orientation, $floorid, $roomid, $seatid, $date, $time1, $time2, $permission, $usertoseatid, $description)
	{
		global $index_path, $sessionusername;
		$hour = $time1;
		$title = $description;
		$hours=$time2-$time1;
		$count_blocks=0;
		
		if ($permission == 1) {
			if ($usertoseatid == 0) {
				$title = $title."&#10;No Permission for this seat";
			}
		}
		
		while($hour < $time2) {
			
			$reserved = false;
			$blocked = false;
			
			$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
			$statement = $pdo->prepare("SELECT reservation.userid,user.fullname, user.username from reservation LEFT JOIN user ON reservation.userid = user.userid WHERE reservation.seatid = ? AND reservation.reservationdate = ? AND reservation.time = ? LIMIT 1");
			//$statement = $pdo->prepare("SELECT reservation.userid,user.fullname from reservation LEFT JOIN user ON reservation.userid = user.userid LEFT JOIN blocks ON reservation.seatid = blocks.seatid WHERE blocks.blockedseatid = ? AND reservation.reservationdate = ? AND reservation.time = ? LIMIT 1");
			$statement->execute(array($seatid,$date,$hour));
			$row = $statement->fetch();
			$userid = $row['userid'];
			$fullname = $row['fullname'];
			$username = $row['username'];
			if ($userid != 0)
			{
				$reserved=true;
				if ($title == "")
				{
					$title = $hour.":00-".($hour + 1).":00 reserved by ";
				} else {
					$title = $title."&#10;".$hour.":00-".($hour + 1).":00 reserved by ";
				}
				if ($fullname == "")
				{
					$title = $title.$username;
				} else {
					$title = $title.$fullname;
				}
				
			} else {
				$timestamp = strtotime($date) + ($hour*60*60);
				$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
				//$statement = $pdo->prepare("SELECT reservation.userid,user.fullname from reservation LEFT JOIN user ON reservation.userid = user.userid WHERE reservation.seatid = ? AND reservation.reservationdate = ? AND reservation.time = ? LIMIT 1");
				$statement = $pdo->prepare("SELECT reservation.userid,user.fullname, user.username from reservation LEFT JOIN user ON reservation.userid = user.userid LEFT JOIN blocks ON reservation.seatid = blocks.seatid WHERE blocks.blockedseatid = ? AND reservation.timestamp BETWEEN ? AND ? LIMIT 1");
				$statement->execute(array($seatid,$timestamp-3601,$timestamp+3601));
				$row = $statement->fetch();
				$userid = $row['userid'];
				$fullname = $row['fullname'];
				$username = $row['username'];
				if ($userid != 0)
				{
					$blocked=true;
					if ($title == "")
					{
						$title = $hour.":00-".($hour + 1).":00 blocked by ";
					} else {
						$title = $title."&#10;".$hour.":00-".($hour + 1).":00 blocked by ";
					}
					if ($fullname == "")
					{
						$title = $title.$username;
					} else {
						$title = $title.$fullname;
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
		
		//search for the whole day for all reservations for that seat and check if user is different than session user
		$dayblocked = 0;
		if ($permission == 2) {
			$hour = 0;
			while($hour < 24) {
				
				$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
				$statement = $pdo->prepare("SELECT reservation.userid,user.fullname, user.username from reservation LEFT JOIN user ON reservation.userid = user.userid WHERE reservation.seatid = ? AND reservation.reservationdate = ? AND reservation.time = ? LIMIT 1");
				//$statement = $pdo->prepare("SELECT reservation.userid,user.fullname from reservation LEFT JOIN user ON reservation.userid = user.userid LEFT JOIN blocks ON reservation.seatid = blocks.seatid WHERE blocks.blockedseatid = ? AND reservation.reservationdate = ? AND reservation.time = ? LIMIT 1");
				$statement->execute(array($seatid,$date,$hour));
				$row = $statement->fetch();
				$userid = $row['userid'];
				$fullname = $row['fullname'];
				$username = $row['username'];
				if ($userid != 0)
				{
					if ($username != $sessionusername) {
						$dayblocked = 1;
					}
				} 
				$hour = $hour+1;
			}
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
		
		if ($permission == 1 && $usertoseatid == 0) {
			$img = $img."r.png";
		} else if ($count_blocks == $hours) {
			$img = $img."r.png";
		} else if ($dayblocked > 0) {
			$img = $img."r.png";
			if ($title == "")
			{
				$title = "Seat limited to one user per day";
			} else {
				$title = $title."&#10;Seat limited to one user per day";
			}
		} else if ($count_blocks > 0) {
			$img = $img."o.png";
		} else {
			$img = $img."g.png";
			$link = $index_path."?floorid=$floorid&roomid=$roomid&rdate=$date&time1=$time1&time2=$time2&seatid=$seatid&insert=1";
		}
		
		$html = "<td width='1'>";
		if ($link == "") 
		{
			$html = $html."<img src='/img/$img' title='$title'>";
		} else {
			$html = $html."<a href='$link'>";
			$html = $html."<img src='/img/$img' title='$title'>";
			$html = $html."</a>";
		}
		$html = $html."</td>";
		return $html;	
	}

function view_room($roomid, $rdate, $time1, $time2, $userid)
{
	
	$html = "<div class='container'>Select green seat to book";
	$html = $html."<table class='table table-bordered'><tbody>";

	$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
	//$statement = $pdo->prepare("SELECT seat.seatid, seat.row, seat.orientation, seat.type, seat.permission, room.floorid from seat LEFT JOIN room ON seat.roomid = room.roomid WHERE seat.roomid = ? ORDER BY seat.row, seat.col");
	//$statement = $pdo->prepare("SELECT seat.seatid, seat.row, seat.orientation, seat.type, seat.permission, room.floorid, usertoseat.usertoseatid from seat LEFT JOIN room ON seat.roomid = room.roomid LEFT JOIN usertoseat ON seat.seatid = usertoseat.usertoseatid AND usertoseat.userid = ? WHERE seat.roomid = ? ORDER BY seat.row, seat.col");
	$statement = $pdo->prepare("SELECT seat.seatid, seat.row, seat.orientation, seat.type, seat.permission, seat.description, room.floorid, usertoseat.usertoseatid from seat LEFT JOIN room ON seat.roomid = room.roomid LEFT JOIN usertoseat ON seat.seatid = usertoseat.seatid AND usertoseat.userid = ? WHERE seat.roomid = ? ORDER BY seat.row, seat.col");
	if ($statement->execute(array($userid,$roomid))) {
		$gridrow = 0;
		while($row = $statement->fetch()) {
			if ($row['row'] > $gridrow) {
				$gridrow = $row['row'];
				$html = $html."<tr>";
			}
			$seatid = $row['seatid'];
			$floorid = $row['floorid'];
			$orientation = $row['orientation'];
			$usertoseatid = $row['usertoseatid'];
			$permission = $row['permission'];
			$description = $row['description'];
			

			if ($row['type'] == 1) {
				$img = img_with_title($orientation, $floorid, $roomid, $seatid, $rdate, $time1, $time2, $permission, $usertoseatid, $description);
				$html = $html.$img;
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
		$html = $html."SQL Error <br />";
		$html = $html.$statement->queryString."<br />";
		$html = $html.$statement->errorInfo()[2];
	}

	$html = $html."</tbody></table></div>";
	return $html;
	
}

function view_room_list($floorid, $roomid, $rdate, $time1, $time2, $userid)
{
	$html = "<h2>Reservation list</h2><div class='table-responsive'><table class='table table-striped table-sm'>";
	$html = $html."<thead><tr><th>Room Name</th><th>Seat ID</th><th>Seat Info</th><th>Date</th><th>Time</th><th>User</th>";
	$html = $html."<th>Delete</th></tr></thead><tbody>";
	
	$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
	$statement = $pdo->prepare("SELECT reservation.*, room.roomname, user.username, seat.description FROM reservation LEFT JOIN seat ON reservation.seatid = seat.seatid LEFT JOIN room ON seat.roomid = room.roomid LEFT JOIN user ON reservation.userid = user.userid WHERE room.roomid = ? and reservation.reservationdate = ? and reservation.time >= ? AND reservation.time <= ? AND reservation.userid = ? ORDER BY room.roomname, reservation.reservationdate, reservation.time");
	if ($statement->execute(array($roomid,$rdate,$time1,$time2-1,$userid))) {
		while($row = $statement->fetch()) {
			$id = $row['reservationid'];
			$rtime1 = $row['time'];
			$rtime2 = $row['time'] + 1;
			$html = $html."<tr>";
			$html = $html."<td>".$row['roomname']."</td>";
			$html = $html."<td>".$row['seatid']."</td>";
			$html = $html."<td>".$row['description']."</td>";
			$html = $html."<td>".$row['reservationdate']."</td>";
			$html = $html."<td>".$row['time'].":00-$rtime2:00</td>";
			$html = $html."<td>".$row['username']."</td>";
			$html = $html."<td>";
			$html = $html."<a class='btn btn-sm btn-outline-secondary' href='$index_path?&floorid=$floorid&roomid=$roomid&rdate=$rdate&time1=$time1&time2=$time2&id=$id&delete=1' role='button'>Delete</a>";
			$html = $html."</td>";
			$html = $html."</tr>";
		}
	} else {
		echo "SQL Error <br />";
		echo $statement->queryString."<br />";
		echo $statement->errorInfo()[2];
	}
	$html = $html."</tbody></table></div>";
       	
	return $html;
}

function view_room_list_admin($floorid, $roomid, $rdate, $time1, $time2, $userid)
{
	$html = "<h2>Reservation list</h2><div class='table-responsive'><table class='table table-striped table-sm'>";
	$html = $html."<thead><tr><th>Room Name</th><th>Seat ID</th><th>Seat Info</th><th>Date</th><th>Time</th><th>User</th>";
	$html = $html."<th>Delete</th></tr></thead><tbody>";
	
	$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
	$statement = $pdo->prepare("SELECT reservation.*, room.roomname, user.username, seat.description FROM reservation LEFT JOIN seat ON reservation.seatid = seat.seatid LEFT JOIN room ON seat.roomid = room.roomid LEFT JOIN user ON reservation.userid = user.userid WHERE room.roomid = ? and reservation.reservationdate = ? and reservation.time >= ? AND reservation.time <= ? ORDER BY room.roomname, reservation.reservationdate, reservation.time");
	if ($statement->execute(array($roomid,$rdate,$time1,$time2-1))) {
		while($row = $statement->fetch()) {
			$id = $row['reservationid'];
			$rtime1 = $row['time'];
			$rtime2 = $row['time'] + 1;
			$html = $html."<tr>";
			$html = $html."<td>".$row['roomname']."</td>";
			$html = $html."<td>".$row['seatid']."</td>";
			$html = $html."<td>".$row['description']."</td>";
			$html = $html."<td>".$row['reservationdate']."</td>";
			$html = $html."<td>".$row['time'].":00-$rtime2:00</td>";
			$html = $html."<td>".$row['username']."</td>";
			$html = $html."<td>";
			$html = $html."<a class='btn btn-sm btn-outline-secondary' href='$index_path?&floorid=$floorid&roomid=$roomid&rdate=$rdate&time1=$time1&time2=$time2&id=$id&delete=1' role='button'>Delete</a>";
			$html = $html."</td>";
			$html = $html."</tr>";
		}
	} else {
		echo "SQL Error <br />";
		echo $statement->queryString."<br />";
		echo $statement->errorInfo()[2];
	}
	$html = $html."</tbody></table></div>";
       	
	return $html;
}

function request_access($username)
{
	global $index_path;
	
	$html = "<h2>Request access to another floor</h2><div class='table-responsive'><table class='table table-striped table-sm'>";
	$html = $html."<thead><tr><th>Floor</th><th>Department</th><th></th></tr></thead><tbody>";
	
	$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
	$statement = $pdo->prepare("SELECT floor.floorid, floor.floorname, floor.department, groups.info, groups.groupid from floor inner join floortogroup on floor.floorid = floortogroup.floorid inner join groups on floortogroup.groupid = groups.groupid WHERE floor.floorid NOT IN (SELECT floor.floorid FROM floor INNER JOIN floortogroup ON floor.floorid = floortogroup.floorid INNER JOIN usertogroup ON floortogroup.groupid = usertogroup.groupid INNER JOIN user ON usertogroup.userid = user.userid WHERE user.username = ? ORDER BY floor.floorname)");
	
	if ($statement->execute(array($username))) {
		while($row = $statement->fetch()) {
			$floorid = $row['floorid'];
			$groupid = $row['groupid'];
			$floorname = $row['floorname'];
			$department = $row['department'];
			
			$html = $html."<tr><td>$floorname</td><td>$department</td>";
			$html = $html."<td><a class='btn btn-sm btn-outline-secondary' href='$index_path?sendrequest=$groupid' role='button'>Send Request</a></td></tr>";
			
		}
	}
      
    $html = $html."</tbody></table></div><br><p>The assigned floor administrator will receive a message for review</p>";
	return $html;
}

include ("includes/html.php");
echo head("Seat Reservation - Admin");
echo body_top("Seat Reservation - Admin",$sessionusername,$index_path);

if ($userroomid != 0) {
	echo display_menu($isadmin, $getfloorid, $userroomid, $getrequest);
} else {
	echo display_menu($isadmin, $getfloorid, $getroomid, $getrequest);
}
	 
?>

    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group mr-2">
          </div>
        </div>
      </div>

	  
<?php
	  
//if room/seat empty and no get value then list floors
if ($getrdate != "") {
	echo view_selection($getroomid, $getfloorid, $getrdate, $gettime1, $gettime2);
	echo view_room($getroomid, $getrdate, $gettime1, $gettime2, $userid);
	if ($isadmin > 0) {
		echo view_room_list_admin($getfloorid, $getroomid, $getrdate, $gettime1, $gettime2, $userid);
	} else {
		echo view_room_list($getfloorid, $getroomid, $getrdate, $gettime1, $gettime2, $userid);
	}
	
} else if ($userroomid != 0) {
	echo view_selection($userroomid, $getfloorid, $getrdate, $gettime1, $gettime2);
	echo view_room($userroomid, date('Y-m-d'), date('H'), date('H')+1, $userid);
	if ($isadmin > 0) {
		echo view_room_list_admin($getfloorid, $userroomid, date('Y-m-d'), date('H'), date('H')+1, $userid);
	} else {
		echo view_room_list($getfloorid, $userroomid, date('Y-m-d'), date('H'), date('H')+1, $userid);
	}
	//showroom()
//} else if ($userseatid != 0) {
	//showroom for seat x
	//echo "seatid:$userseatid";
} else if ($getroomid != 0) {
	//showroom 
	echo view_selection($getroomid, $getfloorid, $getrdate, $gettime1, $gettime2);
	echo view_room($getroomid, date('Y-m-d'), date('H'), date('H')+1, $userid);
	echo view_room_list($getfloorid, $getroomid, date('Y-m-d'), date('H'), date('H')+1, $userid);
} else if ($getfloorid != 0) {
	//show all rooms for floor
	echo list_all_rooms($getfloorid, $userid);
} else if ($getrequest != 0) {
		echo request_access($sessionusername);
} else {
	//show all floors
	echo list_all_floor($sessionusername);
	
}
	  
echo body_bottom();	
  
?>
      
	  
