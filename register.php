<?php
 
include('db.php');
session_start();
 
if (isset($_POST['register'])) {
 
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
 
    $query = pg_query($db, "SELECT * FROM users WHERE email='$email'");
 
    if (pg_numrows($query) > 0) {
        echo '<p class="error">The email address is already registered!</p>';
    }
 
    if (pg_numrows($query) == 0) {
        $result = pg_query($db, "INSERT INTO users(USERNAME,PASSWORD,EMAIL) VALUES ('$username','$password_hash','$email')");
 
        if ($result) {
            echo '<p class="success">Your registration was successful!</p>';
        } else {
            echo '<p class="error">Something went wrong!</p>';
        }
    }
}
 
?>

	<!DOCTYPE html>
	<html lang="en">

	<head>
	    <title>Login Page</title>
	</head>

	<body>
	    <form method="post" action="" name="signup-form">
	        <div class="form-element">
	            <label>Username</label>
	            <input type="text" name="username" pattern="[a-zA-Z0-9]+" required />
	        </div>
	        <div class="form-element">
	            <label>Email</label>
	            <input type="email" name="email" required />
	        </div>
	        <div class="form-element">
	            <label>Password</label>
	            <input type="password" name="password" required />
	        </div>
	        <button type="submit" name="register" value="register">Register</button>
	    </form>
	</body>

	</html>