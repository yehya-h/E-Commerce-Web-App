<?php
include_once ($_SERVER['DOCUMENT_ROOT']."/connection.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>

<body>
    <?php
    $error1 = $error2 = $error3 = 1;
    $flag = 1;
    if (isset ($_POST['submit'])) {
        if (
            isset ($_POST['name']) && !empty ($_POST['name']) &&
            isset ($_POST['category']) && !empty ($_POST['category']) &&
            isset ($_POST['stock']) && !empty ($_POST['stock']) &&
            isset ($_POST['price']) && !empty ($_POST['price'])
            //&&isset ($_FILES['img']) && !empty ($_FILES['img'])
        ) {
            $product_id = htmlspecialchars($_POST['product_id']); //sent in hidden
            $product = $dbHelper->getProductById($product_id);
            $name = htmlspecialchars($_POST['name']);
            $category_id = htmlspecialchars($_POST['category']);
            if (isset ($_POST['desc'])) $description = htmlspecialchars($_POST['desc']);
            else $description = null;
           
            // if ($_POST['stock'] < 0) {
            //     $error1 = 0;
            //     $flag = 0;
            //     $stock = $product->getStock();
            // } else
            //     $stock = htmlspecialchars($_POST['stock']);
            // if ($_POST['price'] <= 0) {
            //     $error2 = 0;
            //     $flag = 0;
            //     $price = $product->getPrice();
            // } else
            //     $price = htmlspecialchars($_POST['price']);
           
           //modified
           if (!is_numeric($_POST['stock'])) {
            $error1 = 0;
            $flag = 0;
        } else if ($_POST['stock'] < 0) {
            $error1 = 0;
            $flag = 0;
            $stock = $product->getStock();
        } else
            $stock = htmlspecialchars($_POST['stock']);

        if (!is_numeric($_POST['price'])) {
            $error2 = 0;
            $flag = 0;
        } else if ($_POST['price'] <= 0) {
            $error2 = 0;
            $flag = 0;
            $price = $product->getPrice();
        } else
            $price = htmlspecialchars($_POST['price']);
           
            if (isset ($_POST['discount']) && $_POST['discount']!='') {
                // if ($_POST['discount'] <= 0 || $_POST['discount']>=100) {
                //     $error3 = 0;
                //     $flag = 0;
                //     $discount = $product->getDiscount();
                // } else
                //     $discount = htmlspecialchars($_POST['discount']);
            
                //modified
                if (!is_numeric($_POST['discount'])) {
                    $error3 = 0;
                    $flag = 0;
                } else if ($_POST['discount'] <= 0 || $_POST['discount'] >= 100) {
                    $error3 = 0;
                    $flag = 0;
                    $discount = $product->getDiscount();
                } else
                    $discount = htmlspecialchars($_POST['discount']);
            } else
                $discount = null;
            if ($error1 == 0) {
                //$message = "<br><p style='color:red ; font-weight:bold'>Stock can't be negative!</p><br>";
                //modified
                $message = "<br><p style='color:red ; font-weight:bold'>Stock must be a number and can't be negative!</p><br>";
            }
            if ($error2 == 0) {
              //  $message = "<br><p style='color:red ; font-weight:bold'>Price can't be negative!</p><br>";
              // modified
              $message = "<br><p style='color:red ; font-weight:bold'>Price must be a number and can't be negative!</p><br>";
            
            }
            if ($error3 == 0) {
               // $message = "<br><p style='color:red ; font-weight:bold'>Discount can't be negative!</p><br>";
               // modified
               $message = "<br><p style='color:red ; font-weight:bold'>Discount must be a number and can't be negative!</p><br>";
           
            }
            if ($flag == 1 && $error1 == 1 && $error2 == 1 && $error3 == 1) {
                $product->setName($name);
                $product->setCategoryId($category_id);
                $product->setDescription($description);
                $product->setStock($stock);
                $product->setPrice($price);
                $product->setDiscount($discount);
                $dbHelper->updateProduct($product);
                $message = "<p style='color:green ; font-weight:bold'>Updated successfully</p>";
            }
        }
    }
    ?>
</body>

</html>