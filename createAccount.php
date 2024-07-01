<?php

include_once("include_all.php");
include_once("connection.php");
session_start();
$errormsg="";
$mailmsg="";
$error=false;
$sent=false;
// print_r($_SESSION);
// echo "<br>POST:<br>";
// print_r($_POST);

include_once ("check_login.php");
if ($isAdmin == true)
    header("Location:admin/admin.php");
else if ($isOwner == true)
    header("Location:admin/owner.php");
else if($_SESSION['signed_in']==true) header("Location:index.php");


if(!isset($_SESSION['new']['beforeSubmit'])){
    $_SESSION['new']['beforeSubmit']=true;
    //$_SESSION['new']=array();
    //$_SESSION['new']['account']=array();
}

if(isset($_POST['createAccount'])){
  //  echo "TEST";
    $_SESSION['new']['beforeSubmit']=false;

    if($dbHelper->accountExist($_POST['email'])!=null){

        //account already exists
        $error=true;
        $errormsg="An account already exists for this email";

    }

    else{

        //check if passwords fields are identical
        if($_POST['passwd1']!=$_POST['passwd2']){

            $error=true;
            $errormsg="Please make sure that the password must be the same in both fields";
        }

        else{

            $_SESSION['new']['account']=new Account(null,$_POST['email'],$_POST['passwd1'],0,
            hash("sha256",Functions::myShuffle($_POST['email'])));

            $_SESSION['new']['client']=new Client(null,null,$_POST['firstName'],$_POST['lastName'],
            ($_POST['phone_number']!="")?$_POST['phone_number']:null,null,null,20);

            if(!isset($_SESSION[$_SESSION['new']['account']->getEmail()]['firstTime']))
            $_SESSION[$_SESSION['new']['account']->getEmail()]['firstTime']=1;

            $_SESSION['new']['payment']=array();
            $_SESSION['new']['shipment']=array();

            // if(!isset($_SESSION[$_SESSION['new']['account']->getEmail()]['verify'])){
            //     $_SESSION[$_SESSION['new']['account']->getEmail()]['verify']['nb']=-1;
            //     $_SESSION[$_SESSION['new']['account']->getEmail()]['verify']['time']=time();
        
            // }

            // if(isset($_POST['cardNumber']) && !empty($_POST['cardNumber']) && isset($_POST['nameOnCard']) && 
            // !empty($_POST['nameOnCard']) && isset($_POST['expiryDate']) && !empty($_POST['expiryDate']) && 
            // isset($_POST['securityCode']) && !empty($_POST['securityCode'])){
                

        //         if(isset($_POST['cardNumber']) &&  isset($_POST['nameOnCard']) &&  isset($_POST['expiryDate']) &&  
        //         isset($_POST['securityCode']) && !empty($_POST['securityCode'])){

        //         if(!empty($_POST['cardNumber']) && !empty($_POST['nameOnCard']) && !empty($_POST['expiryDate'])
        //          && !empty($_POST['securityCode']) ){

        //         for($i=1;$i<count($_POST['cardNumber']);$i++){

        //             $_SESSION['new']['payment'][$i-1]=new PaymentInfo(null,null,$_POST['cardNumber'][$i],
        //             $_POST['nameOnCard'][$i],$_POST['expiryDate'][$i],$_POST['securityCode'][$i]);

        //         }
        //     }

        //     else{

        //         $error=true;
        //         $errormsg="Please make sure that you fill all the fields of your payment
        //          information with the specified format";

        //     }
        // }


        if(isset($_POST['cardNumber']) &&  isset($_POST['nameOnCard']) &&  isset($_POST['expiryDate']) &&  
        isset($_POST['securityCode']) && !empty($_POST['securityCode'])){
         
                echo "";//"<br>101<br>";
            for($i=1;$i<count($_POST['cardNumber']);$i++){
        if(!empty($_POST['cardNumber'][$i]) && !empty($_POST['nameOnCard'][$i]) && !empty($_POST['expiryDate'][$i])
         && !empty($_POST['securityCode'][$i]) ){

       
            echo "";// "<br>107<br>";
            $_SESSION['new']['payment'][$i-1]=new PaymentInfo(null,null,$_POST['cardNumber'][$i],
            $_POST['nameOnCard'][$i],$_POST['expiryDate'][$i],$_POST['securityCode'][$i]);

        }
   // }

    else{
        echo "";// "<br>115<br>";
        $error=true;
        $errormsg="Please make sure that you fill all the fields of your payment
         information with the specified format";
        break;
    }
}
}
   echo "";// "<br>123<br>";


            // if(isset($_POST['country']) && !empty($_POST['country']) && isset($_POST['fullName']) &&
            //  !empty($_POST['fullName']) && isset($_POST['street_nb']) && !empty($_POST['street_nb']) &&
            //  isset($_POST['city']) && !empty($_POST['city']) && isset($_POST['state']) && !empty($_POST['state']) &&
            //  isset($_POST['zipCode']) && !empty($_POST['zipCode']) && isset($_POST['phoneNumber']) &&
            //   !empty($_POST['phoneNumber']) /* && $_POST['country'][1]!="" && count($_POST['country'])>1*/){





            //     if(isset($_POST['country']) &&  isset($_POST['fullName']) && isset($_POST['street_nb']) && 
            //     isset($_POST['city']) &&  isset($_POST['state']) &&   isset($_POST['zipCode']) &&  
            //     isset($_POST['phoneNumber'])){

            //         if(!empty($_POST['country']) && !empty($_POST['fullName']) && !empty($_POST['street_nb']) &&
            //          !empty($_POST['city']) && !empty($_POST['state']) && !empty($_POST['zipCode']) && 
            //          !empty($_POST['phoneNumber']) ){

            //     for($i=1;$i<count($_POST['country']);$i++){

            //         $_SESSION['new']['shipment'][$i-1]=new ShipmentInfo(null,$_POST['country'][$i],null,
            //         $_POST['fullName'][$i],$_POST['street_nb'][$i],
            //         (!empty($_POST['building'][$i]))?$_POST['building'][$i]:null,$_POST['city'][$i],$_POST['state'][$i],
            //         $_POST['zipCode'][$i],$_POST['phoneNumber'][$i]);
            //     }
            //   }
            

            //   else /*if(!empty($_POST['country'])>1)*/{

            //     $error=true;
            //     $errormsg="Please make sure that you fill all the fields of your shipment
            //      information with the specified format";

            //   }
            // }



            if(isset($_POST['country']) &&  isset($_POST['fullName']) && isset($_POST['street_nb']) && 
            isset($_POST['city']) &&  isset($_POST['state']) &&   isset($_POST['zipCode']) &&  
            isset($_POST['phoneNumber'])){
                echo "";// "<br>168<br>";
                for($i=1;$i<count($_POST['country']);$i++){


                if(!empty($_POST['country'][$i]) && !empty($_POST['fullName'][$i]) && !empty($_POST['street_nb'][$i]) &&
                 !empty($_POST['city'][$i]) && !empty($_POST['state'][$i]) && !empty($_POST['zipCode'][$i]) && 
                 !empty($_POST['phoneNumber'][$i]) ){

                    echo "";//<br>176<br>";
                $_SESSION['new']['shipment'][$i-1]=new ShipmentInfo(null,$_POST['country'][$i],null,
                $_POST['fullName'][$i],$_POST['street_nb'][$i],
                (!empty($_POST['building'][$i]))?$_POST['building'][$i]:null,$_POST['city'][$i],$_POST['state'][$i],
                $_POST['zipCode'][$i],$_POST['phoneNumber'][$i]);
            }
         // }
        

          else /*if(!empty($_POST['country'])>1)*/{
            echo "";// "<br>186<br>";
            $error=true;
            $errormsg="Please make sure that you fill all the fields of your shipment
             information with the specified format";
             break;

          }
        }
        }

        echo "";// "<br>196<br>";
        }
    }
}
echo "";//"<br>200<br>";
if($error==true)  echo "";//"<br>201<br>";//echo '<br>ERROR TEST<br>';

