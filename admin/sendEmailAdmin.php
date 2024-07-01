<?php
include_once ($_SERVER['DOCUMENT_ROOT']."/connection.php");
include_once("../include_all.php");//added
include_once("../classes/Functions.php");//added
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>

<body>
    <?php
    $error = 1;
    if (isset($_POST['submit'])) {
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $account = $dbHelper->accountExist($_POST['email']);

            if ($account == null //|| $account->getIsAdmin() == 0
            ) {
                //if ($account!=null) $account_id = $account->getAccountId();else
                //$account_id = 0; // $dbHelper->addAccount();account_id=".$account_id."&
                $email = htmlspecialchars($_POST['email']);
                $subject = "Congratulations! You're One Step Closer to Becoming an Admin";
                
                // $body = "<html><body><p>We are delighted to announce that you're one step closer to becoming an admin at S&S!</p>
                // <p>To proceed further and complete the administrative setup, please click on the following link to
                //  access the required form: <a href='http://192.168.1.111:3000/admin/fillAdminInformations.php?email=".$email."'> click here to fill the form</a></p>
                // <p>Best regards,</p>
                //  <p>S&S team</p></body></html>";

                  // $body = "<html><body><p>We are delighted to announce that you're one step closer to becoming an admin at S&S!</p>
                // <p>To proceed further and complete the administrative setup, please click on the following link to
                //  access the required form: <a href='http://192.168.1.25:3000/admin/fillAdminInformations.php?email=".$email."'> click here to fill the form</a></p>
                // <p>Best regards,</p>
                //  <p>S&S team</p></body></html>";

                    //$body MODIFIED BY OMAR 
                 $body = "<html><body><p>We are delighted to announce that you're one step closer to becoming an admin at S&S!</p>
                 <p>To proceed further and complete the administrative setup, please click on the following link to
                  access the required form: <a href='http://".HOST_ADDRESS."/admin/fillAdminInformations.php?email=".$email."'> click here to fill the form</a></p>
                 <p>Best regards,</p>
                  <p>S&S team</p></body></html>";
                $sent = Functions::sendMail(COMPANY_MAIL, MAIL_APP_PASSWORD, $email, $subject, $body);

                if ($sent)
                $message = "<p style='color:green ; font-weight:bold'>Email sent successfully</p>";
                else {
                    $message = "<p style='color:red ; font-weight:bold'>Problem occured while sending the email!</p>";
                    $error = 0;
                }

            } else {
                $message = "<p style='color:red ; font-weight:bold'>There exist an account with this email!</p>";
                $error = 0;
            }
        }
    }
    ?>
</body>

</html>