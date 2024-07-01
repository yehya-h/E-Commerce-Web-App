<?php
    class Account{
        //same for all
        private $account_id;
        private $email;
        private $password;
        private $isAdmin;
        private $token;

        public function __construct($account_id, $email, $password, $isAdmin, $token){
            $this->account_id = $account_id;
            $this->email = $email;
            $this->password = $password;
            $this->isAdmin = $isAdmin;
            $this->token = $token;
        }

        public function getAccountId(){
            return $this->account_id;
        }

        public function setAccountId($account_id){
             $this->account_id = $account_id;
         }

        public function getEmail(){
            return $this->email;
        }

        public function setEmail($email){
            $this->email = $email;
        }

        public function getPassword(){
            return $this->password;
        }
        
        public function setPassword($password){
            $this->password = $password;
        }

        public function getIsAdmin(){
            return $this->isAdmin;
        }

         public function setIsAdmin($isAdmin){
             $this->isAdmin = $isAdmin;
         }

        public function getToken(){
            return $this->token;
        }

        public function setToken($token){
            $this->token = $token;
        }
    }

