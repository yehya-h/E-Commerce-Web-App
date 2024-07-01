<?php

include_once("include_all.php");
include_once("connection.php");
session_start();

if(isset($_GET['del'])){

if($_SESSION['user']=="client"){

        header('Location:sign_out.php?isClient=1&deleteAccount=1&acc_id='.$_SESSION['ACCOUNT']->getAccountId());
    //delete account, client, cart, cartitems, review, payment info
    //keep orders, shipment infos
}

else if ($_SESSION['user']=="admin"){

    //delete account & admin
    header('Location:sign_out.php?isAdmin=1&deleteAccount=1&acc_id='.$_SESSION['ACCOUNT']->getAccountId());

    
}


}


?>