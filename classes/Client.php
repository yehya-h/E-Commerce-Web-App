<?php
    class Client{
        //same for all
        private $client_id;
        private $account_id;
        private $firstName;
        private $lastName;
        private $phoneNumber;
        private $cart_id;
        private $paymentInfo_id;
        private $points;

        public function __construct($client_id, $account_id, $firstName, $lastName, $phoneNumber, $cart_id, 
        $paymentInfo_id, $points){
            $this->client_id = $client_id;
            $this->account_id = $account_id;
            $this->firstName = $firstName;
            $this->lastName = $lastName;
            $this->phoneNumber = $phoneNumber;
            $this->cart_id = $cart_id;
            $this->paymentInfo_id = $paymentInfo_id;
            $this->points = $points;
        }

        public function getClientId(){
            return $this->client_id;
        }

        public function setClientId($client_id){
            $this->client_id = $client_id;
        }

        public function getAccountId(){
            return $this->account_id;
        }
        
        public function setAccountId($account_id){
            $this->account_id = $account_id;
        }
        
        public function getFirstName(){
            return $this->firstName;
        }

        public function setFirstName($firstName){
            $this->firstName = $firstName;
        }
        
        public function getLastName(){
            return $this->lastName;
        }

        public function setLastName($lastName){
            $this->lastName = $lastName;
        }

        public function getPhoneNumber(){
            return $this->phoneNumber;
        }

        public function setPhoneNumber($phoneNumber){
            $this->phoneNumber = $phoneNumber;
        }

        public function getCartId(){
            return $this->cart_id;
        }

        public function setCartId($cart_id){
            $this->cart_id = $cart_id;
        }

        public function getPaymentInfoId(){
            return $this->paymentInfo_id;
        }

        public function setPaymentInfoId($paymentInfo_id){
            $this->paymentInfo_id = $paymentInfo_id;
        }

        public function getPoints(){
            return $this->points;
        }

        public function setPoints($points){
            $this->points = $points;
        }
    }

