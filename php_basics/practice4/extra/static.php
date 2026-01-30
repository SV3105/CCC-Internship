<?php 
// Static properties and methods can be used without creating an instance of the class.

//The static keyword is also used to declare variables in a function which keep their value after the function has ended.

class counter{
    public static $count = 0;
    static function increment(){
       return self::$count++ ;
    }

    static function getCount(){
        return self::$count;
    }

}
 echo "counter: ". counter::getCount(). "<br>";
 counter::increment();
 counter::increment();
 counter::increment();
 echo "counter: ". counter::getCount(). "<br>";
 echo "<br>";
?>