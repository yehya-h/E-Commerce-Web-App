<?php
//MODIFICATION (OMAR):
include_once("../connection.php");
include_once("../include_all.php");
//-----------------------------
//session_start();
session_start();
include_once("../check_login.php");
if($_SESSION['signed_in']==true){
if((!isset($_COOKIE['isOwner']) || $_COOKIE['isOwner'] == "false") && 
(!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] == "false")) header("location:../index.php");
}
else{
    header("Location:../index.php");
}
unset($_SESSION['admin']);
//print_r($_SESSION);
//print_r($_COOKIE);
//  Dear Programmer,
//  When I wrote this code, only god and A knew how it worked
//  Now, only god knows it.
//  Therefore, if you're trying to optimize this routine and
//  it fails (more surely), please increase this counter as 
//  a warning for the next person:
//  Total hours wasted here =  254;

echo '<script>';
echo 'var host="'.HOST_ADDRESS.'";';
echo '</script>';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="../logos/primary_icon.jpeg" /> <!-- modified -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> <!--modified -->
    <title>Admin Panel</title>

<!--  STYLE TAG ADDED BY OMAR  -->
    <style>
     /* header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #2b2a2a;
    color: #fff;
    padding: 20px;
  }
  
  h1 {
    font-size: 2em;
    margin: 0;
  }
  
  nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    color: #f1f1f1;
  }
  
  nav ul li {
    display: inline-block;
    margin-left: 20px;
    color: #f1f1f1;
  }
  
  nav ul li a {
    color: #f1f1f1;
    text-decoration: none;
  } */
  

</style>
<!-- ---------------------------------------------------------------------------------   -->
</head>

<body>
<!-- HEADER TAG AND CODE ADDED BY OMAR    -->

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
                } else if ($isOwner == true) {
                    echo '<div class="center-nav"><div><h1>Owner Panel</h1></div></div>
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

    <!-- ------------------------------------------------------------------------------------------- -->

    <!-- <h2>Admin Panel</h2> -->
    <div class="button-container">
        <button onclick='getLink("manageCategories.php")'>Manage Categories</button>
        <button onclick='getLink("manageProducts.php")'>Manage Products</button>
        <button onclick='getLink("manageCountries.php")'>Manage Countries</button>
        <button onclick='getLink("viewOrders.php")'>View Orders</button>
        <button onclick='getLink("viewAccounts.php")'>View Accounts</button>
        <button onclick='getLink("viewStats.php")'>View Stats</button>
    </div>

    <div class="popup-overlay" id="popup-overlay" style="display:none;">
            <div class="popup-content" id="popup-content">
                <h2>Are you sure you want to delete this account?</h2>
                <button class='add-button' onclick="deleteAccount()">Yes</button>
                <button class='add-button' onclick="closePopUp()">No</button>
            </div>
        </div>


        <script type="text/javascript" src="script.js"></script>

        <script>

            

// function getLink(link) {
//     window.location.href = link;
// }

// function goToPage(src) {
//     console.log("GO to page");
//     if (src == "delete") {
//         openPopUp();
//     } else {
//         console.log(src);
//         console.log("HOST: "+host);
//         var next="http://"+host+"/"+"manageAccount.php?forward=updateProfile";
//         console.log("Next: " + next );
//         //window.location.href = src;
//          window.location.href="http://192.168.1.10:3000/manageAccount.php?forward=updateProfile";

//         //window.location.href="http://192.168.1.10:3000/test.php";
//         //window.location.assign(src);
//         console.log(window.location.href);
//         console.log("After");
//     }
// }

// // Function to close the popup
// function closePopUp() {
//     document.getElementById('popup-overlay').style.display = 'none';
// }

// // Function to open the popup when the page loads
// function openPopUp() {
//     document.getElementById('popup-overlay').style.display = 'block';
// }

// function deleteAccount() {
//     document.getElementById('popup-overlay').style.display = 'none';
//     window.location.href = "../manageAccount.php?forward=deleteAccount";
// }

            
            </script>
   

    <footer>
        <!-- common footer -->
    </footer>
</body>

</html>