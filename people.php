<?php

session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  exit;
}

require "db.php";
$cid = $_GET['id'];
$sql = "SELECT people.*,obj_view.name,obj_view.overview, obj_view.logo_url
			from
			(SELECT * from people 
				where object_id='$cid') AS people,obj_view
			where obj_view.id = people.object_id";

$result = pg_query($db, $sql);
if (!$result) {
  echo pg_last_error($db);
  exit;
}
$row = pg_fetch_row($result);
$sql1 = "SELECT object_id,degree_type,subject,institution,graduated_at from degrees
				where object_id = '$cid'";

$result1 = pg_query($db, $sql1);
if(!$result1) {
    echo pg_last_error($db);
    exit;
}
$row1 = pg_fetch_row($result1);

$sql2 = " SELECT nm.name,relationships.* FROM relationships
 LEFT JOIN
 (select name,id from obj_view) as nm
 ON relationships.relationship_object_id = nm.id
 WHERE relationships.person_object_id = '$cid';";

$result2 = pg_query($db, $sql2);
if(!$result2) {
    echo pg_last_error($db);
    exit;
}
	
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
       <?php
        if($row1 == true)
        {echo "<tr>";
            echo"<th>Degree</th>";
            echo"<th>Subject</th>";
            echo"<th>Institution</th>";
			echo"<th>Class of</th>";
        echo"</tr>";?>
        <?php
        // output data of each row
        while ($row1 = pg_fetch_row($result1)) { ?>

            <tr>
                <td><?php echo $row1[1]; ?></a></td>
                <td> <?php echo $row1[2]; ?> </td>
				<td> <?php echo $row1[3]; ?> </td>
				<td> <?php echo $row1[4]; ?> </td>
            </tr>
        <?php
        } }?>
    </table>

    <table class=\"table\">
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <?php
        // output data of each row
        while ($row2 = pg_fetch_row($result2)) { ?>

            <tr>
                <td> <?php echo $row2[9];echo " at "; ?> </td>
                <td> <?php echo $row2[0];echo " from "; ?> </td>
                <td> <?php echo $row2[5];echo " till "; ?> </td>
                <td> <?php echo $row2[6]; ?> </td>
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