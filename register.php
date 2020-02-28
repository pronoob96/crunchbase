<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: acquisitions.php");
    exit;
}

include('db.php');

if (isset($_POST['register'])) {

    $username = $_POST['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    

    $query1 = pg_query($db, "SELECT * FROM users WHERE e_mail='$email'");
    $query2 = pg_query($db, "SELECT * FROM users WHERE username='$username'");

    if (pg_numrows($query1) > 0) {
        echo '<p class="error">The email address is already registered!</p>';
    }

    if (pg_numrows($query2) > 0) {
        echo '<p class="error">The username is already registered!</p>';
    }

    if (pg_numrows($query1) == 0 && pg_numrows($query2) == 0) {
        $result = pg_query($db, "INSERT INTO users(name,username,password,e_mail,created_at,last_login) VALUES ('$name','$username',hashcode('$password'),'$email',now()::timestamp,now()::timestamp)");

        if ($result) {
            echo '<p class="success">Your registration was successful!</p>';
            $sql = "SELECT * FROM users WHERE username='$username'";
            $result = pg_query($db, $sql);
            $row = pg_fetch_row($result);
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $row[0];
            $_SESSION['username'] = $row[1];
            $_SESSION['email'] = $row[3];
            $_SESSION['name'] = $row[4];
            $_SESSION['created_at'] = $row[5];
            $_SESSION['last_login'] = $row[6];
            header("location: acquisitions.php");
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
            <label>Name</label>
            <input type="text" name="name" required />
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