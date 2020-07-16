<?php
	include ("../config/session_admin.php");
	include ("../config/db.php");
	include ("../includes/menu.php");
	include ("../includes/html.php");
	
	echo head("Seat Reservation - Admin");
	echo body_top_admin("Seat Reservation - Admin",$username);
	echo display_admin_menu("floor", $isadmin);
	echo body_title_top("Admin - Floor");
	if ($isadmin == 2) {
		echo add_button("Add Floor", "addfloor.php");
	}
	echo body_title_bottom();
?> 

<h2>Floor list</h2>
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
		$statement = $pdo->prepare("SELECT floor.floorid, floor.floorname, floor.department FROM floor INNER JOIN floortogroup ON floor.floorid = floortogroup.floorid INNER JOIN usertogroup ON floortogroup.groupid = usertogroup.groupid INNER JOIN user ON usertogroup.userid = user.userid WHERE user.username = ? ORDER BY floor.floorname");
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

<?php
	echo body_bottom();
?>