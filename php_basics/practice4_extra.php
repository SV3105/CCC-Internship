<?php
class Student{
    public $name;
    public $marks;

    function __construct($name, $marks){
        $this->name = $name;
        $this->marks = $marks;
    }
    function getResult(){
        if($this->marks > 40){
            echo "Pass". "<br>";
        }
        else{
            echo "fail". "<br>";
        }
    }
}

$s1 = new Student("xyz", 98);
$s2 = new Student("abc", 04);

echo $s1->getResult();
echo $s2->getResult();

echo("<br />")
?>

<?php 
class BankAccount{
    private $balance;

    function __construct($balance){
        $this->balance = $balance;
    }

    function deposit($amount){
        if($amount > 0){
        $this->balance += $amount;
        }else{
            echo "deposit valid amount! <br />";
        }
    }

    function withdraw($amount){
        if($amount <= $this->balance)
            {
                 $this->balance -= $amount;
            }else{
            echo "there is no enough balance to withdraw this amount! <br />";
        }
    }

    function getBalance(){
        return $this->balance;
    }
    
}

$c1 = new BankAccount(20000);
$c2 = new BankAccount(5000);

$c1->withdraw(5000);
echo $c1->getBalance()."<br>";
echo $c2->getBalance()."<br>";

$c1->withdraw(22000);
$c2->deposit(000);

$c1->withdraw(2000);
$c2->deposit(5000); 

echo $c1->getBalance()."<br>";
echo $c2->getBalance()."<br>";
echo "<br />"
?>

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
 echo "counter: ". counter::getCount();
?>