<?php
	include ("../config/session_admin.php");
	include ("../config/db.php");
	include ("../includes/menu.php");
	include ("../includes/html.php");

	echo head("Seat Reservation - Admin");
	echo body_top_admin("Seat Reservation - Admin",$username);
	echo display_admin_menu("usertoseat", $isadmin);
	echo body_title_top("Admin - User");
	echo add_button("Add UserToSeat", "addusertoseat.php");
	echo body_title_bottom();
?>

	<h2>User-Seat Assignment list</h2>
	<div class="table-responsive">
		<table class="table table-striped table-sm">
			<thead>
				<tr>
				<th>Username</th>
				<th>Full Name</th>
				<th>SeatID</th>
				<th>Seat Info</th>
				<th>Edit</th>
				</tr>
			</thead>
			<tbody>

			<?php
			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("SELECT usertoseat.usertoseatid, user.username, user.fullname, usertoseat.seatid, seat.description FROM usertoseat LEFT JOIN seat ON usertoseat.seatid = seat.seatid LEFT JOIN user ON usertoseat.userid = user.userid WHERE user.userid IN (SELECT usertogroup.userid FROM usertogroup INNER JOIN usertogroup as utg2 ON usertogroup.groupid = utg2.groupid INNER JOIN user as user2 ON utg2.userid = user2.userid WHERE user2.username = ?) ORDER BY user.username, seat.description");

			if ($statement->execute(array($username))) {
			
				$gridrow = 0;
				while($row = $statement->fetch()) {
					$id = $row['usertoseatid'];
					echo "<tr>";
					echo "<td>".$row['username']."</td>";
					echo "<td>".$row['fullname']."</td>";
					echo "<td>".$row['seatid']."</td>";
					echo "<td>".$row['description']."</td>";
					echo "<td><a class='btn btn-sm btn-outline-secondary' href='addusertoseat.php?&id=$id' role='button'>Edit</a></td>";
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
