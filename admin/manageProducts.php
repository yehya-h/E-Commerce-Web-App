
<?php
include_once ($_SERVER['DOCUMENT_ROOT'] . "/connection.php");
session_start();
include_once("../check_login.php");
//$error_message = "";
if(!isset($_SESSION['admin']['searched'])) $_SESSION['admin']['searched'] = 0;
//$back = "<button class='search-button'><a href='admin.php'>Back</a></button>";
$back = "<a href='admin.php' class='back-button'><button>Back</button></a>";
if($_SESSION['signed_in']==true){
if (isset($_COOKIE['isOwner']) && $_COOKIE['isOwner'] == "true")
$back = "<a href='owner.php' class='back-button'><button>Back</button></a>";    
//$back = "<button class='search-button'><a href='owner.php'>Back</a></button>";
if (
    (!isset($_COOKIE['isOwner']) || $_COOKIE['isOwner'] == "false") &&
    (!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] == "false")
)
    header("location:../index.php");
}else{
    header("Location:../index.php");
}
//setting sessions
if (!isset($_SESSION['admin']['productsFilter']))
    $_SESSION['admin']['productsFilter'] = "product_id";
if (isset($_POST['submitFilter'])){
    $_SESSION['admin']['productsFilter'] = $_POST['toFilter'];
    $_SESSION['admin']['searched'] = 1;
    $_SESSION['admin']['productsSearch'] = "";
    $_SESSION['admin']['minPrice'] = '';
    $_SESSION['admin']['maxPrice'] = '';
}
if (!isset($_SESSION['admin']['productsSearch']))
    $_SESSION['admin']['productsSearch'] = "";
if (isset($_POST['submitSearch'])){
    $_SESSION['admin']['productsSearch'] = htmlspecialchars($_POST['toSearch']);
    $_SESSION['admin']['current'] = 1;
}
if (!isset($_SESSION['admin']['minPrice']) || !isset($_SESSION['admin']['maxPrice']))
    $_SESSION['admin']['minPrice'] = $_SESSION['admin']['maxPrice'] = '';
if (isset($_POST['submitSearchPrice'])) {
    $_SESSION['admin']['productsSearch']=""; //new
    $_SESSION['admin']['current'] = 1;
    // if ($_POST['minPrice'] <= $_POST['maxPrice']) {
        $_SESSION['admin']['minPrice'] = $_POST['minPrice'];
        $_SESSION['admin']['maxPrice'] = $_POST['maxPrice'];
    // } else
    //     $error_message= "<div class='error-container'><p style='color:red ; font-weight:bold'>Min price can't be greater than Max Price!</p></div>";
}
if (!isset($_SESSION['admin']['current']))
    $_SESSION['admin']['current'] = 1;
