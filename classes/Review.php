<?php
    class Review{
        //same FOR ALL
        private $review_id;
        private $client_id;
        private $product_id;
        private $text;

        public function __construct($review_id, $client_id, $product_id, $text){
            $this->review_id = $review_id;
            $this->client_id = $client_id;
            $this->product_id = $product_id;
            $this->text = $text;
        }

        public function getReviewId(){
            return $this->review_id;
        }

        public function setReviewId($review_id){
            $this->review_id = $review_id;
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

        public function getText(){
            return $this->text;
        }

        public function setText($text){
            $this->text = $text;
        }
    }

