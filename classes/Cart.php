<?php 
    class Cart{
        //same for all
        private $cart_id;
        private $client_id;

        public function __construct($cart_id, $client_id){
            $this->cart_id = $cart_id;
            $this->client_id = $client_id;
        }

        public function getCartId(){
            return $this->cart_id;
        }

        public function setCartId($cart_id){
            $this->cart_id = $cart_id;
        }

        public function getClientId(){
            return $this->client_id;
        }

        public function setClientId($client_id){
            $this->client_id = $client_id;
        }
    }

