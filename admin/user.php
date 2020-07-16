<?php
	include ("../config/session_admin.php");
	include ("../config/db.php");
	include ("../includes/menu.php");
	include ("../includes/html.php");
	
	if(isset($_GET['admin'])){
        //SQL
        
        $pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
        $statement = $pdo->prepare("UPDATE user SET isadmin = ? WHERE userid = ?");
        if ($statement->execute(array($_GET['admin'],$_GET['id']))) {
            header("Location: user.php");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
        
    }
	
	echo head("Seat Reservation - Admin");
	echo body_top_admin("Seat Reservation - Admin",$username);
	echo display_admin_menu("user", $isadmin);
	echo body_title_top("Admin - User");
	echo add_button("Add User", "adduser.php");
	echo body_title_bottom();
?> 

	<h2>User list</h2>
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
				$statement = $pdo->prepare("select DISTINCT user.* from user inner join usertogroup on user.userid = usertogroup.userid where usertogroup.groupid IN (select usertogroup.groupid from usertogroup INNER JOIN user on usertogroup.userid = user.userid where user.username = ?) ORDER BY user.username");
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
						if ($isadmin == 2)
						{
							if ($dbisadmin == 0)
							{
								echo "<td><a class='btn btn-sm btn-outline-secondary' href='user.php?id=$id&admin=1' role='button'>Admin</a></td>";
							} else {
								echo "<td><a class='btn btn-sm btn-outline-secondary' href='user.php?id=$id&admin=0' role='button'>Admin</a></td>";
							}
						}
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