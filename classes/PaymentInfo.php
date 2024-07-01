<?php
    class PaymentInfo{

        //SAME FOR ALL
        private $paymentInfo_id;
        private $client_id;
        private $cardNumber;
        private $nameOnCard;
        private $expiryDate;
        private $securityCode;

        public function __construct($paymentInfo_id, $client_id,  $cardNumber, $nameOnCard, $expiryDate, $securityCode){
            $this->paymentInfo_id = $paymentInfo_id;
            $this->client_id=$client_id;
            $this->cardNumber = $cardNumber;
            $this->nameOnCard = $nameOnCard;
            $this->expiryDate = $expiryDate;
            $this->securityCode = $securityCode;
        }

        public function getPaymentInfoId(){
            return $this->paymentInfo_id;
        }

        public function setPaymentInfoId($paymentInfo_id){
            $this->paymentInfo_id = $paymentInfo_id;
        }

        public function getClientId(){
            return $this->client_id;
        }

        public function setClientId($client_id){

            $this->client_id=$client_id;
        }

        public function getCardNumber(){
            return $this->cardNumber;
        }

        public function setCardNumber($cardNumber){
            $this->cardNumber = $cardNumber;
        }

        public function getNameOnCard(){
            return $this->nameOnCard;
        }

        public function setNameOnCard($nameOnCard){
            $this->nameOnCard = $nameOnCard;
        }

        public function getExpiryDate(){
            return $this->expiryDate;
        }

        public function setExpiryDate($expiryDate){
            $this->expiryDate = $expiryDate;
        }

        public function getSecurityCode(){
            return $this->securityCode;
        }

        public function setSecurityCode($securityCode){
            $this->securityCode = $securityCode;
        }
    }

