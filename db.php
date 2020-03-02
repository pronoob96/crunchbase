<?php
   $host        = "host = 10.17.50.137";
   $port        = "port = 5432";
   $dbname      = "dbname = group_23";
   $credentials = "user = group_23 password=115-931-519";

   $db = pg_connect( "$host $port $dbname $credentials"  );
   
?>