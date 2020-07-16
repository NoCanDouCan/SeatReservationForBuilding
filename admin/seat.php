<?php
	include ("../config/session_admin.php");
	include ("../config/db.php");
	include ("../includes/menu.php");
	include ("../includes/html.php");

	$getroomid = 0;
	if (isset($_GET['roomid'])) {
		$getroomid = $_GET['roomid'];
	}

	echo head("Seat Reservation - Admin");
	echo body_top_admin("Seat Reservation - Admin",$username);
	echo display_admin_menu("seat", $isadmin);
	echo body_title_top("Admin - Seat");
	echo add_button("Add Seat", "addseat.php?roomid=$getroomid");
	echo body_title_bottom();
?> 

    <h2>Seat view</h2>
    <form action="seat.php" method="GET">
		<select id="roomid" name="roomid">

        <?php

			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("SELECT room.roomid, room.roomname FROM room INNER JOIN roomtogroup ON room.roomid = roomtogroup.roomid INNER JOIN usertogroup ON roomtogroup.groupid = usertogroup.groupid INNER JOIN user ON usertogroup.userid = user.userid WHERE user.username = ? ORDER BY room.roomname");
			if ($statement->execute(array($username))) {
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
		<input type="submit" value="Submit" />
	</form>


	<?php
		if ($getroomid > 0) {

		echo "<div class='container'><table class='table table-bordered'><tbody>";


			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("SELECT seat.seatid ,seat.orientation ,seat.row ,seat.col ,seat.type , seat.description, room.roomname FROM seat LEFT JOIN room ON room.roomid = seat.roomid WHERE seat.roomid = ? ORDER BY seat.row,seat.col");

			if ($statement->execute(array($getroomid))) {
			
				$gridrow = 0;
				while($row = $statement->fetch()) {
					if ($row['row'] > $gridrow) {
					   $gridrow = $row['row'];
					   echo "<tr>";
					}
					$seatid = $row['seatid'];
					$description = $row['description'];

					if ($row['type'] == 1) {
						if ($row['orientation'] == 1) {
													//table face up
							echo "<td width='1'><a href='addseat.php?&id=$seatid'><img src='/img/ug.png' title='$description'></a></td>";
												} else if ($row['orientation'] == 2) {
													//table face right
							echo "<td width='1'><a href='addseat.php?&id=$seatid'><img src='/img/rg.png' title='$description'></a></td>";
												} else if($row['orientation'] == 3) {
													//table face down
							echo "<td width='1'><a href='addseat.php?&id=$seatid'><img src='/img/dg.png' title='$description'></a></td>";
												} else if($row['orientation'] == 4) {
													//table face left
							echo "<td width='1'><a href='addseat.php?&id=$seatid'><img src='/img/lg.png' title='$description'></a></td>";
												}
					} else if ($row['type'] == 2) {
						if ($row['orientation'] == 1) {
							echo "<td width='1'><a href='addseat.php?&id=$seatid'><img src='/img/udoor.png' title='door'></a></td>";
												} else if ($row['orientation'] == 2) {
							echo "<td width='1'><a href='addseat.php?&id=$seatid'><img src='/img/rdoor.png' title='door'></a></td>";
												} else if($row['orientation'] == 3) {
							echo "<td width='1'><a href='addseat.php?&id=$seatid'><img src='/img/ddoor.png' title='door'></a></td>";
												} else if($row['orientation'] == 4) {
							echo "<td width='1'><a href='addseat.php?&id=$seatid'><img src='/img/ldoor.png' title='door'></a></td>";
												}

					} else if ($row['type'] == 3) {
						if ($row['orientation'] == 1) {
							echo "<td width='1'><a href='addseat.php?&id=$seatid'><img src='/img/hwindow.png' title='window'></a></td>";
												} else if ($row['orientation'] == 2) {
							echo "<td width='1'><a href='addseat.php?&id=$seatid'><img src='/img/vwindow.png' title='window'></a></td>";
												} else if($row['orientation'] == 3) {
							echo "<td width='1'><a href='addseat.php?&id=$seatid'><img src='/img/hwindow.png' title='window'></a></td>";
												} else if($row['orientation'] == 4) {
							echo "<td width='1'><a href='addseat.php?&id=$seatid'><img src='/img/vwindow.png' title='window'></a></td>";
												}


					} else if ($row['type'] == 4) {
						echo "<td width='1'><a href='addseat.php?&id=$seatid'><img src='/img/blank.png'></a></td>";
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


      <h2>Seat list</h2>
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
				$statement = $pdo->prepare("SELECT seat.*, room.roomname FROM seat LEFT JOIN room ON room.roomid = seat.roomid WHERE room.roomid = ? ORDER BY room.roomname, room.roomid, seat.row, seat.col");

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
						} else if ($permission == 1) {
							echo "<td>Only for assigned users</td>";
						} else {
							echo "<td>Everyone but limited to a single user per day</td>";
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
	  
<?php
	echo body_bottom();
?>	  
