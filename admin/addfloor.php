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
        $statement = $pdo->prepare("DELETE FROM floor WHERE floorid = ?");
        if ($statement->execute(array($_POST['id']))) {
            header("Location: floor.php");
            die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
    } else if(isset($_POST['id'])){
        //SQL
        
        $pdo = new PDO($dbserver, $dbuser, $dbpw);
        $statement = $pdo->prepare("UPDATE floor SET floorname = ?, department = ? WHERE floorid = ?");
        if ($statement->execute(array($_POST['floorname'],$_POST['department'],$_POST['id']))) {
            header("Location: floor.php");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
        
    } else if(isset($_POST['floorname'])){
        //SQL
        
        $pdo = new PDO($dbserver, $dbuser, $dbpw);
        $statement = $pdo->prepare("INSERT INTO floor (floorname,department) VALUES (?,?)");
        if ($statement->execute(array($_POST['floorname'],$_POST['department']))) {
            header("Location: floor.php");
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
	echo display_admin_menu("floor", $isadmin);
	echo body_title_top("Add Floor");
	if($getid > 0){
		echo delete_button($getid, "addfloor.php");
	}
	echo body_title_bottom();
    
?> 

                    <form action="" method="POST">
                        <table>
                            <?php
                                if(isset($_GET['id'])){
                                    $id = $_GET['id'];

                                    $pdo = new PDO($dbserver, $dbuser, $dbpw);
                                    $statement = $pdo->prepare("SELECT * FROM floor where floorid = ? LIMIT 1");
                                    if ($statement->execute(array($id))) {
                                        while($row = $statement->fetch()) {
                                            $floorname = $row['floorname'];
											$department = $row['department'];
                                        }
                                    }
                                    echo "<tr><td><label for='id'>ID: </label></td><td><input id='id' type='text' name='id' size='50' value='$id' readonly /></td></tr>";
                                }
                            ?>
                            <tr>
                                <td>
                                    <label for="source">Floorname: </label>
                                </td>
                                <td>
                                    <input id="floorname" type="text" name="floorname" size="50" <?php echo "value='$floorname'";?>/>
                                </td>
                                <td>
                                    <label for="example1"> Example: N.3 </label>
                                </td>
                            </tr>
							<tr>
                                <td>
                                    <label for="source">Department: </label>
                                </td>
                                <td>
                                    <input id="department" type="text" name="department" size="50" <?php echo "value='$department'";?>/>
                                </td>
                                <td>
                                    <label for="example1"> Example: Perceiving Systems </label>
                                </td>
                            </tr>
                        </table>
                        <input type="submit" name="submit" value="Submit" />
                    </form>
					
<?php
	echo body_bottom();
?>
