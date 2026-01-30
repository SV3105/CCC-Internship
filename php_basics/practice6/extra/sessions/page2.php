<?php 
session_start();
if(isset($_SESSION['views'])){
    $_SESSION['views']++;
}
else{
    $_SESSION['views'] = 1;
}

echo "you visited this page ". $_SESSION['views']. " times";
?>