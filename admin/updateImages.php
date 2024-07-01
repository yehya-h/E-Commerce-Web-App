<?php
include_once ("updateImagesSave.php");
session_start();
include_once("../check_login.php");
if($_SESSION['signed_in']==true){
if((!isset($_COOKIE['isOwner']) || $_COOKIE['isOwner'] == "false") && 
(!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] == "false")) header("location:../index.php");
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
    <title>Update Images</title>
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
    if (isset ($_GET['id']))
        $product = $dbHelper->getProductById(htmlspecialchars($_GET['id'])); //case update product --> update images
    else if (isset ($_GET['prod_id']))
        $product = $dbHelper->getProductById(htmlspecialchars($_GET['prod_id'])); //case update images --> update images (fail or delete)
    
    //delete image
    if (isset($_GET['Delimg']) && isset ($_GET['prod_id'])) {
        $imgs = $product->getImages();
        $count = $product->getNbImages();
        //echo $count;
        if ($count > 1) {
            $path = $_GET['Delimg'];
            $product->deleteImage($path);
            $dbHelper->updateNbImages($product);
        } else {
            $message = "<p style='color:red ; font-weight:bold'>Can't delete this image since the product must have at least one image</p>";
        }
    }

    if (!empty ($product)) {
        $product->loadImages();
        $images = $product->getImages();
        $count = count($images);
        $id = $product->getProductId();
        $name = $product->getName();
       // print_r($images); //test
        echo "<div class='add-container'>
        <br><br>";

if (isset($message))
        echo "
<div class='error-container'>" . $message . " </div>
";
        
        echo "<p>Click the images you want to update or delete</p>
        <form action='updateImages.php' method='POST' enctype='multipart/form-data'>
            
            <table>
                <tr>
                    <td>Product ID</td>
                    <td colspan='2'><input type='text' disabled class='add-input' name='id' value='" . $id . "'></td>
                </tr>
                <tr>
                    <td>Product Name</td>
                    <td colspan='2'><input type='text' disabled class='add-input' name='name' value='" . $name . "'></td>
                </tr>";
        for ($i = 0; $i < $count; $i++) {
            $imageUrl = str_replace('\\', '/', $images[$i]);
            $substring = substr($imageUrl, strrpos($imageUrl, "images/"));
            echo "<tr><td><img src='../" . $substring . "' width=120 height=120></td>
            <td><label class='custom-file-upload'><input type='file' name='img" . $i . "' onchange='previewImage(event," . $i . ")'>Change Image</label></td>
                    <td><div id='preview" . $i . "'></div></td>";
            echo "<td><button class='search-button'><a href='updateImages.php?prod_id=" . $id . "&Delimg=" . $images[$i] . "'>Delete</a></button></td></tr>";
        }
        echo "</table><br><hr>
         <label class='custom-file-upload'><input type='file' name='imageMultiple[]' id='img_id' multiple onchange='preview(event)'>Upload New Images</label>
        <br>
        <div id='previewNew'>

            </div>";
        ?>
        <script>
            function previewImage(event, index) {
                var input = event.target;  //get file 
                var reader = new FileReader(); //read content of the selected image (step 1)
                reader.onload = function () {  //will be triggered upon successful reading of the file(step 1)
                    var imgElement = document.createElement('img');
                    imgElement.src = reader.result;
                    imgElement.style.width = '120px'; // Adjust as needed
                    imgElement.style.height = '120px'; // Adjust as needed
                    var previewDiv = document.getElementById('preview' + index);  //get div of the selected index
                    previewDiv.innerHTML = ''; // Clear previous preview
                    previewDiv.appendChild(imgElement);
                };
                reader.readAsDataURL(input.files[0]);
            }

            function preview(event) {
                var files = event.target.files;
                var previewDiv = document.getElementById('previewNew');
                previewDiv.innerHTML = ''; // Clear previous previews
                for (var i = 0; i < files.length; i++) {
                    var imgElement = document.createElement('img');
                    imgElement.src = URL.createObjectURL(files[i]);
                    imgElement.style.width = '120px'; // Adjust as needed
                    imgElement.style.height = '120px'; // Adjust as needed
                    imgElement.style.marginRight = '10px';
                    previewDiv.appendChild(imgElement);
                }
            }
        </script>
        <?php 
    //     echo "<br><input type='hidden' name='id' value='" . $id . "'>
    //        <br>
    //        <input type='submit' name='submit' class='search-button' value='Update Images'>
    //         <button class='search-button'><a href='updateProduct.php?id=" . $id . "'>Back</a></button>
    //     </form>
    //    </div>";

    echo "<br><input type='hidden' name='id' value='" . $id . "'>
    <br>
    <input type='submit' name='submit' class='search-button' value='Update Images'>
</form>
<br><button class='text-button'><a href='updateProduct.php?id=" . $id . "'>Back</a></button>
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