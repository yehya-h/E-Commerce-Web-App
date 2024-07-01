<?php

include_once("include_all.php");
include_once("connection.php");
session_start();
$errormsg="";
if(isset($_POST['reset'])){


    if(!empty($_POST['passwd1']) && !empty($_POST['passwd2'])){

        if($_POST['passwd1']==$_POST['passwd2']){

            $passwd=hash('sha256',$_POST['passwd1']);
            $_SESSION['account']->setPassword($passwd);
            $dbHelper->updateAccount($_SESSION['account']);
            header("Location:popup.php?page=resetPassword");
        }

        else{

            $errormsg="The 2 fields are different, please make sure that both fields are identical";
        }
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
    <title>Reset Password</title>
</head>
<body>
<div class="popup-overlay" id="popup-overlay"></div>
    <div class='reset'>
        <h1>Reset Password</h1>
        <form action="resetPassword.php" method="POST">

            <?php
            echo '<div class="error-container">';
            if ($errormsg == "")
                echo '<p>Enter your new password</p>';
            else
                echo '<p style="color:red;">' . $errormsg . '</p>';

            echo '</div>';
            ?>
            <div class='fields-col'>
                <div>Password <span style="color:red;">*</span></div><br>
                <input type="password" id="passwd1" name="passwd1" class='add-input' minlength="8" required>
            </div>
            <br>
            <div class='fields-col'>
                <div>Confirm Password <span style="color:red;">*</span></div><br>
                <input type="password" id="passwd2" name="passwd2" class='add-input' minlength="8" required>
            </div><br>
            <div class='fields-row'>
                <input type="checkbox" name="showPassword" id="showPassword" onclick="show()" style="padding-left: 5%;"> Show password <br>
            </div><br>
            <!-- <br> -->
            <input type="submit" name="reset" class='submit-button' value="Reset Password">
            <p class='text-button-acc'><a href="sign_in.php">Back</a></p>
        </form>
    </div>
    <script>
        function show(){

            if(document.getElementById("showPassword").checked==true){

            document.getElementById('passwd1').type = "text";
            document.getElementById('passwd2').type = "text";
            }

        else{
        document.getElementById('passwd1').type = "password";
        document.getElementById('passwd2').type = "password";
            // if(document.getElementById( "passwd" ).type === "password") document.
        }
    }

</script>
</body>
</html>