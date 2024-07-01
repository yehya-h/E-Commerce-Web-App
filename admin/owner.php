<?php
//-----------CODE ADDED BY OMAR---------
include_once("../include_all.php");
include_once("../connection.php");
session_start();
include_once("../check_login.php");
unset($_SESSION['admin']);
if($_SESSION['signed_in']==true){
if(!isset($_COOKIE['isOwner'])){
    if(isset($_COOKIE['isAdmin'])) header("location:admin.php");
    else header("location:../index.php");
}
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> <!--modified -->
    <title>Owner Panel</title>


<!-- ------------STYLE ADDED BY OMAR --------------------- -->
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
  }
   */

</style>
<!-- ----------------------------------------------------------------------------------- -->
</head>

<body>
   <!-- HEADER AND CODE INSIDE ADDED BY OMAR-->

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
                    echo '<div><p>Admin : ' . $admin->getFirstName() . '</p>';
                    echo '<p><a href="../manageAccount.php?user=admin">Account</a>/<a href="../sign_out.php?isAdmin=1">Sign Out</a></p>';
                    echo '<p><a href="#about">About</a></p></div>';
                } else if ($isOwner == true) {
                    echo '<div class="center-nav"><div><h1>Owner Panel</h1></div></div>
                        <div class="right-nav"><div><h3>Owner : ' . OWNER_NAME . '</h3></div>';
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

    <!-- ------------------------------------------------------------------------------------------------ --->

  

    <!-- <h2>Owner Panel</h2> -->
    <div class="button-container">
        <button onclick='getLink("manageAdmins.php")'>Manage Admins</button>
        <button onclick='getLink("manageCategories.php")'>Manage Categories</button>
        <button onclick='getLink("manageProducts.php")'>Manage Products</button>
        <button onclick='getLink("manageCountries.php")'>Manage Countries</button>
        <button onclick='getLink("viewOrders.php")'>View Orders</button>
        <button onclick='getLink("viewAccounts.php")'>View Accounts</button>
        <button onclick='getLink("viewStats.php")'>View Stats</button>
    </div>

    <script>
        function getLink(link) {
            window.location.href = link;
        }
    </script>

    <footer>
        <!-- common footer -->
    </footer>
</body>

</html>