<?php

session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  exit;
}

require "db.php";
$cid = $_GET['id'];
$sql = "SELECT distinct funded_object_id, nm.name, nm.founded_at, nm.tag_list, nm.category_code 
FROM investments
LEFT JOIN (Select id, name, founded_at, tag_list, category_code from obj_view) as nm
ON investments.funded_object_id = nm.id
where investor_object_id = '$cid'
order by nm.name";

$result = pg_query($db, $sql);
if (!$result) {
  echo pg_last_error($db);
  exit;
}	

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Invested Companies:</title>
</head>

<body>
      
		<table class=\"table\">
        <tr>
            <th>Name</th>
            <th>Founded</th>
            <th>Industry Sector</th>
        </tr>
        <?php
        // output data of each row
        while ($row1 = pg_fetch_row($result)) { ?>

            <tr>
                <td><a href="company.php?id=<?php echo $row1[0]; ?>"><?php echo $row1[1]; ?></a></td>
                <td> <?php echo $row1[2]; ?> </td>
				<td> <?php echo $row1[4]; ?> </td>
            </tr>
        <?php
        } ?>
    </table>
		
       <?php		
        pg_close($db);
        ?>
		</p>
   </body>
</html>