<?php
include('../013/fileA.php');
include('../013/fileB.php');

use Library\Database\Connection as DBConn;
use Library\API\Connection;

$db = new DBConn();
$db->conn();
echo "</br>";
$db = new Connection();
$db->conn();
?>