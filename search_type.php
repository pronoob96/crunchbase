<?php

session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require "db.php";

if (isset($_GET['pageno'])) {
    $pageno = $_GET['pageno'];
 } else {
    $pageno = 1;
 }
 
 $no_of_records_per_page = 100;
 $offset = ($pageno - 1) * $no_of_records_per_page;

$name = $_GET['fname'];
$type = $_GET['dropdown'];

$field = 'name';
$sort = 'DESC';
if (isset($_GET['sorting'])) {
    if ($_GET['sorting'] == 'DESC') {
       $sort = 'DESC';
    } else {
       $sort = 'ASC';
    }
 
    if ($_GET['field'] == 'name') {
       $field = "name";
    } elseif ($_GET['field'] == 'entity_type') {
       $field = "entity_type";
    } elseif ($_GET['field'] == 'founded_at') {
       $field = "founded_at";
    }
 }

$count_sql = "SELECT count(*) from obj_view where normalized_name LIKE '%$name%' and category_code = '$type';";

$count_result = pg_query($db, $count_sql);
$total_rows = pg_fetch_row($count_result)[0];
$total_pages = ceil($total_rows / $no_of_records_per_page);

$sql = "SELECT * from obj_view where normalized_name LIKE '%$name%' and category_code = '$type'
        order by $field $sort nulls last
        OFFSET $offset
        LIMIT $no_of_records_per_page;";

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
            <th><a href="search_type.php?fname=<?php echo $name;?>&dropdown=<?php echo $type;?>&sorting=<?php if ($sort == 'DESC') {
                                                                                 echo 'ASC';
                                                                              } else {
                                                                                 echo 'DESC';
                                                                              } ?>&field=name">Name</a></th>
            <th><a href="search_type.php?fname=<?php echo $name;?>&dropdown=<?php echo $type;?>&sorting=<?php if ($sort == 'DESC') {
                                                                                 echo 'ASC';
                                                                              } else {
                                                                                 echo 'DESC';
                                                                              } ?>&field=entity_type">Type</a></th>
            <th><a href="search_type.php?fname=<?php echo $name;?>&dropdown=<?php echo $type;?>&sorting=<?php if ($sort == 'DESC') {
                                                                                 echo 'ASC';
                                                                              } else {
                                                                                 echo 'DESC';
                                                                              } ?>&field=founded_at">Founded At</a></th>
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

<ul class="pagination">
      <li><a href="?fname=<?php echo $name;?>&dropdown=<?php echo $type;?>&sorting=<?php echo $sort ?>&field=<?php echo $field ?>&pageno=1">First</a></li>
      <li class="<?php if ($pageno <= 1) {
                     echo 'disabled';
                  } ?>">
         <a href="?fname=<?php echo $name;?>&dropdown=<?php echo $type;?>&sorting=<?php if ($pageno <= 1) {
                              echo $sort; ?>&field=<?php echo $field; ?>&pageno=1<?php } else {
                                                                                 echo $sort; ?>&field=<?php echo $field; ?>&pageno=<?php echo ($pageno - 1);
                                                                              } ?>"> Prev</a> </li>
      <li class="<?php if ($pageno >= $total_pages) {
                     echo 'disabled';
                  } ?>">
         <a href="?fname=<?php echo $name;?>&dropdown=<?php echo $type;?>&sorting=<?php if ($pageno >= $total_pages) {
                              echo $sort ?>&field=<?php echo $field ?>&pageno=<?php echo ($total_pages);
                                                                           } else {
                                                                              echo $sort ?>&field=<?php echo $field ?>&pageno=<?php echo ($pageno + 1);
                                                                           } ?>"> Next</a> </li> 
      <li><a href="?fname=<?php echo $name;?>&dropdown=<?php echo $type;?>&sorting=<?php echo $sort ?>&field=<?php echo $field ?>&pageno=<?php echo $total_pages; ?>">Last</a></li>
   </ul>

</body>

</html>