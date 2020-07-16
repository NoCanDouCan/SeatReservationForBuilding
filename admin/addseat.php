<?php
    include ("../config/session_admin.php");
	include ("../config/db.php");
	
	$getroomid = 0;
	$getseatid = 0;
	if (isset($_GET['roomid'])) {
		$getroomid = $_GET['roomid'];
	}
	if(isset($_GET['id'])){
		$getseatid = $_GET['id'];
	}

    if (isset($_POST['delete'])){
        $pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
        $statement = $pdo->prepare("DELETE FROM seat WHERE seatid = ?");
        if ($statement->execute(array($_POST['id']))) {
            header("Location: seat.php");
            die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
    } else if(isset($_POST['update'])){
        $pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
        $statement = $pdo->prepare("UPDATE seat SET roomid = ?,orientation = ?,row = ?,col = ?,type = ?,description = ?,permission=? WHERE seatid = ?");
        if ($statement->execute(array($_POST['roomid'],$_POST['orientation'],$_POST['row'],$_POST['column'],$_POST['type'],$_POST['description'],$_POST['permission'],$_POST['id']))) {
            header("Location: seat.php?roomid=".$_POST['roomid']);
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
        
    } else if(isset($_POST['create'])){
        $pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
        $statement = $pdo->prepare("INSERT INTO seat (roomid,orientation,row,col,type,description,permission) VALUES (?,?,?,?,?,?,?)");
        if ($statement->execute(array($_POST['roomid'],$_POST['orientation'],$_POST['row'],$_POST['column'],$_POST['type'],$_POST['description'],$_POST['permission']))) {
            header("Location: seat.php?roomid=".$_POST['roomid']);
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
        
    } 
	
	function display_form($fseatid, $froomid, $username)
	{
		$roomname = "";
		$roomid = 0;
		$orientation = 0;
		$gridrow = 1;
		$col = 1;
		$type = 0;
		$description = "";
		
		$html = "<form action='addseat.php' method='POST'>";
		$html = $html."<table>";
		
		if ($fseatid > 0) {
			$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
			$statement = $pdo->prepare("SELECT seat.*, room.roomname FROM seat LEFT JOIN room ON seat.roomid = room.roomid where seatid = ? LIMIT 1");
			if ($statement->execute(array($fseatid))) {
				while($row = $statement->fetch()) {
					$roomname = $row['roomname'];
					$roomid = $row['roomid'];
					$orientation = $row['orientation'];
					$gridrow = $row['row'];
					$col = $row['col'];
					$type = $row['type'];
					$description = $row['description'];
					$permission = $row['permission'];
				}
			}
			
			$html = $html."<tr><td><label for='id' hidden>ID: </label></td><td><input id='id' type='text' name='id' size='50' value='$fseatid' hidden readonly /></td></tr>";
		} else {
			$roomid = $froomid;
		}
			
		$html = $html."<tr><td><label for='source'>Roomname: </label></td><td>";
		$html = $html."<select id='roomid' name='roomid'>";
		
		$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
		$statement = $pdo->prepare("SELECT room.roomid, room.roomname FROM room INNER JOIN roomtogroup ON room.roomid = roomtogroup.roomid INNER JOIN usertogroup ON roomtogroup.groupid = usertogroup.groupid INNER JOIN user ON usertogroup.userid = user.userid WHERE user.username = ? ORDER BY room.roomname");
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
		
		$html = $html."<tr><td><label for='source'>Type: </label></td>";
		$html = $html."<td><input id='type' type='radio' name='type' value='1'";
		if (empty ($type)) { 
			$html = $html."checked";
		} else if ($type == 1) { 
			$html = $html."checked";
		} 
		$html = $html."/>Seat<br>";
		
		$html = $html."<input id='type' type='radio' name='type' value='2' ";
		if ($type == 2) { 
			$html = $html."checked";
		} 
		$html = $html."/>Door<br>";
		
		$html = $html."<input id='type' type='radio' name='type' value='3' "; 
		if ($type == 3) { 
			$html = $html."checked";
		} 
		$html = $html."/>Window<br>";
		
		$html = $html."<input id='type' type='radio' name='type' value='4' ";
		if ($type == 4) { 
			$html = $html."checked";
		}
		$html = $html."/>Blank</td>";
		
		$html = $html."<td><label for='example1'> Select the type, seat is the default. Blank is for empty spaces, windows and doors for orientation </label></td>";
		$html = $html."</tr>";
		
		$html = $html."<tr><td><label for='source'>Orientation: </label></td>";
		$html = $html."<td><input id='orientation' type='radio' name='orientation' value='1' ";
		if (empty ($type)) { 
			$html = $html."checked";
		} else if ($orientation == 1) { 
			$html = $html."checked";
		} 
		$html = $html."/><img src='/img/ug.png'><img src='/img/udoor.png'><img src='/img/hwindow.png'><br>";
		$html = $html."<input id='orientation' type='radio' name='orientation' value='2' ";
		if ($orientation == 2) { 
			$html = $html."checked";
		}
		$html = $html."/><img src='/img/rg.png'><img src='/img/rdoor.png'><img src='/img/vwindow.png'><br>";
		$html = $html."<input id='orientation' type='radio' name='orientation' value='3' ";
		if ($orientation == 3) { 
			$html = $html."checked";
		} 
		$html = $html."/><img src='/img/dg.png'><img src='/img/ddoor.png'><img src='/img/hwindow.png'><br>";
		$html = $html."<input id='orientation' type='radio' name='orientation' value='4' ";
		if ($orientation == 4) { 
			$html = $html."checked";
		} 
		$html = $html."/><img src='/img/lg.png'><img src='/img/ldoor.png'><img src='/img/vwindow.png'>";
		$html = $html."</td>";
		$html = $html."<td><label for='example1'> Select the orientation of the seat, door or window </label></td></tr>";
		
		$html = $html."<tr><td><label for='source'>Row: </label></td>";
		$html = $html."<td><input id='row' type='text' name='row' size='50' value='$gridrow'/></td>";
		$html = $html."<td><label for='example1'> Example: 1 will place the seat in first row, numbers should increase by 1, missing numbers will be ignored </label></td>";		
		
		$html = $html."</tr><tr><td><label for='source'>Column: </label></td>";
		$html = $html."<td><input id='column' type='text' name='column' size='50' value='$col'/></td>";
		$html = $html."<td><label for='example1'> Example: 1 will place the seat in first column, numbers should increase by 1, missing numbers will be ignored </label></td>";
		
		$html = $html."</tr><tr><td><label for='source'>Description: </label></td>";           
		$html = $html."<td><input id='description' type='text' name='description' size='50' value='$description'/></td>";
		$html = $html."<td><label for='example1'> Seat description, useful for seat<->user assignment </label></td>";
		
		$html = $html."</tr><tr><td><label for='source'>Permission: </label></td><td>";           
		if ($permission == 0) {
			$html = $html."<input id='permission' type='radio' name='permission' value='0' checked/>Everyone<br>";
			$html = $html."<input id='permission' type='radio' name='permission' value='2' />Everyone but limited to a single user per day<br>";
			$html = $html."<input id='permission' type='radio' name='permission' value='1' />Only for assigned users<br>";
		} else if ($permission == 1){
			$html = $html."<input id='permission' type='radio' name='permission' value='0' />Everyone<br>";
			$html = $html."<input id='permission' type='radio' name='permission' value='2' />Everyone but limited to a single user per day<br>";
			$html = $html."<input id='permission' type='radio' name='permission' value='1' checked/>Only for assigned users<br>";	
		} else {
			$html = $html."<input id='permission' type='radio' name='permission' value='0' checked/>Everyone<br>";
			$html = $html."<input id='permission' type='radio' name='permission' value='2' checked/>Everyone but limited to a single user per day<br>";
			$html = $html."<input id='permission' type='radio' name='permission' value='1' />Only for assigned users<br>";	
		}
		$html = $html."</td><td><label for='example1'> </label></td>";
		$html = $html."</tr></table>";
		$html = $html."<input type='submit' ";
		if($fseatid > 0) { 
			$html = $html."name='update'";
		} else {
			$html = $html."name='create'";
		} 
		$html = $html."value='Submit' />";
		$html = $html."</form>";
		$html = $html."";
               
		return $html;
		
	}
    
	include ("../includes/menu.php");
	include ("../includes/html.php");

	echo head("Seat Reservation - Admin");
	echo body_top_admin("Seat Reservation - Admin",$username);
	echo display_admin_menu("seat", $isadmin);
	echo body_title_top("Add Seat");
	 if($getseatid > 0){
		echo delete_button($getseatid , "addseat.php");
	 }
	echo body_title_bottom();
	echo display_form($getseatid, $getroomid, $username);
	echo body_bottom();
?> 

