<?php
include_once ("saveProduct.php");
session_start();//added(OMAR)
include_once("../check_login.php");//added (OMAR)
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
    <title>Add Product</title>
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
    $categories = $dbHelper->getAllCategories("name");
    $id = $dbHelper->getMaxProductId();

    if($categories==null){
        echo "<p style='color:red ; font-weight:bold'>can't add products 
        since there exists no categories!please add some categories.</p>
        <button><a href='addCategory.php'>Add Category</a></button>";
    }else{
    if ($error1 == 0 || $error2 == 0 || $error3 == 0 || $error4 = 0) {
        $name = (isset ($_POST['name'])) ? $_POST['name'] : '';
        $desc = (isset ($_POST['desc'])) ? $_POST['desc'] : '';
        $stock = (isset ($_POST['stock'])) ? $_POST['stock'] : '';
        $price = (isset ($_POST['price'])) ? $_POST['price'] : '';
        $discount = (isset ($_POST['discount'])) ? $_POST['discount'] : '';
        $rating = (isset ($_POST['rating'])) ? $_POST['rating'] : '';
        $img = isset ($_FILES['img']) ? $_FILES['img']['name'] : [];
    } else {
        $name = $desc = $stock = $price = $discount = $rating = '';
    }
    
    echo "<div class='add-container'><br><br>";
    

    if(isset($message)) echo "
    <div class='error-container'>" . $message . " </div>
    " ; 

    echo "<form action='addProduct.php' method='POST' enctype='multipart/form-data'>
            <table>
                <tr>
                    <td>Product ID</td>
                    <td><input type='text' disabled name='id' class='add-input' value=". $id. "></td>
                </tr>
                <tr>
                    <td>Product Name</td>
                    <td><input type='text' name='name' maxlength=\"45\" required class='add-input' value='".$name . "'></td>
                </tr>
                <tr>
                    <td>Product's Category</td>
                    <td>
                        <select name='category' class='filter-select'>";
                            $selected = '';
                            foreach ($categories as $value) {
                                if (isset ($_POST['category']) && (($_POST['category']) == $value->getCategoryId())) {
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
                    <td><input type='text' maxlength=\"255\" name='desc' class='add-input' value='".$desc."'></td>
                </tr>
                <tr>
                    <td>Stock</td>
                    <td><input type='number' name='stock' class='add-input' required value='".$stock."'></td>
                </tr>
                <tr>
                    <td>Price</td>
                    <td><input type='text' name='price' class='add-input' required value='".$price."'></td>
                </tr>
                <tr>
                    <td>Discount</td>
                    <td><input type='text' name='discount' class='add-input' value='".$discount."'></td>
                </tr>
                <tr>
                    <td>Photos</td>
                    <input type='hidden' name='product_id' value='".$id."'>
                    <td><label class='custom-file-upload'><input type='file' name='img[]' class='file-upload-container' id='img_id' required multiple onchange='preview(event)'>Upload Images</label>
                    </td>
                </tr>
            </table>
            <div id='preview'>

            </div>";
        }
        ?>
            <script type="text/javascript">
                function preview(event) {
                    var files = event.target.files;  //retrieve files selcted by user
                    var previewDiv = document.getElementById('preview');
                    previewDiv.innerHTML = ''; // Clear previous previews
                    for (var i = 0; i < files.length; i++) {
                        var imgElement = document.createElement('img');
                        imgElement.src = URL.createObjectURL(files[i]); //temporary URL 
                        imgElement.style.width = '120px'; // Adjust as needed
                        imgElement.style.height = '120px'; // Adjust as needed
                        imgElement.style.marginRight = '10px';
                        previewDiv.appendChild(imgElement);
                    }
                }
            </script>
            <br>
            <input type='submit' name='submit' class="search-button" value='Add Product'>
            <!-- <button class="search-button"><a href='manageProducts.php'>Back</a></button> -->
        </form>
        <br><button class="text-button"><a href='manageProducts.php'>Back</a></button>
    </div>
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