<?php
    class Order{
        //same for all
        private $order_id;
        private $client_id;
        private $order_date;
        private $selected_shipmentInfo_id;
        private $total_amount;

        public function __construct($order_id, $client_id, $order_date, $selected_shipmentInfo_id, $total_amount){
            $this->order_id = $order_id;
            $this->client_id = $client_id;
            $this->order_date = $order_date;
            $this->selected_shipmentInfo_id = $selected_shipmentInfo_id;
            $this->total_amount = $total_amount;
        }

        public function getOrderId(){
            return $this->order_id;
        }

        public function setOrderId($order_id){
            $this->order_id = $order_id;
        }

        public function getClientId(){
            return $this->client_id;
        }

        public function setClientId($client_id){
            $this->client_id = $client_id;
        }

        public function getOrderDate(){
            return $this->order_date;
        }

        public function setOrderDate($order_date){
            $this->order_date = $order_date;
        }

        public function getSelectedShipmentInfoId(){
            return $this->selected_shipmentInfo_id;
        }

        public function setSelectedShipmentInfoId($selected_shipmentInfo_id){
            $this->selected_shipmentInfo_id = $selected_shipmentInfo_id;
        }

        public function getTotalAmount(){
            return $this->total_amount;
        }

        public function setTotalAmount($total_amount){
            $this->total_amount = $total_amount;
        }
    }

