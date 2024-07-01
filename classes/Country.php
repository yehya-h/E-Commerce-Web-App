<?php 
    class Country{
        //same for all
        private $country_name;
        private $delivery_time;
        private $delivery_fees;

        public function __construct($country_name, $delivery_time , $delivery_fees){
            $this->country_name = $country_name;
            $this->delivery_time = $delivery_time;
            $this->delivery_fees = $delivery_fees;
        }

        public function setCountryName($country_name){
            $this->country_name = $country_name;
        }

        public function getCountryName(){
            return $this->country_name;
        }

        public function setDeliveryTime($delivery_time){
            $this->delivery_time = $delivery_time;
        }

        public function getDeliveryTime(){
            return $this->delivery_time;
        }
        public function setDeliveryFees($delivery_fees){
            $this->delivery_fees = $delivery_fees;
        }
        public function getDeliveryFees(){
            return $this->delivery_fees;
        }
    }

