<?php
include_once ($_SERVER['DOCUMENT_ROOT'] . "/connection.php");
session_set_cookie_params(3600);
session_start();
//print_r($_SESSION);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> <!-- modifed-->
    <title>Admin Informations</title>
    <style>
        .middle-section{
            min-height: 68%;
        }
        h3{
            color:var(--navy-blue);
        }
    </style>
</head>

<body>
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

            //if ($_SESSION['signed_in'] == true /*&& $isClient==true*/) {
                /*if ($isClient == true) {
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
                    echo '<div><a href="../sign_out.php?isAdmin=1"><i class="bi bi-box-arrow-right"></i> </a></div></div>';
                } else if ($isOwner == true) {*/
                    echo '<div class="center-nav"><div><h1>Shop & Ship Admin Form</h1></div></div>';
                //}
            //} else {
            //    echo '<p><a href="../sign_in.php">Sign in</a>/<a href="../createAccount.php">Create account</a></p>';
            //}
            ?>
            <!-- <li><a href="#about">About</a></li>
                <li><a href="cart.php">Cart</a></li> -->
            </div>
        </nav>
    </header>
    <?php
    $firstName = $lastName = $address = $phone_number =$pass1=$pass2= "";
     if(isset($_SESSION[$_GET['email']])){
       // if(isset($_GET['email'])){
            $email=$_GET['email'];
        // $firstName = $_SESSION[$_GET['email']]['firstName'];
        // $lastName = $_SESSION[$_GET['email']]['lastName'];
        // $address = $_SESSION[$_GET['email']]['address'];
        // $phone_number = $_SESSION[$_GET['email']]['phone_number'];
        // $pass1=$_SESSION[$_GET['email']]['password1'];
        // $pass2=$_SESSION[$_GET['email']]['password2'];

        $firstName = $_SESSION[$email]['firstName'];
        $lastName = $_SESSION[$email]['lastName'];
        $address = $_SESSION[$email]['address'];
        $phone_number = $_SESSION[$email]['phone_number'];
        $pass1=$_SESSION[$email]['password1'];
        $pass2=$_SESSION[$email]['password2'];



    }
    //$account_id = $_GET['account_id'];
    $email = $_GET['email'];
    if(isset($_GET['error'])){
        if($_GET['error']==1){
            $message= "<p style='color:red ; font-weight:bold'>Please make sure that the password must be the same in both fields</p>";
        }else if($_GET['error']==2){
            $message= "<p style='color:red ; font-weight:bold'>problem while adding the Admin </p>";
        }
        else if($_GET['error']==3){ //modified
            $message= "<p style='color:red ; font-weight:bold'>Please enter a unique phone number </p>";
        }
    }
    ?>
    <div class="middle-section">
    <div class='add-container'>
        <h3>Fill you Informations:</h3>
        <?php if(isset($message)) echo "
        <div class='error-container'>" . $message . " </div>
        " ; ?>
        <!-- <form action='http://192.168.1.25:3000/api/saveAdmin.php' method='POST'> MODIFY YOUR IP @ HERE TO WORK -->
            <?php

//or: use the follwing echo (modify your host address in config.php)
 echo '<form action="http://'.HOST_ADDRESS.'/api/saveAdmin.php" method="POST">';

?>
                  <table>
                <tr>
                    <td>First Name</td>
                    <td><input type='text' name='firstName' class="add-input" required value="<?php echo $firstName; ?>"></td>
                </tr>
                <tr>
                    <td>Last Name</td>
                    <td><input type='text' name='lastName' class="add-input" required value="<?php echo $lastName; ?>"></td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td><input type='text' name='address' class="add-input" required value="<?php echo $address; ?>"></td>
                </tr>
                <tr>
                    <td>Phone Number</td>
                    <td><input type='text' name='phone_number' class="add-input" placeholder='Ex: +961-81888888' title='+XXX-XXXXXXX...'
     pattern='\+\d{3}-\d{8,20}' required value=<?php echo $phone_number; ?>></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><input type='email' name='emailDisabled' class="add-input" disabled  value=<?php echo $email; ?>></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td><input type='password' name='password1' class="add-input" minlength='8' required value="<?php echo $pass1; ?>"></td>
                </tr>
                <tr>
                    <td>Confirm Password</td>
                    <td><input type='password' name='password2' class="add-input" minlength='8' required value="<?php echo $pass2; ?>"></td>
                </tr>
            </table>
            <!--<input type='hidden' name='account_id' value=<?php // echo $account_id; ?>>-->
            <br>
            <input type='hidden' name='email' class="search-button" value=<?php echo $email; ?>>
            <input type='submit' name='submit' class="search-button" value='Submit Information'>
        </form>
    </div>
    </div>
</body>

</html>