<?php
define("NAME", "PHP");
echo "Welcome to ". NAME . " Programming! <br />";

$skills = ["PHP", "HTML"];

echo gettype($skills)."<br>";

var_dump($skills);
echo("<br>");

$age = null;
echo gettype($age)."<br>";

$num = 89;
echo is_int($num). "<br>";

$price = "250";
$quantity = 2;

$total = (int)$price * $quantity;
echo $total . "<br>";

$val = "300";
$qty = 3;

$calc = $val * $qty;
echo $calc;

?>
