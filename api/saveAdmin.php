<?php

include_once("android_connection.php");//modified by omar: include_once instead of include
include_once("../include_all.php");//modified by omar: include_once instead of include
include_once("../classes/Functions.php");//added
include_once("../classes/Account.php");//added
include_once("../classes/Admin.php"); // added by Omar
session_start();
//if(!isset($_SESSION[$_POST['email']])) $_SESSION[$_POST['email']] = ""; //COMMENTED(OMAR)
if(!isset($_SESSION[$_POST['email']])) $_SESSION[$_POST['email']] = array();//ADDED(OMAR)

$response = array();

if ($_SERVER['REQUEST_METHOD'] == "POST") {


    if (isset($_POST['submit'])) {

        if (
            isset($_POST['firstName']) && isset($_POST['lastName']) &&
            isset($_POST['address']) && isset($_POST['phone_number']) && isset($_POST['email']) && isset($_POST['password1']) &&
            isset($_POST['password2'])
        ) {
            $email = $_POST['email'];
            $_SESSION[$_POST['email']] = $_POST;
            if ($_POST['password1'] != $_POST['password2']) {
                // echo "<p style='color:red ; font-weight:bold'>Please make sure that the password must be the same in both fields</p>";
             //  $_SESSION[$_POST['email']] = $_POST;
            //    $_SESSION[$_POST['email']]['firstName']=$_POST['firstName'];
            //    $_SESSION[$_POST['email']]['lastName']=$_POST['lastName'];
            //    $_SESSION[$_POST['email']]['address']=$_POST['address'];
            //    $_SESSION[$_POST['email']]['phone_number']=$_POST['phone_number'];
            //    $_SESSION[$_POST['email']]['password1']=$_POST['password1'];
            //    $_SESSION[$_POST['email']]['password2']=$_POST['password2'];
                header("location:../admin/fillAdminInformations.php?email=".$email."&error=1");
            } else {
                //add the admin
                //modified
                //add the admin
                if (isset($_POST['phone_number']) && !empty($_POST['phone_number'])) {//modified
                    if ($androidDBHelper->isUniquePhoneNumber($_POST['phone_number']) == 0) { 
                        $shuffledEmail = Functions::myShuffle($_POST['email']);
                        $token = hash("sha256", $shuffledEmail);
                        $acc = new Account(-1, $_POST['email'], $_POST['password1'], 1, $token);
                        $acc_id = $androidDBHelper->addAccount($acc);
                        if ($acc_id != -1) {
                            $admin = new Admin(-1, $acc_id, $_POST['firstName'], $_POST['lastName'], $_POST['address'], $_POST['phone_number']);
                            $id = $androidDBHelper->addAdmin($admin, $acc_id);
                            if ($id != -1) {
                                $response['error'] = "false";
                                $response['message'] = "success";
                                echo "<p style='color:green ; font-weight:bold'>Admin added</p>";
                                header("Location:../popup.php?page=saveAdmin");  //modified
                            }
                            //no error in respone
                            //header to pop up : created successfully
                        } else {
                            $response['error'] = "true";
                            $response['message'] = "fail";
                            // echo "<p style='color:red ; font-weight:bold'>problem while adding the Admin </p>";
                            header("location:../admin/fillAdminInformations.php?email=" . $email . "&error=2");
                            //error occured: header
                        }
                    } else { //modified
                        header("location:../admin/fillAdminInformations.php?email=" . $email . "&error=3");
                    }
                }
            }
        }
    }
    echo json_encode($response);
}


//                 if($androidDBHelper->isUniquePhoneNumber($_POST['phone_number']) == 0){ //modified again
//                 $shuffledEmail = Functions::myShuffle($_POST['email']);
//                 $token = hash("sha256", $shuffledEmail);
//                 $acc = new Account(-1, $_POST['email'], $_POST['password1'], 1, $token);
//                 $acc_id = $androidDBHelper->addAccount($acc);
//                 if ($acc_id != -1) {
//                     $admin = new Admin(-1, $acc_id, $_POST['firstName'], $_POST['lastName'], $_POST['address'], $_POST['phone_number']);
//                     $id = $androidDBHelper->addAdmin($admin, $acc_id);
//                     if ($id != -1) {
//                         $response['error'] = "false";
//                         $response['message'] = "success";
//                         echo "<p style='color:green ; font-weight:bold'>Admin added</p>";
//                        // header("location:../sign_in.php");
//                        // modified
//                        header("Location:../popup.php?page=saveAdmin");  //modified
//                     }
//                     //no error in respone
//                     //header to pop up : created successfully
//                 } else {
//                     $response['error'] = "true";
//                     $response['message'] = "fail";
//                     // echo "<p style='color:red ; font-weight:bold'>problem while adding the Admin </p>";
//                     header("location:../admin/fillAdminInformations.php?email=".$email."&error=2");
//                     //error occured: header
//                 }
//             } else{
//                 //modified
//                 header("location:../admin/fillAdminInformations.php?email=".$email."&error=3");
//             }
//         }
//     }
// }//added
//     echo json_encode($response);
// }
?>