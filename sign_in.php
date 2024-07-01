<?php
include_once("include_all.php");
include_once("connection.php");
//$expiryTime = time() + (60 * 60 * 24); // Expiry time: 24 hours from now
//session_set_cookie_params($expiryTime);
session_start();
include_once("check_login.php");
if($_SESSION['signed_in']==true){
if($isClient) header("Location:index.php");
else if($isAdmin) header("Location:admin/admin.php");
else header("Location:admin/owner.php");
}


//print_r($_SESSION);
echo '<br><br>';
//print_r($_COOKIE);
echo '<br><br>';
//print_r(session_get_cookie_params());
$errormsg1="";
$errormsg2="";
$errormsg3="";
$t=0;
if(isset($_POST['signin'])){

    if(!empty($_POST['email']) && !empty($_POST['password'])){

        //check if it is the owner or not 
        if($_POST['email']==OWNER_EMAIL && $_POST['password']==OWNER_PASSWORD){
            //generate $_COOKIE['isOwner']=="true"
            setcookie( "isOwner", "true", time()+3600);//owner can only have 1 hour  cookie for security reasons
            header( "Location: admin/owner.php" );
        }

        $id=$dbHelper->authenticate($_POST['email'],$_POST['password']);

        if($id!=-1){
            //authentication succeeded
            $acc=$dbHelper->getAccountById($id);
            
            if ($acc!=null){
                $email=$acc->getEmail();
                $shuffled_email=Functions::myShuffle($email);
                $token=hash('sha256',$shuffled_email);
                $acc->setToken($token);
                $dbHelper->updateAccount($acc);
            }
            
            if(isset($_POST['keep_signed'])){
                // echo '<br>Remember me<br> ';
               // $_SESSION['keep_signed']="remember me";
               $t=time()+3600*24*90;
                setcookie("token",$acc->getToken(),time()+3600*24*90);//keep signed in for 3 months
            }
            else{
                $t=time()+3600*24*7;
                //keep signed in for one week
                setcookie("token",$acc->getToken(),time()+3600*24*7);
            }

            if($acc->getIsAdmin()==1){
                //generate cookie isAdmin("true")
                setcookie("isAdmin","true",$t);
                header("Location:admin/admin.php");//also here we can send a token to identify which admin is operating
            }
            else{
                setcookie("isClient","true",$t);
                if(isset($_POST['product_id'])){
                    $link="Location:displayProduct.php?product_id=".$_POST['product_id'];
                    header($link);
                }
                else
                header("Location:index.php");/*we might send token as get to identify the user
                and in index we appy condition that if $_GET['token'] equlas $_COOKIE['token'] then it's true 
                and we can obtain the user account & profile from this token*/
            }

        }

        else{
            //authentication failed
            $errormsg1="Invalid Credentials, try again using a valid email and password or create a new account. ";
        }

    }
    else{
        $errormsg2="The password field is empty, please enter your password in order to sign in and continue";
    }
}

else if(isset($_POST['forgot'])){
    if(($_SESSION['account']=$dbHelper->accountExist($_POST['email']))!=null){
        $_SESSION['otp'][$_SESSION['account']->getEmail()]['code']=Functions::generateOtp();
        $_SESSION['otp'][$_SESSION['account']->getEmail()]['time']=time();
        if(//$_SESSION['otp'][$_SESSION['account']->getEmail()]['firstAttempt']!=0 ||
         //empty($_SESSION['otp'][$_SESSION['account']->getEmail()]['firstAttempt'])||
         !isset($_SESSION['otp'][$_SESSION['account']->getEmail()]['firstAttempt']) //||
                    )
        $_SESSION['otp'][$_SESSION['account']->getEmail()]['firstAttempt']=1;

        header("Location:otp.php");
    }
    else{
        //invalid email => the account is not found
        $errormsg3="The entered email is invalid, no account found for this email. Please enter a valid email to proceed.";
    }
}

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/signIn.css">
    <link rel="icon" type="image/x-icon" href="logos/primary_icon.jpeg" /> <!-- modified -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Sign in</title>
</head>
<body>
<!--Normal case: -->
<div class="popup-overlay" id="popup-overlay"></div>
    <div class='centered-div'>
        <div class='fields-row'>
            <img src="../logos/primary_icon.jpeg" alt="logo" width='50rem' height='50rem'>
            <h2> Sign In</h2>
        </div>
        <div class="error-container">
            <?php if ($errormsg1 != "")
                echo '<p style="color:red;">' . $errormsg1 . '</p>';
            else if ($errormsg2 != "")
                echo '<p style="color:red;">' . $errormsg2 . '</p>';
            else if ($errormsg3 != "")
                echo '<p style="color:red;">' . $errormsg3 . '</p>';
            else
                echo '<br><br>'; ?>
        </div>
        <form action="sign_in.php" method="POST">
            <div class='fields-col'>
                <div>Email address <span style="color:red;">*</span></div><br>
                <input type="email" class='add-input' name="email" required><br>
            </div>
            <div class='fields-col'>
                <div>Password </div><br>
                <input id="passwd" type="password" class='add-input' name="password" minlength="8"><br>
            </div>

            <div class='fields-row'>
                <input type="checkbox" name="keep_signed"> Remember me &nbsp;<br>
                <input type="checkbox" name="showPassword" id="showPassword" onclick="show()" style="padding-left: 5%;"> Show password <br>
            </div><br>
            <input type="submit" class='submit-button' name="signin" value="Sign in" /><br>
            <br>
            <input type="submit" class='text-button' name="forgot" value="Forgot Password?">
            <br>
            <p class='text-button'>First time user? <a href="createAccount.php">Sign Up</a></p>
            <p class='text-button-acc'><a href="index.php">Back</a></p>
        </form>
    </div>
    <script>
        function show(){
            if(document.getElementById("showPassword").checked==true)
            document.getElementById('passwd').type = "text";
        else
        document.getElementById('passwd').type = "password";
            // if(document.getElementById( "passwd" ).type === "password") document.
        }

</script>
</body>
</html>