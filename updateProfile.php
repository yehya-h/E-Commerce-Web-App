<?php
include_once("include_all.php");
include_once("connection.php");
session_start();
if(!isset($_SESSION['user']) || empty($_SESSION['user'])) header("Location:index.php");//add it in all files that have session
//session_destroy();
$error = false;
$errormsg = "";
$display="none";
$firstName="";
$lastName="";
$paymentInfos=null;
$shipmentInfos=null;
// print_r($_POST);
//echo "<br>".(count($_POST['cardNumber']));


include_once ("check_login.php");
// if ($isAdmin == true)
//     header("Location:admin/admin.php");
// else 
if ($isOwner == true)
    header("Location:admin/owner.php");

if (isset($_SESSION['user']) && $_SESSION['user'] == "client") {
   // echo "TEST";

    if(isset($_GET['deleteShipment'])){
        //dont delete, just set the client id to null
        if($dbHelper->deleteShipmentInfo($_GET['id'])==false){
            $error=true;
            $errormsg="An error occured in deleting shipment info";
        }
        else  header("Location:updateProfile.php");

    }

   // echo "<br>After Shipment";


    if(isset($_GET['deletePayment'])){
        //delete
        if($dbHelper->deletePaymentInfo($_GET['id'])==false){
            $error=true;
            $errormsg="An error occured in deleting  payment info.";
        }

        else header("Location:updateProfile.php");
    }
    //echo "<br>After payment";

    $display="block";
    $client = $dbHelper->getClientByToken($_COOKIE['token']);
    //print_r($client);
    $shipmentInfos = $dbHelper->getShipmentInfoByClientId($client->getClientId());
    $paymentInfos = $dbHelper->getPaymentInfoByClientId($client->getClientId());

    //echo "<br>After declaration";

    $paymentCount=($paymentInfos!=null)? json_encode(count($paymentInfos)) : 0;
    $shipmentCount=($shipmentInfos!=null)? json_encode(count($shipmentInfos)) : 0;


    // echo '<script>';
    // echo 'var paymentCount=' . json_encode(count($shipmentInfos)) . ';';
    // echo 'var shipmentCount=' . json_encode(count($paymentInfos)) . ';';
    // echo '</script>';

    echo '<script>';
    echo 'var paymentCount=' . $paymentCount. ';';
    echo 'var shipmentCount=' . $shipmentCount . ';';
    echo '</script>';

    //echo "<br>T1";

    if (isset($_POST['update'])) {

        $client->setFirstName($_POST['firstName']);
        $client->setLastName($_POST['lastName']);
        $client->setPhoneNumber($_POST['phone_number']);

        //$_SESSION['update']['payment']=array();
        //$_SESSION['update']['shipment']=array();

        if (
            isset($_POST['cardNumber']) &&  isset($_POST['nameOnCard']) &&  isset($_POST['expiryDate']) &&
            isset($_POST['securityCode']) ) {

            for ($i = 1; $i < count($_POST['cardNumber']); $i++) {
                if (
                    !empty($_POST['cardNumber'][$i]) && !empty($_POST['nameOnCard'][$i]) && !empty($_POST['expiryDate'][$i])
                    && !empty($_POST['securityCode'][$i])
                ) {

                    // for($i=1;$i<count($_POST['cardNumber']);$i++){

                    //     $_SESSION['update']['payment'][$i-1]=new PaymentInfo(null,null,$_POST['cardNumber'][$i],
                    //     $_POST['nameOnCard'][$i],$_POST['expiryDate'][$i],$_POST['securityCode'][$i]);

                    // }
                } 
                    else {

                    $error = true;
                    $errormsg = "Please make sure that you fill all the fields of your payment
                 information with the specified format";
                    break;
                }
            }

            
            if (  isset($_POST['country']) &&  isset($_POST['fullName']) && isset($_POST['street_nb']) &&
                isset($_POST['city']) &&  isset($_POST['state']) &&   isset($_POST['zipCode']) &&
                isset($_POST['phoneNumber']) ) {

                for ($i = 1; $i < count($_POST['country']); $i++) {
                    if (
                        !empty($_POST['country'][$i]) && !empty($_POST['fullName'][$i]) && !empty($_POST['street_nb'][$i]) &&
                        !empty($_POST['city'][$i]) && !empty($_POST['state'][$i]) && !empty($_POST['zipCode'][$i]) &&
                        !empty($_POST['phoneNumber'][$i])) {

                        // for($i=1;$i<count($_POST['country']);$i++){

                        //     $_SESSION['update']['shipment'][$i-1]=new ShipmentInfo(null,$_POST['country'][$i],null,
                        //     $_POST['fullName'][$i],$_POST['street_nb'][$i],
                        //     (!empty($_POST['building'][$i]))?$_POST['building'][$i]:null,$_POST['city'][$i],$_POST['state'][$i],
                        //     $_POST['zipCode'][$i],$_POST['phoneNumber'][$i]);
                        // }
                    } 
                        else /*if(!empty($_POST['country'])>1)*/ {

                        $error = true;
                        $errormsg = "Please make sure that you fill all the fields of your shipment 
                        information with the specified format";
                    }
                }
            }
        }

        if($error==false){
            echo "<br>Entered cond";
            //add infos and update client:

            //add shipment
            if(!empty($_POST['country']) && count($_POST['country'])>1){

                for($i=1;$i<count($_POST['country']);$i++){

                    $shipment=new ShipmentInfo(null,$_POST['country'][$i],$client->getClientId(),
                    $_POST['fullName'][$i],$_POST['street_nb'][$i],
                    (!empty($_POST['building'][$i]))?$_POST['building'][$i]:null,$_POST['city'][$i],
                    $_POST['state'][$i],$_POST['zipCode'][$i],$_POST['phoneNumber'][$i]);

                    if($dbHelper->addShipmentInfo($shipment,$client->getClientId())==false){

                        $error=true;
                        $errormsg="An error occured in adding shipment info";
                    }
                }
            }

            //add payment:
            if(!empty($_POST['cardNumber']) && count($_POST['cardNumber'])>1){

                // for($i=1;$i<count($_POST['cardNumber']);$i++){

                //     $payment=new PaymentInfo(null,$client->getClientId(),$_POST['cardNumber'][$i],$_POST['nameOnCard'][$i],$_POST['expiryDate'][$i],$_POST['securityCode'][$i]);
                //     if($dbHelper->addPaymentInfo($payment,$client->getClientId())==false){

                //         $error=true;
                //         $errormsg="An error occured in Adding payment info";
                //         break;
                //     }
                //     else{
                //       //  $error=true;//just for testing !!!!
                //         echo "<br>Payment added<br>";
                //     }

                // }

                for($i=1;$i<count($_POST['cardNumber']);$i++){

                    $payment=new PaymentInfo(null,$client->getClientId(),$_POST['cardNumber'][$i],$_POST['nameOnCard'][$i],$_POST['expiryDate'][$i],$_POST['securityCode'][$i]);
                    if(($id=$dbHelper->addPaymentInfo($payment,$client->getClientId()))==-1){

                        $error=true;
                        $errormsg="An error occured in Adding payment info";
                        break;
                    }
                    else{
                        $client->setPaymentInfoId($id);
                        if($dbHelper->updateClient($client)==false){
                            $error=true;
                            $errormsg="Error in updating payment info id for client";
                            break;
                        }
                      //  $error=true;//just for testing !!!!
                        echo "<br>Payment added<br>";
                    }

                }
            }
            else{

                //no added payment methods, so explicitly update the client
                if($dbHelper->updateClient($client)==false){

                    $error=true;
                    $errormsg="Error while updating  Client Information.";
                }
            }

            if($error==false){


                header("Location:popup.php?page=updateProfile");
            }



        }

        else{

            $errormsg="An unexpected error has occured !!";
            //an error occured
        }

    }


    // if(isset($_GET['deleteShipment'])){
    //     //dont delete, just set the client id to null
    //     if($dbHelper->deleteShipmentInfo($_GET['id'])==false){
    //         $error=true;
    //         $errormsg="An error occured in deleting shipment info";
    //     }

    // }


    // if(isset($_GET['deletePayment'])){
    //     //delete
        
    // }
 //   echo "<br>T2";
}

