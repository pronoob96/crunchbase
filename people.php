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
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title><?php echo $row[6]; ?></title>
</head>

<body>

  <img style="border-radius: 6px;" src="<?php echo $row[8] ?>" />

  About:
  <?php echo $row[7];
  pg_close($db);
  ?>

</body>

</html>