<?php
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
} else {

    require "db.php";

    if (isset($_POST['new_user'])) {

        $new_username = $_POST['new_username'];
        $old_username = $_SESSION['username'];

        $query2 = pg_query($db, "SELECT * FROM users WHERE username='$new_username'");

        if (pg_numrows($query2) > 0) {
            echo '<p class="error">The username is already registered!</p>';
        }

        if (pg_numrows($query2) == 0) {
            $result = pg_query($db, "UPDATE users SET username='$new_username' WHERE username='$old_username'");
            $_SESSION['username'] = $new_username;
            echo $_SESSION['username'];
        }
    }

    if (isset($_POST['del_user'])) {

        $username = $_SESSION['username'];

        $result = pg_query($db, "DELETE FROM users WHERE username='$username'");
        if (session_destroy()) {
            header("Location: login.php");
        }
    }

    if (isset($_POST['change_pass'])) {

        $username = $_SESSION['username'];
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $result = pg_query($db, "SELECT * FROM users WHERE username='$username' and password=hashcode('$old_password')");

        if (pg_numrows($result) == 0) {
            echo '<p class="error">Wrong password</p>';
        }
        else if($confirm_password != $new_password){
            echo '<p class="error">Password did not match</p>';
        }
        else{
            $result = pg_query($db, "UPDATE users SET password=hashcode('$new_password') WHERE username='$username'");
            echo '<p class="error">Password Updated</p>';
        }

    }

?>
    <!DOCTYPE html>
    <html>

    <head>
    </head>

    <body>
        <form method="post" action="" name="update-form">
            <div class="form-element">
                <label>New Username</label>
                <input type="text" name="new_username" required />
            </div>
            <button type="submit" name="new_user" value="new_user">Change username</button>
        </form>
        <form method="post" action="" name="delete-form">
            <button type="submit" name="del_user" value="del_user">Delete Account</button>
        </form>
        <form method="post" action="" name="change-form">
            <div class="form-element">
                <label>Old Password</label>
                <input type="password" name="old_password" required />
            </div>
            <div class="form-element">
                <label>New Password</label>
                <input type="password" name="new_password" required />
            </div>
            <div class="form-element">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required />
            </div>
            <button type="submit" name="change_pass" value="change_pass">Change Password</button>
        </form>
    </body>

    </html>
<?php

    #header("location: login.php");
}
?>