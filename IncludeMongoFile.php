<?php

//include mogo files
$con = new MongoDBConnectionDB();
$conClient = $con->getConnectionDB();
$db = $conClient->db_semantic; # connect to data base db_semantic
$col = $db->colsem;         # select colsem collection 

?>