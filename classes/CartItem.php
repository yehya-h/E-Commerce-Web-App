<?php
    class CartItem{
        //same for all
        private $item_id;
        private $cart_id;
        private $product_id;
        private $quantity;

        public function __construct($item_id, $cart_id, $product_id, $quantity){
            $this->item_id = $item_id;
            $this->cart_id = $cart_id;
            $this->product_id = $product_id;
            $this->quantity = $quantity;
        }

        public function getItemId(){
            return $this->item_id;
        }

        public function setItemId($item_id){
            $this->item_id = $item_id;
        }

        public function getCartId(){
            return $this->cart_id;
        }

        public function setCartId($cart_id){
            $this->cart_id = $cart_id;
        }
        
        public function getProductId(){
            return $this->product_id;
        }

        public function setProductId($product_id){
            $this->product_id = $product_id;
        }

        public function getQuantity(){
            return $this->quantity;
        }

        public function setQuantity($quantity){
            $this->quantity = $quantity;
        }
    }

