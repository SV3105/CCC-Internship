<?php 
class product {
    private $price;

    function __set($prop, $value){
        if($prop == "price"){
        if($value>0){
            $this->price = $value;
            echo $value;
            }
        else{
            echo "enter valid value<br>";
            }
        } else {
            echo "Property does not exist<br>";
        }
    }
}

$obj1 = new product();
$obj1->price = 10;

echo "<br>";
?>

<?php 
class Config{
    private $pass = 1234;
    public $name= "sneha";

    function __isset($prop){
        if(isset($this->$prop)){
            echo "$prop does exist";
            
        }
        else{
            echo "$prop does not exist";
            
        }
    }
}

$obj = new Config();
isset($obj->pass);

?>

<?php 
//All magic methods, with the exception of __construct(), __destruct(), and __clone(), must be declared as public, otherwise an fatal error is emitted. 
?>