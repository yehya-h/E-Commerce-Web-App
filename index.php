<?php

include_once("connection.php");
include_once("include_all.php");

session_set_cookie_params(3600);
session_start();
// print_r(session_get_cookie_params());
// echo '<br><br>';
// print_r($_COOKIE);

// //session_destroy();
// echo "<br><br>";
//print_r($_SESSION);
include_once("check_login.php");
if($isAdmin==true) header("Location:admin/admin.php");
else if($isOwner==true) header("Location:admin/owner.php");

if(isset($_POST['search'])) //echo "<br>button clicked: ".$_POST['searchField']."<br>";
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/index.css">
    <link rel="stylesheet" href="style/footer.css" />
    <link rel="icon" type="image/x-icon" href="logos/primary_icon.jpeg" /> <!-- modified -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>S&S</title>
    
</head>
<body>
<header>
        <nav class="nav-container">
            <div class='left-nav'>
                <div class="pc-logo">
                    <a href="../index.php"><img src="../logos/primary_logo.png" alt="logo" width='220rem'
                            height='90rem'></a>
                </div>
                <div class="phone-logo">
                    <a href="../index.php"><img src="logos/primary_logo_phone_2.png" alt="logo" width='70em'
                            height='60em'></a>
                </div>
            </div>
            <div class="center-nav">
                <div class="search-bar">
                    <form id="filter" action="index.php" method="POST">
                        <!-- <label for="categoryFilter">Select Categories</label> -->
                        <div class="filter-wrapper">
                            <select id="categoryFilter" name="categoryFilter" class="filter-select">
                                <?php
                                $categories = $dbHelper->getAllCategories("name");
                                foreach ($categories as $category) {
                                    echo "<option value='" . $category->getCategoryId() . "'>" . $category->getName() . "</option>";
                                }
                                ?>
                            </select>
                            <input type="submit" name="filterByCategory" value="ok" class='filter-button' />
                        </div>
                    </form>
                    <form action="index.php" method="post">
                        <input type="text" placeholder="What are you looking for?" name="searchField" required
                            class='search-input' />
                        <button type="submit" name="search" class='search-button'><i class="bi bi-search"></i></button>
                    </form>
                </div>
            </div>
            <?php
            if ($_SESSION['signed_in'] == true && $isClient == true) {
                $client = $dbHelper->getClientByToken($_COOKIE['token']);
                if ($client == null) {
                    //account not founed depending on token => somebody signed in from another device
                    //in this case, sign out from this device to avoid errors
                    header("Location:sign_out.php?isClient=1");
                }


                //---------------------------
                // if(isset($_POST['addToCart'])){
                //     if(isset($_GET['addToCart'])){


                //     $product = $dbHelper->getProductById($_GET['product_id']);
                //     $productId = $_GET['product_id'];
                //     $stock = $product->getStock();
                    
                //     $selectedQuantity = 1;
    
                //     if($client==null){
                //         header("Location:sign_out.php?isClient=1");
                //     }
                //     $cartId = $client->getCartId();
                //     $cartItem = $dbHelper->getCartItemByCaPrId($cartId, $productId);
                //     //first time added to cart:
                //     if(! $cartItem){
                //         $cartItem = new CartItem(-1, $cartId, $productId, $selectedQuantity);
                //         $dbHelper->addCartItem($cartItem);
                //     }
                //     //added to cart before:
                //     else{
                //         $newQuantity = $selectedQuantity + $cartItem->getQuantity();
                //         if($newQuantity <= $stock){
                //             $cartItem->setQuantity($newQuantity);
                //             $dbHelper->updateCartItemQuantity($cartItem);
                //         }
                //         else{
                //             $cartItem->setQuantity($stock);
                //             $dbHelper->updateCartItemQuantity($cartItem);
                //         }
                //     }

                // }
                //-----------------------------
                $cart_id = $client->getCartId();
                $cartItems = 0;
                if ($dbHelper->getNbCartItems($cart_id) != null) {
                    $cartItems = $dbHelper->getNbCartItems($cart_id);
                }
                echo '<div class="right-nav"><h3>&nbsp<div class="account-select" onclick="showOptions()">
                    ' . $client->getFirstName() . '
                    <i class="bi bi-person-fill-gear"></i>
                    <select id="accountSelect" onchange="goToPage(this.value)">
                            <option disabled selected>--choose--</option>
                            <option value="manageAccount.php?forward=viewOrders">View Orders</option>
                            <option value="manageAccount.php?forward=updateProfile">Update Profile</option>
                            <option value="manageAccount.php?forward=changePassword">Change Password</option>
                            <option value="delete">Delete Account</option>
                        </select>
                        </div>
                    ';
                echo '&nbsp|&nbspPoints:' . $client->getPoints();
                echo '&nbsp<h2 style="background-color:transparent;"><div class="cart-icon"><a href="cart.php"><i class="bi bi-bag-fill"></i> </a>
                                   <span id="nbItems">' . $cartItems . '</span></div></h2>
                                ';
                // echo '&nbsp<a href="#about">About</a>';
                echo '&nbsp&nbsp<h2 style="background-color:transparent;">
                <a href="sign_out.php?isClient=1"><i class="bi bi-box-arrow-right"></i> </a></h2></h3></div>';
            } else {
                echo '<div class="right-nav">
                    <h3><a href="sign_in.php">Sign In&nbsp<i class="bi bi-person-fill"></i></a></h3>&nbsp &nbsp
                    <div class="cart-icon"><a href="cart.php"><i class="bi bi-bag-fill"></i> </a>
                                   <span id="nbItems">0</span></div></div>';
                // &nbsp &nbsp<a href="createAccount.php">Create account&nbsp<i class="bi bi-person-fill-add"></i></a>;
            }
            ?>
            <!-- <li><a href="#about">About</a></li> -->
            <!-- <li><a href="cart.php">Cart</a></li> -->
            <!-- </ul> -->
        </nav>
    </header>
    <div class="middle-section">
        <div class="popup-overlay" id="popup-overlay" style="display:none;">
            <div class="popup-content" id="popup-content">
                <h2>Are you sure you want to delete this account?</h2>
                <button class='add-button' onclick="deleteAccount()">Yes</button> <button class='add-button'
                    onclick="closePopUp()">No</button>
            </div>
        </div>
        


