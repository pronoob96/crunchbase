<?php

session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
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

$field = 'q1.funded_at';
$sort = 'DESC';
if (isset($_GET['sorting'])) {
   if ($_GET['sorting'] == 'DESC') {
      $sort = 'DESC';
   } else {
      $sort = 'ASC';
   }

   if ($_GET['field'] == 'name') {
      $field = "name";
   } elseif ($_GET['field'] == 'funding_round_type') {
      $field = "funding_round_type";
   } elseif ($_GET['field'] == 'raised_amount_usd') {
      $field = "raised_amount_usd";
   } elseif ($_GET['field'] == 'investors') {
      $field = "investors";
   }
}

$count_sql = "Select count(*) from
               (Select nm.name,funding_rounds.* from funding_rounds
               left join
               (select id,name from obj_view) as nm
               on nm.id = funding_rounds.object_id) as q1
               left join
               (Select t.funding_round_id,STRING_AGG(t.iname,', ') as investors
               from (Select nm.name as iname,investments.* from 
               (select id,name from obj_view) as nm,investments
               where nm.id = investments.investor_object_id) as t
               group by t.funding_round_id) as q2
               on q1.funding_round_id = q2.funding_round_id
               ";

$count_result = pg_query($db, $count_sql);
$total_rows = pg_fetch_row($count_result)[0];
$total_pages = ceil($total_rows / $no_of_records_per_page);


