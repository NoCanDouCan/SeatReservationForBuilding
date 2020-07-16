<?php
    include ("../config/session_admin.php");
	include ("../config/db.php");
	
	//get variables
	$getid = 0;
	if(isset($_GET['id'])){
		$getid = $_GET['id'];
	}

    if (isset($_POST['delete'])){
        $pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
        $statement = $pdo->prepare("DELETE FROM blocks WHERE blockid = ?");
        if ($statement->execute(array($_POST['id']))) {
            header("Location: block.php");
            die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
    } else if(isset($_POST['update'])){
        //SQL
        
        $pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
        $statement = $pdo->prepare("UPDATE blocks SET seatid = ?,blockedseatid = ? WHERE blockid = ?");
        if ($statement->execute(array($_POST['seatid'],$_POST['blockedseatid'],$_POST['id']))) {
            header("Location: block.php");
	        die();
        } else {
            echo "SQL Error <br />";
            echo $statement->queryString."<br />";
            echo $statement->errorInfo()[2];
        }
        
    } else if(isset($_POST['create'])){
        //SQL
        
        $pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
        $statement = $pdo->prepare("INSERT INTO blocks (seatid,blockedseatid) VALUES (?,?)");
        if ($statement->execute(array($_POST['seatid'],$_POST['blockedseatid']))) {
            header("Location: block.php");
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
	echo display_admin_menu("block", $isadmin);
	echo body_title_top("Add Block");
	if($getid > 0){
		echo delete_button($getid, "addblock.php");
	}
	echo body_title_bottom();
?> 



                    <form action="addblock.php" method="POST">

                        <table>
                            <?php
                                if(isset($_GET['id'])){
                                    $id = $_GET['id'];


                                    $pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
                                    $statement = $pdo->prepare("SELECT blocks.* from blocks where blockid = ? LIMIT 1");
                                    if ($statement->execute(array($id))) {
                                        while($row = $statement->fetch()) {
                                            $id = $row['blockid'];
                                            $seatid = $row['seatid'];
                                            $blockedseatid = $row['blockedseatid'];
                                        }
                                    }
                                    echo "<tr><td><label for='id'>ID: </label></td><td><input id='id' type='text' name='id' size='50' value='$id' readonly /></td></tr>";
                                }
                            ?>
                            <tr>
                                <td>
                                    <label for="source">Seat: </label>
                                </td>
                                <td>
                                    <input id="seatid" type="text" name="seatid" size="50" <?php echo "value='$seatid'";?>/>
                                </td>
                                <td>
                                    <label for="example1"> Enter SeatID that will block other Seats when booked </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="source">Blocked Seat: </label>
                                </td>
                                <td>
                                    <input id="blockedseatid" type="text" name="blockedseatid" size="50" <?php echo "value='$blockedseatid'";?>/>
                                </td>
                                <td>
                                    <label for="example1"> Enter SeatID that will be blocked </label>
                                </td>
                            </tr>


                        </table>
                        <input type="submit" <?php if(isset($_GET['id'])) { echo "name='update'";} else {echo "name='create'";} ?> value="Submit" />
                    </form>
                
<?php
echo body_bottom();
?>				
