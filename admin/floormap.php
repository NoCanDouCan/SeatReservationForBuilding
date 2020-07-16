<?php
	include ("../config/session_admin.php");
	include ("../config/db.php");
	include ("../includes/menu.php");
	include ("../includes/html.php");

	$getfloorid = 0;
	if (isset($_GET['floorid'])) {
		$getfloorid = $_GET['floorid'];
	}
	$getrdate = date('Y-m-d');
	if (isset($_GET['rdate'])) {
		$getrdate = $_GET['rdate'];
	}
	$gettime1 = 0;
	if (isset($_GET['time1'])) {
		$gettime1 = $_GET['time1'];
	}
	$gettime2 = 24;
	if (isset($_GET['time2'])) {
		$gettime2 = $_GET['time2'];
	}
	$getedit = 0;
	if (isset($_GET['edit'])) {
		$getedit = $_GET['edit'];
	}
	if (isset($_GET['del'])) {
		$floormapid = $_GET['del'];
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("delete from floormap WHERE floormapid = ?");
		if ($statement->execute(array($floormapid))) {
			
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
	} else if (isset($_GET['move'])) {
		$floormapid = $_GET['move'];
		$getrow = $_GET['row'];
		$getcol = $_GET['col'];
		
		//update db
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("update floormap set row = ?, col = ? WHERE floormapid = ?");
		if ($statement->execute(array($getrow, $getcol, $floormapid))) {
			
		}
		
	} else if (isset($_GET['add'])) {
		$getseatid = $_GET['seatid'];
		$roomid = -1;
		$floorid = -1;
		$getrow = $_GET['row'];
		$getcol = $_GET['col'];
		echo "SeatID:".$getseatid." floorid:".$getfloorid."<br>";

		if ($getseatid == "blank"){
			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("insert into floormap (seatid, floorid, roomid, col, row) VALUES (?,?,?,?,?)");
			if ($statement->execute(array(-1, $getfloorid, -1, $getcol, $getrow))) {
				
			} else {
				echo "SQL Error <br />";
				echo $statement->queryString."<br />";
				echo $statement->errorInfo()[2];
			}
		} else if ($getseatid == "windowv") {
			//insert into db
			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("insert into floormap (seatid, floorid, roomid, col, row) VALUES (?,?,?,?,?)");
			if ($statement->execute(array(-2, $getfloorid, -1, $getcol, $getrow))) {
				
			} else {
				echo "SQL Error <br />";
				echo $statement->queryString."<br />";
				echo $statement->errorInfo()[2];
			}

		} else if ($getseatid == "windowh") {
			//insert into db
			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("insert into floormap (seatid, floorid, roomid, col, row) VALUES (?,?,?,?,?)");
			if ($statement->execute(array(-3, $getfloorid, -1, $getcol, $getrow))) {
				
			} else {
				echo "SQL Error <br />";
				echo $statement->queryString."<br />";
				echo $statement->errorInfo()[2];
			}

		} else if ($getseatid == "wallv") {
			//insert into db
			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("insert into floormap (seatid, floorid, roomid, col, row) VALUES (?,?,?,?,?)");
			if ($statement->execute(array(-4, $getfloorid, -1, $getcol, $getrow))) {
				
			} else {
				echo "SQL Error <br />";
				echo $statement->queryString."<br />";
				echo $statement->errorInfo()[2];
			}

		} else if ($getseatid == "wallh") {
			//insert into db
			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("insert into floormap (seatid, floorid, roomid, col, row) VALUES (?,?,?,?,?)");
			if ($statement->execute(array(-5, $getfloorid, -1, $getcol, $getrow))) {
				
			} else {
				echo "SQL Error <br />";
				echo $statement->queryString."<br />";
				echo $statement->errorInfo()[2];
			}

		} else {
			//search for floorid, roomid
			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("SELECT seat.roomid, room.floorid FROM seat INNER JOIN room on seat.roomid = room.roomid WHERE seat.seatid = ? LIMIT 1");
			if ($statement->execute(array($getseatid))) {
				while($row = $statement->fetch()) {
					$roomid = $row['roomid'];
					$floorid = $row['floorid'];
				}
			} else {
				echo "SQL Error <br />";
				echo $statement->queryString."<br />";
				echo $statement->errorInfo()[2];
			}
			
			if ($roomid > 0 && $floorid > 0){
				//insert into db
				$pdo = new PDO($dbserver, $dbuser, $dbpw);
				$statement = $pdo->prepare("insert into floormap (seatid, floorid, roomid, col, row) VALUES (?,?,?,?,?)");
				if ($statement->execute(array($getseatid, $floorid, $roomid, 0, 0))) {
					
				} else {
					echo "SQL Error <br />";
					echo $statement->queryString."<br />";
					echo $statement->errorInfo()[2];
				}
			}
		}
		
	}
	
	function button_edit_row($dbrow, $dbcol, $seatid, $floormapid)
	{
		global $getfloorid, $getrdate, $gettime1, $gettime2, $getedit,$dbserver, $dbuser, $dbpw;
		$description = "";
		$roomname = "";
		$floorname = "";
		$seatrow = 0;
		$seatcol = 0;
		
		if ($seatid != -1) {
			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("SELECT seat.description, room.roomname, floor.floorname, seat.col, seat.row FROM seat INNER JOIN room on seat.roomid = room.roomid INNER JOIN floor ON room.floorid = room.floorid WHERE seat.seatid = ? LIMIT 1");
			if ($statement->execute(array($seatid))) {
				while($row = $statement->fetch()) {
					$description = $row['description'];
					$roomname = $row['roomname'];
					$floorname = $row['floorname'];
					$seatrow = $row['row'];
					$seatcol = $row['col'];
				}
			}
		}
		
		$html = "<table class='table table'><tbody>";
		
		$html = $html."<tr>";
		$html = $html."<td width='1'>";
		$html = $html."</td>";
		$html = $html."<td width='1'>";
		$html = $html."<a class='btn btn-primary' href='floormap.php?floorid=".$getfloorid."&rdate=".$getrdate."&time1=".$gettime1."&time2=".$gettime2."&edit=".$getedit."&move=".$floormapid."&row=".($dbrow-1)."&col=".$dbcol."' >^</a>";
		$html = $html."</td>";
		$html = $html."<td width='1'>";
		$html = $html."</td>";
		$html = $html."</tr>";
		
		$html = $html."<tr>";
		$html = $html."<td width='1'>";
		$html = $html."<a class='btn btn-primary' href='floormap.php?floorid=".$getfloorid."&rdate=".$getrdate."&time1=".$gettime1."&time2=".$gettime2."&edit=".$getedit."&move=".$floormapid."&row=".$dbrow."&col=".($dbcol-1)."' ><</a>";
		$html = $html."</td>";
		
		$html = $html."<td width='1'>";
		$html = $html.$dbrow."-".$dbcol;
		$html = $html."<br>";
		$html = $html.$seatrow."-".$seatcol;
		$html = $html."</td>";
		
		$html = $html."<td width='1'>";
		$html = $html."<a class='btn btn-primary' href='floormap.php?floorid=".$getfloorid."&rdate=".$getrdate."&time1=".$gettime1."&time2=".$gettime2."&edit=".$getedit."&move=".$floormapid."&row=".$dbrow."&col=".($dbcol+1)."' >></a>";
		$html = $html."</td>";
		$html = $html."</tr>";
		
		$html = $html."<tr>";
		$html = $html."<td width='1'>";
		$html = $html."</td>";
		$html = $html."<td width='1'>";
		$html = $html."<a class='btn btn-primary' href='floormap.php?floorid=".$getfloorid."&rdate=".$getrdate."&time1=".$gettime1."&time2=".$gettime2."&edit=".$getedit."&move=".$floormapid."&row=".($dbrow+1)."&col=".$dbcol."' >v</a>";
		$html = $html."</td>";
		$html = $html."<td width='1'>";
		$html = $html."<a class='btn btn-primary' href='floormap.php?floorid=".$getfloorid."&rdate=".$getrdate."&time1=".$gettime1."&time2=".$gettime2."&edit=".$getedit."&del=".$floormapid."' >X</a>";		
		$html = $html."</td>";
		$html = $html."</tr>";
		
		$html = $html."<tr>";
		$html = $html."<td width='1' colspan='3'>";
		$html = $html.$floorname."-".$roomname."-".$description;
		$html = $html."</td>";
		$html = $html."</tr>";
		
		$html = $html."</tbody></table>";
		return $html;
	}
	
	function button_add_seat()
	{
		global $dbserver, $dbuser, $dbpw, $username,$getfloorid,$getrdate,$gettime1,$gettime2,$getedit;
		$html = "<form action='floormap.php' method='GET'>";
		$html = $html."<input id='floorid' type='text' name='floorid' size='50' value='$getfloorid' readonly hidden  />";
		$html = $html."<input id='rdate' type='text' name='rdate' size='50' value='$getrdate' readonly hidden  />";
		$html = $html."<input id='time1' type='text' name='time1' size='50' value='$gettime1' readonly hidden  />";
		$html = $html."<input id='time2' type='text' name='time2' size='50' value='$gettime2' readonly hidden  />";
		$html = $html."<input id='edit' type='text' name='edit' size='50' value='$getedit' readonly hidden  />";
		$html = $html."<input id='col' type='text' name='col' size='50' value='0' readonly hidden  />";
		$html = $html."<input id='row' type='text' name='row' size='50' value='0' readonly hidden  />";
		
		
		$html = $html."<select id='seatid' name='seatid'>";
		$html = $html."<option value='blank'>Blank</option>";
		$html = $html."<option value='windowv'>Window vertical</option>";
		$html = $html."<option value='windowh'>Window horizontal</option>";
		$html = $html."<option value='wallv'>Wall vertical</option>";
		$html = $html."<option value='wallh'>Wall horizontal</option>";
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT seat.seatid, seat.description, room.roomname, floor.floorname FROM seat INNER JOIN room on seat.roomid = room.roomid INNER JOIN floor ON room.floorid = floor.floorid INNER JOIN floortogroup on floor.floorid = floortogroup.floorid INNER JOIN usertogroup ON floortogroup.groupid = usertogroup.groupid INNER JOIN user on usertogroup.userid = user.userid WHERE seat.seatid NOT IN (SELECT floormap.seatid FROM floormap) AND user.username = ?");
		if ($statement->execute(array($username))) {
			while($row = $statement->fetch()) {
				$html = $html."<option value='".$row['seatid']."'>".$row['floorname']."-".$row['roomname']."-".$row['description']."</option>";
			}
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
		$html = $html."</select>";
		$html = $html."<input type='submit' name='add' value='Add' />";
		$html = $html."</form>";
		
		return $html;
		
	}
	
	function img_with_title($seatid, $date, $time1, $time2, $description)
	{
		global $index_path, $sessionusername;
		$hour = $time1;
		$title = $description;
		$hours=$time2-$time1;
		$count_blocks=0;
		
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
		
		
		$img = "";
		if ($count_blocks == $hours) {
			$img = $img."sr.png";
		} else if ($count_blocks > 0) {
			$img = $img."so.png";
		} else {
			$img = $img."sg.png";
		}
		
		
		$html = $html."<img src='/img/$img' title='$title'>";
		
		
		return $html;	
	}
	
	function view_floor()
	{
		global $dbserver, $dbuser, $dbpw, $username, $getfloorid, $getrdate, $gettime1, $gettime2, $getedit;
		
		$html = "<div class='container'>Select green seat to book";
		$html = $html."<table class='table table-bordered'><tbody>";
		
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT floormap.floormapid, floormap.seatid, floormap.row, floormap.col, seat.type, seat.orientation, seat.description FROM floormap LEFT JOIN seat on floormap.seatid = seat.seatid WHERE floormap.floorid = ? ORDER BY floormap.row, floormap.col");
		if ($statement->execute(array($getfloorid))) {
			$gridrow = 0;
			$lastcol = 0;
			while($row = $statement->fetch()) {
				$floormapid = $row['floormapid'];
				$seatid = $row['seatid'];
				$dbrow = $row['row'];
				$type = $row['type'];
				$orientation = $row['orientation'];
				$dbcol = $row['col'];
				$description = $row['description'];
				if ($dbrow > $gridrow) {
					$gridrow = $dbrow;
					if ($dbrow > 0) {
						if ($getedit > 0) {
							$html = $html."<td width='1'>";
							$html = $html."<a class='btn btn-primary' href='floormap.php?floorid=".$getfloorid."&rdate=".$getrdate."&time1=".$gettime1."&time2=".$gettime2."&edit=".$getedit."&seatid=blank&add=0&row=".($dbrow-1)."&col=".($lastcol+1)."' >Add Blank</a><br>";
							$html = $html."<a class='btn btn-primary' href='floormap.php?floorid=".$getfloorid."&rdate=".$getrdate."&time1=".$gettime1."&time2=".$gettime2."&edit=".$getedit."&seatid=windowv&add=0&row=".($dbrow-1)."&col=".($lastcol+1)."' >Add Window V</a><br>";
							$html = $html."<a class='btn btn-primary' href='floormap.php?floorid=".$getfloorid."&rdate=".$getrdate."&time1=".$gettime1."&time2=".$gettime2."&edit=".$getedit."&seatid=windowh&add=0&row=".($dbrow-1)."&col=".($lastcol+1)."' >Add Window H</a><br>";
							$html = $html."<a class='btn btn-primary' href='floormap.php?floorid=".$getfloorid."&rdate=".$getrdate."&time1=".$gettime1."&time2=".$gettime2."&edit=".$getedit."&seatid=wallv&add=0&row=".($dbrow-1)."&col=".($lastcol+1)."' >Add Wall V</a><br>";
							$html = $html."<a class='btn btn-primary' href='floormap.php?floorid=".$getfloorid."&rdate=".$getrdate."&time1=".$gettime1."&time2=".$gettime2."&edit=".$getedit."&seatid=wallh&add=0&row=".($dbrow-1)."&col=".($lastcol+1)."' >Add Wall H</a>";
							$html = $html."</td>";
						} 
						$html = $html."</tr>";
					} 
					$html = $html."<tr>";
					
				}
				// if ($gridrow == 0 && $getedit > 0) {
						
					// $html = $html."<tr><td width='1'>";
					// $html = $html."<a class='btn btn-primary' href='floormap.php?floorid=".$getfloorid."&rdate=".$getrdate."&time1=".$gettime1."&time2=".$gettime2."&edit=".$getedit."&add=".$floormapid."&row=0&col=".$dbcol."' >Add Blank</a><br>";
					// $html = $html."<a class='btn btn-primary' href='floormap.php?floorid=".$getfloorid."&rdate=".$getrdate."&time1=".$gettime1."&time2=".$gettime2."&edit=".$getedit."&add=".$floormapid."&row=0&col=".$dbcol."' >Add Window V</a><br>";
					// $html = $html."<a class='btn btn-primary' href='floormap.php?floorid=".$getfloorid."&rdate=".$getrdate."&time1=".$gettime1."&time2=".$gettime2."&edit=".$getedit."&add=".$floormapid."&row=0&col=".$dbcol."' >Add Window H</a><br>";
					// $html = $html."<a class='btn btn-primary' href='floormap.php?floorid=".$getfloorid."&rdate=".$getrdate."&time1=".$gettime1."&time2=".$gettime2."&edit=".$getedit."&add=".$floormapid."&row=0&col=".$dbcol."' >Add Wall V</a><br>";
					// $html = $html."<a class='btn btn-primary' href='floormap.php?floorid=".$getfloorid."&rdate=".$getrdate."&time1=".$gettime1."&time2=".$gettime2."&edit=".$getedit."&add=".$floormapid."&row=0&col=".$dbcol."' >Add Wall H</a>";
					// $html = $html."</td></tr>";
				// }					
				//$html = $html."<td width='1'>".$seatid."<img src='/img/udoor.png'></td>";
				
				$html = $html."<td width='1'>";
				
				if ($type == 1) {
					//$html = $html."<img src='/img/sg.png'>";
					
					$img = img_with_title($seatid, $getrdate, $gettime1, $gettime2, $description);
					$html = $html.$img;
					
				} else if ($type == 2) {
					$html = $html."<img src='/img/sblank.png'>";
				} else if ($type == 3) {
					$html = $html."<img src='/img/shwindow.png'>";
				} else if ($type == 4) {
					$html = $html."<img src='/img/sblank.png'>";
				} else if ($seatid == -1) {
					$html = $html."<img src='/img/sblank.png'>";
				} else if ($seatid == -2) {
					$html = $html."<img src='/img/svwindow.png'>";
				} else if ($seatid == -3) {
					$html = $html."<img src='/img/shwindow.png'>";
				} else if ($seatid == -4) {
					$html = $html."<img src='/img/svwall.png'>";
				} else if ($seatid == -5) {
					$html = $html."<img src='/img/shwall.png'>";
				} 
				if ($getedit > 0) {
					$html = $html."<br>".button_edit_row($dbrow, $dbcol, $seatid, $floormapid);
				}
				$html = $html."</td>";
				
				$lastcol = $dbcol;
			}
			$html = $html."</tr>";
		} else {
			$html = $html."SQL Error <br />";
			$html = $html.$statement->queryString."<br />";
			$html = $html.$statement->errorInfo()[2];
		}

		
		$html = $html."</tbody></table></div>";
		
		if ($getedit > 0) {
			$html = $html."<table class='table table-bordered'><tbody>";
			$html = $html."<tr><td>".button_add_seat()."</td></tr></tbody></table>";
		}
		
		return $html;
	}
		
	
	
	function view_selection()
	{
	global $dbserver, $dbuser, $dbpw, $username,$getfloorid, $getrdate, $gettime1, $gettime2, $getedit;
	$html = "<h2>Reservation view</h2>";
	$html = $html."<form action='floormap.php' method='GET'>";
	
	$html = $html."<select id='floorid' name='floorid'>";
	$pdo = new PDO($dbserver, $dbuser, $dbpw);
	$statement = $pdo->prepare("SELECT floor.floorid, floor.floorname FROM floor INNER JOIN floortogroup ON floor.floorid = floortogroup.floorid INNER JOIN usertogroup ON floortogroup.groupid = usertogroup.groupid INNER JOIN user ON usertogroup.userid = user.userid WHERE user.username = ? ORDER BY floor.floorname");
	if ($statement->execute(array($username))) {
		while($row = $statement->fetch()) {
			if ($getfloorid == $row['floorid']) {
				$html = $html."<option value='".$row['floorid']."' selected>".$row['floorname']."</option>";
			} else {
				$html = $html."<option value='".$row['floorid']."'>".$row['floorname']."</option>";
			}
		}
	} else {
		echo "SQL Error <br />";
		echo $statement->queryString."<br />";
		echo $statement->errorInfo()[2];
	}
	$html = $html."</select>";
	
	$html = $html."<label for='source'>Date:&nbsp;</label>";
	$html = $html."<input id='rdate' type='date' name='rdate' size='50' value='";
	if ($getrdate == "") {
		$html = $html.date('Y-m-d');
	} else {
		$html = $html.$getrdate;
	}
	$html = $html."'/>";
	$html = $html."<label for='source'>&nbsp;Time:&nbsp;</label>";
	$html = $html."<input id='time1' type='number' name='time1' size='1' step='1' min='0' max='24' value='";
	if ($time1 < 0) {
		//$html = $html."0";
		$html = $html.date('H');
	} else {
		$html = $html.$gettime1;
	}
	$html = $html."' />:00 -&nbsp;";
	$html = $html."<input id='time2' type='number' name='time2' size='1' step='1' min='0' max='24' value='";
	if ($time2 < 0) {
		//$html = $html."24";
		$html = $html.(date('H') + 1);
	} else {
		$html = $html.$gettime2;
	}
	$html = $html."' />:00&nbsp;";
	$html = $html."<input type='checkbox' name='edit' id='edit' value='1' hidden ";
	if ($getedit > 0) {
		$html = $html."checked";
	}
	$html = $html."/>";
	//$html = $html."Edit mode";
	$html = $html."<input type='submit' value='Select' />";
	$html = $html."</form>";
	return $html;
	}

	echo head("Seat Reservation - Admin");
	echo body_top_admin("Seat Reservation - Admin",$username);
	echo display_admin_menu("floormap", $isadmin);
	echo body_title_top("Admin - Floormap");
	echo body_title_bottom();
	
	echo view_selection();
	echo view_floor();

	echo body_bottom();
?>	  

