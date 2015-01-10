<?php
	ob_start();
	session_start();
	//Debuging mode default is of
	$debugging = "off";
	//if debuggin is on it will call this function at the end to make sure it all passes
	function debugging(){
		echo("<br /><br />Debug mode: <span style=\"color:green;\">Everything Passed!");
	}
	//PHP errors default is off
	$errors_state = "off";
	if($errors_state == "on"){
		ini_set('display_errors', 'On');
		error_reporting(E_ALL);
	}
	//This fuction sets the username to the already aproved given user and calls the redirect funtion. 
	function set_user(){
			$username = $_POST['username'];
	       	$_SESSION['username'] = $username;
	       	redirect();
	}
	//redirect at this point only redirects to home
	function redirect(){
		header("Location: index.php");
	}
	//The first time the page is loaded and everytime login fails the form is printed
	function render_login_form(){
		$login_form = "";
	        $login_form .= "<form action=\"index.php?path=login\" method=\"post\">";
			    $login_form .= "<input type=\"text\" name=\"username\"><br /> ";
			    $login_form .= "<input type=\"password\" name=\"password\" /><br />";
			    $login_form .= "<input type=\"submit\" value=\"Login\" name=\"login\" />";
		    $login_form .= "</form>";
		    $login_form .= "<br /><a href=\"index.php?path=register\">Don't have an account?</a>";
		return($login_form);
	}
	function render_make_user_form(){
		$user_form = "";
	        $user_form .= "<form action=\"index.php?path=register\" method=\"post\">";
			    $user_form .= "<input type=\"text\" name=\"username\"><br /> ";
			    $user_form .= "<input type=\"password\" name=\"password\" placeholder=\"Password\" /><br />";
			    $user_form .= "<input type=\"submit\" value=\"Make User\" name=\"makepass\" />";
		    $user_form .= "</form>";
		return($user_form);
	}
	//this function checks to see if the user exists and the password is correct if so it progresses if not it calls an error
	function check_creds($user, $pass){
		//creating password
		$user1crypt = '$2y$10$4DXZVxJgNacomVELwqQzBOmCABLSI5BTZ5uQd1FuhEwik3EpBwZmW';
		$user2crypt = '$2y$10$8W0hhsuaL2GlaKQuFyB2deAZd8PUJq9Khw97DPdUj3j0gzUJf8B2m';
		$user3crypt = '$2y$10$nYngDhrwr3iHTeTkDSmh5uxTK0RZIjDRVVDRuLy.kXwPnWsd53t/a';
		//remove unsightly character from user input
		$user = htmlspecialchars($user);
		$pass = htmlspecialchars($pass);
		//the users array stores the list of users, the value on the left is the username the value on the right is the password
		echo $james0vincecrypt;
		$users = array(
	   			"user1" => $user1crypt,
	   			"user2" => $user2crypt,
	   			"user3" => $user3crypt,
	  		);
		//check if user is in the list of users
		if (array_key_exists($user, $users)) {
			//if pass is correct continue
			if($pass == password_verify($pass, $users[$user])){
				set_user();
			}
			//if password is not correct render login failed error
			else{
				echo(get_error("login_failed"));
				echo(render_login_form());
			}
		}
		//if the username is not in the list of users render bad user error
		else{
			echo(get_error("bad_user", $user));
			echo(render_login_form());
		}
	}
	//This fuction takes in the error type and returns the statement for the error
	function get_error($err, $arg = "null"){
		$arg = htmlspecialchars($arg);
		$output = "";
		$output .= "<span style=\"color:red; font-weight:bolder;\">";
		switch ($err) {
		    case "empty_user_pass":
		        $output .= "Username and Password can not be empty.";
	        break;
		    case "login_failed":
		        $output .= "Login faild, please try again.";
	        break;
		    case "bad_user":
		        $output .= "User: {$arg} is not a valid user.";
	        break;
			    default:
			    $output .= "Undefined error.";
		}
		$output .= "</span>";
		return $output;
	}
	//This functions gets generic text that may be use more than once
	function get_statement($statement, $arg = "null"){
		$arg = htmlspecialchars($arg);
		$output = "";
		//$output .= "<span style=\"color:red; font-weight:bolder;\">";
		$userx = $_SESSION['username'];
		switch ($statement) {
		    case "not_logged_in":
		        $output .= "You are not logged in. Why dont you <a href=\"index.php?path=login\">login</a>?";
		   		break;
			case "user_welcome":
		        $output .= "Welcome {$userx}";
	        	break;
		    case "logout":
		        $output .= "<a href=\"index.php?path=logout\">Logout</a>";
	        	break;
		    default:
		       $output .= "Undefined statement.";
		}
				//$output .= "</span>";
				return $output;				
	}
	function make_password($passIn){
		$makepass = $passIn;
		$passcrypt = password_hash($makepass, PASSWORD_BCRYPT);
		return $passcrypt;
	}
	function display_password(){
		//when the register user form has been posted do this
		if(isset($_POST['makepass'])){
			$new_user = $_POST['username'];
			$send_pass = $_POST['password'];
			//any bad chars to be converted to HTML friendly
			$new_crypt_pass = htmlspecialchars(make_password($send_pass));
			echo "Add this to the list of hashes: <br /><br />";
			//This print out give you the encrypted password to add to the list
			echo("$"  . $new_user . "crypt = '" . $new_crypt_pass . "';" );
			echo "<br /><br />";
			echo "Add this to the list of users: <br /><br />";
			//This print out give you the user to add to the array
			echo("\""  . $new_user . "\" => $" . $new_user. "crypt," );
			//echo $new_crypt_pass;
		}
	}
	//This is what is rendered on index.php Asks the user to login or renders a welcome
	if(!isset($_GET['path'])){
		if(!isset($_SESSION['username'])){
			echo(get_statement("not_logged_in"));
		}
		//If the user is logged in it welcomes them and offers them the chance to log out
		else{
			echo(get_statement("user_welcome"));
			echo("&nbsp;");
			echo(get_statement("logout"));
		}
	}
	//This is what happends when index.php?path=logout is called it destorys the session
	if(isset($_GET['path']) && $_GET['path'] == "logout"){
		unset($_SESSION['username']);
		session_destroy();
		redirect();
	}
	//This is what happends when ndex.php?path=login is called it offers you the log in form
	if(isset($_GET['path']) && $_GET['path'] == "login"){
		if(isset($_POST['login'])){
			if(empty($_POST['username']) || empty($_POST['password'])){
				echo(get_error("empty_user_pass"));
				echo(render_login_form());
			}
		else{
				$username = $_POST['username'];
				$password = $_POST['password'];
				check_creds($username, $password);
			}
        }
		else{
			echo(render_login_form());
		}
	}
	if(isset($_GET['path']) && $_GET['path'] == "register"){
		echo(render_make_user_form());
		display_password();

	}
?>
<html>
	<head>
		<title>Test Login App</title>
	</head>
	</body>
	<?php if($debugging == "on"){ debugging();}?>
	</body>
</html>