<?php
    //Add to cart(on submit) handling:
        if(isset($_POST['addToCart'])){
           // if(isset($_GET['addToCart'])){
            //signed in AND Client:
            if($_SESSION['signed_in'] && $isClient ){
                $product = $dbHelper->getProductById($_GET['product_id']);
                $productId = $_GET['product_id'];
                $stock = $product->getStock();
                
                $selectedQuantity = 1;

                // if($client==null){
                //     header("Location:sign_out.php?isClient=1");
                // }
                $cartId = $client->getCartId();
                $cartItem = $dbHelper->getCartItemByCaPrId($cartId, $productId);
                //first time added to cart:
                if(! $cartItem){
                    $cartItem = new CartItem(-1, $cartId, $productId, $selectedQuantity);
                    $dbHelper->addCartItem($cartItem);
                }
                //added to cart before:
                else{
                    $newQuantity = $selectedQuantity + $cartItem->getQuantity();
                    if($newQuantity <= $stock){
                        $cartItem->setQuantity($newQuantity);
                        $dbHelper->updateCartItemQuantity($cartItem);
                    }
                    else{
                        $cartItem->setQuantity($stock);
                        $dbHelper->updateCartItemQuantity($cartItem);
                    }
                }

                //echo "<p style='color:green;' > Added to Cart</p>";
                //header("Location:popup.php?page=displayProduct");
                echo "<script>
                window.location.href='popup.php?page=displayProduct'; </script>";
            }

            //not signed in OR not Client --> direct user to sign in page
            else{
                echo "<script>
                    window.location.href='sign_in.php'; </script>";
            }
        }
