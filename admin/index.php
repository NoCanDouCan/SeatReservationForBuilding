<?php
	include ("../config/session_admin.php");
	include ("../config/db.php");
	include ("../includes/menu.php");
	include ("../includes/html.php");
	
	$getallow = 0;
	$getdeny = 0;
	if (isset($_GET['allow'])) {
		$getallow = $_GET['allow'];
	}
	if (isset($_GET['deny'])) {
		$getdeny = $_GET['deny'];
	}
	
	if ($getdeny > 0)
	{
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("DELETE FROM request WHERE requestid = ?");
		if ($statement->execute(array($getdeny))) {
			header("Location: index.php");
			die();
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
	}
	
	if ($getallow > 0)
	{
		//add user to group then remove request
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("INSERT INTO usertogroup (groupid, userid) (SELECT request.groupid, request.userid FROM request where request.requestid = ? LIMIT 1)");
		if ($statement->execute(array($getallow))) {
			$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
			$statement = $pdo->prepare("DELETE FROM request WHERE requestid = ?");
			if ($statement->execute(array($getallow))) {
				header("Location: index.php");
				die();
			} else {
				echo "SQL Error <br />";
				echo $statement->queryString."<br />";
				echo $statement->errorInfo()[2];
			}
		} else {
			echo "SQL Error <br />";
			echo $statement->queryString."<br />";
			echo $statement->errorInfo()[2];
		}
		
		
	}

	echo head("Seat Reservation - Admin");
	echo body_top_admin("Seat Reservation - Admin",$username);
	echo display_admin_menu("admin", $isadmin);
	echo body_title_top("");
	echo body_title_bottom();
?> 

<h2>User requested access to your group</h2>
<div class="table-responsive">
	<table class="table table-striped table-sm">
	<thead>
		<tr>
			<th>User ID</th>
			<th>Username</th>
			<th>Fullname</th>
			<th>Group ID</th>
			<th>Group</th>
			<th>Allow</th>
			<th>Deny</th>
		</tr>
	</thead>
	<tbody>
  
	<?php
		$pdo = new PDO($dbserver, $dbuser, $dbpw);
		$statement = $pdo->prepare("SELECT request.requestid, request.userid, request.groupid, user.username, user.fullname, groups.info FROM request LEFT JOIN user ON request.userid = user.userid LEFT JOIN groups on request.groupid = groups.groupid LEFT JOIN usertogroup ON groups.groupid = usertogroup.groupid LEFT JOIN user as user2 ON usertogroup.userid = user2.userid WHERE user2.username = ?");
		if ($statement->execute(array($username))) {
			while($row = $statement->fetch()) {
				$id = $row['requestid'];
				echo "<tr>";
				echo "<td>".$row['userid']."</td>";
				echo "<td>".$row['username']."</td>";
				echo "<td>".$row['fullname']."</td>";
				echo "<td>".$row['groupid']."</td>";
				echo "<td>".$row['info']."</td>";
				echo "<td><a class='btn btn-sm btn-outline-secondary' href='index.php?&allow=$id' role='button'>Allow</a></td>";
				echo "<td><a class='btn btn-sm btn-outline-secondary' href='index.php?&deny=$id' role='button'>Deny</a></td>";
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
