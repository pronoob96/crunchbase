<?php
session_start();
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true){
    header("location: acquisitions.php");
    exit;
}

include('db.php');

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];


    $sql = "SELECT * FROM users WHERE username='$username' and password=hashcode('$password')";

    $result = pg_query($db, $sql);



    if (pg_numrows($result) == 0) {
        echo '<p class="error">Username password combination is wrong!</p>';
    } else {
        $row = pg_fetch_row($result);
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $row[0];
        $_SESSION['username'] = $row[1];
        $_SESSION['email'] = $row[3];
        $_SESSION['name'] = $row[4];
        $_SESSION['created_at'] = $row[5];
        $_SESSION['last_login'] = $row[6];
        $sql = "UPDATE users SET last_login = now()::timestamp WHERE id = '$row[0]'";
        $result = pg_query($db, $sql);
        echo '<p class="success">Congratulations, you are logged in!</p>';
        header("location: acquisitions.php");
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login Page</title>
</head>

<body>
    <form method="post" action="" name="signin-form">
        <div class="form-element">
            <label>Username</label>
            <input type="text" name="username" pattern="[a-zA-Z0-9]+" required />
        </div>
        <div class="form-element">
            <label>Password</label>
            <input type="password" name="password" required />
        </div>
        <button type="submit" name="login" value="login">Log In</button>
    </form>
</body>

</html>