<?php

session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require "db.php";
$sql = "Select q1.*,q2.* from
(Select nm.name,funding_rounds.* from funding_rounds
left join
(select id,name from objects) as nm
on nm.id = funding_rounds.object_id) as q1
left join
(Select t.funding_round_id,STRING_AGG(t.iname,', ') as investors
from (Select nm.name as iname,investments.* from 
(select id,name from objects) as nm,investments
where nm.id = investments.investor_object_id) as t
group by t.funding_round_id) as q2
on q1.funding_round_id = q2.funding_round_id
order by q1.funded_at DESC NULLS last
LIMIT 10;";

$result = pg_query($db, $sql);
if (!$result) {
    echo pg_last_error($db);
    exit;
}
	
$sql1 = "select q1.acquiring_object_id,q2.acquired_object_id,q1.By,q2.OF,q1.price_amount,q1.price_currency_code,q2.acquired_at,q2.source_description from
      (select acquisitions.id,acquiring_object_id,normalized_name as By,price_amount,price_currency_code
       from acquisitions
      left join objects on objects.id=acquisitions.acquiring_object_id
      order by id) as q1 
      left join
      (select acquisitions.id,acquired_object_id,normalized_name as OF,acquired_at,source_description
       from acquisitions
      left join objects on objects.id=acquisitions.acquired_object_id
      order by id) as q2
      on q1.id=q2.id
      order by acquired_at desc NULLS last,price_amount desc NULLS last LIMIT 10;";

   $result1 = pg_query($db, $sql1);
   if (!$result1) {
      echo pg_last_error($db);
      exit;
   }

$sql2 = "Select nm.name,ipos.* from ipos
left join
(select id,name from objects) as nm
on ipos.object_id = nm.id
order by ipos.public_at desc NULLS last
LIMIT 10;";

$result2 = pg_query($db, $sql2);
if (!$result2) {
    echo pg_last_error($db);
    exit;
}
	

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Main Page</title>
</head>

<body>
	<form id="searchForm" action="search_res.php" method="get" autocomplete="off">
        <h1>Search!</h1>
        <div class="row">
            <div class="col-sm-6">
                <h5><b>Search For Anything</b></h5>
                <input type="text" placeholder="Search" name="fname" required><br>
            </div>
        </div>
    </form>

	<table class=\"table\">
      <tr>
         <th>Acquiree Name</th>
         <th>Acquirer Name</th>
         <th>Acquired At</th>
         <th>Price</th>
      </tr>
      <?php
      // output data of each row
      while ($row1 = pg_fetch_row($result1)) { ?>

         <tr>
            <td><a href="company.php?id=<?php echo $row1[0]; ?>"><?php echo $row1[2]; ?></a></td>
            <td><a href="company.php?id=<?php echo $row1[1]; ?>"><?php echo $row1[3]; ?></a></td>
            <td> <?php echo $row1[6]; ?> </td>
     
			      <td> <?php echo $row1[5] . " "; echo $row1[4]; ?> </td>
         </tr>
      <?php
      } ?>
	  
   </table>
   <a href="acquisitions.php">Show all</a>


    <table class=\"table\">
        <tr>
            <th>Organization Name</th>
            <th>Transaction Name</th>
            <th>Money Raised</th>
			      <th>Investors </th>
        </tr>
        <?php
        // output data of each row
        while ($row = pg_fetch_row($result)) { ?>

            <tr>
                
                <td><a href="company.php?id=<?php echo $row[3]; ?>"><?php echo $row[0]; ?></a></td>
                <td><?php echo $row[5]; ?></td>
                <td><?php echo $row[9] . " ";echo $row[8]; ?></td>
                <td><?php echo $row[25]; ?> </td>
            </tr>
        <?php
        } ?>
		
    </table>
    <a href="fundings.php">Show all</a>


  <table class=\"table\">
        <tr>
            <th>Company Name</th>
            <th>Stock Listed in</th>
            <th>Money Raised</th>
        </tr>
        <?php
        // output data of each row
        while ($row2 = pg_fetch_row($result2)) { ?>

            <tr>
                
                <td><a href="company.php?id=<?php echo $row2[3]; ?>"><?php echo $row2[0]; ?></a></td>
                <td><?php echo $row2[9]; ?></td>
                <td> <?php echo $row2[7] . " "; echo $row2[6]; ?> </td>
            </tr>
        <?php
        } ?>
    
    </table>
    <a href="ipos.php">Show all</a>
    <?php
    pg_close($db);
    ?>

</body>

</html>