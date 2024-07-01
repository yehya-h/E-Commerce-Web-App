<?php 
    class Category{
        //same for all
        private $category_id;
        private $name;
        private $nbProducts;

        public function __construct($category_id, $name, $nbProducts){
            $this->category_id = $category_id;
            $this->name = $name;
            $this->nbProducts = $nbProducts;
        }

        public function getCategoryId(){
            return $this->category_id;
        }

        public function setCategoryId($category_id){
            $this->category_id = $category_id;
        }

        public function getName(){
            return $this->name;
        }

        public function setName($name){
            $this->name = $name;
        }

        public function getNbProducts(){
            return $this->nbProducts;
        }

        public function setNbProducts($nbProducts){
            $this->nbProducts = $nbProducts;
        }
    }

