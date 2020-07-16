<?php
    session_start();
    error_reporting(E_ALL);
    if (!isset($_SESSION['username'])) {
	    header("Location: ../login.php");
	    die();
    }
    $username = $_SESSION['username'];
	
	$isadmin = 0;
	include ("../config/db.php");
	$pdo = new PDO($dbserver, $dbuser, $dbpw);
	$statement = $pdo->prepare("SELECT user.isadmin FROM user WHERE user.username = ? LIMIT 1 ");
	if ($statement->execute(array($username))) {
		while($row = $statement->fetch()) {
			$isadmin = $row['isadmin'];
		}
	}
	if ($isadmin == 0) {
		header("Location: ../index.php");
		die();
	}
?>