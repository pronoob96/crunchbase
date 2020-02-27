<?php
require "db.php";
$cid = $_GET['id'];
$sql = "SELECT * from objects where id='$cid'";
$sql1 = "SELECT objects.id,ids.pid,objects.name
			from (Select people.object_id as iid,people.id as pid 
                    from (SELECT name from objects 
				    where id = '$cid') as ni,people
			where people.affiliation_name=ni.name) as ids,objects
		where objects.id = ids.iid
		order by ids.pid asc";

$result = pg_query($db, $sql);
if (!$result) {
    echo pg_last_error($db);
    exit;
}
$row = pg_fetch_row($result);

$result1 = pg_query($db, $sql1);
if (!$result1) {
    echo pg_last_error($db);
    exit;
}
$row1 = pg_fetch_row($result1)
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo $row[4]; ?></title>
</head>

<body>
    <table class=\"table\">
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Founded At</th>
        </tr>

        <img style="border-radius: 6px;" src="<?php echo $row[14] ?>" />
        <tr>
            <td><?php echo $row[4]; ?></td>
            <td><?php echo $row[8]; ?></td>
            <td> <?php echo $row[9]; ?> </td>
        </tr>

    </table>

    <?php echo $row[19];
    pg_close($db);
    ?>

    Key People:
    <?php
    // output data of each row
    while ($row1 = pg_fetch_row($result1)) {
        echo $row1[2];
    } ?>

</body>

</html>