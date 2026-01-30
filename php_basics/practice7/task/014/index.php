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
//The use keyword has two purposes: it tells a class to inherit a trait(traits are used to declare methods that can be used in multiple classes.) and it gives an alias to a namespace.
?>