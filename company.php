<?php

session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require "db.php";
$cid = $_GET['id'];
$sql = "SELECT * from objects where id='$cid'";
$sql1 = "SELECT objects.id,ids.pid,objects.name
			from (Select people.object_id as iid,people.id as pid 
                    from (SELECT name from objects 
				    where id = '$cid') as ni,people
			where people.affiliation_name=ni.name) as ids,objects
		where objects.id = ids.iid
		order by ids.pid asc";

$result = pg_query($db, $sql);
if (!$result) {
    echo pg_last_error($db);
    exit;
}
$row = pg_fetch_row($result);

$result1 = pg_query($db, $sql1);
if (!$result1) {
    echo pg_last_error($db);
    exit;
}
$row1 = pg_fetch_row($result1);


$sql2 = "SELECT nm.name, offices.* FROM offices
LEFT JOIN 
(SELECT id, name FROM objects) AS nm
ON offices.object_id = nm.id
where object_id = '$cid';";

$result2 = pg_query($db, $sql2);
if (!$result2) {
    echo pg_last_error($db);
    exit;
}

$sql3 = "SELECT * from objects
where invested_companies <> 0
and entity_type = 'Company'
order by invested_companies DESC;";

$result3 = pg_query($db, $sql3);
if (!$result3) {
    echo pg_last_error($db);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo $row[4]; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <style>
        .fav {
            position: absolute;
            top: 17%;
            right: 5%;
            background-color: none;
            padding: 2px;
        }

        .favo {
            font-size: 25px;
            color: red;
        }
    </style>


</head>

<body>
    <img style="border-radius: 6px;" src="<?php echo $row[14] ?>" />

    <div class="fav">
        <?php
        $id = $_GET['id'];
        $user = $_SESSION['username'];
        $sqlf = "SELECT * FROM follows WHERE username='$user' AND company_id='$id'";
        $resultf = pg_query($db, $sqlf);
        $rowf = pg_fetch_row($result);

        if (pg_numrows($resultf) == 0) {
            $favcl = "fa-heart-o";
        } else {
            $favcl = "fa-heart";
        }
        ?>
        <div style="float: right;">
            <a onclick='addfav("<?php echo $row[0] ?>")'><i class="<?php echo $user ?> fa <?php echo $favcl ?> favo"></i></a>
        </div>

        <script>
            $(".<?php echo $user ?>").click(function() {
                $(this).toggleClass("fa-heart fa-heart-o");
            });
        </script>
        <script>
            function addfav(str) {

                if (window.XMLHttpRequest) {
                    // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else { // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                // xmlhttp.onreadystatechange=function() {
                //   if (this.readyState==4 && this.status==200) {
                //     document.getElementById("txtHint").innerHTML=this.responseText;
                //   }
                // }
                xmlhttp.open("GET", "follow.php?q=" + str, true);
                xmlhttp.send();
                // location.reload();
            }
        </script>
    </div>

    </div>
    
    <div>
        <h4>
            No. of followers: <?php echo $row[40] ?>
        </h4>
    </div>

    <table class=\"table\">
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Founded</th>
        </tr>


        <tr>
            <td><?php echo $row[4]; ?></td>
            <td><?php echo $row[8]; ?></td>
            <td> <?php echo $row[9]; ?> </td>
        </tr>

    </table>

    <?php echo $row[19];
    pg_close($db);
    ?>


    <?php
    // output data of each row
    if ($row1 == true)
        echo "Key People:";
    echo "</br>";
    while ($row1 = pg_fetch_row($result1)) { ?>
        <a href="people.php?id=<?php echo $row1[0]; ?>"><?php echo $row1[2]; ?></a>
    <?php echo "</br>";
    } ?>

    <?php
    if ($row[28] > 0) {
        echo "Investment History: Known to invest in new Start-Ups and Services Companies";
        echo "</br>";
        echo "Number of Investments-";
        echo $row[28];
        echo "</br>";
        echo "First Investment at-";
        echo $row[25];
        echo "</br>";
        echo "Last Investment at-";
        echo $row[26];
        echo "</br>"; ?>
        <a href="invested_companies.php?id=<?php echo $row[0]; ?>">Detail of Invested Companies</a>
    <?php
    } ?>
</body>

</html>