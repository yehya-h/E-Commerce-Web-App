<?php
    class Rating{

        /*THE USED VERSION IS THE ONE OF DBHELPER_22_4_2024
        Modifications: IN OLD VERSIONS, THE ORDER OF PARAMETERS IN THE CONSTRUCTOR WAS INVALID */
        private $rating_id;
        private $client_id;
        private $product_id;
        private $value;

        public function __construct($rating_id, $client_id, $product_id, $value){
            $this->rating_id = $rating_id;
            $this->client_id = $client_id;
            $this->product_id = $product_id;
            $this->value = $value;
        }
        public function getClientId(){
            return $this->client_id;
        }

        public function setClientId($client_id){
            $this->client_id = $client_id;
        }

        public function getProductId(){
            return $this->product_id;
        }

        public function setProductId($product_id){
            $this->product_id = $product_id;
        }
        
        public function getRatingId(){
            return $this->rating_id;
        }

        public function setRatingId($rating_id){
            $this->rating_id = $rating_id;
        }
        
        public function getValue(){
            return $this->value;
        }

        public function setValue($value){
            $this->value = $value;
        }
    }
