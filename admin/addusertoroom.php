<?php
    include ("../config/session_admin.php");
	include ("../config/db.php");

	//get variables
	$getid = 0;
	if(isset($_GET['id'])){
		$getid = $_GET['id'];
	}
	

	
    if (isset($_POST['delete'])){
        $pdo = new PDO($dbserver, $dbuser, $dbpw);
        $statement = $pdo->prepare("DELETE FROM usertoroom WHERE usertoroomid = ?");
        if ($statement->execute(array($_POST['id']))) {
            header("Location: usertoroom.php");
            die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
    } else if(isset($_POST['update'])){
        //SQL
        
        $pdo = new PDO($dbserver, $dbuser, $dbpw);
        $statement = $pdo->prepare("UPDATE usertoroom SET roomid = ?,userid = ? WHERE usertoroom.usertoroomid = ?");
        if ($statement->execute(array($_POST['roomid'],$_POST['userid'],$_POST['id']))) {
            header("Location: usertoroom.php");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
        
    } else if(isset($_POST['create'])){
        //SQL
        
        $pdo = new PDO($dbserver, $dbuser, $dbpw);
        $statement = $pdo->prepare("INSERT INTO usertoroom (userid,roomid) VALUES (?,?)");
        if ($statement->execute(array($_POST['userid'],$_POST['roomid']))) {
            header("Location: usertoroom.php");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
        
    } 
	
	function delete_button2($id)
	{
		$html = "<form action='adduser.php' method='POST'>";
		$html = $html."<input id='id' type='hidden' name='id' value='$id'/>";
		$html = $html."<input id='delete' type='hidden' name='delete' value='1'/>";
		$html = $html."<input class='btn btn-sm btn-outline-secondary' type='submit' name='delete' value='Delete' />";
		$html = $html."</form>";
		return $html;
	}
	
	function display_form($username,$usertoroomid,$dbserver, $dbuser, $dbpw)
	{
		
		
		
		$html = "<form action='addusertoroom.php' method='POST'>";
		$html = $html."<table>";

		$userid = 0;
		$roomid = 0;
		
		if($usertoroomid > 0){
			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("SELECT usertoroom.roomid, usertoroom.userid FROM usertoroom WHERE usertoroom.usertoroomid = ? LIMIT 1");
			if ($statement->execute(array($usertoroomid))) {
				while($row = $statement->fetch()) {
					$userid = $row['userid'];
					$roomid = $row['roomid'];
				}
			}
			$html = $html."<tr><td><label for='id' hidden>ID: </label></td><td><input id='id' type='text' name='id' size='50' value='$usertoroomid' readonly hidden /></td></tr>";
		}
	
		$html = $html."<tr><td><label for='source'>User: </label></td>";
		$html = $html."<td><select id='userid' name='userid'>";
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("select user.userid, user.fullname, user.username from user where userid IN ( SELECT usertogroup.userid FROM usertogroup INNER JOIN usertogroup as utg2 ON usertogroup.groupid = utg2.groupid INNER JOIN user as user2 ON utg2.userid = user2.userid WHERE user2.username = ?) ORDER BY user.fullname, user.username");
		if ($statement->execute(array($username))) {
			while($row = $statement->fetch()) {
				$useriddb = $row['userid'];
				$fullname = $row['fullname'];
				$dbusername = $row['username'];
				if ($row['userid'] == $userid) {
					if ($fullname == "") {
						$html = $html."<option value='".$useriddb."' selected>".$dbusername."</option>";
					} else {
						$html = $html."<option value='".$useriddb."' selected>".$fullname."</option>";
					}
				} else {
				   if ($fullname == "") {
						$html = $html."<option value='".$useriddb."' >".$dbusername."</option>";
					} else {
						$html = $html."<option value='".$useriddb."' >".$fullname."</option>";
					}
				}
			}
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
         $html = $html."</select></td></tr>";
		
		 $html = $html."<tr><td><label for='source'>Room: </label></td>";
		 $html = $html."<td><select id='roomid' name='roomid'>";
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT room.roomid,room.roomname FROM room WHERE room.roomid IN (SELECT roomtogroup.roomid FROM roomtogroup INNER JOIN usertogroup ON usertogroup.groupid = roomtogroup.groupid INNER JOIN user ON usertogroup.userid = user.userid WHERE user.username = ?) ORDER BY room.roomname");
		if ($statement->execute(array($username))) {
			while($row = $statement->fetch()) {
				if ($row['roomid'] == $roomid) {
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
		$html = $html."</select></td></tr>";
		$html = $html."</table>";
		$html = $html."<input type='submit'";
		if($usertoroomid > 0) { 
			$html = $html."name='update'";
		} else {
			$html = $html."name='create'";
		} 
		 $html = $html." value='Submit' />";
		$html = $html."</form>";
		
		return $html;
	}
    
	include ("../includes/menu.php");
	include ("../includes/html.php");
	
	echo head("Seat Reservation - Admin");
	echo body_top_admin("Seat Reservation - Admin",$username);
	echo display_admin_menu("usertoroom", $isadmin);
	echo body_title_top("Add User Room Assignment");
	if($getid > 0){
		echo delete_button($getid, "addusertoroom.php");
	}
	echo body_title_bottom();
	echo display_form($username,$getid,$dbserver, $dbuser, $dbpw);
	echo body_bottom();
?>