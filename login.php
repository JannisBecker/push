<?php
	session_start();

	//If logging out..
	if(isset($_GET['logout'])) {
		session_destroy();
	}

	//If already logged in..
	if(isset($_SESSION['initial'])) {
		header("Location: ./");		
	}

	//Login check
	if(isset($_POST['username']) && isset($_POST['password'])) {
		 //Default Error: "User not found"
		$error = 1;
		$username = $_POST['username'];
		$password = $_POST['password'];

		//Access User DB
		require_once("res/php/sql-config.php");
		$conn = mysql_connect($db_host,$db_user,$db_pass);
		if($conn) {
			$db = mysql_select_db("anuk_push",$conn);
			if($db) {
				$query = "SELECT u.initial, u.username, u.password, p.* FROM users AS u, preferences AS p WHERE BINARY u.username = '$username' AND p.user_id = u.id";
				$res = mysql_query($query,$conn);
				if(mysql_num_rows($res) > 0) {
					$logindata = mysql_fetch_array($res);
					$error = 0;	//Set to no Error now
					if($logindata["password"] == md5($password)) {
						$_SESSION["username"] = $username;
						$_SESSION["initial"] = $logindata["initial"];
						$_SESSION["prefs"]["tilesize"] = $logindata["tilesize"];
						$_SESSION["prefs"]["tilesperpage"] = $logindata["tiles"];
						$_SESSION["prefs"]["navbar"] = $logindata["navbar"];
						header('Location: ./');
					} else {
						$error = 2; //Set Error to "Password wrong"
					}
				}
			} else die ("couldnt open database");
		} else die("cant connect to mysql server");
	}
?>
<html>
	<head>
    	<title>push: Login</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="res/css/style.css">
		<script type="text/javascript" src="res/js/jquery.js"></script>
	</head>
	<body>
	<div class="header">
		<div class="logo-login">
    		<img class="logo-img" src="res/img/logo.png">
        </div>
	</div>		
	<div class="content">
		<div class="login">
			<div class="wrapper">
				<div class="login-header">Login</div>
				<?php
					switch($error) {
						case 1:
							echo("<div class=\"error-alert\">This username does not exist</div>");
							break;
						case 2:
							echo("<div class=\"error-alert\">The given password is incorrect</div>");
							break;
					}
				?>
				<form action="login.php" method="post" class="login">
					<input type="text" name="username" class="login login-user">
					<input type="password" name="password" class="login login-password">
					<input type="submit" class="login login-submit" value="Login">
				</form>
			</div>
		</div>
    	</div>
    </body>
</html>