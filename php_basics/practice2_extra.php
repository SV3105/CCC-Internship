<?php
$age= 8; 
$hasID = true;

if($age>= 18 && $hasID == true){
    echo "Allowed to enter". "<br>";
}
elseif($age<18 && $hasID == true){
    echo "ID present but underage". "<br>";
}
else{
    echo "Not Allowed". "<br>";
}


?>

<?php 
$i=1;
while($i<=10){
    echo $i. "<br>";
    $i++;
}

echo  "<br>";

$j=10;
do{
    echo $j . "<br>";
    $j--;
}
while($j>0);
echo  "<br>";
?>

<?php 
for($i=1; $i<=5; $i++){
    for($j=1; $j<=$i; $j++){
        echo "*";
    }
    echo "<br>";
}

echo  "<br>";

echo "<pre>";
$rows = 5;
for($i=1; $i<=$rows; $i++){
    for($j=$rows - $i; $j>0; $j--){
        echo " ";
    }
    for($k=1; $k <=(2 * $i -1); $k++){
        echo "*";
    }
    echo "<br />";
}

echo "</pre>";

echo  "<br>";

echo "<pre>";
$size = 5;

for($i=1; $i<=$size; $i++){
    for($j=1; $j<=$size; $j++){
        if($i == 1 || $i == $size || $j == 1 || $j == $size){
            echo "*";
        }
        else{
            echo " ";
        }
    }
    echo "<br />";
}
echo "</pre>";
?>