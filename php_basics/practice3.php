<?php declare(strict_types=1);
// task- PHP-005
$tax = 0.18;

function calculateTotal(float $price, int $qty): float
{
    global $tax;
    
    $subtotal = $price * $qty;
    return $subtotal + ($subtotal * $tax);
}

$total = calculateTotal(500, 3);
echo $total;
echo "<br />";
/*
function myFunction() {
  echo "I come from a function!";
}

$myArr = array("Volvo", 15, "myFunction");

$myArr[2]();
*/
?>

<?php 
//task- PHP-006(1)

$str = "         Hello World  ";
echo strtolower(trim($str)). "<br />";
// u use trim or not , php will always print string with removing all unnecessary spaces
echo str_replace("World", "PHP", $str). "<br />";

?>

<?php 
//task- PHP-006(2)
$numbers = [1, 6, 5, 3, 7, 8, 0];
echo in_array(5, $numbers). "<br />";

print_r($numbers);
echo "<br />";
array_push($numbers, 9);
print_r($numbers);
echo "<br />";

$name = ["xyz", "abc"];
$merged = array_merge($name, $numbers);
print_r($merged);

/*sort() - sorts an indexed array in ascending order
rsort() - sorts an indexed array in descending order
asort() - sorts an associative array in ascending order, according to the value
ksort() - sorts an associative array in ascending order, according to the key
arsort() - sorts an associative array in descending order, according to the value
krsort() - sorts an associative array in descending order, according to the key */
?>


