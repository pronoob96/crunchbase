<?php

session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require "db.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <title>Ventures</title>
   <style>
      body {
         font-family: Arial, Helvetica, sans-serif;
      }

      /* The Modal (background) */
      .modal {
         display: none;
         /* Hidden by default */
         position: fixed;
         /* Stay in place */
         z-index: 1;
         /* Sit on top */
         padding-top: 100px;
         /* Location of the box */
         left: 0;
         top: 0;
         width: 100%;
         /* Full width */
         height: 100%;
         /* Full height */
         overflow: auto;
         /* Enable scroll if needed */
         background-color: rgb(0, 0, 0);
         /* Fallback color */
         background-color: rgba(0, 0, 0, 0.4);
         /* Black w/ opacity */
      }

      /* Modal Content */
      .modal-content {
         background-color: #fefefe;
         margin: auto;
         padding: 20px;
         border: 1px solid #888;
         width: 80%;
      }

      /* The Close Button */
      .close {
         color: #aaaaaa;
         float: right;
         font-size: 28px;
         font-weight: bold;
      }

      .close:hover,
      .close:focus {
         color: #000;
         text-decoration: none;
         cursor: pointer;
      }
   </style>
</head>

<body>
   <?php
   $sql = "SELECT q1.investor_object_id, q1.num ,nm.name, nm.last_investment_at from
(SELECT investor_object_id,count(distinct funded_object_id) as num from investments
group by investor_object_id) as q1
LEFT JOIN 
(SELECT id, name, last_investment_at FROM objects) AS nm 
ON nm.id = q1.investor_object_id
order by q1.num DESC;";

   $result = pg_query($db, $sql);
   if (!$result) {
      echo pg_last_error($db);
      exit;
   }
   ?>
   <table class=\"table\">
        <tr>
            <th>Organization Name</th>
            <th>Number of Investments</th>
            <th>Last investment at</th>
        </tr>
        <?php
        // output data of each row
        while ($row3 = pg_fetch_row($result)) { ?>

            <tr>
                
                <td><a href="company.php?id=<?php echo $row3[0]; ?>"><?php echo $row3[2]; ?></a></td>
                <td><?php echo $row3[1]; ?></td>
                <td><?php echo $row3[3] ;?></td>
            </tr>
        <?php
        } ?>
    
    </table>
  
   <?php
   pg_close($db);
   ?>
   
</body>

</html>