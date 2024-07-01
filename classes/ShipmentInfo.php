<?php 
    class ShipmentInfo{
        //SAME FOR ALL
        private $shipmentInfo_id;
        private $country_name;
        private $client_id;
        private $fullName;
        private $street_nb;
        private $building;
        private $city;
        private $state;
        private $zipCode;
        private $phoneNumber;

        public function __construct($shipmentInfo_id, $country_name, $client_id, $fullName, $street_nb, $building,
        $city, $state, $zipCode, $phoneNumber){
            $this->shipmentInfo_id = $shipmentInfo_id;
            $this->country_name = $country_name;
            $this->client_id = $client_id;
            $this->fullName = $fullName;
            $this->street_nb = $street_nb;
            $this->building = $building;
            $this->city = $city;
            $this->state = $state;
            $this->zipCode = $zipCode;
            $this->phoneNumber = $phoneNumber;
        }

        public function getShipmentInfoId(){
            return $this->shipmentInfo_id;
        }

        public function setShipmentInfoId($shipmentInfo_id){
            $this->shipmentInfo_id = $shipmentInfo_id;
        }

        public function getCountryName(){
            return $this->country_name;
        }

        public function setCountryName($country_name){
            $this->country_name = $country_name;
        }

        public function getClientId(){
            return $this->client_id;
        }

        public function setClientId($client_id){
            $this->client_id = $client_id;
        }

        public function getFullName(){
            return $this->fullName;
        }

        public function setFullName($fullName){
            $this->fullName = $fullName;
        }

        public function getStreetNb(){
            return $this->street_nb;
        }

        public function setStreetNb($street_nb){
            $this->street_nb = $street_nb;
        }

        public function getBuilding(){
            return $this->building;
        }

        public function setBuilding($building){
            $this->building = $building;
        }

        public function getCity(){
            return $this->city;
        }

        public function setCity($city){
            $this->city = $city;
        }

        public function getState(){
            return $this->state;
        }

        public function setState($state){
            $this->state = $state;
        }

        public function getZipCode(){
            return $this->zipCode;
        }

        public function setZipCode($zipCode){
            $this->zipCode = $zipCode;
        }

        public function getPhoneNumber(){
            return $this->phoneNumber;
        }

        public function setPhoneNumber($phoneNumber){
            $this->phoneNumber = $phoneNumber;
        }
    }

