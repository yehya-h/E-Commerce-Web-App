<?php

include_once("include_all.php");
include_once("connection.php");
session_start();
$error=false;
$errormsg="";
if(isset($_GET['verify'])){

    //check that time didn't exceeded 3 mins:
   // echo "Success";//just for testing
   if((time()-$_SESSION[$_SESSION['new']['account']->getEmail()]['sendTime'])<180){
    //time not exceeded
    //perform the registration
    $acc_id=$dbHelper->addAccount($_SESSION['new']['account']);
    if($acc_id!=-1){
    //succeeded
        //next step: add the client:
        $client_id=$dbHelper->addClient($_SESSION['new']['client'],$acc_id);

        if($client_id!=-1){
            //client added successfuly
            //add the cart
            // if($dbHelper->addCart($client_id)==true){
            //     // cart added 
            //     echo '<br>Cart added<br>';
            // }

            // else{

            //     echo '<br> Cart not added<br>';
            //     //cart not added
            // }
            if($dbHelper->addCart($client_id)==false){
                $error=true;
                $errormsg= "Error in cart creation !!!";
            }
            else{
                //cart added
            if(!empty($_SESSION['new']['payment'])){

                // foreach($_SESSION['new']['payment'] as $key=>$value){

                //    if( $dbHelper->addPaymentInfo($value,$client_id)==false){
                //     $error=true;
                //     $errormsg="Error in adding Payment info";
                //     break;
                //    }
                // }

                foreach($_SESSION['new']['payment'] as $key=>$value){

                    if( ($id=$dbHelper->addPaymentInfo($value,$client_id))==-1){
                     $error=true;
                     $errormsg="Error in adding Payment info";
                     break;
                    }
                    else{
                        $client=$dbHelper->getClientByClientId($client_id);
                        $client->setPaymentInfoId($id);
                        if($dbHelper->updateClient($client)==false){
                            $error=true;
                            $errormsg="Error in updating payment info id for client";
                            break;
                        }
                    }
                 }
            }

            if($error==false){
                //echo "TESST<br>";
            if(!empty($_SESSION['new']['shipment'])){
                //echo "N?A<br>";
                foreach($_SESSION['new']['shipment'] as $key=>$value){

                    if($dbHelper->addShipmentInfo($value,$client_id)==false){
                       // echo "ERRROR";
                        $error=true;
                        $errormsg="Error in adding Shipment info";
                        break;
                    }
                    else{
                        //echo '<br>GOOD<br>';
                    }
                }
            }
            else{
               // echo "<br>SESSION EMPTY FROM SHIPMENT<br>";
            }
        }
        }
    }

        else{
            //client not added
           // echo "client function error";
           $error=true;
           $errormsg="Error in Client Registration !!!";
        }
        

    }

    else{
        //failed
        $error=true;
        $errormsg="Error in account creation !!!";

    }

   }

   else{
    //time expired
    $error=true;
    $errormsg="Time Expired for this verification request. Please try again."; 

   }
//    $err=($error==true)?1:0;
$err="false";
if($error==true) $err="true";
//else{
    $_SESSION['new']['beforeSubmit']=true;
//}
$link="Location:popup.php?page=verify&error=".$err."&errormsg=".$errormsg."";
echo "<br>".$err;
 header($link);
   //header("Location:popup.php?page=verify&error=true&errormsg='$errormsg'");

}


?>