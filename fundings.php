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
   <title>Funding Rounds</title>
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
;";

   $result = pg_query($db, $sql);
   if (!$result) {
      echo pg_last_error($db);
      exit;
   }
   ?>
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
            <td>
               <button id="myBtn"><?php echo $row[9] . " ";echo $row[8]; ?></button>

               <!-- The Modal -->
               <div id="myModal" class="modal">

                  <!-- Modal content -->
                  <div class="modal-content">
                     <span class="close">&times;</span>
                     <p><?php echo $row[20]; ?></p>
                  </div>

               </div>

            </td>
			<td><?php echo $row[25]; ?></a></td>
         </tr>
      <?php
      } ?>
   </table>
   <?php
   pg_close($db);
   ?>
   <script>
      // Get the modal
      var modal = document.getElementById("myModal");

      // Get the button that opens the modal
      var btn = document.getElementById("myBtn");

      // Get the <span> element that closes the modal
      var span = document.getElementsByClassName("close")[0];

      // When the user clicks the button, open the modal 
      btn.onclick = function() {
         modal.style.display = "block";
      }

      // When the user clicks on <span> (x), close the modal
      span.onclick = function() {
         modal.style.display = "none";
      }

      // When the user clicks anywhere outside of the modal, close it
      window.onclick = function(event) {
         if (event.target == modal) {
            modal.style.display = "none";
         }
      }
   </script>
</body>

</html>