if($error==false && $_SESSION['new']['beforeSubmit']==false){

   // echo "<br>test<br>";
//    if(!isset($_SESSION[$_SESSION['new']['account']->getEmail()]['verify'])){
        if(!isset($_SESSION[$_SESSION['new']['account']->getEmail()]['verify'])){
            echo "";// "<br>208<br>";
        $_SESSION[$_SESSION['new']['account']->getEmail()]['verify']['nb']=-1;
        $_SESSION[$_SESSION['new']['account']->getEmail()]['verify']['time']=time();

    }
    echo "";// "<br>213<br>";
    if($_SESSION[$_SESSION['new']['account']->getEmail()]['firstTime'] == 1 ||//cond 1: 1st time
        ((time()-$_SESSION[$_SESSION['new']['account']->getEmail()]['sendTime'])>180 && 
        ($_SESSION[$_SESSION['new']['account']->getEmail()]['verify']['nb']<3 && 
        (time()-$_SESSION[$_SESSION['new']['account']->getEmail()]['verify']['time']>=0)))||/*cond2: mail expired
         and we loaded the page again and we can still send*/
        (isset($_GET['resend']) && $_SESSION[$_SESSION['new']['account']->getEmail()]['verify']['nb']<3 &&
        (time()-$_SESSION[$_SESSION['new']['account']->getEmail()]['verify']['time']>=0))   )/*cond 3: resend button
        triggered and we can resend */
        {
            echo "";// "<br>Email cond entered<br>";

            //we will send the email
            $link='<a href="http://'.HOST_ADDRESS.'/verify.php?verify=true">Click here to complete your account 
            registration</a>';
            // $body="\nYou are receiving this email because you are trying yo create a new S&S account.\n
            // To verify that it's you: ".$link."\nIf it's not you, you can ignore this message\n
            // Don't share this message with anyone!!!\n
            // S&S  Team";
            $acc=$_SESSION['new']['account'];
            // $body='<br>You are receiving this email because you are trying yo create a new S&S account.<br>
            // To verify that its you: <a href="http://192.168.1.25:3000/verify.php?verify=true">Click here </a> <br>If its not you, you can ignore this message<br>
            // Dont share this message with anyone!!!<br>
            // S&S  Team<br>';//<a href="http://192.168.1.25:3000/verify2.php?verify=true&acc='.json_encode($acc).'">verify2</a>';

            //UNCOMMENT THIS TO WORK
            $body='<br>You are receiving this email because you are trying to create a new S&S account.<br>
            To verify that its you: <a href="http://'.HOST_ADDRESS.'/verify.php?verify=true">Click here </a> <br>If its not you, you can ignore this message<br>
            Dont share this message with anyone!!!<br>
            S&S  Team<br>';//<a href="http://192.168.1.25:3000/verify2.php?verify=true&acc='.json_encode($acc).'">verify2</a>';

            echo "";// "<br>Before send directly<br>";
            $sent=Functions::sendMail(COMPANY_MAIL,MAIL_APP_PASSWORD,
            $_SESSION['new']['account']->getEmail(),"New Account Verification Link",$body);
            echo "";// "<br>After send directly<br>";

            if($sent){
                echo "";// "<br>SENT<br>";
                $_SESSION[$_SESSION['new']['account']->getEmail()]['sendTime']=time();
                $mailmsg="An email is sent to you to verify and complete the registration.
                \nPlease check your inbox and follow the steps to complete your registration.";
                $_SESSION[$_SESSION['new']['account']->getEmail()]['verify']['nb']++;
                $_SESSION[$_SESSION['new']['account']->getEmail()]['firstTime']=0;
            }

            else{
                echo "";// "<br>NOT SENT<br>";
                $mailmsg="Error sending email. Please try again later.";
            }

        }

        if(isset($_GET['resend'])){

            if($sent==false){

                if($_SESSION[$_SESSION['new']['account']->getEmail()]['verify']['nb'] != -1)
                $_SESSION[$_SESSION['new']['account']->getEmail()]['verify']['time']=time()+3600;

                $_SESSION[$_SESSION['new']['account']->getEmail()]['verify']['nb']=-1;

                $time=(int)(($_SESSION[$_SESSION['new']['account']->getEmail()]['verify']['time']-time())/60);
                $msg = "You cannot resend codes currently as you exceeded the number of times allowed, try again after " . $time. " minutes";
                $encodedmsg=json_encode($msg);
                echo '<script>';
                echo 'alert('.$encodedmsg.');';
                echo '</script>';
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
    <link rel="stylesheet" href="style/footer.css" />
    <link rel="icon" type="image/x-icon" href="logos/primary_icon.jpeg" /> <!-- modified -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Create Account</title>
</head>
<body>
    <script>
        var x=0;
        var y=0;
        </script>
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

 <div class='create-container'>
        <h1>Create New Account</h1>
        <form action="createAccount.php" method="post">
            <?php
            echo '<p style="color:red;">' . $errormsg . '</p><br>';
            ?>
            <div class='acc-container'>
                <div class='table-container'>
                    <table>
                        <tr>
                            <th>First Name <span style="color:red;">*</span></th>
                            <td><input type="text" name="firstName" class='add-input' required value=<?php echo !empty($_POST['firstName']) ? $_POST['firstName'] : ""; ?>></td>
                        </tr>
                        <tr>
                            <th>Last Name <span style="color:red;">*</span></th>
                            <td><input type="text" name="lastName" class='add-input' required value=<?php echo !empty($_POST['lastName']) ? $_POST['lastName'] : ""; ?>></td>
                        </tr>
                        <tr>
                            <th>Email <span style="color:red;">*</span></th>
                            <td><input type="email" name="email" class='add-input' required value=<?php echo !empty($_POST['email']) ? $_POST['email'] : ""; ?>></td>
                        </tr>
                        <tr>
                            <th>Password <span style="color:red;">*</span></th>
                            <td><input id="passwd1" type="password" name="passwd1" minlength="8" class='add-input' required
                                    value=<?php echo !empty($_POST['passwd1']) ? $_POST['passwd1'] : ""; ?>></td>
                        </tr>
                        <tr>
                            <th>Confirm Password <span style="color:red;">*</span></th>
                            <td><input id="passwd2" type="password" name="passwd2" minlength="8" class='add-input' required
                                    value=<?php echo !empty($_POST['passwd2']) ? $_POST['passwd2'] : ""; ?>></td>
                        </tr>
                        <tr>
                        <!-- <div class='fields-row'> --><th><span style="color:red;"></span></th>
               <th> <input type="checkbox" name="showPassword" id="showPassword" onclick="show()" style="padding-left: 5%;"> Show password <br></th>
            <!-- </div> -->
</tr>
                        <tr>
                            <th>Phone Number</th>
                            <td><input type="text" name="phone_number" class='add-input' placeholder="Ex: +961-81888888"
                                    title="+XXX-XXXXXXX..." pattern="\+\d{3}-\d{8,20}" value=<?php echo !empty($_POST['phone_number']) ? $_POST['phone_number'] : ""; ?>></td>
                        </tr>
                    </table>
                </div>
            </div>
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
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>Security Code <span style="color:red;">*</span></th>
                                <td><input type="text" name="securityCode[]" minlength="3" maxlength="4"
                                        class='add-input' pattern="\d{3,4}"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <br>
            <button type="button" class='add-button' onclick="addPaymentInfo()">Add Payment Information</button>
            <script>

                function addPaymentInfo() {

                    if (x < 4) {

                        var paymentInfoContainer = document.getElementById('paymentInfoContainer');
                        var clonedPaymentInfo = paymentInfoContainer.querySelector('.paymentInfo').cloneNode(true);
                        clonedPaymentInfo.style.display = 'block';
                        paymentInfoContainer.appendChild(clonedPaymentInfo);
                        x++;
                    }
                    else alert("You can add only 4 payment information forms for your account");
                }

            </script>
            <br>
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
            <button type="button" class='add-button' onclick="addShipmentInfo()">Add a Shipment Information</button>
            <br>
            <script>

                function addShipmentInfo() {

                    if (y < 4) {

                        var shipmenfInfoContainer = document.getElementById('shipmentInfoContainer');
                        var clonedShipmentInfo = shipmenfInfoContainer.querySelector('.shipmentInfo').cloneNode(true);
                        clonedShipmentInfo.style.display = 'block';
                        shipmenfInfoContainer.appendChild(clonedShipmentInfo);
                        y++;
                    }

                    else alert("You can add only 4 shipment information forms for your account");
                }

            </script>
            <br>
            <input type="submit" class='add-button' name="createAccount" value="createAccount">
            <br>
            <?php echo "<br>". $mailmsg; ?>
            <p class='text-button-acc'>Email not received? <a href="createAccount.php?resend=1">Resend Email</a></p>
            <p class='text-button-acc'><a href="sign_in.php">Back</a></p>
        </form>
    </div>
            </div>

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
            window.location.href="manageAccount.php?forward=deleteAccount";
        }


 function show(){

if(document.getElementById("showPassword").checked==true){

document.getElementById('passwd1').type = "text";
document.getElementById('passwd2').type = "text";
// document.getElementById('passwd3').type = "text";
}

else{
document.getElementById('passwd1').type = "password";
document.getElementById('passwd2').type = "password";
// document.getElementById('passwd3').type = "password";
// if(document.getElementById( "passwd" ).type === "password") document.
}
}

</script>
</body>
</html>