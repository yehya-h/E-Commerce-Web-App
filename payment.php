<?php
session_start();
include_once ("connection.php");
// $_COOKIE['token'] = "e16621e9e667c1ce8e8f2964521b067b203ebf9ff9be6fdffc50d4824bcb4dfe";
// if (!isset($_COOKIE['token']))
//     header("Location: check_login.php");

/*-----------CODE ADDED BY OMAR */
include_once("check_login.php");
if($_SESSION['signed_in']==true){

    if($isOwner==true) header("Location:admin/owner.php");
    if($isAdmin==true) header("Location:admin/admin.php");
    if($isClient!=true) header("Location:index.php");
    //otherwise it must be a client 

}
else header("Location:sign_in.php");

$client = $dbHelper->getClientByToken($_COOKIE['token']);
if($client==null){
    header("Location:sign_out.php?isClient=1");
}
$clientId = $client->getClientId();

if (!isset($_SESSION['order'][$clientId]) || empty($_SESSION['order'][$clientId]))
    header("Location: cart.php");
$errormsg = "";
if (isset($_POST['addShip'])) {
    if (
        !empty($_POST['country']) && !empty($_POST['fullName']) && !empty($_POST['street_nb']) &&
        !empty($_POST['city']) && !empty($_POST['state']) && !empty($_POST['zipCode']) &&
        !empty($_POST['phoneNumber'])
    ) {
        $newShipmentInfo = new ShipmentInfo(
            null,
            $_POST['country'],
            $clientId,
            $_POST['fullName'],
            $_POST['street_nb'],
            (!empty($_POST['building'])) ? $_POST['building'] : null,
            $_POST['city'],
            $_POST['state'],
            $_POST['zipCode'],
            $_POST['phoneNumber']
        );
        $added = $dbHelper->addShipmentInfo($newShipmentInfo, $clientId);
        if ($added == false)
            $errormsg_1 = "Error occured";
        // else
        //     header("location: payment.php");
    } else {
        //$error = true;
        $errormsg_1 = "Please make sure that you fill all the fields of your shipment
                 information with the specified format";
    }
}
if (isset($_POST['addPay'])) {
    if (
        !empty($_POST['cardNumber']) && !empty($_POST['nameOnCard']) && !empty($_POST['expiryDate'])
        && !empty($_POST['securityCode'])
    ) {
        $newPaymentInfo = new PaymentInfo(
            null,
            $clientId,
            $_POST['cardNumber'],
            $_POST['nameOnCard'],
            $_POST['expiryDate'],
            $_POST['securityCode']
        );
        $added = $dbHelper->addPaymentInfo($newPaymentInfo, $clientId);
        if ($added == false)
            $errormsg_2 = "Error occured";
    } else {
        //$error=true;
        $errormsg_2 = "Please make sure that you fill all the fields of your payment
information with the specified format";
    }
}
if (isset($_POST['placeorder'])) {
    if (!isset($_POST['ship']) || !isset($_POST['payMethod']) || !isset($_POST['points']) || (isset($_POST['payMethod']) && $_POST['payMethod'] == 'credit' && !(isset($_POST['pay'])))) {
        $errormsg_3 = "<div><p class='error-message error'>Please fill out all informations</p></div>";
    } elseif ($_SESSION['modified'][$clientId] == true) {
        $errormsg_3= "<div><p class='error-message error'>Cart Modified</p></div>";
    } else {
        // $subTotal_amount = 0 ;
        // foreach ($_SESSION['order'][$clientId] as $itemId) {
        //     $item = $dbHelper->getCartItemByItemId($itemId);
        //     $product = $dbHelper->getProductById($item->getProductId());
        // if ($product->getStock() < $item->getQuantity())
        //         return false;// or header("Location: cart.php");
        //     $productId = $product->getProductId();
        //     $price = ($product->getPrice() * ((100 - $product->getDiscount()) / 100)) * $item->getQuantity();
        //     $subTotal_amount += $price;
        //      $orderItems[] =  new OrderItem(null,null,$productId,$item->getQuantity());
        // }
        //     $selectedShipInfo = $dbHelper->getShipmentInfoByShipmentId($_POST['ship']);
        //     $country = $dbHelper->getCountryByName($selectedShipInfo->getCountryName());
        //     $total_amount =$subTotal_amount + $country->getDeliveryFees();
        //     $order = new Order(null, $clientId, date('Y-m-d'), $_POST['ship'], $total_amount);
        if ($dbHelper->addOrder($clientId, $_SESSION['order'][$clientId], $_POST['ship'], $_POST['points'])) {
            unset($_SESSION['order'][$clientId]);
            header("Location: orderSummary.php");
        } else {
            header("Location: cart.php");
        }
    }
}
$_SESSION['modified'][$clientId] = false;
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.9">
    <title>Checkout | S&S</title>
    <!-- <link rel="stylesheet" href="style/payment.css" />
    <link rel="stylesheet" href="style/header.css"> -->
    <link rel="stylesheet" href="style/payment.css" />
    <link rel="stylesheet" href="style/header.css" />
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
    ///////////////////////////////////////////////////////////////
    $shipmentInfos = $dbHelper->getShipmentInfoByClientId($clientId);
    $paymentInfos = $dbHelper->getPaymentInfoByClientId($clientId);
    $selected = null;
    $subTotal = 0;
    $num_items = 0;
    $points = (isset($_POST['points'])) ? $_POST['points'] : 0;
    if (isset($errormsg_3)) echo $errormsg_3 ;
    echo "<form method='POST' action='payment.php'>
    <div class='page-title'><h1>Checkout</h1></div>";
    echo "<div class='info'>
            <div class='shipmentandpayment'>
          <div class='title'><h3>Choose a shipment info</h3></div>";
    echo "<div class='shipment'>";//all shipments
    if ($shipmentInfos != null) {
        foreach ($shipmentInfos as $shipmentInfo) {
            //$shipmentInfo = new ShipmentInfo(null,null,null,null,null,null,null,null,null,null);
            if (isset($_POST['ship']))
                $selected = ($_POST['ship'] == $shipmentInfo->getShipmentInfoId()) ? 'checked' : null;
            // $disabled = ($dbHelper->country_unique($shipmentInfo->getCountryName()) == 1) ? null : 'disabled';
            // $selected = (is_null($disabled)) ? $selected : null;
    
            if ($dbHelper->country_unique($shipmentInfo->getCountryName()) == 1) {
                $disabled = '';
                //$noship = '';
                $unavailable = '';
            } else {
                $disabled = "disabled ";
                //$noship = "style='background-color: #ffcccc;'";
                $selected = null;
                echo "<div><p class='error-message' style='margin: 0.5rem 0 0 0;'>Location currently unreachable</p></div>";
            }
            echo "<div class='shipmentinfo' >";//".$noship."
            echo "<input type='radio' name='ship' value='" . $shipmentInfo->getShipmentInfoId() . "' " . $selected . " " . $disabled . ">
            <div class='shipmentinfo-li'>
            <div>
            <li name='country' id='" . $shipmentInfo->getCountryName() . "'><span>Country: </span>" . $shipmentInfo->getCountryName() . "</li>
            <li><span>Full Name: </span>" . $shipmentInfo->getFullName() . "</li>
            <li><span>Phone Number: </span>" . $shipmentInfo->getPhoneNumber() . "</li>
            <li><span>City: </span>" . $shipmentInfo->getCity() . "</li></div>
            <div>
            <li><span>State: </span>" . $shipmentInfo->getState() . "</li>
            <li><span>StreetNb: </span>" . $shipmentInfo->getStreetNb() . "</li>
            <li><span>Building: </span>" . $shipmentInfo->getBuilding() . "</li>
            <li><span>ZipCode: </span>" . $shipmentInfo->getZipCode() . "</li></div>
            </div>
            </div>";
        }
    }
    if ($shipmentInfos == null || ($shipmentInfos != null && count($shipmentInfos) < 4)) {
        include_once ('addShipmentInfo.php');
        echo "<div class='button'><button id='shipForm'>Add new ShipmentInfo</button></div>";
    }
    //echo "<input type='submit' name='addNewShip' value='add new ShipmentInfo'>";
    //echo "<button><a href='addShipmentInfo.php'>add new ShipmentInfo</a></button>";
    echo "</div>";//all shipments
    echo "<div class='title'><h3>Choose a payment method</h3></div>";
     $checkCredit = (isset($_POST['payMethod']) && $_POST['payMethod'] == 'credit') ? 'checked' : null;
    echo "<div class='paymentmethod'>
    <div><input id='cash' type='radio' name='payMethod' value='cash' checked><span> Cash </span></div>
    <div><input id='credit' type='radio' name='payMethod' value='credit' ".$checkCredit." ><span> Credit Card </span></div>
    </div>";
    $display = (is_null($checkCredit)) ? 'none' : 'block';
    echo "<div id='allPayments' style='display:".$display.";'>";//all payments
    if ($paymentInfos != null) {
        foreach ($paymentInfos as $paymentInfo) {
            //$paymentInfo = new PaymentInfo(null,null,null,null,null);
            //$selected = ($client->getPaymentInfoId() == $paymentInfo->getPaymentInfoId()) ? 'checked' : null;
            if (isset($_POST['pay']))
                $selected = ($_POST['pay'] == $paymentInfo->getPaymentInfoId()) ? 'checked' : null;
            echo "<div class='paymentinfo'>
            <input type='radio' name='pay' value='" . $paymentInfo->getPaymentInfoId() . "' " . $selected . ">
    
            <div>
            <li><span>Card Number: </span>" . $paymentInfo->getCardNumber() . "</li>
            <li><span>Name On Card: </span>" . $paymentInfo->getNameOnCard() . "</li>
            <li><span>Expiry Date: </span>" . $paymentInfo->getExpiryDate() . "</li>
            <li><span>Security Code: </span>" . $paymentInfo->getSecurityCode() . "</li>
            </div>
            </div>";
        }
    }
    if ($paymentInfos == null || ($paymentInfos != null && count($paymentInfos) < 4)) {
        include_once ('addpaymentInfo.php');
        echo "<div class='button'><button id='payForm'>Add new PaymentInfo</button></div>";
    }
    echo "</div>";//all payments
    echo "</div>";
    ?>
    <div class="order">
        <div class='title'>
            <h3>Apply Coupon</h3>
        </div>
        <script>
            var clientPoints = <?php echo json_encode($client->getPoints()); ?>
        </script>
        <input type="range" id="points" name="points" min="0" step="1" max="<?php echo $client->getPoints(); ?>"
            value="<?php echo $points ?>">
        <p class="b"><span id="selectedpts">0</span><span> points</span></p>
        <hr>
        <div class='title'>
            <h3>Order Summary</h3>
        </div>
        <div class="bill">
            <!-- SelectedItems -->
            <div class='item'><img class='fake-img'><span class='name b'>Item</span><span
                    class='quantity b'>Quantity</span><span class='price b'>Price</span></div>
            <?php
            foreach ($_SESSION['order'][$clientId] as $itemId) {
                $item = $dbHelper->getCartItemByItemId($itemId);
                $product = $dbHelper->getProductById($item->getProductId());
                $productId = $product->getProductId();
                $price = ($product->getPrice() * ((100 - $product->getDiscount()) / 100)) * $item->getQuantity();
                $subTotal += $price;
                $num_items += $item->getQuantity();
                $product->loadImages2();
                $images = $product->getImages();
                echo "<div class='item'>
                <img src='" . $images[0] . "'>
                <span class='name'>" . $product->getName() . "</span>
                <span class='quantity'>" . $item->getQuantity() . "</span>
                <span class='price'>" . $price . " $</span>
            </div>";
            }
            ?>
        </div><!-- //SelectedItems -->
        <hr>
        <div class="sp b"><span>SubTotal(<span id='checkedItems'><?php echo $num_items ?></span> items)</span><span
                id='subTotal'><?php echo $subTotal ?> $</span></div>
        <div class="sp b"><span>Delivery </span><span id='deliveryFee'>--</span></div>
        <div class="sp b"><span>Coupon </span><span id='coupon'>--</span></div>
        <hr>
        <div class="sp b"><span>Total </span><span id='total'><?php echo $subTotal ?> $</span></div>
        <input id='order' type='submit' name='placeorder' value='Place Your Order'><br>
    </div>
    </div>
    </form>

    <script type="text/javascript" src="payment.js"></script>

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
       // window.location.href = "deleteAccount.php?del=1";
       window.location.href="manageAccount.php?forward=deleteAccount";
    }

</script>

</body>

<?php ?>

</html>