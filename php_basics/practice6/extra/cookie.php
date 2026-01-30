<?php 
    setcookie("langauge", "English", time() + 7200, '/' );

    if(isset($_COOKIE['langauge'])){
        echo "langauge is : " . $_COOKIE['langauge'];
    }

    setcookie("langauge", "English", time()-7200, "/");
?>