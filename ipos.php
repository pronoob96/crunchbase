<?php

session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
   header("location: login.php");
   exit;
}

require "db.php";

$field = 'ipos.public_at';
$sort = 'DESC';
if (isset($_GET['sorting'])) {
   if ($_GET['sorting'] == 'DESC') {
      $sort = 'DESC';
   } else {
      $sort = 'ASC';
   }

   if ($_GET['field'] == 'name') {
      $field = "name";
   } elseif ($_GET['field'] == 'stock_symbol') {
      $field = "stock_symbol";
   } elseif ($_GET['field'] == 'raised_amount') {
      $field = "raised_amount";
   } elseif ($_GET['field'] == 'public_at') {
      $field = "public_at";
   }
}

$sql = "Select nm.name,ipos.* from ipos
  left join
  (select id,name from objects) as nm
  on ipos.object_id = nm.id
  order by $field $sort NULLS last;
  ";

if (isset($_GET['sorting'])) {
   if ($_GET['sorting'] == 'ASC') {
      $sort = 'DESC';
   } else {
      $sort = 'ASC';
   }
}

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

   <title>IPOs</title>

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
                                       <th><a href="ipos.php?sorting=<?php echo $sort ?>&field=name">Company Name</a></th>
                                       <th><a href="ipos.php?sorting=<?php echo $sort ?>&field=stock_symbol">Stock Listed</a></th>
                                       <th><a href="ipos.php?sorting=<?php echo $sort ?>&field=raised_amount">Money Raised</a></th>
                                       <th><a href="ipos.php?sorting=<?php echo $sort ?>&field=public_at">Public at</a></th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                    // output data of each row
                                    while ($row = pg_fetch_row($result)) { ?>

                                       <tr>
                                          <td><a href="company.php?id=<?php echo $row[3]; ?>"><?php echo $row[0]; ?></a></td>
                                          <td><?php echo $row[9]; ?></td>
                                          <td><a href="<?php echo $row[10]; ?>"><?php echo $row[7] . " ";
                                                                                 echo $row[6]; ?></a></td>
                                          <td>
                                             <button id="myBtn"><?php echo $row[8]; ?></button>

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
</body>

</html>