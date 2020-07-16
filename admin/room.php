<?php
	include ("../config/session_admin.php");
	include ("../config/db.php");
	include ("../includes/menu.php");
	include ("../includes/html.php");

	echo head("Seat Reservation - Admin");
	echo body_top_admin("Seat Reservation - Admin",$username);
	echo display_admin_menu("room", $isadmin);
	echo body_title_top("Admin - Room");
	echo add_button("Add Room", "addroom.php");
	echo body_title_bottom();
?>

<h2>Room list</h2>
<div class="table-responsive">
	<table class="table table-striped table-sm">
	<thead>
		<tr>
			<th>Room ID</th>
			<th>Room Name</th>
			<th>Floor ID</th>
			<th>Floor Name</th>
			<th>Permission</th>
			<th>Edit</th>
		</tr>
	</thead>
	<tbody>
		  
	<?php
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT room.roomid, room.roomname, room.floorid, floor.floorname, room.permission FROM room LEFT JOIN floor ON room.floorid = floor.floorid INNER JOIN roomtogroup ON room.roomid = roomtogroup.roomid INNER JOIN usertogroup ON roomtogroup.groupid = usertogroup.groupid INNER JOIN user ON usertogroup.userid = user.userid WHERE user.username = ? ORDER BY room.roomname");
		if ($statement->execute(array($username))) {
		
			while($row = $statement->fetch()) {
				$id = $row['roomid'];
				$floorid = $row['floorid'];
				$permission = $row['permission'];
				echo "<tr>";
				echo "<td>".$row['roomid']."</td>";
				echo "<td>".$row['roomname']."</td>";
				echo "<td>".$row['floorid']."</td>";
				echo "<td>".$row['floorname']."</td>";
				if ($permission == 0) {
					echo "<td>Everyone</td>";
				} else {
					echo "<td>Only for assigned users</td>";
				}
				
				echo "<td>";
				echo "<a class='btn btn-sm btn-outline-secondary' href='addroom.php?id=$id&floorid=$floorid' role='button'>Edit</a>";
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
	  
<?php
	echo body_bottom();
?>	
