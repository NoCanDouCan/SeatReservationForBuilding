<?php
	include ("../config/session_admin.php");
	include ("../config/db.php");
	include ("../includes/menu.php");
	include ("../includes/html.php");
	
	echo head("Seat Reservation - Admin");
	echo body_top_admin("Seat Reservation - Admin",$username);
	echo display_admin_menu("block", $isadmin);
	echo body_title_top("Admin - Seat Block");
	echo add_button("Add Block", "addblock.php");
	echo body_title_bottom();
?> 

	<h2>Seat view</h2>
    <form action="block.php" method="GET">
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
if (isset($_GET['roomid'])) {
	$getroomid = $_GET['roomid'];

	if (isset($_GET['seatid'])) {
		$getseatid = $_GET['seatid'];
		if (isset($_GET['block'])) {
			$pdo = new PDO($dbserver, $dbuser, $dbpw);
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
			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("DELETE FROM blocks WHERE blockedseatid = ? AND seatid = ?");
			if ($statement->execute(array($_GET['blockedseatid'],$_GET['seatid']))) {
					echo "seat block removed";
			} else {
					echo "SQL Error <br />";
					echo $statement->queryString."<br />";
					echo $statement->errorInfo()[2];
			}
		}
		echo "<div class='container'>Next select seat to block/unblock";
		echo "<table class='table table-bordered'><tbody>";
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT seat.seatid, seat.row, seat.orientation, seat.type, (select blocks.seatid from blocks where blocks.blockedseatid = seat.seatid and blocks.seatid = ? LIMIT 1) as red from seat WHERE seat.roomid = ? ORDER BY seat.row, seat.col");

		if ($statement->execute(array($getseatid,$getroomid))) {
		
			$gridrow = 0;
			while($row = $statement->fetch()) {
				if ($row['row'] > $gridrow) {
					$gridrow = $row['row'];
					echo "<tr>";
				}
				$seatid = $row['seatid'];
				$red = $row['red'];

				if ($row['type'] == 1) {

					if ($row['orientation'] == 1) {
						//table face up
						if ($getseatid == $seatid) {
							echo "<td width='1'><img src='/img/uo.png'></td>";
						} else if ($red > 0) {
							echo "<td width='1'><a href='block.php?roomid=$getroomid&seatid=$getseatid&blockedseatid=$seatid&unblock=1'><img src='/img/ur.png'></a></td>";
						} else {
							echo "<td width='1'><a href='block.php?roomid=$getroomid&seatid=$getseatid&blockedseatid=$seatid&block=1'><img src='/img/ug.png'></a></td>";
						}

					} else if ($row['orientation'] == 2) {
						//table face right

						if ($getseatid == $seatid) {
							echo "<td width='1'><img src='/img/ro.png'></td>";
						} else if ($red > 0) {
							echo "<td width='1'><a href='block.php?roomid=$getroomid&seatid=$getseatid&blockedseatid=$seatid&unblock=1'><img src='/img/rr.png'></a></td>";
						} else {
							echo "<td width='1'><a href='block.php?roomid=$getroomid&seatid=$getseatid&blockedseatid=$seatid&block=1'><img src='/img/rg.png'></a></td>";
						}


					} else if($row['orientation'] == 3) {
						//table face down

						if ($getseatid == $seatid) {
							echo "<td width='1'><img src='/img/do.png'></td>";
						} else if ($red > 0) {
							echo "<td width='1'><a href='block.php?roomid=$getroomid&seatid=$getseatid&blockedseatid=$seatid&unblock=1'><img src='/img/dr.png'></a></td>";
						} else {
							echo "<td width='1'><a href='block.php?roomid=$getroomid&seatid=$getseatid&blockedseatid=$seatid&block=1'><img src='/img/dg.png'></a></td>";
						}


					} else if($row['orientation'] == 4) {
						//table face left
						if ($getseatid == $seatid) {
							echo "<td width='1'><img src='/img/lo.png'></td>";
						} else if ($red > 0) {
							echo "<td width='1'><a href='block.php?roomid=$getroomid&seatid=$getseatid&blockedseatid=$seatid&unblock=1'><img src='/img/lr.png'></a></td>";
						} else {
							echo "<td width='1'><a href='block.php?roomid=$getroomid&seatid=$getseatid&blockedseatid=$seatid&block=1'><img src='/img/lg.png'></a></td>";
						}

					}

				} else if ($row['type'] == 2) {
					if ($row['orientation'] == 1) {
						echo "<td width='1'><img src='/img/udoor.png'></td>";
											} else if ($row['orientation'] == 2) {
						echo "<td width='1'><img src='/img/rdoor.png'></td>";
											} else if($row['orientation'] == 3) {
						echo "<td width='1'><img src='/img/ddoor.png'></td>";
											} else if($row['orientation'] == 4) {
						echo "<td width='1'><img src='/img/ldoor.png'></td>";
											}

				} else if ($row['type'] == 3) {
					if ($row['orientation'] == 1) {
						echo "<td width='1'><img src='/img/hwindow.png'></td>";
											} else if ($row['orientation'] == 2) {
						echo "<td width='1'><img src='/img/vwindow.png'></td>";
											} else if($row['orientation'] == 3) {
						echo "<td width='1'><img src='/img/hwindow.png'></td>";
											} else if($row['orientation'] == 4) {
						echo "<td width='1'><img src='/img/vwindow.png'></td>";
											}


				} else if ($row['type'] == 4) {
					echo "<td width='1'><img src='/img/blank.png'></td>";
				}




				
			}
			echo "</tr>";
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
		echo "</tbody></table></div>";
	} else {
		echo "<div class='container'>Select seat to show blocked seats";
		echo "<table class='table table-bordered'><tbody>";

		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT seat.seatid, seat.row,seat.orientation, seat.type FROM seat WHERE seat.roomid = ? ORDER BY seat.row,seat.col");

		if ($statement->execute(array($getroomid))) {
		
			$gridrow = 0;
			while($row = $statement->fetch()) {
										if ($row['row'] > $gridrow) {
				   $gridrow = $row['row'];
										   echo "<tr>";
				}
				$seatid = $row['seatid'];


				if ($row['type'] == 1) {
					if ($row['orientation'] == 1) {
											   //table face up
					   echo "<td width='1'><a href='block.php?roomid=$getroomid&seatid=$seatid'><img src='/img/ug.png'></a></td>";
											} else if ($row['orientation'] == 2) {
											   //table face right
					   echo "<td width='1'><a href='block.php?roomid=$getroomid&seatid=$seatid'><img src='/img/rg.png'></a></td>";
											} else if($row['orientation'] == 3) {
											   //table face down
					   echo "<td width='1'><a href='block.php?roomid=$getroomid&seatid=$seatid'><img src='/img/dg.png'></a></td>";
											} else if($row['orientation'] == 4) {
											   //table face left
					   echo "<td width='1'><a href='block.php?roomid=$getroomid&seatid=$seatid'><img src='/img/lg.png'></a></td>";
											}
				} else if ($row['type'] == 2) {
					if ($row['orientation'] == 1) {
						echo "<td width='1'><img src='/img/udoor.png'></td>";
											} else if ($row['orientation'] == 2) {
						echo "<td width='1'><img src='/img/rdoor.png'></td>";
											} else if($row['orientation'] == 3) {
						echo "<td width='1'><img src='/img/ddoor.png'></td>";
											} else if($row['orientation'] == 4) {
						echo "<td width='1'><img src='/img/ldoor.png'></td>";
											}

				} else if ($row['type'] == 3) {
					if ($row['orientation'] == 1) {
						echo "<td width='1'><img src='/img/hwindow.png'></td>";
											} else if ($row['orientation'] == 2) {
						echo "<td width='1'><img src='/img/vwindow.png'></td>";
											} else if($row['orientation'] == 3) {
						echo "<td width='1'><img src='/img/hwindow.png'></td>";
											} else if($row['orientation'] == 4) {
						echo "<td width='1'><img src='/img/vwindow.png'></td>";
											}


				} else if ($row['type'] == 4) {
					echo "<td width='1'><img src='/img/blank.png'></td>";
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

}
?>


		<h2>Seat list</h2>
		<div class="table-responsive">
			<table class="table table-striped table-sm">
				<thead>
				<tr>
				<th>Block ID</th>
				<th>Room Name</th>
				<th>Seat ID</th>
				<th>Blocked Seat ID</th>
				<th>Edit</th>
				</tr>
				</thead>
				<tbody>
		  
			<?php
			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("SELECT blocks.*, room.roomname FROM blocks LEFT JOIN seat ON blocks.seatid = seat.seatid LEFT JOIN room ON seat.roomid = room.roomid WHERE room.roomid = ? ORDER BY room.roomname, blocks.seatid");

			if ($statement->execute(array($getroomid))) {
			
				while($row = $statement->fetch()) {
					$id = $row['blockid'];
					echo "<tr>";
					echo "<td>".$row['blockid']."</td>";
					echo "<td>".$row['roomname']."</td>";
					echo "<td>".$row['seatid']."</td>";
					echo "<td>".$row['blockedseatid']."</td>";
					echo "<td>";
					echo "<a class='btn btn-sm btn-outline-secondary' href='addblock.php?&id=$id' role='button'>Edit</a>";
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
