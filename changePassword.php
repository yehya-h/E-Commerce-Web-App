<?php
include_once("include_all.php");
include_once("connection.php");
session_start();
$error=false;
$errormsg="";
//print_r($_POST);
include_once("check_login.php");
if($_SESSION['signed_in']==true){

    if($isOwner==true) echo '<script>window.location.href="admin/owner.php";</script>';// header("Location:admin/owner.php");
    //if($isAdmin==true) echo '<script>window.location.href="admin/admin.php";</script>';//header("Location:admin/admin.php");
    //if($isClient!=true)echo '<script>window.location.href="sign_in.php";</script>';// header("Location:sign_in.php");
    //otherwise it must be a client 


}
else echo '<script>window.location.href="sign_in.php";</script>';//header("Location:sign_in.php");

if(isset($_POST['changePassword'])){

    if(hash("sha256",$_POST['passwd1'])!=$_SESSION['ACCOUNT']->getPassword()){

        $error = true;
        $errormsg="Please enter your latest old password correctly !!";
    }

    else{

        if($_POST['passwd2']!= $_POST['passwd3']) {

            $error=true;
            $errormsg="Please make sure that you type exactly the same password in the confirmation field";
        }

        else{

            //everything true
            $_SESSION['ACCOUNT']->setPassword(hash("sha256",$_POST['passwd2']));
            
            if($dbHelper->updateAccount($_SESSION['ACCOUNT'])==false){

                $error=true;
                $errormsg="An error occured while updating the account";

            }

            else{

                //everything succeeded
                header("Location:popup.php?page=changePassword");
            }

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
    <title>Change Password</title>
    <style>
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Semi-transparent background */
            z-index: 999;
            /* Ensure the overlay is on top of other elements */
        }

        .popup-content {
            position: fixed;
            top: 50%;
            left: 50%;
            width:25%;
            height:70%;
            transform: translate(-50%, -50%);
            background-color: var(--navy-blue);
            color : white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            /* Center-align content */
        }

        .popup-content form {
            width : 100%;
        }

        @media screen and (max-width: 768px) {
            .popup-content{
                width: 80%;
                height: 80%;
            }
        }
    </style>
    </head>
    <body>
    <div class="popup-overlay" id="popup-overlay">
        <div class="popup-content" id="popup-content">
            <!-- <div class='reset'> -->
                <h1>Change Password</h1>
                <form action="changePassword.php" method="POST">

                    <?php
                    echo '<div class="error-container">';
                    echo '<p style="color:red;">' . $errormsg . '</p>';
                    echo '</div>';

                    ?>
                    <div class='fields-col'>
                        <div>Old Password <span style="color:red;">*</span></div><br>
                        <input type="password" id="passwd1" name="passwd1" class='add-input' minlength="8" required value=<?php echo !empty($_POST['passwd1']) ? $_POST['passwd1'] : ""; ?>><br>
                    </div>
                    <div class='fields-col'>
                        <div>New Password <span style="color:red;">*</span></div><br>
                        <input type="password" id="passwd2" name="passwd2" class='add-input' minlength="8" required value=<?php echo !empty($_POST['passwd2']) ? $_POST['passwd2'] : ""; ?>><br>
                    </div>
                    <div class='fields-col'>
                        <div>Confirm New Password <span style="color:red;">*</span></div>
                        <input type="password" id="passwd3" name="passwd3" class='add-input' minlength="8" required value=<?php echo !empty($_POST['passwd3']) ? $_POST['passwd3'] : ""; ?>><br>
                    </div>
                    <!-- <br> -->
                    <div class='fields-row'>
                <input type="checkbox" name="showPassword" id="showPassword" onclick="show()" style="padding-left: 5%;"> Show password <br>
            </div>
                    <br>
                    <input class='add-button' type="submit" name="changePassword" value="Change Password">
                    <p class='text-button-acc'><a href='index.php'>Back</a></p>

                </form>
            <!-- </div> -->
        </div>
    </div>

    <script>
        function show(){

            if(document.getElementById("showPassword").checked==true){

            document.getElementById('passwd1').type = "text";
            document.getElementById('passwd2').type = "text";
            document.getElementById('passwd3').type = "text";
            }

        else{
        document.getElementById('passwd1').type = "password";
        document.getElementById('passwd2').type = "password";
        document.getElementById('passwd3').type = "password";
            // if(document.getElementById( "passwd" ).type === "password") document.
        }
    }

</script>
</body>
</html>