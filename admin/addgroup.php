<?php
	include ("../config/session_admin.php");
	include ("../config/db.php");

	//get variables
	$getid = 0;
	if(isset($_GET['id'])){
		$getid = $_GET['id'];
	}
	$getfloorid = 0;
	if(isset($_GET['floorid'])){
		$getfloorid = $_GET['floorid'];
	}
	$getroomid = 0;
	if(isset($_GET['roomid'])){
		$getroomid = $_GET['roomid'];
	}
	$getuserid = 0;
	if(isset($_GET['userid'])){
		$getuserid = $_GET['userid'];
	}
	$getaddfloor = 0;
	if(isset($_GET['addfloor'])){
		$getaddfloor = $_GET['addfloor'];
	}
	$getdelfloor = 0;
	if(isset($_GET['delfloor'])){
		$getdelfloor = $_GET['delfloor'];
	}
	$getaddroom = 0;
	if(isset($_GET['addroom'])){
		$getaddroom = $_GET['addroom'];
	}
	$getdelroom = 0;
	if(isset($_GET['delroom'])){
		$getdelroom = $_GET['delroom'];
	}
	$getadduser = 0;
	if(isset($_GET['adduser'])){
		$getadduser = $_GET['adduser'];
	}
	$getdeluser = 0;
	if(isset($_GET['deluser'])){
		$getdeluser = $_GET['deluser'];
	}
	
    if (isset($_POST['delete'])){
        $pdo = new PDO($dbserver, $dbuser, $dbpw);
        $statement = $pdo->prepare("DELETE FROM groups WHERE groupid = ?");
        if ($statement->execute(array($_POST['id']))) {
            header("Location: group.php");
            die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
    } else if(isset($_POST['update'])){
        //SQL
        
        $pdo = new PDO($dbserver, $dbuser, $dbpw);
        $statement = $pdo->prepare("UPDATE groups SET info = ? WHERE groupid = ?");
        if ($statement->execute(array($_POST['info'],$_POST['id']))) {
            header("Location: group.php");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
        
    } else if(isset($_POST['create'])){
        //SQL
        
        $pdo = new PDO($dbserver, $dbuser, $dbpw);
        $statement = $pdo->prepare("INSERT INTO groups (info) VALUES (?)");
        if ($statement->execute(array($_POST['info']))) {
            header("Location: group.php");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
        
    } else if ($getaddfloor > 0) {
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
        $statement = $pdo->prepare("INSERT INTO floortogroup (floorid,groupid) VALUES (?,?)");
        if ($statement->execute(array($getfloorid,$getid))) {
            header("Location: addgroup.php?id=$getid");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
    } else if ($getdelfloor > 0) {
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
        $statement = $pdo->prepare("DELETE FROM floortogroup WHERE floorid = ? AND groupid = ?");
        if ($statement->execute(array($getfloorid,$getid))) {
            header("Location: addgroup.php?id=$getid");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
	} else if ($getaddroom > 0) {
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
        $statement = $pdo->prepare("INSERT INTO roomtogroup (roomid,groupid) VALUES (?,?)");
        if ($statement->execute(array($getroomid,$getid))) {
            header("Location: addgroup.php?id=$getid");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
    } else if ($getdelroom > 0) {
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
        $statement = $pdo->prepare("DELETE FROM roomtogroup WHERE roomid = ? AND groupid = ?");
        if ($statement->execute(array($getroomid,$getid))) {
            header("Location: addgroup.php?id=$getid");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
	} else if ($getadduser > 0) {
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
        $statement = $pdo->prepare("INSERT INTO usertogroup (userid,groupid) VALUES (?,?)");
        if ($statement->execute(array($getuserid,$getid))) {
            header("Location: addgroup.php?id=$getid");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
    } else if ($getdeluser > 0) {
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
        $statement = $pdo->prepare("DELETE FROM usertogroup WHERE userid = ? AND groupid = ?");
        if ($statement->execute(array($getuserid,$getid))) {
            header("Location: addgroup.php?id=$getid");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
	}
	
	function display_form($fgroupid,$dbserver, $dbuser, $dbpw)
	{
		$html = "<form action='addgroup.php' method='POST'>";
		$html = $html."<table>";

		if($fgroupid > 0){
			$id = $fgroupid;

			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("SELECT * FROM groups where groupid = ? LIMIT 1");
			if ($statement->execute(array($id))) {
				while($row = $statement->fetch()) {
					$info = $row['info'];
				}
			}
			$html = $html."<tr><td><label for='id'>ID: </label></td><td><input id='id' type='text' name='id' size='50' value='$id' readonly /></td></tr>";
		}

		$html = $html."<tr>";
		$html = $html."<td>";
		$html = $html."<label for='source'>Group Name: </label>";
		$html = $html."</td>";
		$html = $html."<td>";
		$html = $html."<input id='info' type='text' name='info' size='50' value='$info' />";
		$html = $html."</td>";
		$html = $html."<td>";
		$html = $html."<label for='example1'></label>";
		$html = $html."</td>";
		$html = $html."</tr>";
		$html = $html."</table>";
						
		if($fgroupid > 0) { 
			$html = $html."<input type='submit' name='update' value='Submit' /> </form>";
		} else {
			$html = $html."<input type='submit' name='create' value='Submit' /> </form>";
		} 
		
		return $html;
	}
	
	function display_floortogroup($fgroupid,$dbserver, $dbuser, $dbpw)
	{
		
		$html = "<br><h2>Floor to Group</h2>";
		$html = $html."<div class='table-responsive'>";
		$html = $html."<table class='table table-striped table-sm'>";
		$html = $html."<thead>";
		$html = $html."<tr>";
		$html = $html."<th>Assigned Floor</th>";
		$html = $html."<th>Add Floor</th>";
		$html = $html."</tr>";
		$html = $html."</thead>";
		$html = $html."<tbody>";
		$html = $html."<tr>";
		$html = $html."<td>";
		$html = $html."<table class='table table-striped table-sm'>";
		$html = $html."<tbody>";
			
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT floor.floorid, floor.floorname FROM floor INNER JOIN floortogroup ON floor.floorid = floortogroup.floorid AND floortogroup.groupid = ? ORDER BY floor.floorname");
		if ($statement->execute(array($fgroupid))) {
			while($row = $statement->fetch()) {
				$floorid = $row['floorid'];
				$html = $html."<tr>";
				$html = $html."<td><a class='btn btn-sm btn-outline-secondary' href='addgroup.php?id=$fgroupid&floorid=$floorid&delfloor=1' role='button'>Remove</a></td>";
				$html = $html."<td>".$row['floorname']."</td>";
				$html = $html."</tr>";
			}
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}

			
		$html = $html."</tbody>";
		$html = $html."</table>";
		$html = $html."</td>";
	
		$html = $html."<td>";
		$html = $html."<table class='table table-striped table-sm'>";
		$html = $html."<tbody>";
		
		
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT floor.floorid, floor.floorname, floortogroup.groupid FROM floor LEFT JOIN floortogroup ON floor.floorid = floortogroup.floorid AND floortogroup.groupid = ? ORDER BY floor.floorname");
		if ($statement->execute(array($fgroupid))) {
			while($row = $statement->fetch()) {
				$groupid = $row['groupid'];
				$floorid = $row['floorid'];
				if ($groupid == 0)
				{
					$html = $html."<tr>";
					$html = $html."<td><a class='btn btn-sm btn-outline-secondary' href='addgroup.php?id=$fgroupid&floorid=$floorid&addfloor=1' role='button'>Add</a></td>";
					$html = $html."<td>".$row['floorname']."</td>";
					$html = $html."</tr>";
				}
			}
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
		
		$html = $html."</tbody>";
		$html = $html."</table>";
		$html = $html."</td>";

		$html = $html."</tr>";
		$html = $html."</tbody>";
		$html = $html."</table>";
		$html = $html."</div>";
		
		
		return $html;
	}
	
	function display_roomtogroup($fgroupid,$dbserver, $dbuser, $dbpw)
	{
		
		$html = "<br><h2>Room to Group</h2>";
		$html = $html."<div class='table-responsive'>";
		$html = $html."<table class='table table-striped table-sm'>";
		$html = $html."<thead>";
		$html = $html."<tr>";
		$html = $html."<th>Assigned Room</th>";
		$html = $html."<th>Add Room</th>";
		$html = $html."</tr>";
		$html = $html."</thead>";
		$html = $html."<tbody>";
		$html = $html."<tr>";
		$html = $html."<td>";
		$html = $html."<table class='table table-striped table-sm'>";
		$html = $html."<tbody>";
			
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT room.roomid, room.roomname FROM room INNER JOIN roomtogroup ON room.roomid = roomtogroup.roomid AND roomtogroup.groupid = ? ORDER BY room.roomname");
		if ($statement->execute(array($fgroupid))) {
			while($row = $statement->fetch()) {
				$roomid = $row['roomid'];
				$html = $html."<tr>";
				$html = $html."<td><a class='btn btn-sm btn-outline-secondary' href='addgroup.php?id=$fgroupid&roomid=$roomid&delroom=1' role='button'>Remove</a></td>";
				$html = $html."<td>".$row['roomname']."</td>";
				$html = $html."</tr>";
			}
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}

			
		$html = $html."</tbody>";
		$html = $html."</table>";
		$html = $html."</td>";
	
		$html = $html."<td>";
		$html = $html."<table class='table table-striped table-sm'>";
		$html = $html."<tbody>";
		
		
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT room.roomid, room.roomname, roomtogroup.groupid, floortogroup.groupid as groupid2 FROM room LEFT JOIN roomtogroup ON room.roomid = roomtogroup.roomid AND roomtogroup.groupid = ? LEFT JOIN floortogroup ON room.floorid = floortogroup.floorid AND floortogroup.groupid = ? ORDER BY room.roomname");
		if ($statement->execute(array($fgroupid,$fgroupid))) {
			while($row = $statement->fetch()) {
				$groupid = $row['groupid'];
				$groupid2 = $row['groupid2'];
				$roomid = $row['roomid'];
				if ($groupid == 0 && $groupid2 != 0)
				{
					$html = $html."<tr>";
					$html = $html."<td><a class='btn btn-sm btn-outline-secondary' href='addgroup.php?id=$fgroupid&roomid=$roomid&addroom=1' role='button'>Add</a></td>";
					$html = $html."<td>".$row['roomname']."</td>";
					$html = $html."</tr>";
				}
			}
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
		
		$html = $html."</tbody>";
		$html = $html."</table>";
		$html = $html."</td>";

		$html = $html."</tr>";
		$html = $html."</tbody>";
		$html = $html."</table>";
		$html = $html."</div>";
		
		
		return $html;
	}
	
	function display_usertogroup($fgroupid,$dbserver, $dbuser, $dbpw)
	{
		
		$html = "<br><h2>User to Group</h2>";
		$html = $html."<div class='table-responsive'>";
		$html = $html."<table class='table table-striped table-sm'>";
		$html = $html."<thead>";
		$html = $html."<tr>";
		$html = $html."<th>Assigned User</th>";
		$html = $html."<th>Add User</th>";
		$html = $html."</tr>";
		$html = $html."</thead>";
		$html = $html."<tbody>";
		$html = $html."<tr>";
		$html = $html."<td>";
		$html = $html."<table class='table table-striped table-sm'>";
		$html = $html."<tbody>";
			
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT user.userid, user.fullname, user.username, user.isadmin FROM user INNER JOIN usertogroup ON user.userid = usertogroup.userid AND usertogroup.groupid = ? ORDER BY user.fullname");
		if ($statement->execute(array($fgroupid))) {
			while($row = $statement->fetch()) {
				$userid = $row['userid'];
				$userisadmin = $row['isadmin'];
				
				$html = $html."<tr>";
				$html = $html."<td><a class='btn btn-sm btn-outline-secondary' href='addgroup.php?id=$fgroupid&userid=$userid&deluser=1' role='button'>Remove</a></td>";
					if ($row['fullname'] == "") {
						if ($userisadmin > 0) {
							$html = $html."<td><b>".$row['username']."</b></td>";	
						} else {
							$html = $html."<td>".$row['username']."</td>";
						}
					} else {
						if ($userisadmin > 0) {
							$html = $html."<td><b>".$row['fullname']."</b></td>";	
						} else {
							$html = $html."<td>".$row['fullname']."</td>";
						}
					}
				$html = $html."</tr>";
			}
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}

			
		$html = $html."</tbody>";
		$html = $html."</table>";
		$html = $html."</td>";
	
		$html = $html."<td>";
		$html = $html."<table class='table table-striped table-sm'>";
		$html = $html."<tbody>";
		
		
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT user.userid, user.fullname, user.username, usertogroup.groupid, user.isadmin FROM user LEFT JOIN usertogroup ON user.userid = usertogroup.userid AND usertogroup.groupid = ? ORDER BY user.fullname");
		if ($statement->execute(array($fgroupid))) {
			while($row = $statement->fetch()) {
				$groupid = $row['groupid'];
				$userid = $row['userid'];
				$userisadmin = $row['isadmin'];
				if ($groupid == 0)
				{
					$html = $html."<tr>";
					$html = $html."<td><a class='btn btn-sm btn-outline-secondary' href='addgroup.php?id=$fgroupid&userid=$userid&adduser=1' role='button'>Add</a></td>";
					if ($row['fullname'] == "") {
						if ($userisadmin > 0) {
							$html = $html."<td><b>".$row['username']."</b></td>";	
						} else {
							$html = $html."<td>".$row['username']."</td>";
						}
					} else {
						if ($userisadmin > 0) {
							$html = $html."<td><b>".$row['fullname']."</b></td>";	
						} else {
							$html = $html."<td>".$row['fullname']."</td>";
						}
					}
					$html = $html."</tr>";
				}
			}
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
		
		$html = $html."</tbody>";
		$html = $html."</table>";
		$html = $html."</td>";

		$html = $html."</tr>";
		$html = $html."</tbody>";
		$html = $html."</table>";
		$html = $html."</div>";
		
		
		return $html;
	}
	
	include ("../includes/menu.php");
	include ("../includes/html.php");

	echo head("Seat Reservation - Admin");
	echo body_top_admin("Seat Reservation - Admin",$username);
	echo display_admin_menu("group", $isadmin);
	echo body_title_top("Add Group");
	if($getid > 0){
		echo delete_button($getid, "addgroup.php");
	}
	echo body_title_bottom();
	echo display_form($getid,$dbserver, $dbuser, $dbpw);
	if($getid > 0){
		echo display_floortogroup($getid,$dbserver, $dbuser, $dbpw);
	}
	if($getid > 0){
		echo display_roomtogroup($getid,$dbserver, $dbuser, $dbpw);
	}
	if($getid > 0){
		echo display_usertogroup($getid,$dbserver, $dbuser, $dbpw);
	}
	
	echo body_bottom();
?>
