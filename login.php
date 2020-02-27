<?php

include('db.php');
session_start();

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];


    $sql = "SELECT * FROM users WHERE username='$username'";

    $result = pg_query($db, $sql);



    if (!$result) {
        echo '<p class="error">Username password combination is wrong!</p>';
    } else {
        $row = pg_fetch_row($result);
        if (password_verify($password, $row[3])) {
            $_SESSION['user_id'] = $row[0];
            echo '<p class="success">Congratulations, you are logged in!</p>';
        } else {
            echo '<p class="error">Username password combination is wrong!!!</p>';
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