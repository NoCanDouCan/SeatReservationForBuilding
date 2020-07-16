<?php
session_start();
if (isset($_SESSION['username'])) {
		$username = $_SESSION['username'];
		header("Location: index.php");
		die();
	}
?>




<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">

    <title>Seat Reservation</title>


    <!-- Bootstrap core CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">



    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="https://getbootstrap.com/docs/4.3/examples/dashboard/dashboard.css" rel="stylesheet">
  </head>
  <body>
    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
  <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Seat Reservation</a>
  <!--input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search"-->
  <ul class="navbar-nav px-3">
    <li class="nav-item text-nowrap">
      
    </li>
  </ul>
</nav>

<div class="container-fluid">
  <div class="row">
    <nav class="col-md-2 d-none d-md-block bg-light sidebar">
      <div class="sidebar-sticky">
        <ul class="nav flex-column">       
        </ul>
      </div>
    </nav>

    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Login</h1>
      </div>

<?php
if(isset($_POST['username']) && isset($_POST['password'])){

    $adServer = "ldap://SERVERNAME";

    $ldap = ldap_connect($adServer);
    $username = $_POST['username'];
    $password = $_POST['password'];
	
	if ($password != "") {
		

		$ldaprdn = 'DOMAIN' . "\\" . $username;

		$bind = @ldap_bind($ldap, $ldaprdn, $password);
		
		if ($bind) {
			
			$_SESSION['username'] = $username;
			
			
			//check if user exists in db, if not create item
			$userid = 0;
			$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
			$statement = $pdo->prepare("SELECT user.userid FROM user WHERE user.username = ? LIMIT 1 ");
			if ($statement->execute(array($username))) {
				while($row = $statement->fetch()) {
					$userid = $row['userid'];
				}
			}
			if ($userid == 0) {
				$pdo = new PDO('mysql:host=localhost:3306;dbname=DBNAME', 'USERNAME', 'PASSWORD');
				$statement = $pdo->prepare("INSERT INTO user (username,fullname,isadmin,roomid,seatid) VALUES (?,?,?,?,?)");
				$statement->execute(array($_POST['username'],$_POST['fullname'],$_POST['isadmin'],$_POST['roomid'],$_POST['seatid']));
			}
			
			
	
			header("Location: index.php");
							
			@ldap_close($ldap);
			die();
		} else {
						
			?>
			<p>Username / Password wrong</p>
			<form action="" method="POST">
			<label for="username">Username: </label><input id="username" type="text" name="username" /><br>
			<label for="password">Password: </label><input id="password" type="password" name="password" /><br>    
			<input type="submit" name="submit" value="Submit" />
			</form>
			<?php 
		}

	} else {
				
		?>
			<p>Password empty</p>
			<form action="" method="POST">
			<label for="username">Username: </label><input id="username" type="text" name="username" /><br>
			<label for="password">Password: </label><input id="password" type="password" name="password" /><br>    
			<input type="submit" name="submit" value="Submit" />
			</form>
			<?php 
	}
	
	

    

}else{

	if (isset($_SESSION['username'])) {
		$username = $_SESSION['username'];
		header("Location: index.php");
		die();
	}
	
	?>
    <form action="" method="POST">
        <label for="username">Username: </label><input id="username" type="text" name="username" /><br>
        <label for="password">Password: </label><input id="password" type="password" name="password" /><br>      
		<input type="submit" name="submit" value="Submit" />
    </form>
<?php 
} 
?> 
	  
	  
	  
    </main>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="/docs/4.3/assets/js/vendor/jquery-slim.min.js"><\/script>')</script><script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-xrRywqdh3PHs8keKZN+8zzc5TX0GRTLCcmivcbNJWm2rs5C8PRhcEn3czEjhAO9o" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.9.0/feather.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
        <script src="https://getbootstrap.com/docs/4.3/examples/dashboard/dashboard.js"></script></body>
</html>

