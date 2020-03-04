<?php

session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
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

$sql3 = "SELECT q1.investor_object_id, q1.num ,nm.name, nm.last_investment_at from
(SELECT investor_object_id,count(distinct funded_object_id) as num from investments
group by investor_object_id) as q1
LEFT JOIN 
(SELECT id, name, last_investment_at FROM objects) AS nm 
ON nm.id = q1.investor_object_id
order by q1.num DESC LIMIT 10;";

$result3 = pg_query($db, $sql3);
if (!$result3) {
    echo pg_last_error($db);
    exit;
}

$username = $_SESSION['username'];

$sql4 = "Select nm.name,milestones.* from milestones,
(select objects.id as id,objects.name as name
from objects,follows
where follows.username='$username' and objects.id=follows.company_id
) as nm
where milestones.object_id = nm.id
order by milestones.milestone_at desc NULLS last LIMIT 10;";

$result4 = pg_query($db, $sql4);
if (!$result4) {
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

    <title>Main Page</title>

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
</head>

<body>

    <div class="header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="float-right">
                        <ul>
                            <li class="header-icon dib"><span class="user-avatar"><?php echo $_SESSION['name']?> <i class="ti-angle-down f-s-10"></i></span>
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
                <div>
                    <form id="searchForm" action="search_res.php" method="get" autocomplete="off">
                        <input type="text" placeholder="Search" name="fname" required>
                        <select name="dropdown" id="dropdown">
                            <option value="Company">Company</option>
                            <option value="Person">People</option>
                            <option value="FinancialOrg">Investors</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                </div>
                <!-- /# row -->
                <section id="main-content">
                    <!-- /# row -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-title">
                                    <h4>Featured Acquisitions </h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Acquiree Name</th>
                                                    <th>Acquirer Name</th>
                                                    <th>Acquired At</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // output data of each row
                                                while ($row1 = pg_fetch_row($result1)) { ?>

                                                    <tr>
                                                        <td><a href="company.php?id=<?php echo $row1[0]; ?>"><?php echo $row1[2]; ?></a></td>
                                                        <td><a href="company.php?id=<?php echo $row1[1]; ?>"><?php echo $row1[3]; ?></a></td>
                                                        <td> <?php echo $row1[6]; ?> </td>

                                                        <td> <?php echo $row1[5] . " ";
                                                                echo $row1[4]; ?> </td>
                                                    </tr>
                                                <?php
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <a href="acquisitions.php">Show all</a>
                            </div>
                            <div class="card">
                                <div class="card-title">
                                    <h4>Featured Ventures </h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Organization Name</th>
                                                    <th>Number of Investments</th>
                                                    <th>Last investment at</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // output data of each row
                                                while ($row3 = pg_fetch_row($result3)) { ?>

                                                    <tr>

                                                        <td><a href="company.php?id=<?php echo $row3[0]; ?>"><?php echo $row3[2]; ?></a></td>
                                                        <td><?php echo $row3[1]; ?></td>
                                                        <td><?php echo $row3[3]; ?></td>
                                                    </tr>
                                                <?php
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <a href="allventures.php">Show all</a>
                            </div>
                            <div class="card">
                                <div class="card-title">
                                    <h4>Top News </h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>News</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // output data of each row
                                                while ($row4 = pg_fetch_row($result4)) { ?>

                                                    <tr>
                                                        <td><a href="<?php echo $row4[6]; ?>"><?php echo $row4[5]; ?></a></td>
                                                    </tr>
                                                <?php
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <a href="news.php">Show all</a>
                            </div>
                        </div>
                        <!-- /# column -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-title">
                                    <h4>Featured Fundings </h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Organization Name</th>
                                                    <th>Transaction Name</th>
                                                    <th>Money Raised</th>
                                                    <th>Investors </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // output data of each row
                                                while ($row = pg_fetch_row($result)) { ?>

                                                    <tr>

                                                        <td><a href="company.php?id=<?php echo $row[3]; ?>"><?php echo $row[0]; ?></a></td>
                                                        <td><?php echo $row[5]; ?></td>
                                                        <td><?php echo $row[9] . " ";
                                                            echo $row[8]; ?></td>
                                                        <td><?php echo $row[25]; ?> </td>
                                                    </tr>
                                                <?php
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <a href="fundings.php">Show all</a>
                            </div>
                            <div class="card">
                                <div class="card-title">
                                    <h4>Top IPOs </h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Company Name</th>
                                                    <th>Stock Listed in</th>
                                                    <th>Money Raised</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // output data of each row
                                                while ($row2 = pg_fetch_row($result2)) { ?>

                                                    <tr>

                                                        <td><a href="company.php?id=<?php echo $row2[3]; ?>"><?php echo $row2[0]; ?></a></td>
                                                        <td><?php echo $row2[9]; ?></td>
                                                        <td> <?php echo $row2[7] . " ";
                                                                echo $row2[6]; ?> </td>
                                                    </tr>
                                                <?php
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <a href="ipos.php">Show all</a>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="footer">
                                <p>2018 © Admin Board. - <a href="#">example.com</a></p>
                            </div>
                        </div>
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
    <?php
    pg_close($db);
    ?>

</body>

</html>