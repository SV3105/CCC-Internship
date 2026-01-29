<?php
function calculateDiscount($price, $discountPercent=10){
    $finalPrice = $price * ($discountPercent / 100);

    return $finalPrice;
}

echo calculateDiscount(5000, 5). "<br />";
echo calculateDiscount(3000). "<br />";;
?>

<?php 
$x=2;

function changeValue($num){
    $num = 5;
}

function changeReference(&$n){
    $n = 5;  
}

changeValue($x);
 echo $x. "<br />";

changeReference($x);
 echo $x . "<br />";

?>

<?php
$fname=array("Peter","Ben","Joe");
$age=array("35","37","43");

$c=array_combine($fname,$age); //it creates associative array
print_r($c); 
echo "<br />";
print_r(array_merge($fname, $age));
echo "<br />";
?>

<?php 
function myfunc($val){
    return ($val * 2);
}

$num = [1, 2, 3, 4, 5];

print_r(array_map("myfunc", $num));

// The array_reduce() function sends the values in an array to a user-defined function, and returns a string
echo "<br />";
function myfunction($val1, $val2){
    return $val1 . "-" . $val2;
}
$a = ["Dog", "Cat", "Horse"];
print_r(array_reduce($a, "myfunction"));

echo "<br />";

print_r(array_reduce($num, function($carry, $item){
    return $carry + $item;
}, 0)); // it is used for complex logics, multiply, join strings

echo "<br />";

echo array_sum($num); //only used for adding numbers of array
?>