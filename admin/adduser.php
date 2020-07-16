<?php
    include ("../config/session_admin.php");
	include ("../config/db.php");

	//get variables
	$getid = 0;
	if(isset($_GET['id'])){
		$getid = $_GET['id'];
	}
	
    if (isset($_POST['delete'])){
        $pdo = new PDO($dbserver, $dbuser, $dbpw);
        $statement = $pdo->prepare("DELETE FROM user WHERE userid = ?");
        if ($statement->execute(array($_POST['id']))) {
            header("Location: user.php");
            die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
    } else if(isset($_POST['update'])){
        //SQL
        
        $pdo = new PDO($dbserver, $dbuser, $dbpw);
        $statement = $pdo->prepare("UPDATE user SET username = ?,fullname = ?,roomid= ?,seatid = ? WHERE userid = ?");
        if ($statement->execute(array($_POST['username'],$_POST['fullname'],$_POST['roomid'],$_POST['seatid'],$_POST['id']))) {
            header("Location: user.php");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
        
    } else if(isset($_POST['create'])){
        //SQL
        
        $pdo = new PDO($dbserver, $dbuser, $dbpw);
        $statement = $pdo->prepare("INSERT INTO user (username,fullname,isadmin,roomid,seatid) VALUES (?,?,?,?,?)");
        if ($statement->execute(array($_POST['username'],$_POST['fullname'],$_POST['isadmin'],$_POST['roomid'],$_POST['seatid']))) {
			
			//select userid
			$pdo = new PDO($dbserver, $dbuser, $dbpw);
			$statement = $pdo->prepare("SELECT user.userid FROM user WHERE username = ? LIMIT 1");
			if ($statement->execute(array($_POST['username']))) {
				$newuserid = 0;
				while($row = $statement->fetch()) {
					$newuserid = $row['userid'];
				}
				if ($newuserid > 0) {
					//create usertogroup
					$pdo = new PDO($dbserver, $dbuser, $dbpw);
					$statement = $pdo->prepare("INSERT INTO usertogroup (userid, groupid) VALUES (?,?)");
					if ($statement->execute(array($newuserid,$_POST['group']))) {
						header("Location: user.php");
						die();
					} else {
						echo "SQL Error <br />";
						echo $statement->queryString."<br />";
						echo $statement->errorInfo()[2];
					}
				}
			}
            header("Location: user.php");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
		
		
        
    } 
    include ("../includes/menu.php");
	include ("../includes/html.php");

	echo head("Seat Reservation - Admin");
	echo body_top_admin("Seat Reservation - Admin",$username);
	echo display_admin_menu("user", $isadmin);
	echo body_title_top("Add User");
	if($getid > 0){
		echo delete_button($getid, "adduser.php");
	}
	echo body_title_bottom();
?> 

                    <form action="adduser.php" method="POST">

                        <table>
                            <?php
                                if(isset($_GET['id'])){
                                    $id = $_GET['id'];


                                    $pdo = new PDO($dbserver, $dbuser, $dbpw);
                                    $statement = $pdo->prepare("SELECT user.* FROM user WHERE userid = ? LIMIT 1 ");
                                    if ($statement->execute(array($id))) {
                                        while($row = $statement->fetch()) {
                                            $id = $row['userid'];
                                            $dbusername = $row['username'];
                                            $fullname = $row['fullname'];
                                            $isadmin = $row['isadmin'];
                                            $roomid = $row['roomid'];
                                            $seatid = $row['seatid'];
                                        }
                                    }
                                    echo "<tr><td><label for='id' hidden>ID: </label></td><td><input id='id' type='text' name='id' size='50' value='$id' readonly hidden /></td></tr>";
                                }
                            ?>

							<tr>
                                <td>
                                    <label for="source">Group: </label>
                                </td>
                                <td>
                                    <select id="group" name="group" />
<?php
									$pdo = new PDO($dbserver, $dbuser, $dbpw);
									$statement = $pdo->prepare("SELECT groups.info, groups.groupid FROM groups INNER JOIN usertogroup ON groups.groupid = usertogroup.groupid INNER JOIN user ON usertogroup.userid = user.userid WHERE user.username = ? ORDER BY groups.info");
									if ($statement->execute(array($username))) {
										while($row = $statement->fetch()) {
											$groupid = $row['groupid'];
											$info = $row['info'];
											echo "<option value='".$groupid."'>".$info."</option>";
										}
									} else {
											echo "SQL Error <br />";
											echo $statement->queryString."<br />";
											echo $statement->errorInfo()[2];
										
									}
?>		
									</select>
                                </td>
                                <td>
                                    <label for="example1"></label>
                                </td>
                            </tr>
							
							
							
                            
                            <tr>
                                <td>
                                    <label for="source">Username: </label>
                                </td>
                                <td>
                                    <input id="username" type="text" name="username" size="50" <?php echo "value='$dbusername'";?>/>
                                </td>
                                <td>
                                    <label for="example1"></label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="source">Fullname: </label>
                                </td>
                                <td>
                                    <input id="fullname" type="text" name="fullname" size="50" <?php echo "value='$fullname'";?>/>
                                </td>
                                <td>
                                    <label for="example1"></label>
                                </td>
                            </tr>
                            

                            


                        </table>
                        <input type="submit" <?php if(isset($_GET['id'])) { echo "name='update'";} else {echo "name='create'";} ?> value="Submit" />
                    </form>

<?php
	echo body_bottom();
?>					