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

