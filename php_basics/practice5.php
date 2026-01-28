<?php
//task- PHP-009
class Employee{
     public $name;
     public $salary;  
     
     function __construct($name, $salary){
        $this->name = $name;
        $this->salary = $salary;
     }

     public function getDetails(){
       return "name: " . $this->name . " salary: " . $this->salary;
     }
    }
class Manager extends Employee{
    public $department;

    function __construct($name, $salary, $department){
        parent::__construct($name, $salary);
        $this->department = $department;
    }

    public function getDetails(){
        return parent::getDetails(). " department: " . $this->department;
    }
}

$obj = new Manager("xyz", 10000, "IT");
echo $obj->getDetails();
?>