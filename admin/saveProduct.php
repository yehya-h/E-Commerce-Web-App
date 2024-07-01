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
    $error1 = $error2 = $error3 = $error4 = 1;
    if (isset ($_POST['submit'])) {
        if (
            isset ($_POST['name']) && !empty ($_POST['name']) &&
            isset ($_POST['category']) && !empty ($_POST['category']) &&
            isset ($_POST['stock']) && !empty ($_POST['stock']) &&
            isset ($_POST['price']) && !empty ($_POST['price']) &&
            isset ($_FILES['img']) && !empty ($_FILES['img'])
        ) {
            $flag = 1;
            $nb_images = 0;
            $id = htmlspecialchars($_POST['product_id']) ;
            if ($flag == 1) {
                $product_id = $id;
                $name = htmlspecialchars($_POST['name']) ;
                $category_id = htmlspecialchars($_POST['category']) ;
                
                // if ($_POST['stock'] <= 0) {
                //     $error1 = 0;
                //     $flag = 0;
                // } else
                //     $stock = htmlspecialchars($_POST['stock']) ;

                 //modified
                 if(!is_numeric($_POST['stock'])){
                    $error1 = 0;
                    $flag = 0;
                }
                else if ($_POST['stock'] <= 0) {
                    $error1 = 0;
                    $flag = 0;
                } else
                    $stock = htmlspecialchars($_POST['stock']) ;
                  
                // if ($_POST['price'] <= 0) {
                //     $error2 = 0;
                //     $flag = 0;
                // } else
                //     $price = htmlspecialchars($_POST['price']) ;
                
                   //modified
                   if(!is_numeric($_POST['price'])){
                    $error2 = 0;
                    $flag = 0;
                }
                else if ($_POST['price'] <= 0) {
                    $error2 = 0;
                    $flag = 0;
                } else
                    $price = htmlspecialchars($_POST['price']) ;
            
                $rating = null;
                if (isset ($_POST['desc']) && !empty ($_POST['desc']))
                    $description = htmlspecialchars($_POST['desc']) ;
                else
                    $description = null;
                if (isset ($_POST['discount']) && !empty ($_POST['discount'])) {
                    // if ($_POST['discount'] <= 0 || $_POST['discount']>=100) {
                    //     $error3 = 0;
                    //     $flag = 0;
                    // } else
                    //     $discount = htmlspecialchars($_POST['discount']) ;

                      //modified
                      if(!is_numeric($_POST['discount'])){
                        $error3 = 0;
                        $flag = 0;
                    }
                    else if ($_POST['discount'] <= 0 || $_POST['discount']>=100) {
                        $error3 = 0;
                        $flag = 0;
                    } else
                        $discount = htmlspecialchars($_POST['discount']) ;
                } else
                    $discount = null;
            }
            $count = count($_FILES["img"]["name"]);
            if ($flag == 1) {     //this section to check the type of the files before uploading
                //if ($_FILES['img']['error'] == 0) {
                $target = array();
                $extension = array();
                for ($i = 0; $i < $count; $i++) {
                    $target[] = $_FILES["img"]["name"][$i];
                    $type = pathinfo($target[$i], PATHINFO_EXTENSION);
                    if (!in_array($type, ["jpg", "jpeg", "png", "gif", "bmp", "tiff", "JPG", "JPEG", "PNG", "GIF", "BMP", "TIFF"])) {
                        $message = "<br><p style='color:red ; font-weight:bold'>Can't move forward the image type does not match the required types!</p>";
                        $flag = 0;
                        break;
                    }
                    $extension[$i] = $type;
                }
            }
            $i = 0;
            while ($flag == 1 && $i < $count) {     //this section to upload images
                if (move_uploaded_file($_FILES['img']['tmp_name'][$i], $_SERVER['DOCUMENT_ROOT'] . '\images\prod' . $id . "_" . $i . "." . $extension[$i])) {
                    $nb_images++;
                } else {
                    $message = "<br><p style='color:red ; font-weight:bold'>Error occured while uploading the images!</p>";
                    $error4 = 0;
                    $flag = 0;
                }
                $i++;
            }
            if ($nb_images != $count) {   //for deleting images if there exists an error while uploading the files (before having a product)
                $message = "<br><p style='color:red ; font-weight:bold'>Error uploading images , they'll be deleted</p>";
                Product::deleteImages($id);
            }
            if ($error1 == 0) {
                //$message = "<br><p style='color:red ; font-weight:bold'>Stock can't be negative!</p><br>";
                //modified
                $message = "<br><p style='color:red ; font-weight:bold'>Stock must be a number and can't be negative!</p><br>";
            }
            if ($error2 == 0) {
                //$message = "<br><p style='color:red ; font-weight:bold'>Price can't be negative!</p><br>";
                //modified
                $message = "<br><p style='color:red ; font-weight:bold'>Price must be a number and can't be negative!</p><br>";
            }
            if ($error3 == 0) {
               // $message = "<br><p style='color:red ; font-weight:bold'>Discount can't be negative or >100!</p><br>";
                //modified
                $message = "<br><p style='color:red ; font-weight:bold'>Discount must be a number and can't be negative or >100!</p><br>";
            
            }
            if ($error1 == 1 && $error2 == 1 && $error3 == 1 && $error4 == 1 && $flag==1) {
                $product = new Product(-1, $name, $category_id, $description, $stock, $price, $discount, $rating, $nb_images);
                $dbHelper->addProduct($product);
                $message = "<p style='color:green ; font-weight:bold'>Product added</p>";
            }
        }
    }
    ?>
</body>

</html>