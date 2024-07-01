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
    $flag = 1;
    $modified=0;
    if (isset ($_POST['submit'])) {

        $id = $_POST['id'];
        $product = $dbHelper->getProductById($id);
        $product->loadImages();
        $images = $product->getImages();
        $count = count($images);
        for ($i = 0; $i < $count; $i++) {
            $img = 'img' . $i;

            //case update of a single image
            if (isset ($_FILES[$img]) && !empty($_FILES[$img]['name'])) {
                $target = $_FILES[$img]["name"];
                $type = pathinfo($target, PATHINFO_EXTENSION);
                if (!in_array($type, ["jpg", "jpeg", "png", "gif", "bmp", "tiff", "JPG", "JPEG", "PNG", "GIF", "BMP", "TIFF"])) {
                    $message = "<p style='color:red ; font-weight:bold'>Can't move forward the image type does not match the required types!</p> <br>";
                    $flag=0;
                } else {
                    $path = $images[$i];
                    unlink($path);
                    if (move_uploaded_file($_FILES[$img]['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/images/prod' . $id . "_" . $i . "." . $type)) {
                        $message = "<p style='color:green ; font-weight:bold'>the images have been uploaded successfully</p>";
                        $modified=1;
                    } else {
                        $message = "<p style='color:red ; font-weight:bold'>Error occured while uploading the new images</p>";
                    }
                }
            }
        }

        //case adding new images (cheking type of files)
        $count = count($_FILES['imageMultiple']['name']) -1;
        //echo $count;
        if ($flag == 1 && isset($_FILES['imageMultiple']['name']) && $_FILES['imageMultiple']['name'][0]!='') {
            //if ($_FILES['img']['error'] == 0) {
            $count = count($_FILES["imageMultiple"]["name"]);
            $target = array();
            $extension = array();
            for ($i = 0; $i < $count; $i++) {
                $target[] = $_FILES["imageMultiple"]["name"][$i];
                $type = pathinfo($target[$i], PATHINFO_EXTENSION);
                if (!in_array($type, ["jpg", "jpeg", "png", "gif", "bmp", "tiff", "JPG", "JPEG", "PNG", "GIF", "BMP", "TIFF"])) {
                    $message = "<p style='color:red ; font-weight:bold'>Can't move forward the image type does not match the required types!</p>";
                    $flag = 0;
                    break;
                }
                $extension[$i] = $type;
            }
        }
        //else $message = "<p style='color:red ; font-weight:bold'>Can't move forward the image type does not match the required types!</p>";

        $i = 0; //to access elements from $_FIILES['imageMultiple']
        $nb_images = $product->getNbImages(); //starting index
        $count += $nb_images;                 // count of old+new images
        while ($flag == 1 && $nb_images < $count) {
            if (move_uploaded_file($_FILES['imageMultiple']['tmp_name'][$i], $_SERVER['DOCUMENT_ROOT'] . '/images/prod' . $id . "_" . $nb_images . "." . $extension[$i])) {
                $nb_images++;
                $modified=1;
            } else {
                $message = "<p style='color:red ; font-weight:bold'>Error occured while uploading the images!</p>";
                $flag = 0;
                $modified=0;
            }
            $i++;
        }

        if ($nb_images != $count) { //final nb of images added
            $index=$product->getNbImages();
            $product->deleteImagesByIndex($index,$nb_images);
        } else if($modified==1){
            $product->setNbImages($nb_images);
            $dbHelper->updateNbImages($product);
            $message = "<p style='color:green ; font-weight:bold'>Images updated successfully</p>";
        }
    }

    ?>
</body>

</html>