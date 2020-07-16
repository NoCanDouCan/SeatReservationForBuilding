<?php
	include ("../config/session_admin.php");

	//get variables
	$getid = 0;
	if(isset($_GET['id'])){
		$getid = $_GET['id'];
	}
	
    if (isset($_POST['delete'])){
        $pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
        $statement = $pdo->prepare("DELETE FROM room WHERE roomid = ?");
        if ($statement->execute(array($_POST['id']))) {
            header("Location: room.php");
            die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
    } else if(isset($_POST['id'])){
        //SQL
        
        $pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
        $statement = $pdo->prepare("UPDATE room SET roomname = ?,floorid = ?, permission = ? WHERE roomid = ?");
        if ($statement->execute(array($_POST['roomname'],$_POST['floorid'],$_POST['permission'],$_POST['id']))) {
            header("Location: room.php");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
        
    } else if(isset($_POST['roomname'])){
        //SQL
        
        $pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
        $statement = $pdo->prepare("INSERT INTO room (roomname,floorid,permission) VALUES (?,?,?)");
        if ($statement->execute(array($_POST['roomname'],$_POST['floorid'],$_POST['permission']))) {
			
			//select roomid
			$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
			$statement = $pdo->prepare("SELECT room.roomid FROM room WHERE roomname = ? AND floorid = ? LIMIT 1");
			if ($statement->execute(array($_POST['roomname'],$_POST['floorid']))) {
				$newroomid = 0;
				while($row = $statement->fetch()) {
					$newroomid = $row['roomid'];
				}
				if ($newroomid > 0) {
					//create roomtogroup
					$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
					$statement = $pdo->prepare("INSERT INTO roomtogroup (roomid, groupid) VALUES (?,?)");
					if ($statement->execute(array($newroomid,$_POST['group']))) {
						header("Location: room.php");
						die();
					} else {
						echo "SQL Error <br />";
						echo $statement->queryString."<br />";
						echo $statement->errorInfo()[2];
					}
				}
			}
            header("Location: room.php");
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
	echo display_admin_menu("room", $isadmin);
	echo body_title_top("Add Room");
	if($getid > 0){
		echo delete_button($getid, "addroom.php");
	}
	echo body_title_bottom();

?> 


                    <form action="addroom.php" method="POST">
                        <table>
                            <?php
                                if(isset($_GET['id'])){
                                    $id = $_GET['id'];


                                    $pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
                                    $statement = $pdo->prepare("SELECT room.*, floor.floorname FROM room LEFT JOIN floor ON room.floorid = floor.floorid where roomid = ? LIMIT 1");
                                    if ($statement->execute(array($id))) {
                                        while($row = $statement->fetch()) {
                                            $id = $row['roomid'];
                                            $roomname = $row['roomname'];
                                            $floorid = $row['floorid'];
                                            $floorname = $row['floorname'];
											$permission = $row['permission'];
                                        }
                                    }
                                    echo "<tr><td><label for='id'>ID: </label></td><td><input id='id' type='text' name='id' size='50' value='$id' readonly /></td></tr>";
                                }
                            ?>
							<tr>
                                <td>
                                    <label for="source">Group: </label>
                                </td>
                                <td>
                                    <select id="group" name="group" />
<?php
									$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
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
                                    <label for="source">Floorname: </label>
                                </td>
                                <td>
                                    <select id="floorid" name="floorid">

                        <?php

			$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
				$statement = $pdo->prepare("SELECT * FROM floor INNER JOIN floortogroup ON floor.floorid = floortogroup.floorid INNER JOIN usertogroup ON floortogroup.groupid = usertogroup.groupid INNER JOIN user ON usertogroup.userid = user.userid WHERE user.username = ? ORDER BY floor.floorname");
				if ($statement->execute(array($username))) {
					while($row = $statement->fetch()) {
						if ($row['floorid'] == $floorid) {
						  echo "<option value='".$row['floorid']."' selected>".$row['floorname']."</option>";
						} else {
						   echo "<option value='".$row['floorid']."'>".$row['floorname']."</option>";
						}
						
					}
				} else {
					echo "SQL Error <br />";
					echo $statement->queryString."<br />";
					echo $statement->errorInfo()[2];
				}
			?>

                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label for="source">Roomname: </label>
                                </td>
                                <td>
                                    <input id="roomname" type="text" name="roomname" size="50" <?php echo "value='$roomname'";?>/>
                                </td>
                                <td>
                                    <label for="example1"> Example: N.3020 </label>
                                </td>
                            </tr>
							
							<tr>
                                <td>
                                    <label for="source">Permission: </label>
                                </td>
                                <td>
                                    <input id='permission' type='radio' name='permission' value='0' <?php if ($permission == 0) { echo "checked"; }?>/>Everyone<br>
									<input id='permission' type='radio' name='permission' value='1' <?php if ($permission == 1) { echo "checked"; }?>/>Only for assigned users<br>
                                </td>
                                <td>
                                    <label for="example1">  </label>
                                </td>
                            </tr>

							
                        </table>
                        <input type="submit" name="submit" value="Submit" />
                    </form>
					
<?php	
	echo body_bottom();
?>	
					
                