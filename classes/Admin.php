<?php 
    class Admin{
        //same for all
        private $admin_id;
        private $account_id;
        private $firstName;
        private $lastName;
        private $address;
        private $phone_number;

        public function __construct($admin_id, $account_id, $firstName, $lastName, $address, $phone_number){
            $this->admin_id = $admin_id;
            $this->account_id = $account_id;
            $this->firstName = $firstName;
            $this->lastName = $lastName;
            $this->address = $address;
            $this->phone_number = $phone_number;
        }

        public function setAdminId($admin_id){
            $this->admin_id= $admin_id;
        }

        public function getAdminId(){
            return $this->admin_id;
        }

        public function setAccountId($account_id){
            $this->account_id= $account_id;
        }

        public function getAccountId(){
            return $this->account_id;
        }

        public function setFirstName($firstName){
            $this->firstName= $firstName;
        }

        public function getFirstName(){
            return $this->firstName;
        }

        public function getLastName(){
            return $this->lastName;
        }

        public function setLastName($lastName){
            $this->lastName = $lastName;
        }

        public function getAddress(){
            return $this->address;
        }

        public function setAddress($address){
            $this->address = $address;
        }

        public function getPhoneNumber(){
            return $this->phone_number;
        }

        public function setPhoneNumber($phone_number){
            $this->phone_number = $phone_number;
        }
    }

