<?php 
include_once("include_all.php");
//$expiryTime = time() + (60 * 60 * 24); // Expiry time: 24 hours from now
//session_set_cookie_params($expiryTime);
session_start();
//session_set_cookie_params()
//print_r($_SESSION);
$errormsg="";
$sent=false;
if(!isset($_SESSION[$_SESSION['account']->getEmail()]['resend'])){
    $_SESSION[$_SESSION['account']->getEmail()]['resend']['nb']=-1;
    $_SESSION[$_SESSION['account']->getEmail()]['resend']['time']=time();
    
}

if(//!isset($_SESSION['otp'][$_SESSION['account']->getEmail()]) ||
$_SESSION['otp'][$_SESSION['account']->getEmail()]['firstAttempt']==1 ||
 ((time()-$_SESSION['otp'][$_SESSION['account']->getEmail()]['time'])>180 && 
($_SESSION[$_SESSION['account']->getEmail()]['resend']['nb']<3 && (time()-$_SESSION[$_SESSION['account']->getEmail()]['resend']['time']>=0))) || 
(isset($_GET['resend']) && $_SESSION[$_SESSION['account']->getEmail()]['resend']['nb']<3 && (time()-$_SESSION[$_SESSION['account']->getEmail()]['resend']['time']>=0))){

    // if one of the above cases is true we will send an email
    //testing  purposes only, should be removed in production mode
    if($_SESSION['otp'][$_SESSION['account']->getEmail()]['firstAttempt']==1)// echo "<br>cond1<br>";
    if(((time()-$_SESSION['otp'][$_SESSION['account']->getEmail()]['time'])>180 && 
    ($_SESSION[$_SESSION['account']->getEmail()]['resend']['nb']<3 &&
     (time()-$_SESSION[$_SESSION['account']->getEmail()]['resend']['time']>=0)))) //echo "<br>Cond2<br>";
     if((isset($_GET['resend']) && $_SESSION[$_SESSION['account']->getEmail()]['resend']['nb']<3 && 
     (time()-$_SESSION[$_SESSION['account']->getEmail()]['resend']['time']>=0))) //echo "<br>Cond3<br>";
   // echo'<br>entered cond<br>';
    //handling & logic starts here:
    if($_SESSION['otp'][$_SESSION['account']->getEmail()]['firstAttempt']!=1){
        //because we have generated the 1st time attempt code in the sign in page
    $_SESSION['otp'][$_SESSION['account']->getEmail()]['code']=Functions::generateOtp();
    $_SESSION['otp'][$_SESSION['account']->getEmail()]['time']=time();
    }
    $otpCode=$_SESSION['otp'][$_SESSION['account']->getEmail()]['code'];
    
    $body="Your verification code for your S&S account is:<br> ".$otpCode." <br>Note that
    this code validity will expire within 3 minutes.<br>Don't Share this message with anyone.<br>
    If it's not you trying to reset your password, you can ignore this message<br>S&S Team";

    // $body="\nYour verification code for your S&S account is:<br> ".$_SESSION['otp'][$_SESSION['account']->getEmail()]['code']."<br>Note that
    //  this code validity will expire within 3 minutes.<br>Don't Share this message with anyone.<br>
    //  If it's not you trying to reset your password, you can ignore this message<br>S&S Team";
    $sent=Functions::sendMail(COMPANY_MAIL,MAIL_APP_PASSWORD,$_SESSION['account']->getEmail(),"OTP Verification Code",$body);
    $_SESSION['otp'][$_SESSION['account']->getEmail()]['firstAttempt']=0;
    if($sent) $_SESSION[$_SESSION['account']->getEmail()]['resend']['nb']++;

    //}
}
else{
    //mail can't be send
}

if(isset($_POST['verify']) /*&& $sent==true*/){
   // echo '<br>Ver<br>';
    //check time 1st
    if((time()-$_SESSION['otp'][$_SESSION['account']->getEmail()]['time'])>180){
// here we might add this condition to see if another one will be sent or no depending on the constraints
if(($_SESSION[$_SESSION['account']->getEmail()]['resend']['nb']<3 &&
     (time()-$_SESSION[$_SESSION['account']->getEmail()]['resend']['time']>=0)))
        //password expired but another one can be sent (cond2)
        $errormsg="The password validity time has expired, another one will be sent to you";
        
        else $errormsg="The password validity time has expired, try again after "
        .(int)(( $_SESSION[$_SESSION['account']->getEmail()]['resend']['time']-time())/60)." minutes";
        //this else means that password expired and another can't be sent
    }
    
    else{
       // echo "<br>TTTTTTTT<br>";
        //there is still time
        $otp="";
        for($i=0;$i<6;$i++){
            if(!empty($_POST['arr'][$i]) || $_POST['arr'][$i]==0 || $_POST['arr'][$i]=='0')
            $otp[$i]=$_POST['arr'][$i];
        }
       // echo '<br>'.$otp.'<br>';

        if($otp==$_SESSION['otp'][$_SESSION['account']->getEmail()]['code']){
            if($_SESSION[$_SESSION['account']->getEmail()]['resend']['nb']<3 &&
                (time()-$_SESSION[$_SESSION['account']->getEmail()]['resend']['time'])>=0)
                $_SESSION['otp'][$_SESSION['account']->getEmail()]['firstAttempt']=1;//allow resending of the email
            header("Location:resetPassword.php");
        }

        else{

            $errormsg="The password is incorrect or incomplete , try again";
        }


    }
}

