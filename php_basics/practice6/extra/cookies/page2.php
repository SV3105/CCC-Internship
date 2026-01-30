   <?php
   if(isset($_COOKIE['language'])){
            echo "language is : " . $_COOKIE['language'];
    } else {
            echo "Cookie not found";
    }
 
    ?>