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
   <title>IPOs</title>
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
  
$sql = "Select nm.name,ipos.* from ipos
left join
(select id,name from objects) as nm
on ipos.object_id = nm.id
order by ipos.public_at desc NULLS last;
";

   $result = pg_query($db, $sql);
   if (!$result) {
      echo pg_last_error($db);
      exit;
   }
   ?>
   <table class=\"table\">
      <tr>
         <th>Company Name</th>
         <th>Stock Listed</th>
         <th>Money Raised</th>
		   <th>Public at </th>
      </tr>
      <?php
      // output data of each row
      while ($row = pg_fetch_row($result)) { ?>

         <tr>
            <td><a href="company.php?id=<?php echo $row[3]; ?>"><?php echo $row[0]; ?></a></td>
            <td><?php echo $row[9]; ?></td>
            <td><a href="<?php echo $row[10]; ?>"><?php echo $row[7] . " ";echo $row[6];?></a></td>
            <td>
               <button id="myBtn"><?php echo $row[8];?></button>

               <!-- The Modal -->
               <div id="myModal" class="modal">

                  <!-- Modal content -->
                  <div class="modal-content">
                     <span class="close">&times;</span>
                     <p><?php echo $row[11]; ?></p>
                  </div>

               </div>

            </td>

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