if(isset($_GET['resend'])){
   // echo '<br>'. $_SESSION[$_SESSION['account']->getEmail()]['resend']['nb'].'<br>';
    if($sent==false){
        if($_SESSION[$_SESSION['account']->getEmail()]['resend']['nb']!=-1)
        $_SESSION[$_SESSION['account']->getEmail()]['resend']['time']=time()+3600;//one hour cooldown before resending the mail
        $_SESSION[$_SESSION['account']->getEmail()]['resend']['nb']=-1;
       // echo '<br>resend block<br>';
        //echo '<script>window.alert("sss");</script>';


        // $msg='You cannot resend codes currently as you exceeded the number of times allowed,
        //  try again after '.time()-$_SESSION[$_SESSION['account']->getEmail()]['resend']['time'].' minutes';
        //  echo '<script>window.alert("'.$msg.'");</script>';
        $time=(int)(( $_SESSION[$_SESSION['account']->getEmail()]['resend']['time']-time())/60);
        $msg = "You cannot resend codes currently as you exceeded the number of times allowed, try again after " . $time. " minutes";
         //instead of "test" place $msg

         $encodedMsg = json_encode($msg);
         echo '<script>';
         echo 'alert(' . $encodedMsg . ');';
         echo "window.location.href='index.php?s=1'";//we can pass here get variables
         echo '</script>';



        //  echo '<script>';
        //  echo 'console.log("' . $msg . '");'; // Debugging statement
        //  echo 'window.alert("' . $msg . '");'; // Alert statement
        //  echo '</script>';



        //  echo '<script>console.log("test");
        //  window.alert("sss");</script>';
        //  echo $msg;
        //  echo '<script>var msg='.$msg.';
        //         console.log(msg);
        //                 window.alert("hello");</script>';
            //echo '<script>window.alert("' . $msg . '");</script>';

        //echo '<script>window.alert("You cannot resend codes currently as you exceeded the number of times allowed,
         //try again after '.time()-$_SESSION['resend']['time'].' minutes");</script>';
    }
}
//echo "OTP";
   // echo Functions::sendMail("omarallahamlu2021@gmail.com","sauz gesh wuqo wcsx","omarallaham58@gmail.com","TEST_OTP","BBB");
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/signIn.css">
    <link rel="icon" type="image/x-icon" href="logos/primary_icon.jpeg" /> <!-- modified -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>OTP</title>

    <style>
        /* body,
        div,
        form,
        p {
            margin: 0;
            padding: 0;
        }

        .form {
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: space-around;
            width: fit-content;
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            margin: 0 auto;
            /* center the form horizontally 
            position: absolute;
            left: 0;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            /* center the form vertically 
        } */

        .title {
            font-size: 2rem;
            /* use rem for font size */
            font-weight: bold;
            color: black;
        }


        .message {
            color: #a3a3a3;
            font-size: 1rem;
            /* use rem for font size */
            margin-top: 1.2rem;
            /* use rem for margin */
            text-align: center;
        }

        /*.inputs {
            margin-top: 1rem;
            /* use rem for margin 
        }

        .inputs input {
            width: 2em;
            /* use em for width 
            height: 2em;
            /* use em for height 
            text-align: center;
            border: none;
            border-bottom: 0.15em solid #d2d2d2;
            /* use em for border-bottom width 
            margin: 0 1em;
            /* use em for margin 
        }

        .inputs input:focus {
            border-bottom: 0.15em solid royalblue;
            /* use em for border-bottom width 
            outline: none;
        }*/

        .action {
            margin-top: 2.4rem;
            /* use rem for margin */
            padding: 1rem 2rem;
            /* use rem for padding */
            border-radius: 0.8rem;
            /* use rem for border-radius */
            border: none;
            background-color: royalblue;
            color: white;
            cursor: pointer;
            align-self: end;
        }
    </style>

</head>
<div class="popup-overlay" id="popup-overlay"> </div>
    <div class='otp'>
        <form action="otp.php" method="POST">
            <div>
                <h2>OTP</h2>
                <h2>Verification Code</h2>
            </div>
            <?php

            if ($errormsg == "") {
                echo '<p class="message">We have sent the OTP code to your email</p>';
            } else {
                echo '<p class="message" style="color:red;">' . $errormsg . '</p>';
            }

            ?>
            <div class="form-inputs">
                <?php
                for ($i = 0; $i < 6; $i++) {
                    // echo '<input id="input'.$i.'" type="text" name="arr['.$i.']" maxlength="1" required/ >';
                    echo '<input id="input' . $i . '" type="text" name="arr[' . $i . ']" maxlength="1"/>';
                }
                ?>
            </div>
            <!-- </div> -->
            <!-- <button class="action" type="submit" name="verify2">Verify Me</button> -->
            <br>

            <input type="submit" class="otp-submit-button" name="verify" value="VERIFY">
            <br>
            <p class="message">Email not sent or an error occured?</p>
            <!-- <button class="action"><a href="otp.php?resend=1">Resend Code</a></button> -->
            <div class='fields-row'>
            <p class='text-button'><a href="sign_in.php">Back </a></p>
            <p class='text-button'><a href="otp.php?resend=1"> Resend Code</a></p>
            </div>
            <!-- <button onclick="window.location='index.php?s=2'">ss</button> -->
            <!-- <a href="otp.php?resend=1"><button>CLICK</button></a> -->
        </form>
    </div>
</body>
</html>