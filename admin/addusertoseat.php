<?php
    include ("../config/session_admin.php");
	include ("../config/db.php");

	//get variables
	$getid = 0;
	if(isset($_GET['id'])){
		$getid = $_GET['id'];
	}
	$getseatid = 0;
	if(isset($_GET['seatid'])){
		$getseatid = $_GET['seatid'];
	}
	

	
    if (isset($_POST['delete'])){
        $pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
        $statement = $pdo->prepare("DELETE FROM usertoseat WHERE usertoseatid = ?");
        if ($statement->execute(array($_POST['id']))) {
            header("Location: usertoseat.php");
            die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
    } else if(isset($_POST['update'])){
        //SQL
        
        $pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
        $statement = $pdo->prepare("UPDATE usertoseat SET seatid = ?,userid = ? WHERE usertoseat.usertoseatid = ?");
        if ($statement->execute(array($_POST['seatid'],$_POST['userid'],$_POST['id']))) {
            header("Location: usertoseat.php");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
        
    } else if(isset($_POST['create'])){
        //SQL
        
        $pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
        $statement = $pdo->prepare("INSERT INTO usertoseat (userid,seatid) VALUES (?,?)");
        if ($statement->execute(array($_POST['userid'],$_POST['seatid']))) {
            header("Location: usertoseat.php");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
        
    } 
	
	function display_form($usertoseatid, $getseatid,$username)
	{
		$html = "<form action='addusertoseat.php' method='POST'>";
		$html = $html."<table>";

		if($usertoseatid > 0){
			$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
			$statement = $pdo->prepare("SELECT usertoseat.* FROM usertoseat WHERE usertoseat.usertoseatid = ? LIMIT 1 ");
			if ($statement->execute(array($usertoseatid))) {
				while($row = $statement->fetch()) {
					$userid = $row['userid'];
					$seatid = $row['seatid'];
				}
			}
			$html = $html."<tr><td><label for='id' hidden>ID: </label></td><td><input id='id' type='text' name='id' size='50' value='$usertoseatid' readonly hidden  /></td></tr>";
		} else {
			$seatid = $getseatid;
		}
		
		$html = $html."<tr><td><label for='source'>User: </label></td>";
		$html = $html."<td><select id='userid' name='userid'>";
		$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
		$statement = $pdo->prepare("SELECT * FROM user WHERE user.userid IN (SELECT usertogroup.userid FROM usertogroup INNER JOIN usertogroup as utg2 ON usertogroup.groupid = utg2.groupid INNER JOIN user as user2 ON utg2.userid = user2.userid WHERE user2.username = ?) ORDER BY user.fullname");
		if ($statement->execute(array($username))) {
			while($row = $statement->fetch()) {
				if ($row['userid'] == $userid) {
				  $html = $html."<option value='".$row['userid']."' selected>".$row['fullname']."</option>";
				} else {
				   $html = $html."<option value='".$row['userid']."'>".$row['fullname']."</option>";
				}
			}
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
        $html = $html."</select></td></tr>";
		
		$html = $html."<tr><td><label for='source'>Seat: </label></td>";
		$html = $html."<td><select id='seatid' name='seatid'>";
		$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
		$statement = $pdo->prepare("SELECT seat.seatid, seat.description, room.roomname FROM seat LEFT JOIN room ON seat.roomid = room.roomid WHERE room.roomid IN (SELECT roomtogroup.roomid FROM roomtogroup INNER JOIN usertogroup ON roomtogroup.groupid = usertogroup.groupid INNER JOIN user ON user.userid = usertogroup.userid WHERE user.username = ?) AND seat.type = 1 ORDER BY room.roomname, seat.description, seat.seatid");
		if ($statement->execute(array($username))) {
			while($row = $statement->fetch()) {
				if ($row['seatid'] == $seatid) {
				  $html = $html."<option value='".$row['seatid']."' selected>".$row['roomname']."-".$row['seatid']."-".$row['description']."</option>";
				} else {
				  $html = $html."<option value='".$row['seatid']."'>".$row['roomname']."-".$row['seatid']."-".$row['description']."</option>";
				}
				
			}
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
		$html = $html."</select></td></tr>";
		$html = $html."</table>";
		$html = $html."<input type='submit'";
		if($usertoseatid > 0) { 
			$html = $html."name='update'";
		} else {
			$html = $html."name='create'";
		} 
		$html = $html." value='Submit' />";
		$html = $html."</form>";
		
		return $html;
	}
    
	include ("../includes/html.php");
	include ("../includes/menu.php");
	
	echo head("Seat Reservation - Admin");
	echo body_top_admin("Seat Reservation - Admin",$username);
	echo display_admin_menu("usertoseat", $isadmin);
	echo body_title_top("Add User Seat Assignment");
	if($getid > 0){
		echo delete_button($getid, "addusertoseat.php");
	}
	echo body_title_bottom();
	echo display_form($getid,$getseatid,$username);
	echo body_bottom();
?>