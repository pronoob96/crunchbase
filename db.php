<?php
   $host        = "host = localhost";
   $port        = "port = 5432";
   $dbname      = "dbname = crunchbase";
   $credentials = "user = postgres password=1234";

   $db = pg_connect( "$host $port $dbname $credentials"  );
   
?>