?>

        <?php
        //if user clicks on search by category
        if (isset($_POST['filterByCategory'])) {
            $categoryId = $_POST['categoryFilter'];
            $category = $dbHelper->getCategoryById($categoryId);
            //keep the selection
            echo "<script>
            var selectCategory = document.getElementById('categoryFilter');
            selectCategory.value='" . $categoryId . "';
        </script>";

            $prodsByCategory = $dbHelper->getProductsByCategory($categoryId);

            //display products by category:
            echo "<h2 style='margin-top:50px;' >" . $category->getName() . ":</h2><br>";
            echo "<div>";
            displayProducts($prodsByCategory);
            echo "</div>";
        }


        //if user used the search bar:
        else if (isset($_POST['search'])) {
            $name = $_POST['searchField'];
            $prodsByName = $dbHelper->getProductsByName($name);

            echo "<h2 style='margin-top:50px;' >Results for '" . htmlspecialchars($name) . "':</h2><br>";
            echo "<div>";
            displayProducts($prodsByName);
            echo "</div>";
        }

        //default (no search):
        else {
            ?>
 <!-- Content of index.php (products): -->
 <br><br>
                <!-- Top Deals -->
                <h2 class="section-title"><img src='logos\top-deal.png'></h2>
                <br>

                <div class="container">
                    <div id="topDealsContainer" class="prodContainer">
                        <?php
                        $topDeals = $dbHelper->getTopDeals();
                        displayProducts($topDeals);

                        ?>

                    </div>

                    <!-- scroll buttons for Top Deals container -->
                    <button class="leftScroll" onclick="leftScroll('topDealsContainer')"><i
                            class="bi bi-caret-left-fill"></i></button>

                    <button class="rightScroll" onclick="rightScroll('topDealsContainer')"><i
                            class="bi bi-caret-right-fill"></i></button>
                </div>
                <script>
                    function leftScroll(containerId) {
                        var container = document.getElementById(containerId);
                        container.scrollLeft -= 190;
                    }

                    function rightScroll(containerId) {
                        var container = document.getElementById(containerId);
                        container.scrollLeft += 190;
                    }
                </script>


                <!-- New Arrivals -->


                <h2 class="section-title"><img src='logos\new-arrival-3.png'></h2>
                <br>
                <div class="container">
                    <div id="newArrivalsContainer" class="prodContainer">
                        <?php
                        //$products first sorted for new arrivals using db function
                        $products = $dbHelper->getAllProducts("product_id", "DESC");

                        //display new arrivals:
                        displayProducts($products);

                        ?>

                    </div>

                    <!-- scroll buttons for New Arrivals container -->
                    <button class="leftScroll" onclick="leftScroll('newArrivalsContainer')"><i
                            class="bi bi-caret-left-fill"></i></button>
                    <button class="rightScroll" onclick="rightScroll('newArrivalsContainer')"><i
                            class="bi bi-caret-right-fill"></i></button>
                </div>

                <!-- Discounts -->
                <h2 class="section-title"><img src='logos\discounts.png'></h2>
                <br>
                <div class="container">
                    <div id="discountsContainer" class="prodContainer">
                        <?php

                        //custom function to use it in usort function for sorting the products array according to discount DESC
                        function sortDiscountDesc($p1, $p2)
                        {
                            return $p2->getDiscount() - $p1->getDiscount();
                        }
                        if($products)
                        usort($products, 'sortDiscountDesc');

                        //display discounts:
                        displayProducts($products);

                        ?>
                    </div>

                    <!-- scroll buttons for Discounts container -->
                    <button class="leftScroll" onclick="leftScroll('discountsContainer')"><i
                            class="bi bi-caret-left-fill"></i></button>
                    <button class="rightScroll" onclick="rightScroll('discountsContainer')"><i
                            class="bi bi-caret-right-fill"></i></button>
                </div>
            <?php echo "</div>";
        } ?>

<?php
        //function to display products:
        function displayProducts($products)
        {
            if ($products) {
                foreach ($products as $product) {
                    echo "<a href='displayProduct.php?product_id=" . $product->getProductId() . "' class='product-wrapper'>";
                    echo "<div class='displayProd'>";
                    $product->loadImages2();
                    echo "<img src='" . $product->getImages()[0] . "' style='width:80%;' >";
                    if(strlen($product->getName())<=15)
                    echo "<h3>" . $product->getName() . "</h3>";
                else
                echo "<h3>" . substr($product->getName(),0,15)."..." . "</h3>";

                    $rating = $product->getRating();
                    if (isset($rating)) {
                        echo "<div class='price-nav'>&nbsp&nbsp&nbsp<span class='discount-price' style='font-weight:bold;'>" . number_format($rating, 2) . "</span>&nbsp
                         <span class='star' >&#9733</span></div>";
                    } else
                        echo "<div class='price-nav'>&nbsp&nbsp&nbsp<span>-- </span>&nbsp<span class='star' >&#9733</span></div>";

                    $price = $product->getPrice();
                    $discount = $product->getDiscount();
                    if (isset($discount)) {
                        echo "<span class='price'>$" . number_format($product->getNewPrice(), 2) . "</span>";
                        echo "<div class='discount-price'><span style='color:gray;' ><del>$" . $price . "</del>&nbsp;</span>
                        <span style='color:green ; font-weight:bold;' >
                        " . $discount . "% OFF</span></div>";
                    } else {
                        echo "<span class='price'>$" . $price . "</span><div class='discount-price'><br></div>";
                    }

                    // echo "</div>";
                    // echo "</a>";

                    echo "<br>";

            //add to cart button:
            $stock = $product->getStock();
            if($stock > 0){
                //Add to cart form:
                echo "<form action='index.php?product_id=". $product->getProductId()."' method='post' class='add-to-cart-form'>";
                echo "<button type='submit' name='addToCart'  class='addBtn' ><h2><i class='bi bi-bag-plus'></i></h2></button>";
                echo "</form>";
                // echo '<form action="index.php" method="GET" class="add-to-cart-form">
                //         <input type="number" name="product_id" value="'.$product->getProductId().'" hidden="true" />
                //         <button type="submit" name="addToCart" class="addBtn" ><h2><i class="bi bi-bag-plus"></i></h2></button></form>';
            }
            //out of stock
            else{
                 echo '<form action="popup.php" class="add-to-cart-form" method="GET">
                 <input type="text" name="page" value="index" hidden="true"/>
                 <button type="submit" class="addBtn" style="color: red;" ><h2><i class="bi bi-bag-x"></i></h2></button>
                 </form>';
                 //echo '<form action="" class="add-to-cart-form"><button class="addBtn" style="color: red;" onClick="goToPage(popup.php?page=index)" ><h2><i class="bi bi-bag-x"></i></h2></button></form>';
                 
                }

            echo "</div></a>";
                }
            }

            //no results
            else {
                echo "<p style='text-align:center;' >No results found.</p>";
            }
        }
        ?>
        </div>
        <br><br><br> <!--  modified --!-->

