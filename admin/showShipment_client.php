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
    if (isset ($_GET['shipment_id'])) {
        $shipment_id = $_GET['shipment_id'];
        $shipment_info = $dbHelper->getShipmentInfoByShipmentId($shipment_id);
        if($shipment_info != null){
            if($shipment_info->getBuilding()==null){
                $building = "N/A";
            }else{
                $building = $shipment_info->getBuilding();
            } 
            
            echo "<div class='table-container' id='popup'>
            <h2>Shipment Information</h2>
            <table boder =1>
            <tr><th>Shipment ID</th><td>" . $shipment_info->getShipmentInfoId() . "</td></tr>
            <tr><th>Client ID</th><td>" . $shipment_info->getClientId() . "</td></tr>
            <tr><th>country</th><td>" . $shipment_info->getCountryName() . "</td></tr>
            <tr><th>Full Name</th><td>" . $shipment_info->getFullName() . "</td></tr>
            <tr><th>State</th><td>" . $shipment_info->getState() . "</td></tr>
            <tr><th>City</th><td>" . $shipment_info->getCity() . "</td></tr>
            <tr><th>Street Number</th><td>" . $shipment_info->getStreetNb() . "</td></tr>
            <tr><th>Building</th><td>" . $building . "</td></tr>
            <tr><th>ZIP Code</th><td>" . $shipment_info->getZipCode() . "</td></tr>
            <tr><th>Phone Number</th><td>" . $shipment_info->getPhoneNumber() . "</td></tr>
            </table>";
            //<button class='close-btn' onclick='closePopup()'>Close</button></div>
        }
    }
    ?>
</body>
</html>