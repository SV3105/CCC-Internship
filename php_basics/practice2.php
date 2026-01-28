<?php
//task- PHP-003(1)
$marks = 80;

if($marks >= 90){
    echo "Grade A";
}elseif($marks >=70){
    echo "Grade B";
}
elseif($marks >= 50){
    echo "Grade C";
}
else{
    echo "Fail";
}

//task- PHP-003(2)
$day = "Wednesday";
echo '<br />';
switch($day){
    case "Monday":
    case "Tuesday":
    case "Wednesday":
    case "Thursday":
    case "Friday":
        echo "Weekdays";
        break;
    case "Saturday":
    case "Sunday":
        echo "Weekends";
        break;
    default:
        echo "Invalid input";
}
?>

<?php 
//task- PHP-004(1)

echo "<br />";
for($i=1; $i<=10; $i++){
    echo "5 x $i = ". (5 * $i). "<br />";
}

//task- PHP-004(2)

$student = ["name" => "xyz", "Age" => 34, "Skills" => ["HTML", "CSS", "JS", "PHP"]];

foreach($student as $key => $value){
    if(is_array($value)){
        echo "$key => ", implode(',' , $value). '<br />';

    }else{
        echo "$key => $value <br />";
    }
}
?>