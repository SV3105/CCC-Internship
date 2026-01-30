<?php 
include('./fileA.php');
include('./fileB.php');

$object = new Library\Database\Connection();
$object->conn();
echo "<br />";
$object = new Library\API\Connection();
$object->conn();


//if we creates same named classes in diff files but includes at one place, it will give error when we try to get methods from class -> that is why we use namespace
?>