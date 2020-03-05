<?php

session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require "db.php";
$name = $_GET['fname'];
$type = $_GET['dropdown'];
$sql = "SELECT * from objects where normalized_name LIKE '%$name%' and category_code = '$type'";

$result = pg_query($db, $sql);
if (!$result) {
    echo pg_last_error($db);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Search Results</title>
</head>

<body>
    <table class=\"table\">
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Founded At</th>
        </tr>
        <?php
        // output data of each row
        while ($row = pg_fetch_row($result)) { ?>

            <tr>
                <?php if ($row[1] == 'Company') { ?>
                    <td><a href="company.php?id=<?php echo $row[0]; ?>"><?php echo $row[4]; ?></a></td>
                <?php } else if($row[1]== 'Person') { ?>
                    <td><a href="people.php?id=<?php echo $row[0]; ?>"><?php echo $row[4]; ?></a></td>
                <?php } else { ?>
                    <td><a href="ventures.php?id=<?php echo $row[0]; ?>"><?php echo $row[4]; ?></a></td>
                <?php } ?>
                <td><?php echo $row[1]; ?></a></td>
                <td> <?php echo $row[9]; ?> </td>
            </tr>
        <?php 
        } ?>
    </table>
    <?php
    pg_close($db);
    ?>

</body>

</html>