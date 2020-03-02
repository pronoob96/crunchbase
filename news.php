<?php

session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require "db.php";

$username = $_SESSION['username'];

$sql = "Select nm.name,milestones.* from milestones,
(select objects.id as id,objects.name as name
from objects,follows
where follows.username='$username' and objects.id=follows.company_id
) as nm
where milestones.object_id = nm.id
order by milestones.milestone_at desc NULLS last";

$result = pg_query($db, $sql);
if (!$result) {
    echo pg_last_error($db);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>News</title>
</head>

<body>
    <table class=\"table\">
        <tr>
            <th>News</th>
        </tr>
        <?php
        // output data of each row
        while ($row = pg_fetch_row($result)) { ?>

            <tr>
                <td><a href="<?php echo $row[6]; ?>"><?php echo $row[5]; ?></a></td>
            </tr>
        <?php
        } ?>

    </table>
</body>
<?php
pg_close($db);
?>

</html>