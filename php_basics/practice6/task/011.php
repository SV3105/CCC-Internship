<?php 
    setcookie("user_preference", "dark_mode", time() + 3600, "/");

    if(isset($_COOKIE['user_preference'])){
        echo "user_preference is: ". $_COOKIE["user_preference"];
    }
?>