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
$row1 = pg_fetch_row($result1)
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
        <a  onclick='addfav("<?php echo $row[0] ?>")' ><i class="<?php echo $user ?> fa <?php echo $favcl ?> favo" ></i></a>
        </div>

        <script>
        $(".<?php echo $user?>").click(function() {
        $(this).toggleClass("fa-heart fa-heart-o");
         });
       </script>
       <script>
      function addfav(str) {
        
        if (window.XMLHttpRequest) {
          // code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
        } else { // code for IE6, IE5
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        // xmlhttp.onreadystatechange=function() {
        //   if (this.readyState==4 && this.status==200) {
        //     document.getElementById("txtHint").innerHTML=this.responseText;
        //   }
        // }
        xmlhttp.open("GET","follow.php?q="+str,true);
        xmlhttp.send();
        // location.reload();
      }
    </script>
    </div>

    <table class=\"table\">
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Founded At</th>
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

    Key People:
    <?php
    // output data of each row
    while ($row1 = pg_fetch_row($result1)) {
        echo $row1[2];
    } ?>

</body>

</html>