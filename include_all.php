<?php

$files = glob('classes/*.php');
foreach ($files as $file) {
    include_once $file;
}

//$acc=new Account(0,"em","passwd","adm",2122);
//echo $acc->email;
//echo CST;

include_once ("classes/Category.php");
include_once ("classes/Product.php");
include_once ("classes/Country.php");
include_once ("classes/Order.php");
include_once ("classes/ShipmentInfo.php");
include_once ("classes/Client.php");
include_once ("classes/Account.php");
include_once ("classes/Admin.php");
include_once ("classes/Cart.php");
include_once ("classes/CartItem.php");
include_once ("classes/OrderItem.php");
include_once ("classes/PaymentInfo.php");
include_once ("classes/Rating.php");
include_once ("classes/Review.php");
include_once ("classes/Functions.php");
include_once ("classes/config.php");

?>