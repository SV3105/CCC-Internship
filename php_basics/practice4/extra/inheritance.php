
<?php 
class person{
    public $name;

    function __construct($name){
        $this->name = $name;
    }
}

class teacher extends person{
    public $subject;
     
    function __construct($name, $subject){
        parent::__construct($name);
        $this->subject = $subject;
    }

    function getDetails(){
        echo "Name: ". $this->name . " Subject: ". $this->subject;
    }
}

$t1 = new teacher("abc", "Hindi");
echo $t1->getDetails();
?>