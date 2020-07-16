<?php
	include ("../config/session_admin.php");
	include ("../config/db.php");
	include ("../includes/menu.php");
	include ("../includes/html.php");
	
	if (isset($_GET['del'])){
		$groupid = $_GET['del'];
		$userid = 0;
		
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT user.userid from user where username = ? LIMIT 1");
		if ($statement->execute(array($username))) {
			while($row = $statement->fetch()) {
				$userid = $row['userid'];
			}
		}
		
		if ($userid > 0){
			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("DELETE FROM usertogroup WHERE userid = ? AND groupid = ?");
			if ($statement->execute(array($userid,$groupid))) {
				header("Location: group.php");
				die();
			} else {
				echo "SQL Error <br />";
				echo $statement->queryString."<br />";
				echo $statement->errorInfo()[2];
			}
		}
    } else if(isset($_GET['add'])){
        $groupid = $_GET['add'];
		$userid = 0;
		
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT user.userid from user where username = ? LIMIT 1");
		if ($statement->execute(array($username))) {
			while($row = $statement->fetch()) {
				$userid = $row['userid'];
			}
		}
		
		if ($userid > 0){
			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("INSERT INTO usertogroup (userid, groupid) VALUES (?,?)");
			if ($statement->execute(array($userid,$groupid))) {
				header("Location: group.php");
				die();
			} else {
				echo "SQL Error <br />";
				echo $statement->queryString."<br />";
				echo $statement->errorInfo()[2];
			}
		}
        
    }

	echo head("Seat Reservation - Admin");
	echo body_top_admin("Seat Reservation - Admin",$username);
	echo display_admin_menu("group", $isadmin);
	echo body_title_top("Admin - Group");
	echo add_button("Add Group", "addgroup.php");
	echo body_title_bottom();
?> 

<h2>Group list</h2>
<div class="table-responsive">
	<table class="table table-striped table-sm">
	<thead>
		<tr>
			<th>Group ID</th>
			<th>Info</th>
			<th>Add/Remove</th>
			<th>Edit</th>
		</tr>
	</thead>
	<tbody>
  
	<?php
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT groups.groupid, groups.info, (SELECT usertogroup.userid from usertogroup INNER JOIN user ON usertogroup.userid = user.userid WHERE usertogroup.groupid = groups.groupid AND user.username = ? LIMIT 1) as member FROM groups ORDER BY groups.info");
		if ($statement->execute(array($username))) {
			while($row = $statement->fetch()) {
				$id = $row['groupid'];
				$userid = $row['member'];
				echo "<tr>";
				echo "<td>".$row['groupid']."</td>";
				echo "<td>".$row['info']."</td>";
				echo "<td>";
				if ($userid > 0){
					echo "<a class='btn btn-sm btn-outline-secondary' href='group.php?&del=$id' role='button'>Remove</a>";
				} else {
					echo "<a class='btn btn-sm btn-outline-secondary' href='group.php?&add=$id' role='button'>Add</a>";
				
				}
				echo "</td>";
				echo "<td>";
				echo "<a class='btn btn-sm btn-outline-secondary' href='addgroup.php?&id=$id' role='button'>Edit</a>";
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