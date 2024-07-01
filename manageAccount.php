<?php
session_start();
include_once("check_login.php");
include_once("connection.php");
if($_SESSION['signed_in']==true && ($isClient || $isAdmin)) $_SESSION['ACCOUNT']=$dbHelper->getAccountByToken($_COOKIE['token']);
else header("Location:index.php");
//else header("Location:index.php");
//$_SESSION['user']=($_SESSION['ACCOUNT']->getIsAdmin()==1)?"admin":"client";
// $user=$_SESSION['ACCOUNT']->getIsAdmin();
// if($user==1){
//     $_SESSION['user']="admin";
// }
// else if($user==0) {
//     $_SESSION['user']="client";
// }
// }
// else header("Location:index.php");
$_SESSION['user']=($_SESSION['ACCOUNT']->getIsAdmin()==1)?"admin":"client";
if(isset($_GET['forward'])){

    $page=$_GET['forward'].'.php';
    switch($_GET['forward']){

        case "viewOrders":
            header("Location:viewOrders.php");
            break;

        case "updateProfile":
            header("Location:updateProfile.php");
            break;

        case "changePassword":
            header("Location:changePassword.php");
            break;

            
        case "deleteAccount":
            header("Location:deleteAccount.php?del=1");
            break;

            
        default: break;


    }

    


}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>

.popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            z-index: 999; /* Ensure the overlay is on top of other elements */
        }
        .popup-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center; /* Center-align content */
        }
        .popup-content button {
            margin-top: 20px; /* Add some space between the form and the button */
        }
    </style>
</head>
<body><?php //we can put the header here later.... ?>
    <h1>Manage Account</h1>
    <h3>Email: <?php echo $_SESSION['ACCOUNT']->getEmail();?></h3>
    <?php

if(isset($_GET['user'])){
    $_SESSION['user']=$_GET['user'];
}

if(isset($_SESSION['user'])){

if($_SESSION['user']=='client' && $isClient==true){

    echo '<br><a href="viewOrders.php">View Orders</a>';
    echo '<br><a href="updateProfile.php">Update Profile</a><br>';
    echo '<a href="changePassword.php">Change Password</a><br>';
   // echo '<button onclick="openPopUp()"><a href="deleteAccount.php">Delete Account</a></button>';
    echo '<button onclick="openPopUp()">Delete Account</button>';
}
else if($_SESSION['user']=='admin' && $isAdmin==true){
echo '<br><a href="updateProfile.php">Update Profile</a><br>';
echo '<a href="changePassword.php">Change Password</a><br>';
//echo '<button onclick="openPopUp()"><a href="deleteAccount.php">Delete Account</a></button>';
echo '<button onclick="openPopUp()">Delete Account</button>';
}

}

?>
<br>
<a href="index.php">Back</a><br>
<div class="popup-overlay" id="popup-overlay" style="display:none;">
    <div class="popup-content" id="popup-content">
        <h2>Are you sure you want to delete this account?</h2>
        <!-- <p>This is a popup message.</p> -->
        <!-- <button onclick="closePopup(<?php //echo $next?>)">OK</button> -->
        <button onclick="deleteAccount()">Yes</button>   <button onclick="closePopUp()">No</button>
    </div>
</div>

<script>
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

    function deleteAccount(){
        document.getElementById('popup-overlay').style.display = 'none';
       // window.location.href="deleteAccount.php?del=1";
       window.location.href="manageAccount.php?forward=deleteAccount";
    }
    
</script>
</body>
</html>