if (isset($_GET['current']))
    $_SESSION['admin']['current'] = $_GET['current'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-xJcExa9d2u9eyuRtoQo5A8r3Fo/KCL8urTJi5N0vWc5jFfcAn4/d8Md0EExV/hGl" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="../logos/primary_icon.jpeg" /> <!-- modified -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Manage Products</title>
</head>


<body>
    <?php $maxChars = 20; ?>
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

    <script>
        // Add event listener to handle description hover
        document.addEventListener('DOMContentLoaded', function () {
            const descriptionCell = document.querySelectorAll('.description');

            descriptionCell.forEach(cell => {
                const truncatedText = cell.querySelector('.truncated');
                const fullDescription = cell.dataset.fullDescription;

                // Show full description on hover
                cell.addEventListener('mouseenter', () => {
                    truncatedText.textContent = fullDescription;
                });

                // Restore truncated description when mouse leaves
                cell.addEventListener('mouseleave', () => {
                    truncatedText.textContent = truncateText(fullDescription, <?php echo $maxChars; ?>);
                });
            });

            // Helper function to truncate text
            function truncateText(text, maxLength) {
                return text.length > maxLength ? text.slice(0, maxLength) + '...' : text;
            }
        });
    </script>
    <?php
    //delete product
    if (isset($_GET['id'])) {
        $prod = $dbHelper->getProductById($_GET['id']);
        $prod->deleteImagesAll();
        $dbHelper->removeProduct($prod);
        header("location:manageProducts.php");
    }
    ?>

    <div class="bar-container">
        <!-- <h3>Product Administration</h3> -->
        <!-- <button class='i-button'><a href="addProduct.php">Add Product</a></button> -->
        <a href="addProduct.php"><button class='i-button'>Add Product</button></a>
        <div class="search-bar">
            <form action="manageProducts.php" method="POST">
            <div class="filter-wrapper">
                <select name="toFilter" class="filter-select">
                    <?php
                    $selectOptions = array("product_id", "name", "category_id", "price", "stock"); //fixed search options
                    foreach ($selectOptions as $value) {
                        $selected = ($_SESSION['admin']['productsFilter'] == $value) ? 'selected' : '';
                        echo "<option name='" . $value . "' value='" . $value . "' " . $selected . ">" . $value;
                    }
                    ?>
                </select>
                <input type="submit" name="submitFilter" value="Filter" class='search-button'>
                </div>
            </form>
            <?php
            if (isset($_POST['submitFilter']) || isset($_POST['submitSearch']) || (isset($_GET['current']) && $_SESSION['admin']['searched']==1) || isset($_POST['submitSearchPrice'])) {
                if ($_SESSION['admin']['productsFilter'] == "name" || $_SESSION['admin']['productsFilter'] == "product_id") { //to display search bar
                    if (isset($_POST['submitSearch']) && !empty($_POST['toSearch'])) {
                        $holder = "value='" . htmlspecialchars($_POST['toSearch']) . "'";
                    } else
                        $holder = "placeholder='search products...'";
                    echo "<form action='manageProducts.php' method='POST'>
                    <input type='text' name='toSearch' " . $holder . "' class='search-input'>
                    <input type='submit' name='submitSearch' value='search' class='search-button'></form>";
                } else if ($_SESSION['admin']['productsFilter'] == "category_id") { //to display a select of categories
                    $categories = $dbHelper->getAllCategories("name");
                    if (!empty($categories)) {
                        echo "<form action='manageProducts.php' method='POST'>
                        <div class='filter-wrapper'>
                          <select name='toSearch' class='filter-select'>";
                        foreach ($categories as $value) {
                            $selected = ($_SESSION['admin']['productsSearch'] == $value->getCategoryId()) ? 'selected' : '';
                            echo "<option name='" . $value->getName() . "' value='" . $value->getCategoryId() . "' " . $selected . ">" . $value->getName();
                        }
                        echo "</select><input type='submit' name='submitSearch' value='search' class='search-button'></div></form>";
                    } else {
                        echo "<div class='error-container'><p style='color:red ; font-weight:bold'>Can't filter since no categories!</p></div>";
                    }
                } else if ($_SESSION['admin']['productsFilter'] == "price") { //to display a select of prices
                    $min = $_SESSION['admin']['minPrice'];
                    $max = $_SESSION['admin']['maxPrice'];
                    echo "<form action='manageProducts.php' method='POST'>
                    Min Price:<input type='number' id='Price' name='minPrice' min='0' value='" . $min . "' class='range-input'>
                    Max Price:<input type='number' id='Price' name='maxPrice' min='0' value='" . $max . "' class='range-input'>
                    <input type='submit' name='submitSearchPrice' value='search' class='search-button'></form>";
                } else if ($_SESSION['admin']['productsFilter'] == "stock") { //to display a select of stock
                    $stock = array("high in stock", "low in stock", "out of stock"); //fixed
                    echo "<form action='manageProducts.php' method='POST'>
                          <div class='filter-wrapper'>
                          <select name='toSearch' class='filter-select'>";
                    for ($i = 0; $i < count($stock); $i++) {
                        $selected = ($_SESSION['admin']['productsSearch'] == $stock[$i]) ? 'selected' : '';
                        echo "<option name='" . $stock[$i] . "' value='" . $stock[$i] . "' " . $selected . ">" . $stock[$i];
                    }
                    echo "</select><input type='submit' name='submitSearch' value='search' class='search-button'></div></form>";
                }
            }
            ?>
        </div>
    </div>

    <?php
    $products = null;
    //get products according to the SESSION containing the current to filter & search &order
    if (isset($_POST['submitFilter'])) {
        $_SESSION['admin']['current'] = 1; //new
        $products = $dbHelper->getAllProducts($_SESSION['admin']['productsFilter']);
    } else if (isset($_POST['submitSearch']) || $_SESSION['admin']['productsSearch']!= "") { //new
        if ($_SESSION['admin']['productsFilter'] == "name") {
            $products = $dbHelper->getProductsByName($_SESSION['admin']['productsSearch']);
        } else if ($_SESSION['admin']['productsFilter'] == "product_id") {
            $products = $dbHelper->getProductById($_SESSION['admin']['productsSearch']);
        } else if ($_SESSION['admin']['productsFilter'] == "category_id") {
            $products = $dbHelper->getProductsByCategory($_SESSION['admin']['productsSearch']);
        } else if ($_SESSION['admin']['productsFilter'] == "stock") {
            switch ($_SESSION['admin']['productsSearch']) {
                case 'high in stock':
                    $nbProducts = 2;
                    break;
                case 'low in stock':
                    $nbProducts = 1;
                    break;
                case 'out of stock':
                    $nbProducts = 0;
                    break;
                default:
                    $nbProducts = 2;
            }
            $products = $dbHelper->getProductsByStock($nbProducts);
        }
    } else if (isset($_POST['submitSearchPrice']) || $_SESSION['admin']['productsFilter'] == "price") { //new || $_SESSION['admin']['minPrice'] != '' || $_SESSION['admin']['maxPrice'] != ''
        if ($_SESSION['admin']['productsFilter'] == "price") {
            $a = $b = 0;
            if($_SESSION['admin']['minPrice'] != '' && $_SESSION['admin']['maxPrice'] != '' && $_SESSION['admin']['minPrice']>$_SESSION['admin']['maxPrice']){
                $error_message = "<div class='error-container'><p style='color:red ; font-weight:bold'>Min price can't be greater than Max Price!</p></div>";
            }
            else if ($_SESSION['admin']['minPrice'] != '' && $_SESSION['admin']['maxPrice'] != '' && $_SESSION['admin']['minPrice']<=$_SESSION['admin']['maxPrice']) {
                $a = $_SESSION['admin']['minPrice'];
                $b = $_SESSION['admin']['maxPrice'];
                $products = $dbHelper->getProductsByPrice($a, $b);
            } else if ($_SESSION['admin']['minPrice'] == '' && $_SESSION['admin']['maxPrice'] != '') {
                $a = 0;
                if ($dbHelper->getMinPrice() != null)
                    $a = $dbHelper->getMinPrice();
                $b = $_SESSION['admin']['maxPrice'];
                $products = $dbHelper->getProductsByPrice($a, $b);
            } else if ($_SESSION['admin']['minPrice'] != '' && $_SESSION['admin']['maxPrice'] == '') {
                $a = $_SESSION['admin']['minPrice'];
                $b = 0;
                if ($dbHelper->getMaxPrice() != null)
                    $b = $dbHelper->getMaxPrice();
                    $products = $dbHelper->getProductsByPrice($a, $b);
            }else{
                //$error_message = "<div class='error-container'><p style='color:red ; font-weight:bold'>Min price can't be greater than Max Price!</p></div>";
                $products = $dbHelper->getAllProducts($_SESSION['admin']['productsFilter']);
            }
            //$products = $dbHelper->getProductsByPrice($a, $b);
        }
    } else {
        $products = $dbHelper->getAllProducts($_SESSION['admin']['productsFilter']); //default
    }
    //print_r($products);
    if (is_array($products))
        $count = count($products);
    else
        $count = 0;
    if(isset($error_message)) echo $error_message;
    if (empty($products)) {
        echo "<div class='error-container'><p style='color:red ; font-weight:bold'>No products</p>" . $back . " </div>";
    } else {
        //echo "<div class='total-box'>total number of products<br>" . count($products) . "</div>";

        //pagination
        $perPage = 4;
        $totalPages = ceil($count / $perPage);
        $first = ($_SESSION['admin']['current'] * $perPage) - $perPage;
        if (is_array($products) && $products != null) {   //case where $products is an array of elements (default or by search)
            $temp = array_slice($products, $first, $perPage);

            echo " <div class='table-container'>
        <table border=1>
                   <tr>
                   <th>
                     <th>ID</th>
                     <th>Product Name</th>
                     <th>Category</th>
                     <th>Description</th>
                     <th>Stock</th>
                     <th>Price</th>
                     <th>Discount</th>
                     <th>Rating</th>
                     <th>Update</th>
                     <th>Delete</th>
                   </tr>";
            foreach ($temp as $key => $value) {
                if ($value->getDescription() == null) {
                    $truncatedDesc = $description = "N/D";
                } else {
                    $description = $value->getDescription();
                    $maxChars = 20; // Maximum characters to display initially
                    $truncatedDesc = strlen($description) > $maxChars ? substr($description, 0, $maxChars) . '...' : $description;
                }
                if ($value->getDiscount() == null) {
                    $discount = "N/A";
                } else
                    $discount = $value->getDiscount();
                if ($value->getRating() == null) {
                    $rating = "N/A";
                } else
                    $rating = $value->getRating();
                if ($value != null) {
                    $value->loadImages();
                    $images = $value->getImages();
                    //added by omar:-----
                    $imageUrl="";
                    foreach($images as $key=>$value2){
                        if($value!=null){
                        $imageUrl = str_replace('\\', '/', $value2);
                        break;
                        }
                    }
                    //-------------
                   // $imageUrl = str_replace('\\', '/', $images[0]);//commented (OMAR)
                    $substring = substr($imageUrl, strrpos($imageUrl, "images/"));

                    $cat = $dbHelper->getCategoryById($value->getCategoryId());
            //         echo "<tr>
            //         <td><img src='../" . $substring . "' width=80 height=80></td>
            //         <td>" . $value->getProductId() . "</td>
            //         <td>" . $value->getName() . "</td>
            //         <td>" . $cat->getName() . "</td>
            //         <td class='description' data-full-description='" . $description . "'>
            //        <span class='truncated'>" . $truncatedDesc . "</span>
            //         </td>
            //         <td>" . $value->getStock() . "</td>
            //         <td>" . $value->getPrice() . "</td>
            //         <td>" . $discount . "</td>
            //         <td>" . $rating . "</td>
            //         <td><button class='i-button'><a href='updateProduct.php?id=" . $value->getProductId() . "'>update</a></button></td>
            //         <td><button class='i-button'><a href='popupAdmin.php?id=" . $value->getProductId() . "&page=manageProducts'>delete</a></button></td>
            // </tr>";

            echo "<tr>
            <td><img src='../" . $substring . "' width=80 height=80></td>
            <td>" . $value->getProductId() . "</td>
            <td>" . $value->getName() . "</td>
            <td>" . $cat->getName() . "</td>
            <td class='description' data-full-description='" . $description . "'>
           <span class='truncated'>" . $truncatedDesc . "</span>
            </td>
            <td>" . $value->getStock() . "</td>
            <td>" . $value->getPrice() . "</td>
            <td>" . $discount . "</td>
            <td>" . $rating . "</td>
            <td><a href='updateProduct.php?id=" . $value->getProductId() . "'><button class='i-button'>update</button></a></td>
            <td><a href='popupAdmin.php?id=" . $value->getProductId() . "&page=manageProducts'><button class='i-button'>delete</button></a></td>
    </tr>";
                }
            }
            echo "</table>";
        } else if ($products != null) {    //case where $products is a single element (by search)
            $temp = $products;
            $totalPages = ceil(1 / $perPage);
            if ($temp->getDescription() == null) {
                $truncatedDesc = $description = "N/D";
            } else {
                $description = $temp->getDescription();
                $maxChars = 20; // Maximum characters to display initially
                $truncatedDesc = strlen($description) > $maxChars ? substr($description, 0, $maxChars) . '...' : $description;
            }
            if ($temp->getDiscount() == null) {
                $discount = "N/A";
            } else
                $discount = $temp->getDiscount();
            if ($temp->getRating() == null) {
                $rating = "N/A";
            } else
                $rating = $temp->getRating();

            $temp->loadImages();
            $images = $temp->getImages();
            $imageUrl = str_replace('\\', '/', $images[0]);
            $substring = substr($imageUrl, strrpos($imageUrl, "images/"));
            // echo " <div class='table-container'>
            // <table border=1>
            //            <tr>
            //              <th>ID</th>
            //              <th>Product Name</th>
            //              <th>Category ID</th>
            //              <th>Description</th>
            //              <th>Stock</th>
            //              <th>Price</th>
            //              <th>Discount</th>
            //              <th>Rating</th>
            //              <th>Update</th>
            //              <th>Delete</th>
            //            </tr>
            //            <tr>
            //            <td><img src='../" . $substring . "' width=120 height=120></td>
            //             <td>" . $temp->getProductId() . "</td>
            //             <td>" . $temp->getName() . "</td>
            //             <td>" . $temp->getCategoryId() . "</td>
            //             <td class='description' data-full-description='" . $description . "'>
            //        <span class='truncated'>" . $truncatedDesc . "</span>
            //         </td>
            //             <td>" . $temp->getStock() . "</td>
            //             <td>" . $temp->getPrice() . "</td>
            //             <td>" . $discount . "</td>
            //             <td>" . $rating . "</td>
            //             <td><button class='i-button'><a href='updateProduct.php?id=" . $temp->getProductId() . "'>update</a></button></td>
            //             <td><button class='i-button'><a href='popupAdmin.php?id=" . $temp->getProductId() . "&page=manageProducts'>delete</a></button></td>
            //     </tr></table>";

            echo " <div class='table-container'>
            <table border=1>
                       <tr>
                       <th>
                         <th>ID</th>
                         <th>Product Name</th>
                         <th>Category ID</th>
                         <th>Description</th>
                         <th>Stock</th>
                         <th>Price</th>
                         <th>Discount</th>
                         <th>Rating</th>
                         <th>Update</th>
                         <th>Delete</th>
                       </tr>
                       <tr>
                       <td><img src='../" . $substring . "' width=120 height=120></td>
                        <td>" . $temp->getProductId() . "</td>
                        <td>" . $temp->getName() . "</td>
                        <td>" . $temp->getCategoryId() . "</td>
                        <td class='description' data-full-description='" . $description . "'>
                   <span class='truncated'>" . $truncatedDesc . "</span>
                    </td>
                        <td>" . $temp->getStock() . "</td>
                        <td>" . $temp->getPrice() . "</td>
                        <td>" . $discount . "</td>
                        <td>" . $rating . "</td>
                        <td><a href='updateProduct.php?id=" . $temp->getProductId() . "'><button class='i-button'>update</button></a></td>
                        <td><a href='popupAdmin.php?id=" . $temp->getProductId() . "&page=manageProducts'><button class='i-button'>delete</button></a></td>
                </tr></table>";
        } else {
            // echo "<div class='error-container'><p style='color:red ; font-weight:bold'><br>No products to display</p><br> 
            // <button class='search-button'><a href='manageProducts.php'>Back</a></button></div>";

            echo "<div class='error-container'><p style='color:red ; font-weight:bold'><br>No products to display</p><br> 
            <a href='manageProducts.php'> <button class='back-button'>Back</button></a></div>";

        }
        //pagination
        if ($_SESSION['admin']['current'] > 1)
            echo "<button class='page-button'><a href='manageProducts.php?current=" . ($_SESSION['admin']['current'] - 1) . "'><</a></button>";

        echo " Page " . $_SESSION['admin']['current'] . "/" . $totalPages;

        if ($_SESSION['admin']['current'] < $totalPages)
            echo " <button class='page-button'><a href='manageProducts.php?current=" . ($_SESSION['admin']['current'] + 1) . "'>></a></button>";
        echo "<br><br>" . $back . "
            </div><br>";
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