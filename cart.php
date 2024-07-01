<?php
session_start();
include_once ("connection.php");
// $_COOKIE['token'] = "e16621e9e667c1ce8e8f2964521b067b203ebf9ff9be6fdffc50d4824bcb4dfe";
// if (!isset($_COOKIE['token']))
//     header("Location: check_login.php");

    /*-----------CODE ADDED BY OMAR */
include_once("check_login.php");
if($_SESSION['signed_in']==true){

    if($isOwner==true) echo '<script>window.location.href="admin/owner.php";</script>';// header("Location:admin/owner.php");
    if($isAdmin==true) echo '<script>window.location.href="admin/admin.php";</script>';//header("Location:admin/admin.php");
    if($isClient!=true)echo '<script>window.location.href="sign_in.php";</script>';// header("Location:sign_in.php");
    //otherwise it must be a client 


}
else echo '<script>window.location.href="sign_in.php";</script>';//header("Location:sign_in.php");



    ////////////////////////////////////////////////////////////////////
    $client = $dbHelper->getClientByToken($_COOKIE['token']);
    // if($client==null){
    //     header("Location:sign_out.php?isClient=1");
    // }
    $clientId = $client->getClientId();
    $cart_id = $client->getCartId();
    $items = $dbHelper->getCartItemsByCartId($cart_id);
    //$order = $_SESSION['order'][$clientId];
    //print_r($_SESSION);
    $error = null;
    if (isset($_POST['update']) || isset($_POST['checkout'])) {
        $_SESSION['modified'][$clientId] = true;
        unset($_SESSION['order'][$clientId]);
        if (isset($_POST['cb']))
            $_SESSION['order'][$clientId] = array_keys($_POST['cb']);
        $quantities = null;
        if ($items != null)
            foreach ($items as $key => $item) {
                $id = $item->getItemId();
                if (isset($_POST['quantity'][$id])) {
                    //if (isset($_POST['cb'][$id]))
                    // $_SESSION['order'][$clientId][] = $id;
                    if ($_POST['quantity'][$id] != $item->getQuantity()) {
                        $quantities[$id] = $_POST['quantity'][$id];
                        $item->setQuantity($_POST['quantity'][$id]);
                    }
                } else {
                    $dbHelper->removeCartItem($id);
                    unset($items[$key]);
                }
            }
        if ($quantities != null)
            $dbHelper->updateQuantities($quantities);
        // }
        // if (isset($_POST['checkout'])) {
    
        if (!isset($_POST['cb'])) {
            if (isset($_POST['checkout']))
                // echo "<script>alert('No items selected for checkout. Please select at least one item to proceed with your order.');</script>";
                $error[0] = 'No items selected for checkout. Please select at least one item to proceed with your order.';
        } else {
            foreach ($_POST['cb'] as $itemId => $productId) {
                $product = $dbHelper->getProductById($productId);
                if ($product->getStock() < $_POST['quantity'][$itemId]) {
                    $error[$itemId] = "Only " . $product->getStock() . " of this product in stock";
                }
            }
            if ($error != null)
                $error[0] = "We're sorry, but it seems that some of the items in your cart have quantities that exceed our current stock availability.";
            elseif (isset($_POST['checkout']))
                header("Location: payment.php");
        }
    }
    ?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | S&S</title>
    <link rel="stylesheet" href="style/header.css"/>
    <link rel="stylesheet" href="style/cart.css" />
    <link rel="stylesheet" href="style/footer.css" />
    <link rel="icon" type="image/x-icon" href="../logos/primary_icon.jpeg" />
    <!-- <link rel="icon" type="image/x-icon" href=".\images\logo_project.png" /> -->
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
                                   <span name="nbItems">' . $cartItems . '</span></div></h2>
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
            </ul>
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
    if ($items == null) {
        ?>
        <div class="empty-cart">
            <h2>Your shopping cart is empty</h2>
            <p>What are you waiting for ?</p>
            <br>
            <a href="index.php">START SHOPPING</a>
        </div>
        <?php
    } else {
        $itemsCount = count($items);
        $allItems = 0;
        foreach ($items as $item)
            $allItems += $item->getQuantity();
        if (isset($error))
            echo "<div><p class='error-message error'>" . $error[0] . "</p></div>";
        echo "<form method='POST' action='cart.php'>
        <div><span class='page-title'>Cart</span><span class='count'> (<span id='allItems'>" . $allItems . "</span> items)</span></div>";
        echo "<div class='page-info'>
                <div class='cart'>";
        echo "<div class='item row-title' ><div class='product'>PRODUCT</div><div class='prices'><div class='price'><span>PRICE</span></div><div class='quantity'><span>QUANTITY</span></div><div class='price'><span>TOTAL</span></div></div></div>";
        foreach ($items as $item) {
            $id = $item->getItemId();
            $quantity = $item->getQuantity();
            $product = $dbHelper->getProductById($item->getProductId());
            $productId = $product->getProductId();
            $price = $product->getPrice() * ((100 - $product->getDiscount()) / 100);
            $stock = $product->getStock();
            $disabled = ($stock == 0) ? 'disabled' : '';
            $checked = '';
            if (isset($_SESSION['order'][$clientId]) && in_array($id, $_SESSION['order'][$clientId])) {
                if ($stock >= $quantity) {
                    $checked = 'checked';
                } else {
                    $key = array_search($id, $_SESSION['order'][$clientId]);
                    if ($key != false)
                        unset($_SESSION['order'][$clientId][$key]);
                    if (isset($_SESSION['order'][$clientId][0]) || $_SESSION['order'][$clientId][0] == $id) // handling for 0 because false is 0 ($key != false)
                        unset($_SESSION['order'][$clientId][0]);
                }
            }
            $lowStock = ($stock == 0) ? 'style="background-color: #ffcccc;"' : '';
            $product->loadImages2();
            $images = $product->getImages();
            if (isset($error[$id]))
                echo "<div class='error-message'><span id='error" . $id . "' class='error-message'>" . $error[$id] . "</span></div>";
            echo "<div id='" . $id . "' class='item' name='item' " . $lowStock . ">";
            if ($stock == 0) {
                echo "<span class='out-of-stock'>Out of Stock</span>";
            }
            echo "<div class='checkbox'><input type='checkbox' id='cb" . $id . "' value='" . $productId . "' name='cb[" . $id . "]' " . $disabled . " " . $checked . "></div>";
            echo "<div><a href='displayProduct.php?product_id=".$productId."'><img src='" . $images[0] . "' loading='lazy'></a></div>";
            echo "<div class='product-info'>
            <p class='product-name'>" . $product->getName() . "</p>
            <p class='description'>" . $product->getDescription() . "</p>
            <button class='remove' onclick='remove(" . $id . ")'><i class='bi bi-trash3-fill'></i> remove</button>
            </div>";
            echo "<div class='prices' >
            <div class='price'>$<span id='price" . $id . "'>" . $price . "</span></div>
            <div class='quantity'>
            <button id='btn_dec' onclick='decrement(" . $id . ")'>-</button>
            <input id='quantity" . $id . "' name='quantity[" . $id . "]' type='number' value='" . $quantity . "' >
            <button id='btn_inc' onclick='increment(" . $id . ")'>+</button>
            </div>
            <div class='price'>$<span id='totalitem" . $id . "'>" . $price * $quantity . "</span></div>";
            echo "</div>";
            echo "</div>";
        }
        echo "</div>";
        ?>
        <div class="summary">
            <div class="title">
                <h3>Order Summary</h3>
            </div>
            <p>SubTotal(<span id='checkedItems'>0</span> items)</p>
            <div class="title">
                <h3>Order Totals</h3>
            </div>
            <p class='sp'><span>Sub-Total </span><span id='subTotal'>0 $</span></p>
            <!-- <div><span>Total </span><span id='Total'>0 $</span></div> -->
            <div>
                <input id='checkout' type='submit' name='checkout' value='Checkout'><br>
                <input id='update' hidden='true' type='submit' name='update' value='Update Cart'>
            </div>
        </div>
        </div>
        </form>
        <div><a href='index.php' class='shopping'>Continue Shopping</a></div>
                <?php
    }
    ?>


            </div> <!-- close the middle section div -->
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
//this code must be at last
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
                                <span name="nbItems" style="background-color:var(--bordo);">' . $cartItems . '</span></div></h2></div>

                            

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
        window.location.href="manageAccount.php?forward=deleteAccount";
    }

</script>

</body>
    <script type="text/javascript" src="cart.js"></script> <!-- Not with header -->
</html>