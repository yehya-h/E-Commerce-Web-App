<?php
    class Product{
        /*Using AdminV03 version after adding getNewPrice() method
            problems expected if the $_SERVER['DOCUMENT_ROOT'] is used in images methods:
                1- cart.php: line 112
                2- payment.php: line 267
            Alternative methods will be provided and commented for all situations */

        private $product_id;
        private $name;
        private $category_id;
        private $description;
        private $stock;
        private $price;
        private $discount;
        private $rating;
        private $nb_images;
        private $images = array();

        public function __construct($product_id, $name, $category_id, $description, $stock, $price, $discount,
        $rating, $nb_images){
            $this->product_id = $product_id;
            $this->name = $name;
            $this->category_id = $category_id;
            $this->description = $description;
            $this->stock = $stock;
            $this->price = $price;
            $this->discount = $discount;
            $this->rating = $rating;
            $this->nb_images = $nb_images;
        }

//THE FOLLOWING FUNCTIONS WORKED PROPERLY IN ADMIN (YEHYA) BUT FAILED IN PAYMENT AND CART (ALAA)
        public function loadImages(){
            $this->images = glob($_SERVER['DOCUMENT_ROOT']."/images/prod".$this->product_id."_*");
           // $this->images = glob($_SERVER['DOCUMENT_ROOT']."/images/prod".$this->product_id."_*");
            // $this->nb_images = count($this->images);
        }

        public function deleteImage($path){
            preg_match_all('!\d+!', $path, $digits);
            $index = (int)$digits[0][1];
            unlink($path);
            $rootPath = $_SERVER['DOCUMENT_ROOT'];
            //shift elts to the left according to index
            for(; $index < $this->nb_images-1; $index++){
                $current = glob($rootPath."/images/prod".$this->product_id."_".($index+1)."*");
                if($current!=null && $current!=false){
                $crntExtension = pathinfo($current[0], PATHINFO_EXTENSION);
                rename($current[0], $rootPath."/images/prod".$this->product_id."_".$index.".".$crntExtension);
                }
            }
            $this->nb_images--;
            //update product table set nb_images-- in the DB
        }

        public function deleteImagesAll(){
            $images = glob($_SERVER['DOCUMENT_ROOT']."/images/prod".$this->product_id."_*");
            foreach($images as $image){
                unlink($image);
            }
        }

        public static function deleteImages($product_id){
            $images = glob($_SERVER['DOCUMENT_ROOT']."/images/prod".$product_id."_*");
            foreach($images as $image){
                unlink($image);
            }
        }

        public function deleteImagesByIndex($index, $length){ //$length is the length of all images
            $images = array();
            for($i=$index; $i<$length; $i++){
                $img = glob($_SERVER['DOCUMENT_ROOT']."/images/prod".$this->product_id."_".$i."*");
                $images[] = $img[0];
            }
            foreach($images as $image){
                unlink($image);
            }
        }

        /*-----------------------------------------------------------------------------------------------------------
        THE FOLLOWING METHODS WORKED PROPERLY WITH THE PAYMENT AND CART BUT FAILED WITH THE ADMIN:
         */

         public function loadImages2(){
            $this->images = glob("images/prod".$this->product_id."_*");
            // $this->nb_images = count($this->images);
        }

        // public function deleteImage($path){
        //     preg_match_all('!\d+!', $path, $digits);
        //     $index = (int)$digits[0][1];
        //     unlink($path);
        //     //shift elts to the left according to index
        //     for(; $index < $this->nb_images-1; $index++){
        //         $current = glob("images/prod".$this->product_id."_".($index+1)."*");
        //         $crntExtension = pathinfo($current[0], PATHINFO_EXTENSION);
        //         rename($current[0], "images/prod".$this->product_id."_".$index.".".$crntExtension);
        //     }
        //     $this->nb_images--;
        //     //update product table set nb_images-- in the DB
        // }

        // public function deleteImagesAll(){
        //     $images = glob("images/prod".$this->product_id."_*");
        //     foreach($images as $image){
        //         unlink($image);
        //     }
        // }

        // public static function deleteImages($product_id){
        //     $images = glob("images/prod".$product_id."_*");
        //     foreach($images as $image){
        //         unlink($image);
        //     }
        // }

        // public function deleteImagesByIndex($index, $length){ //$length is the length of all images
        //     $images = array();
        //     for($i=$index; $i<$length; $i++){
        //         $img = glob("images/prod".$this->product_id."_".$i."*");
        //         $images[] = $img[0];
        //     }
        //     foreach($images as $image){
        //         unlink($image);
        //     }
        // }

        /*---------------------------------------------------------------------------------------------------------
        
        THE FOLLOWING FUNCTIONS ARE EXPECTED TO WORK FOR BOTH ADMIN AND PAYMENT:
        */


        public function loadImages3(){
            $this->images = glob("../images/prod".$this->product_id."_*");
            // $this->nb_images = count($this->images);
        }

        // public function deleteImage($path){
        //     preg_match_all('!\d+!', $path, $digits);
        //     $index = (int)$digits[0][1];
        //     unlink($path);
        //     //shift elts to the left according to index
        //     for(; $index < $this->nb_images-1; $index++){
        //         $current = glob("../images/prod".$this->product_id."_".($index+1)."*");
        //         $crntExtension = pathinfo($current[0], PATHINFO_EXTENSION);
        //         rename($current[0], "../images/prod".$this->product_id."_".$index.".".$crntExtension);
        //     }
        //     $this->nb_images--;
        //     //update product table set nb_images-- in the DB
        // }

        // public function deleteImagesAll(){
        //     $images = glob("../images/prod".$this->product_id."_*");
        //     foreach($images as $image){
        //         unlink($image);
        //     }
        // }

        // public static function deleteImages($product_id){
        //     $images = glob("../images/prod".$product_id."_*");
        //     foreach($images as $image){
        //         unlink($image);
        //     }
        // }

        // public function deleteImagesByIndex($index, $length){ //$length is the length of all images
        //     $images = array();
        //     for($i=$index; $i<$length; $i++){
        //         $img = glob("../images/prod".$this->product_id."_".$i."*");
        //         $images[] = $img[0];
        //     }
        //     foreach($images as $image){
        //         unlink($image);
        //     }
        // }

        /*IF THE ABOVE METHODS DIDNT FUNCTION WELL FOR BOTH ADMIN AND CART=> UNCOMMENT THE ONES THAT WORKED
         FOR CART AND JUST RENAME THEM AND USE THE ONES THAT WORKED FOR ADMIN IN ADMIN AND THE ONES THAT
          WORKED ON CART AND PAYMENT THERE */

        public function getNewPrice(){
            if(isset($this->discount)){
                return ($this->price - ($this->price * ($this->discount / 100)));
            }
            return $this->price;
        }

        public function getProductId(){
            return $this->product_id;
        }

        public function setProductId($product_id){
            $this->product_id = $product_id;
        }

        public function getName(){
            return $this->name;
        }
 
        public function setName($name){
            $this->name = $name;
        }

        public function getCategoryId(){
            return $this->category_id;
        }

        public function setCategoryId($category_id){
            $this->category_id = $category_id;
        }

        public function getDescription(){
            return $this->description;
        }

        public function setDescription($description){
            $this->description = $description;
        }

        public function getStock(){
            return $this->stock;
        }

        public function setStock($stock){
            $this->stock = $stock;
        }

        public function getPrice(){
            return $this->price;
        }

        public function setPrice($price){
            $this->price = $price;
        }

        public function getDiscount(){
            return $this->discount;
        }

        public function setDiscount($discount){
            $this->discount = $discount;
        }

        public function getRating(){
            return $this->rating;
        }

        public function setRating($rating){
            $this->rating = $rating;
        }

        public function getNbImages(){
            return $this->nb_images;
        }

        public function setNbImages($nb_images){
            $this->nb_images = $nb_images;
        }

        public function getImages(){
            return $this->images;
        }
    }