$sql = "Select q1.*,q2.* from
(Select nm.name,funding_rounds.* from funding_rounds
left join
(select id,name from obj_view) as nm
on nm.id = funding_rounds.object_id) as q1
left join
(Select t.funding_round_id,STRING_AGG(t.iname,', ') as investors
from (Select nm.name as iname,investments.* from 
(select id,name from obj_view) as nm,investments
where nm.id = investments.investor_object_id) as t
group by t.funding_round_id) as q2
on q1.funding_round_id = q2.funding_round_id
order by $field $sort NULLS last
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

   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">

   <title>Funding Rounds</title>

   <!-- ================= Favicon ================== -->


   <!-- Styles -->
   <link href="assets/css/lib/weather-icons.css" rel="stylesheet" />
   <link href="assets/css/lib/owl.carousel.min.css" rel="stylesheet" />
   <link href="assets/css/lib/owl.theme.default.min.css" rel="stylesheet" />
   <link href="assets/css/lib/font-awesome.min.css" rel="stylesheet">
   <link href="assets/css/lib/themify-icons.css" rel="stylesheet">
   <link href="assets/css/lib/menubar/sidebar.css" rel="stylesheet">
   <link href="assets/css/lib/bootstrap.min.css" rel="stylesheet">

   <link href="assets/css/lib/helper.css" rel="stylesheet">
   <link href="assets/css/style.css" rel="stylesheet">
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

   <div class="header">
      <div class="container-fluid">
         <div class="row">
            <div class="col-lg-12">
               <div class="float-right">
                  <ul>
                     <li class="header-icon dib"><span class="user-avatar"><?php echo $_SESSION['name'] ?> <i class="ti-angle-down f-s-10"></i></span>
                        <div class="drop-down dropdown-profile">
                           <div class="dropdown-content-body">
                              <ul>
                                 <li><a href="user_settings.php"><i class="ti-settings"></i> <span>Setting</span></a></li>
                                 <li><a href="logout.php"><i class="ti-power-off"></i> <span>Logout</span></a></li>
                              </ul>
                           </div>
                        </div>
                     </li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
   </div>
   
   <div class="content-wrap">
      <div class="main">
         <div class="container-fluid">
            <!-- /# row -->
            <section id="main-content">
               <!-- /# row -->
               <div class="row">
                  <div class="col-lg-12">
                     <div class="card">
                        <div class="card-title">
                           <h4>Acquisitions </h4>
                        </div>
                        <div class="card-body">
                           <div class="table-responsive">
                              <table class="table">
                                 <thead>
                                    <tr>
                                       <th><a href="fundings.php?sorting=<?php if ($sort == 'DESC') {
                                                                                 echo 'ASC';
                                                                              } else {
                                                                                 echo 'DESC';
                                                                              } ?>&field=name">Organization Name</a></th>
                                       <th><a href="fundings.php?sorting=<?php if ($sort == 'DESC') {
                                                                                 echo 'ASC';
                                                                              } else {
                                                                                 echo 'DESC';
                                                                              } ?>&field=funding_round_type">Transaction Name</a></th>
                                       <th><a href="fundings.php?sorting=<?php if ($sort == 'DESC') {
                                                                                 echo 'ASC';
                                                                              } else {
                                                                                 echo 'DESC';
                                                                              } ?>&field=raised_amount_usd">Money Raised</a></th>
                                       <th><a href="fundings.php?sorting=<?php if ($sort == 'DESC') {
                                                                                 echo 'ASC';
                                                                              } else {
                                                                                 echo 'DESC';
                                                                              } ?>&field=investors">Investors</a></th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                    // output data of each row
                                    while ($row = pg_fetch_row($result)) { ?>

                                       <tr>
                                          <td><a href="company.php?id=<?php echo $row[3]; ?>"><?php echo $row[0]; ?></a></td>
                                          <td><?php echo $row[5]; ?></td>
                                          <td>
                                             <button id="myBtn"><?php echo $row[9] . " ";
                                                                  echo $row[8]; ?></button>

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
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- /# column -->
               </div>
            </section>
         </div>
      </div>
   </div>

   <ul class="pagination">
      <li><a href="?sorting=<?php echo $sort ?>&field=<?php echo $field ?>&pageno=1">First</a></li>
      <li class="<?php if ($pageno <= 1) {
                     echo 'disabled';
                  } ?>">
         <a href="?sorting=<?php if ($pageno <= 1) {
                              echo $sort; ?>&field=<?php echo $field; ?>&pageno=1<?php } else {
                                                                                 echo $sort; ?>&field=<?php echo $field; ?>&pageno=<?php echo ($pageno - 1);
                                                                              } ?>"> Prev</a> </li>
      <li class="<?php if ($pageno >= $total_pages) {
                     echo 'disabled';
                  } ?>">
         <a href="?sorting=<?php if ($pageno >= $total_pages) {
                              echo $sort ?>&field=<?php echo $field ?>&pageno=<?php echo ($total_pages);
                                                                           } else {
                                                                              echo $sort ?>&field=<?php echo $field ?>&pageno=<?php echo ($pageno + 1);
                                                                           } ?>"> Next</a> </li> 
      <li><a href=" ?sorting=<?php echo $sort ?>&field=<?php echo $field ?>&pageno=<?php echo $total_pages; ?>">Last</a></li>
   </ul>

   <!-- jquery vendor -->
   <script src="assets/js/lib/jquery.min.js"></script>
   <script src="assets/js/lib/jquery.nanoscroller.min.js"></script>
   <!-- nano scroller -->
   <script src="assets/js/lib/menubar/sidebar.js"></script>
   <script src="assets/js/lib/preloader/pace.min.js"></script>
   <!-- sidebar -->
   <script src="assets/js/lib/bootstrap.min.js"></script>

   <!-- bootstrap -->

   <script src="assets/js/lib/circle-progress/circle-progress.min.js"></script>
   <script src="assets/js/lib/circle-progress/circle-progress-init.js"></script>

   <script src="assets/js/lib/morris-chart/raphael-min.js"></script>
   <script src="assets/js/lib/morris-chart/morris.js"></script>
   <script src="assets/js/lib/morris-chart/morris-init.js"></script>

   <!--  flot-chart js -->
   <script src="assets/js/lib/flot-chart/jquery.flot.js"></script>
   <script src="assets/js/lib/flot-chart/jquery.flot.resize.js"></script>
   <script src="assets/js/lib/flot-chart/flot-chart-init.js"></script>
   <!-- // flot-chart js -->


   <script src="assets/js/lib/vector-map/jquery.vmap.js"></script>
   <!-- scripit init-->
   <script src="assets/js/lib/vector-map/jquery.vmap.min.js"></script>
   <!-- scripit init-->
   <script src="assets/js/lib/vector-map/jquery.vmap.sampledata.js"></script>
   <!-- scripit init-->
   <script src="assets/js/lib/vector-map/country/jquery.vmap.world.js"></script>
   <!-- scripit init-->
   <script src="assets/js/lib/vector-map/country/jquery.vmap.algeria.js"></script>
   <!-- scripit init-->
   <script src="assets/js/lib/vector-map/country/jquery.vmap.argentina.js"></script>
   <!-- scripit init-->
   <script src="assets/js/lib/vector-map/country/jquery.vmap.brazil.js"></script>
   <!-- scripit init-->
   <script src="assets/js/lib/vector-map/country/jquery.vmap.france.js"></script>
   <!-- scripit init-->
   <script src="assets/js/lib/vector-map/country/jquery.vmap.germany.js"></script>
   <!-- scripit init-->
   <script src="assets/js/lib/vector-map/country/jquery.vmap.greece.js"></script>
   <!-- scripit init-->
   <script src="assets/js/lib/vector-map/country/jquery.vmap.iran.js"></script>
   <!-- scripit init-->
   <script src="assets/js/lib/vector-map/country/jquery.vmap.iraq.js"></script>
   <!-- scripit init-->
   <script src="assets/js/lib/vector-map/country/jquery.vmap.russia.js"></script>
   <!-- scripit init-->
   <script src="assets/js/lib/vector-map/country/jquery.vmap.tunisia.js"></script>
   <!-- scripit init-->
   <script src="assets/js/lib/vector-map/country/jquery.vmap.europe.js"></script>
   <!-- scripit init-->
   <script src="assets/js/lib/vector-map/country/jquery.vmap.usa.js"></script>
   <!-- scripit init-->
   <script src="assets/js/lib/vector-map/vector.init.js"></script>

   <script src="assets/js/lib/weather/jquery.simpleWeather.min.js"></script>
   <script src="assets/js/lib/weather/weather-init.js"></script>
   <script src="assets/js/lib/owl-carousel/owl.carousel.min.js"></script>
   <script src="assets/js/lib/owl-carousel/owl.carousel-init.js"></script>
   <script src="assets/js/scripts.js"></script>
   <!-- scripit init-->
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
   <?php
   pg_close($db);
   ?>
</body>

</html>