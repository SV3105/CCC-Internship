<?php 
//PHP-007
class Employee{
     public $name;
     private $salary;  
     
     function __construct($name, $salary){
        $this->name = $name;
        $this->salary = $salary;
     }
//PHP-008
     public function getSalary(){
        return $this->salary;
     }

     public function setSalary($amount){
        if($amount > 0){
            $this->salary = $amount;
        }else{
            echo "invalid salary amount";
        }
     }
}

$obj = new Employee('xyz', 50000);
 $obj->setSalary(20000);
 echo $obj->getSalary();



?>