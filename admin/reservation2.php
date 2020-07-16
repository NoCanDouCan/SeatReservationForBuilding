<?php
session_start();
if (!isset($_SESSION['username'])) {
	header("Location: /seat/login.php");
	die();
}
$username = $_SESSION['username'];

$isadmin = 0;
$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
$statement = $pdo->prepare("SELECT user.isadmin FROM user WHERE user.username = ? LIMIT 1 ");
if ($statement->execute(array($username))) {
	while($row = $statement->fetch()) {
		$isadmin = $row['isadmin'];
	}
}
if ($isadmin == 0) {
	header("Location: /seat/index.php");
	die();
}

if (isset($_GET['id'])) {
	$id = $_GET['id'];
	update_timestamp_id($id);
}

function update_timestamp_id($id)
{
	$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
	$statement = $pdo->prepare("SELECT reservation.reservationid, reservation.reservationdate,reservation.time,reservation.timestamp from reservation WHERE reservation.reservationid = ? LIMIT 1");
	$statement->execute(array($id));
	$row = $statement->fetch();
	$reservationid = $row['reservationid'];
	$reservationdate = $row['reservationdate'];
	$old_timestamp = $row['timestamp'];
	$time = $row['time'];
	if ($reservationid != 0)
	{
		$timestamp = strtotime($reservationdate);
		$timestamp = $timestamp + ($time*60*60);
		
		$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
		$statement = $pdo->prepare("UPDATE reservation SET reservation.timestamp = ? WHERE reservation.reservationid = ?");
		if ($statement->execute(array($timestamp,$reservationid))) {
			header("Location: reservation2.php");
			die();
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
	}
}


	include ("../includes/menu.php");

//todo: click on red blocked seat unblock it
//todo: click on green seat reserves all timeslots
//todo: click on orange seat reserves all open timeslots

?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">

    <title>Seat Reservation - Admin</title>

    <!-- Bootstrap core CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">



    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="https://getbootstrap.com/docs/4.3/examples/dashboard/dashboard.css" rel="stylesheet">
  </head>
  <body>
    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
  <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="/seat/index.php">Seat Reservation - Admin</a>
  <!--input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search"-->
  <ul class="navbar-nav px-3">
    <li class="nav-item text-nowrap">
      <a class="nav-link" href="/seat/logout.php">
	  <?php echo "$username ";?>Sign out
	  </a>
    </li>
  </ul>
</nav>

<div class="container-fluid">
  <div class="row">
	<?php
	echo display_admin_menu("reservation");
	?>

    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Admin - Seat Reservation</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group mr-2">
            <a class='btn btn-sm btn-outline-secondary' href='addreservation.php' role='button'>Add Reservation</a>
          </div>
        </div>
      </div>

  
	  
	  
      <h2>Reservation view</h2>

        <form action="/seat/admin/reservation.php" method="GET">

		<select id="roomid" name="roomid">

        <?php
			if(isset($_GET['reservationdate'])) {
				$reservationdate = $_GET['reservationdate'];
			}
			if(isset($_GET['time1'])) {
				$time1 = $_GET['time1'];
			} else {
				$time1 = date('H');
			}
			if(isset($_GET['time2'])) {
				$time2 = $_GET['time2'];
			} else {
				$time2 = date('H') + 1;
			}
		

			$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
				$statement = $pdo->prepare("SELECT * FROM room ORDER BY room.roomname");
				if ($statement->execute()) {
					while($row = $statement->fetch()) {
						if ($_GET['roomid'] == $row['roomid']) {
                                                	echo "<option value='".$row['roomid']."' selected>".$row['roomname']."</option>";
						} else {
                                                	echo "<option value='".$row['roomid']."'>".$row['roomname']."</option>";
						}
					}
				} else {
					echo "SQL Error <br />";
					echo $statement->queryString."<br />";
					echo $statement->errorInfo()[2];
				}
			?>

                </select>

		<input id="reservationdate" type="date" name="reservationdate" size="50" <?php if(isset($_GET['reservationdate'])) { echo "value='$reservationdate'"; } else { echo "value='".date('Y-m-d')."'"; } ?>/>

                <input id="time1" type="number" name="time1" size="1" step="1" min="0" max="24" <?php echo "value='$time1'"; ?> />:00 - 
                <input id="time2" type="number" name="time2" size="1" step="1" min="0" max="24" <?php echo "value='$time2'"; ?> />:00


		<input type="submit" value="Submit" />

	</form>



<?php

	function img_with_title($orientation, $roomid, $seatid, $date, $time1, $time2)
	{
		$hour = $time1;
		$title = "";
		$hours=$time2-$time1;
		$count_blocks=0;
		
		while($hour < $time2) {
			
			$reserved = false;
			$blocked = false;
			
			$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
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
				$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
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
		
		
		echo "<td width='1'><a href='$link'><img src='/seat/img/$img' title='$title'></a></td>";
			
	}

	if (isset($_GET['roomid'])) {

		$getroomid = $_GET['roomid'];
		$getreservationdate = $_GET['reservationdate'];
		$gettime1 = $_GET['time1'];
		$gettime2 = $_GET['time2'];
		$counthours = $gettime2 - $gettime1;

		if (isset($_GET['block'])) {
			$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
				$statement = $pdo->prepare("INSERT INTO blocks (seatid,blockedseatid) VALUES (?,?)");
				if ($statement->execute(array($_GET['seatid'],$_GET['blockedseatid']))) {
						echo "seat block added";
				} else {
					echo "SQL Error <br />";
						echo $statement->queryString."<br />";
						echo $statement->errorInfo()[2];
				}

		}
		if (isset($_GET['unblock'])) {
			$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
				$statement = $pdo->prepare("DELETE FROM blocks WHERE blockedseatid = ? AND seatid = ?");
				if ($statement->execute(array($_GET['blockedseatid'],$_GET['seatid']))) {
						echo "seat block removed";
				} else {
						echo "SQL Error <br />";
						echo $statement->queryString."<br />";
						echo $statement->errorInfo()[2];
				}

		}

		echo "<div class='container'>Select seat to edit";
		echo "<table class='table table-bordered'><tbody>";

		$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
		$statement = $pdo->prepare("SELECT seat.seatid, seat.row, seat.orientation, seat.type from seat WHERE seat.roomid = ? ORDER BY seat.row, seat.col");

		if ($statement->execute(array($getroomid))) {
			
			$gridrow = 0;
			while($row = $statement->fetch()) {
				if ($row['row'] > $gridrow) {
					$gridrow = $row['row'];
					echo "<tr>";
				}
				$seatid = $row['seatid'];
				$orientation = $row['orientation'];

				if ($row['type'] == 1) {
					echo img_with_title($orientation, $getroomid, $seatid, $getreservationdate, $gettime1, $gettime2);
				} else if ($row['type'] == 2) {
					if ($row['orientation'] == 1) {
						echo "<td width='1'><img src='/seat/img/udoor.png'></td>";
											} else if ($row['orientation'] == 2) {
						echo "<td width='1'><img src='/seat/img/rdoor.png'></td>";
											} else if($row['orientation'] == 3) {
						echo "<td width='1'><img src='/seat/img/ddoor.png'></td>";
											} else if($row['orientation'] == 4) {
						echo "<td width='1'><img src='/seat/img/ldoor.png'></td>";
					}
				} else if ($row['type'] == 3) {
					if ($row['orientation'] == 1) {
						echo "<td width='1'><img src='/seat/img/hwindow.png'></td>";
					} else if ($row['orientation'] == 2) {
						echo "<td width='1'><img src='/seat/img/vwindow.png'></td>";
					} else if($row['orientation'] == 3) {
						echo "<td width='1'><img src='/seat/img/hwindow.png'></td>";
					} else if($row['orientation'] == 4) {
						echo "<td width='1'><img src='/seat/img/vwindow.png'></td>";
					}
				} else if ($row['type'] == 4) {
					echo "<td width='1'><img src='/seat/img/blank.png'></td>";
				}
			}
			echo "</tr>";
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}

		echo "</tbody></table></div>";

	}
?>


      <h2>Reservation list</h2>
      <div class="table-responsive">
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th>Reservation ID</th>

              <th>Seat ID</th>
              <th>Seat Info</th>
              <th>Date</th>
              <th>Time</th>
			  <th>Calc Timestamp</th>
			  <th>DB Timestamp</th>
			  <th>DB Date+Time</th>

			  <th>Update</th>
            </tr>
          </thead>
          <tbody>
		  
		  <?php

		$getroomid = $_GET['roomid'];
		$getreservationdate = $_GET['reservationdate'];
		$gettime1 = $_GET['time1'];
		$gettime2 = $_GET['time2'];



			$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
				$statement = $pdo->prepare("SELECT reservation.*, room.roomname, user.username, seat.description, reservation.timestamp FROM reservation LEFT JOIN seat ON reservation.seatid = seat.seatid LEFT JOIN room ON seat.roomid = room.roomid LEFT JOIN user ON reservation.userid = user.userid ORDER BY reservation.reservationid");

				if ($statement->execute(array())) {

				
					while($row = $statement->fetch()) {
                        $id = $row['reservationid'];
						$time = $row['time'];
						$time2 = $row['time'] + 1;
						$reservationdate = $row['reservationdate'];
						$timestamp1 = strtotime($reservationdate);
						$timestamp2 = 60*60*$time;
						$timestamp3 = $row['timestamp'];
						$calcdate = date("Y-m-d H:i:s", $timestamp3);
						echo "<tr>";
						echo "<td>".$row['reservationid']."</td>";

						echo "<td>".$row['seatid']."</td>";
						echo "<td>".$row['description']."</td>";
						echo "<td>".$row['reservationdate']."</td>";
						echo "<td>".$row['time'].":00-$time2:00</td>";
						echo "<td>".$timestamp1."+".$timestamp2."=".($timestamp1+$timestamp2)."</td>";
						if ($timestamp3 != ($timestamp1+$timestamp2)) {
							echo "<td><b>".$timestamp3."</b></td>";	
						} else {
							echo "<td>".$timestamp3."</td>";	
						}
											
						echo "<td>".$calcdate."</td>";

						echo "<td>";
						
						echo "<a class='btn btn-sm btn-outline-secondary' href='reservation2.php?&id=$id' role='button'>Update</a>";
						echo "</td>";
						
						echo "</tr>";
					}
				} else {
					echo "SQL Error <br />";
					echo $statement->queryString."<br />";
					echo $statement->errorInfo()[2];
				}
			?>

          </tbody>
        </table>
      </div>
	  
	  
    </main>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="/docs/4.3/assets/js/vendor/jquery-slim.min.js"><\/script>')</script><script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-xrRywqdh3PHs8keKZN+8zzc5TX0GRTLCcmivcbNJWm2rs5C8PRhcEn3czEjhAO9o" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.9.0/feather.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
        <script src="https://getbootstrap.com/docs/4.3/examples/dashboard/dashboard.js"></script></body>
</html>