else if($_SESSION['user']=="admin"){
    //echo "ADMIN";
    $admin=$dbHelper->getAdminByToken($_COOKIE['token']);
    if($admin==null){
        header("Location:sign_out.php?isClient=1");
    }

    if(isset($_POST['update'])){

        $admin->setFirstName($_POST['firstName']);
        $admin->setLastName($_POST['lastName']);
        $admin->setAddress($_POST['address']);
        $admin->setPhoneNumber($_POST['phone_number']);

        if($dbHelper->updateAdmin($admin)==false){

            $error=true;
            $errormsg="An error occured while updating the admin";
        }

        else{

            //success
            header("Location:popup.php?page=updateProfile");
        }

    }




}

?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/profile.css">
    <link rel="stylesheet" href="style/header.css">
    <link rel="stylesheet" href="style/footer.css">
    <link rel="icon" type="image/x-icon" href="logos/primary_icon.jpeg" /> <!-- modified -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Update Profile</title>
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
            } 
            else if ( $_SESSION['signed_in'] == true && $isAdmin == true) {
                $admin = $dbHelper->getAdminByToken($_COOKIE['token']);
                if($admin==null){
                    header("Location:../sign_out.php?isAdmin=1");
                }
                // echo '<div class="center-nav"><div><h1>Admin Panel</h1></div></div>
                // <div class="right-nav"><div><h3>Admin : ' . $admin->getFirstName() . ' </h3></div>';
                // echo '<div><a href="../manageAccount.php?user=admin"><i class="bi bi-person-fill-gear"></i> </a></div>
                // <div><a href="../sign_out.php?isAdmin=1"><i class="bi bi-box-arrow-right"></i> </a></div></div>';
                // //echo '<p><a href="#about">About</a></p></div>';
                echo '<div class="center-nav"><div><h1>Admin Panel</h1></div></div>
                <div class="right-nav"><div><h3>Admin : ' . $admin->getFirstName() . ' </h3></div>&nbsp';

                echo '<div class="account-select">
                <i class="bi bi-person-fill-gear"></i>
                <select id="accountSelect" onchange="goToPage(this.value)">
                    <option disabled selected>--choose--</option>
                    <option value="../manageAccount.php?forward=updateProfile">Update Profile</option>
                    <option value="../manageAccount.php?forward=changePassword">Change Password</option>
                    <option value="delete">Delete Account</option>
                </select></div>';
                // echo '<div class="account-select">
                // <i class="bi bi-person-fill-gear"></i>
                // <select id="accountSelect" onchange="goToPage(this.value)">
                //     <option disabled selected>--choose--</option>
                //     <option value="manageAccount.php?forward=updateProfile">Update Profile</option>
                //     <option value="manageAccount.php?forward=changePassword">Change Password</option>
                //     <option value="delete">Delete Account</option>
                // </select></div>';
                echo '<div><a href="../sign_out.php?isAdmin=1"><i class="bi bi-box-arrow-right"></i> </a></div></div>';
            }
            else {
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
<div class='create-container'>
        <h1>Update Profile</h1>
        <form action="updateProfile.php" method="POST">
            <div class='acc-container'>
                <div class='table-container-field'>
                    <h2>Personal Information</h2>
                    <?php if ($_SESSION['user'] == "client") {
                        $firstName = $client->getFirstName();
                    } else if ($_SESSION['user'] == "admin") {
                        $firstName = $admin->getFirstName();
                    }
                    ?>
                    <?php
                    echo '<p style="color:red;">' . $errormsg . '</p>';
                    ?>
                    <table>

                        <tr>
                            <th>First Name</th>
                            <td><input type="text" name="firstName" class='add-input' value="<?php echo $firstName; ?>"
                                    required></td>
                        </tr>

                        <?php
                        if ($_SESSION['user'] == "client") {

                            $lastName = $client->getLastName();
                        } else if ($_SESSION['user'] == "admin") {

                            $lastName = $admin->getLastName();
                        }
                        ?>

                        <tr>
                            <th>Last Name</th>
                            <td><input type="text" name="lastName" class='add-input' value="<?php echo $lastName; ?>"
                                    required></td>
                        </tr>

                        <?php
                        if ($_SESSION['user'] == "admin")
                            echo '<tr>
                                    <th>Address</th>
                                    <td><input type="text" class="add-input" name="address" value="' . $admin->getAddress() . '" required></td></tr>';
                        ?>
                        <tr>
                            <th>Phone Number</th>
                            <td><input type="text" name="phone_number" class='add-input' placeholder="Ex: +961-81888888"
                                    title="+XXX-XXXXXXX..." pattern="\+\d{3}-\d{8,20}" value=<?php
                                    if ($_SESSION['user'] == "client") {
                                        echo (!empty($client->getPhoneNumber()) && $client->getPhoneNumber() != null) ? $client->getPhoneNumber() : "";
                                    } else if ($_SESSION['user'] == "admin") {
                                        echo (!empty($admin->getPhoneNumber()) && $admin->getPhoneNumber() != null) ? $admin->getPhoneNumber() : "";
                                    }
                                    ?>></td>
                        </tr>
                    </table>
                    <br>
                </div>
            </div>
            <br>
            <div id="clientRelated" style="display:<?php echo $display; ?>;">
                <hr style="display: <?php echo $display; ?>;">
                <h2>Payment Information</h2>
                <?php

                if (!empty($paymentInfos)) {

                    //display them in  a table
                    echo '<div class="table-radius">
                    <div class="table-container-display">
                            <table border=1>
                            <thead>
                                <tr>
                                    <th>Card Number</th>
                                    <th>Name On Card</th>
                                    <th>Expiry Date</th>
                                    <th>Security Code</th>
                                    <th> </th>
                                </tr></thead><tbody>';
                    foreach ($paymentInfos as $key => $value) {
                        echo '<tr><td label="Card Number">' . $value->getCardNumber() . '</td>
                                <td label="Name On Card">' . $value->getNameOnCard() . '</td>
                                <td label="Expiry Date">' . $value->getExpiryDate() . '</td>
                                <td label="Security Code">' . $value->getSecurityCode() . '</td>
                                <td><button class="i-button"><a href="updateProfile.php?deletePayment=1&id=' . $value->getPaymentInfoId() . '">Delete</a></button></td>
                            </tr>';
                    }
                    echo '</tbody></table></div></div>';
                }
                ?>
                <!-- <script>setMobileTable('.table-container-display')</script> -->

                <br>
                <div id="paymentInfoContainer" class='payment-container'>
                    <div class="paymentInfo" style="display: none;">
                        <div class='table-container'>
                            <table>
                                <tr>
                                    <th>Card Number <span style="color:red;">*</span></th>
                                    <td><input type="text" name="cardNumber[]" class='add-input'
                                            title="Only digits (8 to 19)" pattern="\d{8,19}"></td>
                                </tr>
                                <tr>
                                    <th>Name On Card <span style="color:red;">*</span></th>
                                    <td><input type="text" class='add-input' name="nameOnCard[]"></td>
                                </tr>
                                <tr>
                                    <th>Expiry Date <span style="color:red;">*</span></th>
                                    <td><select name="expiryDate[]" class='select-button'>
                                            <br>
                                            <?php

                                            for ($i = 24; $i < 30; $i++) {

                                                $start = ($i == 24) ? 6 : 1;
                                                for ($j = $start; $j < 13; $j++) {

                                                    $date = "";
                                                    if ($j < 10)
                                                        $date .= "0";
                                                    $date .= $j . "/" . $i;

                                                    echo '<option name="' . $date . '">' . $date . '</option>';
                                                }
                                            }
                                            ?>
                                        </select></td>
                                </tr>
                                <tr>
                                    <th>Security Code <span style="color:red;">*</span></th>
                                    <td><input type="text" class='add-input' name="securityCode[]" minlength="3"
                                            maxlength="4" pattern="\d{3,4}"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <br>

                <?php
                if ( $_SESSION['user']=="client" && ($paymentInfos == null || (is_array($paymentInfos) &&count($paymentInfos) < 4))) {
                    echo '<button type="button" class="add-button" onclick="addPaymentInfo()">Add Payment Information</button>';
                }
                ?>

                <script>

                    function addPaymentInfo() {
                        if (paymentCount < 4) {

                            var paymentInfoContainer = document.getElementById('paymentInfoContainer');
                            var clonedPaymentInfo = paymentInfoContainer.querySelector('.paymentInfo').cloneNode(true);
                            clonedPaymentInfo.style.display = 'block';
                            paymentInfoContainer.appendChild(clonedPaymentInfo);
                            paymentCount++;
                        }
                        else alert("You can add only 4 payment information forms for your account");
                    }

                </script>

                <hr style="display: <?php echo $display; ?>;">

                <?php

                if (!empty($shipmentInfos)) {

                    echo '<h2>Shipment Information</h2>
            <div class="table-radius">
                <div class="table-container-display"><table border=1>
                        <thead>
                                <tr>
                                    <th>Country</th>
                                    <th>Full Name</th>
                                    <th>Street Number</th>
                                    <th>Building</th>
                                    <th>City</th>
                                    <th>State</th>
                                    <th>Zip Code</th>
                                    <th>Phone Number</th>
                                    <th> </th>
                                </tr></thead><tbody>';
                    foreach ($shipmentInfos as $key => $value) {
                        $building = (!empty($value->getBuilding()) && $value->getBuilding() != null) ? $value->getBuilding() : 'N/A';
                        echo '<tr>
                            <td label="Country">' . $value->getCountryName() . '</td>
                            <td label="Full Name">' . $value->getFullName() . '</td>
                            <td label="Street Number">' . $value->getStreetNb() . '</td>
                            <td label="Building">' . $building . '</td>
                            <td label="City">' . $value->getCity() . '</td>
                            <td label="State">' . $value->getState() . '</td>
                            <td label="Zip Code">' . $value->getZipCode() . '</td>
                            <td label="Phone Number">' . $value->getPhoneNumber() . '</td>
                            <td><button class="i-button"><a href="updateProfile.php?deleteShipment=1&id=' . $value->getShipmentInfoId() . '">Delete</a></button></td>
                            </tr>';
                    }
                    echo '</tbody></table></div>';
                }
                ?>
                <!-- <script>setMobileTable('.table-container-display')</script> -->
            </div>
            <br>
            <div id="shipmentInfoContainer" class='payment-container'>
                <div class="shipmentInfo" style="display: none;">
                    <div class='table-container'>
                        <table>
                            <tr>
                                <th>Country <span style="color:red;">*</span></th>
                                <td><select name="country[]" class='select-button'>
                                        <?php
                                        $countries = $dbHelper->getAllCountries("country_name");
                                        foreach ($countries as $country) {
                                            echo '<option name="' . $country->getCountryName() . '">' . $country->getCountryName() . '</option>';

                                        }
                                        ?>
                                    </select></td>
                            </tr>
                            <tr>
                                <th>Full Name <span style="color:red;">*</span></th>
                                <td><input type="text" class='add-input' name="fullName[]"></td>
                            </tr>
                            <tr>
                                <th>Street Number <span style="color:red;">*</span></th>
                                <td><input type="text" class='add-input' name="street_nb[]"></td>
                            </tr>
                            <tr>
                                <th>Building (Optional)</th>
                                <td><input type="text" class='add-input' name="building[]" placeholder="Optional Field">
                                </td>
                            </tr>
                            <tr>
                                <th>City <span style="color:red;">*</span></th>
                                <td><input type="text" class='add-input' name="city[]"></td>
                            </tr>
                            <tr>
                                <th>State <span style="color:red;">*</span></th>
                                <td><input type="text" class='add-input' name="state[]"></td>
                            </tr>
                            <tr>
                                <th>Zip Code <span style="color:red;">*</span></th>
                                <td><input type="text" class='add-input' name="zipCode[]"
                                        placeholder="Examples: 1300, 13001, 1333-5555"
                                        title="XXXX or XXXXX or XXXX-XXXX or XXXXX-XXXX" pattern="\d{4,5}(-\d{4})?">
                                </td>
                            </tr>
                            <tr>
                                <th>Phone Number <span style="color:red;">*</span></th>
                                <td><input type="text" class='add-input' name="phoneNumber[]"
                                        placeholder="Ex: +961-81888888" title="+XXX-XXXXXXX..."
                                        pattern="\+\d{3}-\d{8,20}"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <br>
            <?php

            if ($_SESSION['user']=="client" && ($shipmentInfos == null ||(is_array($shipmentInfos) && count($shipmentInfos) < 4))) {

                echo '<button type="button" class="add-button" onclick="addShipmentInfo()">Add a Shipment Information</button>';
            }

            ?>
    </div>
    <script>

        function addShipmentInfo() {

            if (shipmentCount < 4) {

                var shipmenfInfoContainer = document.getElementById('shipmentInfoContainer');
                var clonedShipmentInfo = shipmenfInfoContainer.querySelector('.shipmentInfo').cloneNode(true);
                clonedShipmentInfo.style.display = 'block';
                shipmenfInfoContainer.appendChild(clonedShipmentInfo);
                shipmentCount++;
            }

            else alert("You can add only 4 shipment information forms for your account");
        }

    </script>

    <br><br>
    <input type="submit" class='add-button' name="update" value="Update Profile">
    <p class='text-button-acc'><a href="index.php">Back</a></p>
    </form>
    </div>
    </div>
    </div>
    <footer>
        <div class="footer">
            <div class="contact">
                <div style="text-align:center; ">
                    <p>Need Help?
                        <br>Contact us through any of these support chanels
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
          //  window.location.href = "deleteAccount.php?del=1";
          window.location.href="manageAccount.php?forward=deleteAccount";
        }

    </script>

</body>

</html>