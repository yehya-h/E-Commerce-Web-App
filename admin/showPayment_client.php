<?php
include_once ($_SERVER['DOCUMENT_ROOT']."/connection.php");
session_start();
include_once("../check_login.php");
if($_SESSION['signed_in']==true){
if((!isset($_COOKIE['isOwner']) || $_COOKIE['isOwner'] == "false") && 
(!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] == "false")) header("location:../index.php");
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
    <title></title>
</head>
<body>
<?php
    if (isset ($_GET['payment_id'])) {
        $payment_id = $_GET['payment_id'];
        $payment_info = $dbHelper->getPaymentInfoByPaymentId($payment_id);
        if($payment_info != null){
            echo "<div class='table-container' id='popup'>
            <h2>Payment Information</h2>
            <table boder =1>
            <tr><th>Payment ID</th><td>" . $payment_info->getPaymentInfoId() . "</td></tr>
            <tr><th>Client ID</th><td>" . $payment_info->getClientId() . "</td></tr>
            <tr><th>Card Number</th><td>" . $payment_info->getCardNumber() . "</td></tr>
            <tr><th>Name on Card</th><td>" . $payment_info->getNameOnCard() . "</td></tr>
            <tr><th>Expiry Date</th><td>" . $payment_info->getExpiryDate() . "</td></tr></table>";
           // <tr><th>Security Code</th><td>" . $payment_info->getSecurityCode() . "</td></tr>
            
            //<button class='close-btn' onclick='closePopup()'>Close</button></div>
        }
    }
    ?>
</body>
</html>