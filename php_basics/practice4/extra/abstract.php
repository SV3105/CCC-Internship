<?php 
    abstract class payment{
        protected $amount;
        function __construct($amount){
            $this->amount = $amount;
        }

        abstract public function processPayment();
    }

    class creditCardPayment extends payment{
       
       public function processPayment(){
            return "Processing credit card payment of $this->amount";
        }
    }

    class upiPayment extends payment{
        public function processPayment(){
            return "Processing UPI payment of $this->amount";
        }
    }

    $credit = new creditCardPayment(1200);
    $upi = new upiPayment(5000);

    echo $credit->processPayment(). "<br />";
    echo $upi->processPayment();
?>