<footer>
        <div class="footer">
            <div class="contact">
                <div style="text-align:center; ">
                    <p>Need Help?
                        <br>Contact us through any of these support channels
                    </p>
                </div>
                <div class="contact-us">
                    <div class="margin-right">
                        <p><a href="mailto:shopandshiponlinetrading@gmail.com" class="linkFooter">Contact us via
                                Mail<br>
                                <i class="bi-envelope-fill"></i></a></p>
                    </div>

                    <div class="margin-left">
                        <p><a href="https://wa.me/+96106388426" class="linkFooter">Whatsapp Us<br>
                                <i class="bi-whatsapp"></i></a></p>
                    </div>
                </div>
            </div>
            <p style="font-size:0.8rem; ">&copy; 2024 S&S. All Rights Reserved</p>
        </div>
    </footer>


        <?php
        if ($_SESSION['signed_in'] == true && $isClient == true) {
            echo '<div class="bottom-bar-phone">
                            <div class="bar-phone-items">
                                <div><h2 style="color:var(--navly-blue);"><div class="account-select">
                                <i class="bi bi-person-fill-gear"></i>
                                <select id="accountSelect" onchange="goToPage(this.value)">
                                        <option disabled selected>--choose--</option>
                                        <option value="manageAccount.php?forward=viewOrders">View Orders</option>
                                        <option value="manageAccount.php?forward=updateProfile">Update Profile</option>
                                        <option value="manageAccount.php?forward=changePassword">Change Password</option>
                                        <option value="delete">Delete Account</option>
                                    </select>
                                    </div></h2></div>
                                    
                                    <div><h4 style="color:var(--navy-blue);">Points:' . $client->getPoints() . '</h4></div>

                                <div><h2 ><div class="cart-icon"><a href="cart.php" style="color:var(--navy-blue);"><i class="bi bi-bag-fill"></i> </a>
                                    <span id="nbItems" style="background-color:var(--bordo);">' . $cartItems . '</span></div></h2></div>

                                

                                <div><h2 ><a href="sign_out.php?isClient=1" style="color:var(--navy-blue);"><i class="bi bi-box-arrow-right"></i> </a>   </h2></div>
                                </div>
                        </div>';

        } else {
            echo '<div class="bottom-bar-phone">
                            <div class="bar-phone-items">

                                <div><h2 ><div class="cart-icon"><a href="cart.php" style="color:var(--navy-blue);"><i class="bi bi-bag-fill"></i> </a>
                                    <span id="nbItems" style="background-color:var(--bordo);">0</span></div></h2></div>

                                <div><h3><a href="sign_in.php" style="color:var(--navy-blue);">Sign In&nbsp<i class="bi bi-person-fill"></i></a></h3></h2></div>
                                </div>
                        </div>';
        }


        ?>

        <script>

            function showOptions() {
                var select = document.getElementById('accountSelect');
                select.focus(); // Focus on the select element to show its options
            }

            function goToPage(src) {
                if (src == "delete") {
                    openPopUp();
                } else {
                    window.location.href = src;
                }
            }

            // Function to close the popup
            function closePopUp() {
                document.getElementById('popup-overlay').style.display = 'none';
                // window.location.href="sign_in.php";
                //window.location.href=next;
            }

            // Function to open the popup when the page loads
            function openPopUp() {
                document.getElementById('popup-overlay').style.display = 'block';
            }

            function deleteAccount() {
                document.getElementById('popup-overlay').style.display = 'none';
                //window.location.href = "deleteAccount.php?del=1";
                window.location.href = "manageAccount.php?forward=deleteAccount";
            }

        </script>
        <?php  //echo $_SESSION['keep_signed']; ?>
       
</body>
</html>
