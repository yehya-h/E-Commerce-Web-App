<?php
include_once ("updateSaveProduct.php");
session_start();
include_once("../check_login.php");
if($_SESSION['signed_in']==true){
if (
    (!isset($_COOKIE['isOwner']) || $_COOKIE['isOwner'] == "false") &&
    (!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] == "false")
)
    header("location:../index.php");
}
else header("Location:../index.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="../logos/primary_icon.jpeg" /> <!-- modified -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Update Product</title>
</head>

<body>
     <!-- -------------------------------------------------------------------------------------------------------------------------- -->
     <header>
        <nav class="nav-container">
            <div class='left-nav'>
                <div>
                    <a href="../index.php"><img src="../logos/primary_logo.png" alt="logo" width='220rem'
                            height='90rem'></a>
                </div>
            </div>

            <?php
            include_once ("../check_login.php");
            // if ($isAdmin == true)
            //     header("Location:admin.php");
            if ($isClient == true)
                header("Location:../index.php");

            if ($_SESSION['signed_in'] == true /*&& $isClient==true*/) {
                if ($isClient == true) {
                    $client = $dbHelper->getClientByToken($_COOKIE['token']);
                    if($client==null){
                        header("Location:../sign_out.php?isClient=1");
                    }
                    echo '<div><li>Hello ' . $client->getFirstName() . '<br>Points:  ' . $client->getPoints() . '</li>';
                    echo '<li><a href="../manageAccount.php?user=client">Account</a>/<a href="../sign_out.php?isClient=1">Sign Out</a></li>';
                    echo '<li><a href="#about">About</a></li>';
                    echo '<li><a href="../cart.php">Cart</a></li></div>';
                } else if ($isAdmin == true) {
                    $admin = $dbHelper->getAdminByToken($_COOKIE['token']);
                    if($admin==null){
                        header("Location:../sign_out.php?isAdmin=1");
                    }
                    echo '<div class="center-nav"><div><h1>Product Administration</h1></div></div>
                    <div class="right-nav"><div><h3>Admin : ' . $admin->getFirstName() . ' </h3></div>';
                    // echo '<div><a href="../manageAccount.php?user=admin"><i class="bi bi-person-fill-gear"></i></a></div>
                    // <div><a href="../sign_out.php?isAdmin=1"><i class="bi bi-box-arrow-right"></i></a></div></div>';
                    //echo '<p><a href="#about">About</a></p></div>';
                    // Account
                    // Sign Out
                    echo '<div class="account-select">
                    <i class="bi bi-person-fill-gear"></i>
                    <select id="accountSelect" onchange="goToPage(this.value)">
                        <option disabled selected>--choose--</option>
                        <option value="../manageAccount.php?forward=updateProfile">Update Profile</option>
                        <option value="../manageAccount.php?forward=changePassword">Change Password</option>
                        <option value="delete">Delete Account</option>
                    </select></div>';
                    echo '<div><a href="../sign_out.php?isAdmin=1"><i class="bi bi-box-arrow-right"></i> </a></div></div>';
                } else if ($isOwner == true) {
                    echo '<div class="center-nav"><div><h1>Product Administration</h1></div></div>
                        <div class="right-nav"><div><h3>Owner : ' . OWNER_NAME . ' </h3></div>';
                    echo '<div><p><a href="../sign_out.php?isOwner=1">
                    <i class="bi bi-box-arrow-right"></i> Sign Out</a></p></div></div>';
                }
            } else {
                echo '<p><a href="../sign_in.php">Sign in</a>/<a href="../createAccount.php">Create account</a></p>';
            }
            ?>
            <!-- <li><a href="#about">About</a></li>
                <li><a href="cart.php">Cart</a></li> -->
            </div>
        </nav>
    </header>
    <!-- -------------------------------------------------------------------------------------------------------------------------- -->
    <?php

    if (isset($_GET['id'])) { //case main page --> update product
        $product = $dbHelper->getProductById(htmlspecialchars($_GET['id']));
    } else if (isset($_POST['product_id'])) { //case update product --> fail
        $product = $dbHelper->getProductById(htmlspecialchars($_POST['product_id']));
    }
    $categories = $dbHelper->getAllCategories("name");
    if (!empty($product)) {
        $product->loadImages();
        $images = $product->getImages();
        $count = count($images);
      //  print_r($images); //test
    
        //original product info
        $id = $product->getProductId();
        $name = $product->getName();
        $category_id = $product->getCategoryId();
        $desc = $product->getDescription();
        $stock = $product->getStock();
        $price = $product->getPrice();
        $discount = $product->getDiscount();

        //fail update info
        if (isset($_POST['submit']) && $flag == 0) {
            $id = htmlspecialchars($_POST['product_id']);
            $name = htmlspecialchars($_POST['name']);
            $category_id = htmlspecialchars($_POST['category']);
            $desc = htmlspecialchars($_POST['desc']);
            $stock = htmlspecialchars($_POST['stock']);
            $price = htmlspecialchars($_POST['price']);
            $discount = htmlspecialchars($_POST['discount']);
        }
        echo "<div class='add-container'>
        <br><br>";

        if (isset($message))
            echo "
    <div class='error-container'>" . $message . " </div>
    ";

        echo "<form action='updateProduct.php' method='POST' enctype='multipart/form-data'>
            <table>
                <tr>
                    <td>Product ID</td>
                    <td><input type='text' class='add-input' disabled name='id' value='" . $id . "'></td>
                </tr>
                <tr>
                    <td>Product Name</td>
                    <td><input type='text' maxlength=\"45\" class='add-input' name='name' required value='" . $name . "'></td>
                </tr>
                <tr>
                    <td>Product's Category</td>
                    <td>
                        <select name='category' class='filter-select'>";
        $selected = '';
        foreach ($categories as $value) {
            if ($category_id == $value->getCategoryId()) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            echo "<option name='" . $value->getName() . "' value='" . $value->getCategoryId() . "' " . $selected . ">" . $value->getName();
        }
        echo "</select>
                    </td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td><input type='text' maxlength=\"255\" name='desc' class='add-input' value='" . $desc . "'></td>
                </tr>
                <tr>
                    <td>Stock</td>
                    <td><input type='number' name='stock' class='add-input' required value='" . $stock . "'></td>
                </tr>
                <tr>
                    <td>Price</td>
                    <td><input type='text' name='price' class='add-input' required value='" . $price . "'></td>
                </tr>
                <tr>
                    <td>Discount</td>
                    <td><input type='text' name='discount' class='add-input' value='" . $discount . "'></td>
                </tr>
                <tr>
                    <td>Photos</td>
                    <td><button class='search-button'><a href='updateImages.php?id=" . $id . "'>Update Images</a></button></td>
                </tr>
            </table>";
        foreach ($images as $key => $value) {
            $imageUrl = str_replace('\\', '/', $value);
            $substring = substr($imageUrl, strrpos($imageUrl, "images/"));
            echo "<img src='../" . $substring . "' class='image-view' >";
        }

    //     echo "<br>
    //         <input type='hidden' name='product_id' value='" . $id . "'>
    //        <br>
    //         <input type='submit' class='search-button' name='submit' value='Update Product'>
    //         <button class='search-button'><a href='manageProducts.php'>Back</a></button>
    //     </form>
    // </div>";

    echo "<br>
            <input type='hidden' name='product_id' value='" . $id . "'>
           <br>
            <input type='submit' class='search-button' name='submit' value='Update Product'>
       </form>
       <br><button class='text-button'><a href='manageProducts.php'>Back</a></button>
    </div>";
    }

    ?>
    <div class="popup-overlay" id="popup-overlay" style="display:none;">
            <div class="popup-content" id="popup-content">
                <h2>Are you sure you want to delete this account?</h2>
                <button class='add-button' onclick="deleteAccount()">Yes</button>
                <button class='add-button' onclick="closePopUp()">No</button>
            </div>
        </div>
      <script type="text/javascript" src="script.js"></script>
    <footer>
        <!-- common footer -->
    </footer>
</body>

</html>