<?php
	include ("../config/session_admin.php");
	include ("../config/db.php");
	include ("../includes/menu.php");
	include ("../includes/html.php");
	
	echo head("Seat Reservation - Admin");
	echo body_top_admin("Seat Reservation - Admin",$username);
	echo display_admin_menu("zombies", $isadmin);
	echo body_title_top("Admin - Zombies");
	echo body_title_bottom();
?> 

<h2>Floor without group</h2>
<div class="table-responsive">
	<table class="table table-striped table-sm">
	<thead>
		<tr>
			<th>Floor ID</th>
			<th>Floor Name</th>
			<th>Department</th>
			<th>Edit</th>
		</tr>
	</thead>
	<tbody>
  
	<?php
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT floor.floorid, floor.floorname, floor.department FROM floor LEFT JOIN floortogroup ON floor.floorid = floortogroup.floorid WHERE floortogroup.floorid IS NULL ");
		if ($statement->execute(array($username))) {
			while($row = $statement->fetch()) {
				$id = $row['floorid'];
				echo "<tr>";
				echo "<td>".$row['floorid']."</td>";
				echo "<td>".$row['floorname']."</td>";
				echo "<td>".$row['department']."</td>";
				echo "<td>";
				echo "<a class='btn btn-sm btn-outline-secondary' href='addfloor.php?&id=$id' role='button'>Edit</a>";
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

<h2>Room without floor</h2>
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
		$statement = $pdo->prepare("SELECT room.roomid, room.roomname, room.floorid, floor.floorname FROM room LEFT JOIN floor ON room.floorid = floor.floorid WHERE floor.floorid IS NULL ORDER BY room.roomname");
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

<h2>Room without group</h2>
<div class="table-responsive">
	<table class="table table-striped table-sm">
	<thead>
		<tr>
			<th>Room ID</th>
			<th>Room Name</th>
			<th>Floor ID</th>
			<th>Permission</th>
			<th>Edit</th>
		</tr>
	</thead>
	<tbody>
		  
	<?php
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT room.roomid, room.roomname, room.floorid FROM room LEFT JOIN roomtogroup ON room.roomid = roomtogroup.roomid WHERE roomtogroup.roomid IS NULL ORDER BY room.roomname");
		if ($statement->execute(array($username))) {
		
			while($row = $statement->fetch()) {
				$id = $row['roomid'];
				$floorid = $row['floorid'];
				$permission = $row['permission'];
				echo "<tr>";
				echo "<td>".$row['roomid']."</td>";
				echo "<td>".$row['roomname']."</td>";
				echo "<td>".$row['floorid']."</td>";
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

<h2>Seat without room</h2>
      <div class="table-responsive">
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th>Seat ID</th>
              <th>Room ID</th>
              <th>Room Name</th>
              <th>Type</th>
              <th>Orientation</th>
              <th>Row</th>
              <th>Column</th>
              <th>Info</th>
			  <th>Permission</th>
			<th>Edit</th>
            </tr>
          </thead>
          <tbody>
		  
		  <?php
				$pdo = new PDO($dbserver, $dbuser, $dbpw);
				$statement = $pdo->prepare("SELECT seat.*, room.roomname FROM seat LEFT JOIN room ON room.roomid = seat.roomid WHERE room.roomid IS NULL ORDER BY room.roomname, room.roomid, seat.row, seat.col");

				if ($statement->execute(array($getroomid))) {
				
					while($row = $statement->fetch()) {
						$id = $row['seatid'];
						$permission = $row['permission'];
						echo "<tr>";
						echo "<td>".$row['seatid']."</td>";
						echo "<td>".$row['roomid']."</td>";
						echo "<td>".$row['roomname']."</td>";


						if ($row['type'] == 1) {
						   echo "<td>Seat</td>";
						} else if ($row['type'] == 2) {
						   echo "<td>Door</td>";
						} else if ($row['type'] == 3) {
						   echo "<td>Window</td>";
						} else if ($row['type'] == 4) {
						   echo "<td>Blank</td>";
						}


						if ($row['orientation'] == 1) {
						   echo "<td>Up</td>";
						} else if ($row['orientation'] == 2) {
						   echo "<td>Right</td>";
						} else if ($row['orientation'] == 3) {
						   echo "<td>Down</td>";
						} else if ($row['orientation'] == 4) {
						   echo "<td>Left</td>";
						}
						echo "<td>".$row['row']."</td>";
						echo "<td>".$row['col']."</td>";						
						echo "<td>".$row['description']."</td>";
						if ($permission == 0) {
							echo "<td>Everyone</td>";
						} else {
							echo "<td>Only for assigned users</td>";
						}
						echo "<td>";
						echo "<a class='btn btn-sm btn-outline-secondary' href='addseat.php?&id=$id' role='button'>Edit</a>";
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
	  
	  <h2>User without group</h2>
	<div class="table-responsive">
		<table class="table table-striped table-sm">
			<thead>
				<tr>
				<th>UserID</th>
				<th>Username</th>
				<th>Full Name</th>
				<th>IsAdmin</th>
				<th>Edit</th>
				</tr>
			</thead>
			<tbody>


			<?php


				$pdo = new PDO($dbserver, $dbuser, $dbpw);
				$statement = $pdo->prepare("select user.* from user left join usertogroup on user.userid = usertogroup.userid WHERE usertogroup.userid IS NULL ORDER BY user.username");
				if ($statement->execute(array($username))) {
				
					$gridrow = 0;
					$dbisadmin = 0;
					while($row = $statement->fetch()) {
						$id = $row['userid'];
						$dbisadmin = $row['isadmin'];
						echo "<tr>";
						echo "<td>".$row['userid']."</td>";
						echo "<td>".$row['username']."</td>";
						echo "<td>".$row['fullname']."</td>";
						echo "<td>".$row['isadmin']."</td>";
						echo "<td><a class='btn btn-sm btn-outline-secondary' href='adduser.php?&id=$id' role='button'>Edit</a></td>";
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
	
	<h2>User with multiple groups</h2>
	<div class="table-responsive">
		<table class="table table-striped table-sm">
			<thead>
				<tr>
				<th>UserID</th>
				<th>Username</th>
				<th>Full Name</th>
				<th>IsAdmin</th>
				<th>Info</th>
				<th>Edit</th>
				</tr>
			</thead>
			<tbody>


			<?php
				$pdo = new PDO($dbserver, $dbuser, $dbpw);
				$statement = $pdo->prepare("SELECT count(usertogroup.userid),usertogroup.userid, user.username, user.fullname, user.isadmin, GROUP_CONCAT(groups.info) as info from usertogroup LEFT JOIN user ON usertogroup.userid = user.userid LEFT JOIN groups on usertogroup.groupid = groups.groupid GROUP BY usertogroup.userid HAVING count(usertogroup.userid) > 1 ");
				if ($statement->execute()) {
				
					$gridrow = 0;
					$dbisadmin = 0;
					while($row = $statement->fetch()) {
						$id = $row['userid'];
						$dbisadmin = $row['isadmin'];
						echo "<tr>";
						echo "<td>".$row['userid']."</td>";
						echo "<td>".$row['username']."</td>";
						echo "<td>".$row['fullname']."</td>";
						echo "<td>".$row['isadmin']."</td>";
						echo "<td>".$row['info']."</td>";
						echo "<td><a class='btn btn-sm btn-outline-secondary' href='adduser.php?&id=$id' role='button'>Edit</a></td>";
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
	
	<h2>User duplicate</h2>
	<div class="table-responsive">
		<table class="table table-striped table-sm">
			<thead>
				<tr>
				<th>Username</th>
				<th>Full Name</th>
				</tr>
			</thead>
			<tbody>


			<?php
				$pdo = new PDO($dbserver, $dbuser, $dbpw);
				$statement = $pdo->prepare("SELECT count(user.username), user.username, user.fullname from user GROUP BY user.username,user.fullname HAVING count(user.username) > 1");
				if ($statement->execute()) {
				
					$gridrow = 0;
					$dbisadmin = 0;
					while($row = $statement->fetch()) {
						echo "<tr>";
						echo "<td>".$row['username']."</td>";
						echo "<td>".$row['fullname']."</td>";
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
	
	<h2>UserToRoom</h2>
	<div class="table-responsive">
		<table class="table table-striped table-sm">
			<thead>
				<tr>
				<th>UserID</th>
				<th>RoomID</th>
				<th>Username</th>
				<th>Full Name</th>
				<th>Roomname</th>
				<th>Edit</th>
				</tr>
			</thead>
			<tbody>


			<?php
				$pdo = new PDO($dbserver, $dbuser, $dbpw);
				$statement = $pdo->prepare("SELECT usertoroom.usertoroomid, usertoroom.roomid, usertoroom.userid, room.roomname, user.username, user.fullname from usertoroom LEFT JOIN user on usertoroom.userid = user.userid LEFT JOIN room ON usertoroom.roomid = room.roomid WHERE user.userid IS NULL OR room.roomid IS NULL");
				if ($statement->execute()) {
				
					$gridrow = 0;
					$dbisadmin = 0;
					while($row = $statement->fetch()) {
						$id = $row['usertoroomid'];
						$dbisadmin = $row['isadmin'];
						echo "<tr>";
						echo "<td>".$row['userid']."</td>";
						echo "<td>".$row['roomid']."</td>";
						echo "<td>".$row['username']."</td>";
						echo "<td>".$row['fullname']."</td>";
						echo "<td>".$row['roomname']."</td>";
						echo "<td><a class='btn btn-sm btn-outline-secondary' href='addusertoroom.php?&id=$id' role='button'>Edit</a></td>";
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
	
	<h2>UserToSeat</h2>
	<div class="table-responsive">
		<table class="table table-striped table-sm">
			<thead>
				<tr>
				<th>UserID</th>
				<th>SeatID</th>
				<th>Username</th>
				<th>Seat Info</th>
				<th>Edit</th>
				</tr>
			</thead>
			<tbody>


			<?php
				$pdo = new PDO($dbserver, $dbuser, $dbpw);
				$statement = $pdo->prepare("SELECT usertoseat.usertoseatid, usertoseat.userid, usertoseat.seatid, user.username, seat.description FROM usertoseat LEFT JOIN user ON usertoseat.userid = user.userid LEFT JOIN seat ON usertoseat.seatid = seat.seatid WHERE seat.seatid IS NULL OR user.userid IS NULL");
				if ($statement->execute()) {
				
					$gridrow = 0;
					$dbisadmin = 0;
					while($row = $statement->fetch()) {
						$id = $row['usertoseatid'];
						echo "<tr>";
						echo "<td>".$row['userid']."</td>";
						echo "<td>".$row['seatid']."</td>";
						echo "<td>".$row['username']."</td>";
						echo "<td>".$row['description']."</td>";
						echo "<td><a class='btn btn-sm btn-outline-secondary' href='adduser.php?&id=$id' role='button'>Edit</a></td>";
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