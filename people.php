<?php

session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  exit;
}

require "db.php";
$cid = $_GET['id'];
$sql = "SELECT people.*,objects.name,objects.overview, objects.logo_url
			from
			(SELECT * from people 
				where object_id='$cid') as people,objects
			where objects.id = people.object_id";

$result = pg_query($db, $sql);
if (!$result) {
  echo pg_last_error($db);
  exit;
}
$row = pg_fetch_row($result)
$sql1 = "Select object_id,degree_type,subject,institution,graduated_at from degrees
				where object_id = '$cid'";
   
$result1 = pg_query($db, $sql1);
if(!$result1) {
    echo pg_last_error($db);
    exit;
}
$row1 = pg_fetch_row($result1)
	

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title><?php echo $row[6]; ?></title>
</head>

<body>
      
            <img  style="border-radius: 6px;" src="<?php echo $row[8] ?>"/>
        
		<h3> About:</h3>
		<table class=\"table\">
        <tr>
            <th>Degree</th>
            <th>Subject</th>
            <th>Institution</th>
			<th>Class of</th>
        </tr>
        <?php
        // output data of each row
        while ($row = pg_fetch_row($result)) { ?>

            <tr>
                <td><?php echo $row[1]; ?></a></td>
                <td> <?php echo $row[2]; ?> </td>
				<td> <?php echo $row[3]; ?> </td>
				<td> <?php echo $row[4]; ?> </td>
            </tr>
        <?php
        } ?>
    </table>
		
		
        <p>
		<?php echo $row[7];
        pg_close($db);
        ?>
		</p>
   </body>
</html>
