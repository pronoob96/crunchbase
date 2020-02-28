<?php
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
} else {
?>
    <!DOCTYPE html>
    <html>

    <head>
    </head>

    <body>

        <?php
        $q = $_GET['q'];
        $user = $_SESSION['username'];
        require "db.php";
        $sql = "SELECT * FROM follows WHERE username='$user' AND company_id='$q'";
        $result = pg_query($db, $sql);

        if (pg_numrows($result) == 0) {
            //insert
            $sql = "INSERT INTO follows (username, company_id, followed_at) VALUES ('$user' ,'$q',now()::timestamp)";
            $result = pg_query($db, $sql);
        } else {
            //delete
            $sql = "DELETE FROM follows WHERE username='$user' AND company_id='$q'";
            $result = pg_query($db, $sql);
        }

        ?>
    </body>

    </html>
<?php

    #header("location: login.php");
}
?>