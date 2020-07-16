<?php
    include ("../config/session_admin.php");
	
	include ("../includes/html.php");
	include ("../includes/menu.php");
	
	function check_timeslot($seatid, $date, $hour, $id){
		$reserved = false;
		$blocked = false;
			
		$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
		$statement = $pdo->prepare("SELECT reservation.userid, reservation.reservationid from reservation LEFT JOIN user ON reservation.userid = user.userid WHERE reservation.seatid = ? AND reservation.reservationdate = ? AND reservation.time = ? LIMIT 1");
		$statement->execute(array($seatid,$date,$hour));
		$row = $statement->fetch();
		$userid = $row['userid'];
		$reservationid = $row['reservationid'];
		if ($userid != 0)
		{
			$reserved=true;
		} else if ($reservationid != $id) {
			$reserved=true;
		} else {
			$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
			$statement = $pdo->prepare("SELECT reservation.userid from reservation LEFT JOIN user ON reservation.userid = user.userid LEFT JOIN blocks ON reservation.seatid = blocks.seatid WHERE blocks.blockedseatid = ? AND reservation.reservationdate = ? AND reservation.time = ? LIMIT 1");
			$statement->execute(array($seatid,$date,$hour));
			$row = $statement->fetch();
			$userid = $row['userid'];
			if ($userid != 0)
			{
				$blocked=true;
			}
		}
		if ($reserved == false && $blocked == false) {
			return false;
		} else {
			return true;
		}
	}
	
	$getreservationdate = "";
	$gettime1 = "";
	$gettime2 = "";
	$getseatid = "";
	
	if (isset($_GET['reservationdate'])) {
		$getreservationdate = $_GET['reservationdate'];
	}
	if (isset($_GET['time1'])) {
		$gettime1 = $_GET['time1'];
	}
	if (isset($_GET['time2'])) {
		$gettime2 = $_GET['time2'];
	}
	if (isset($_GET['seatid'])) {
		$getseatid = $_GET['seatid'];
	}
	

    if (isset($_POST['delete'])){
        $pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
        $statement = $pdo->prepare("DELETE FROM reservation WHERE reservationid = ?");
        if ($statement->execute(array($_POST['id']))) {
            header("Location: reservation.php");
            die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
		
    } else if(isset($_POST['update'])){
	    $postreservationid = $_POST['id'];
        $postuserid = $_POST['userid'];
		$postseatid = $_POST['seatid'];
		$postreservationdate = $_POST['reservationdate'];
		$posttime1 = $_POST['time1'];
		$posttime2 = $_POST['time2'];

		
		$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
		$statement = $pdo->prepare("UPDATE reservation SET userid = ? WHERE reservationid = ?");
		if ($statement->execute(array($postuserid,$postreservationid))) {
			
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
		
		$roomid = 0;
		$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
		$statement = $pdo->prepare("SELECT seat.roomid FROM seat WHERE seat.seatid = ? LIMIT 1");
		if ($statement->execute(array($postseatid))) {
			while($row = $statement->fetch()) {
				$roomid = $row['roomid'];
			}
		} else {
				echo "SQL Error <br />";
				echo $statement->queryString."<br />";
				echo $statement->errorInfo()[2];
			}
	
		header("Location: reservation.php?roomid=$roomid&reservationdate=$postreservationdate&time1=$posttime1&time2=$posttime2");
		die();

        
        
    } else if(isset($_POST['create'])){
	
		$postuserid = $_POST['userid'];
		$postseatid = $_POST['seatid'];
		$postreservationdate = $_POST['reservationdate'];
		$posttime1 = $_POST['time1'];
		$posttime2 = $_POST['time2'];
		$hour = $posttime1;
		$timestamp = strtotime($postreservationdate);
		
		while ($hour < $posttime2)
		{
			$timestamp2 = $timestamp + ($hour*60*60);
			//check if already reserved or blocked
			if (check_timeslot($postseatid, $postreservationdate, $hour, 0) == false){
				$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
				$statement = $pdo->prepare("INSERT INTO reservation (seatid, reservationdate, time, userid, timestamp) VALUES (?,?,?,?,?)");
				if ($statement->execute(array($postseatid,$postreservationdate,$hour,$postuserid,$timestamp2))) {
					
				} else {
					echo "SQL Error <br />";
					echo $statement->queryString."<br />";
					echo $statement->errorInfo()[2];
				}
			}
			$hour = $hour + 1;
		}
		
		$roomid = 0;
		$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
		$statement = $pdo->prepare("SELECT seat.roomid FROM seat WHERE seat.seatid = ? LIMIT 1");
		if ($statement->execute(array($postseatid))) {
			while($row = $statement->fetch()) {
				$roomid = $row['roomid'];
			}
		} else {
				echo "SQL Error <br />";
				echo $statement->queryString."<br />";
				echo $statement->errorInfo()[2];
			}
		header("Location: reservation.php?roomid=$roomid&reservationdate=$postreservationdate&time1=$posttime1&time2=$posttime2");
		die();
    } 
    
	function display_form($id)
	{
		$html = "<form action='/seat/admin/addreservation.php' method='POST'>";
		$html = $html."<table>";

		$getreservationdate = "";
		$gettime1 = "";
		$gettime2 = "";
		$userid = 0;
		
		if($id > 0){
			$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
			$statement = $pdo->prepare("SELECT reservation.* FROM reservation where reservationid = ? LIMIT 1");
			if ($statement->execute(array($id))) {
				while($row = $statement->fetch()) {
					//$id = $row['reservationid'];
					$getseatid = $row['seatid'];
					$getreservationdate = $row['reservationdate'];
					$gettime1 = $row['time'];
					$gettime2 = $row['time'] + 1;
					$userid = $row['userid'];
				}
			}
			$html = $html."<tr><td><label for='id'>ID: </label></td><td><input id='id' type='text' name='id' size='50' value='$id' readonly /></td></tr>";
		}
		$html = $html."<tr><td>";
		$html = $html."<label for='source'>Seat: </label>";
		$html = $html."</td>";
		
		$html = $html."<td>";
		$html = $html."<select id='seatid' name='seatid'>";
		$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
		$statement = $pdo->prepare("SELECT * FROM seat ORDER BY seat.seatid");
		if ($statement->execute()) {
			while($row = $statement->fetch()) {
				if ($row['seatid'] == $getseatid) {
					$html = $html."<option value='".$row['seatid']."' selected>".$row['seatid']." - ".$row['description']."</option>";
				} else {
					$html = $html."<option value='".$row['seatid']."'>".$row['seatid']." - ".$row['description']."</option>";
				}
			}
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}

		$html = $html."</select>";
		$html = $html."</td></tr>";
		$html = $html."<tr><td>";
		$html = $html."<label for='source'>Date: </label>";
		$html = $html."</td><td>";
		$html = $html."<input id='reservationdate' type='date' name='reservationdate' size='50' ";
		if($getreservationdate != "") { 
			$html = $html."value='$getreservationdate'"; 
		} else { 
			$html = $html."value='".date('Y-m-d')."'"; 
		}
		$html = $html."/>";
		$html = $html."</td><td>";
		$html = $html."<label for='example1'> Select the date for the reservation </label>";
		$html = $html."</td></tr>";

		$html = $html."<tr><td>";
		$html = $html."<label for='source'>Start Time: </label>";
		$html = $html."</td><td>";
		$html = $html."<input id='time1' type='number' name='time1' size='1' step='1' min='0' max='24' ";
		if ($gettime1 != "") { 
			$html = $html."value='$gettime1'"; 
		} else { 
			$html = $html."value='".date('H')."'"; 
		}
		$html = $html."/>:00";
		$html = $html."</td><td>";
		$html = $html."<label for='example1'> Enter the start time for the reservation.</label>";
		$html = $html."</td></tr>";

		$html = $html."<tr><td>";
		$html = $html."<label for='source'>End Time: </label>";
		$html = $html."</td>";
		$html = $html."<td>";
		$html = $html."<input id='time2' type='number' name='time2' size='1' step='1' min='0' max='24' ";
		if ($gettime2 != "") { 
			$html = $html."value='$gettime2'"; 
		} else { 
			$html = $html."value='".(date('H')+1)."'"; 
		}
		$html = $html."/>:00";
		$html = $html."</td><td>";
		$html = $html."<label for='example1'> Enter the end time for the reservation.</label>";
		$html = $html."</td></tr>";

		$html = $html."<tr><td>";
		$html = $html."<label for='source'>User: </label>";
		$html = $html."</td><td>";
		$html = $html."<select id='userid' name='userid'>";

		$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
		$statement = $pdo->prepare("SELECT * FROM user ORDER BY user.username");
		if ($statement->execute()) {
			while($row = $statement->fetch()) {
				if ($row['userid'] == $userid) {
					$html = $html."<option value='".$row['userid']."' selected>".$row['username']."</option>";
				} else {
					$html = $html."<option value='".$row['userid']."'>".$row['username']."</option>";
				}
			}
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
		$html = $html."</select>";
		$html = $html."</td>";
		$html = $html."<td>";
		$html = $html."<label for='example1'> Blocked timeslots will be ignored</label>";
		$html = $html."</td>";
		$html = $html."</tr>";

		$html = $html."</table>";
		$html = $html."<input type='submit' ";
		if(isset($_GET['id'])) { 
			$html = $html."name='update'";
		} else {
			$html = $html."name='create'";
			} 
		$html = $html." value='Submit' />";
		$html = $html."</form>";
					
		return $html;
	}
	
	echo head("Seat Reservation - Admin");
	echo body_top("Seat Reservation - Admin",$username);
	echo display_admin_menu("reservation", $isadmin);
	echo body_title_top("Add Reservation");
	if($getid > 0){
		echo delete_button($getid, "addreservation.php");
	}
	echo body_title_bottom();
	echo display_form($getid);
	echo body_bottom();
?>					
           
