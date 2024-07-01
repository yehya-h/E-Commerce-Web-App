<?php
include_once("sendEmailAdmin.php");
if(!isset($_COOKIE['isOwner'])){
    if(isset($_COOKIE['isAdmin'])) header("location:admin.php");
    else header("location:../index.php");//MODIFIED(OMAR)
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="../logos/primary_icon.jpeg" /> <!-- modified -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> <!--modified -->
    <title>Add Admin</title>
</head>

<body>
           <!-- -------------------------------------------------------------------------------------------------------------------------- -->
           <header>
        <nav class="nav-container">
            <div class='left-nav'>
                <div>
                    <a href="../index.php"><img src="../logos/primary_logo.png" alt="logo" width='210rem'
                            height='90rem'></a>
                </div>
            </div>

            <?php
            include_once ("../check_login.php");
            if ($isAdmin == true)
                header("Location:admin.php");
            if ($isClient == true)
                header("Location:../index.php");

            if ($_SESSION['signed_in'] == true /*&& $isClient==true*/) {
                if ($isClient == true) {
                    $client = $dbHelper->getClientByToken($_COOKIE['token']);
                    if($client==null){

                            header("Location:../sign_out.php?isClient=1");
                    }
                    echo '<div><li>Hello ' . $client->getFirstName() . '<br>Points:  ' . $client->getPoints() . '</li>';
                    echo '<li><a href="../manageAccount.php?user=client">Account</a>/<a href="../sign_out.php?isClient=1">Sign Out</a></li>';
                    echo '<li><a href="#about">About</a></li>';
                    echo '<li><a href="../cart.php">Cart</a></li></div>';
                } else if ($isAdmin == true) {
                    $admin = $dbHelper->getAdminByToken($_COOKIE['token']);
                    if($admin==null){

                        header("Location:../sign_out.php?isAdmin=1");
                    }
                    echo '<div><p>Admin : ' . $admin->getFirstName() . '</p>';
                    echo '<p><a href="../manageAccount.php?user=admin">Account</a>/<a href="../sign_out.php?isAdmin=1">Sign Out</a></p>';
                    echo '<p><a href="#about">About</a></p></div>';
                } else if ($isOwner == true) {
                    echo '<div class="center-nav"><div><h1>Admins Administration</h1></div></div>
                        <div class="right-nav"><div><h3>Owner : ' . OWNER_NAME . '</h3></div>';
                    echo '<div><p><a href="../sign_out.php?isOwner=1">
                    <i class="bi bi-box-arrow-right"></i> Sign Out</a></p></div></div>';
                }
            } else {
                echo '<p><a href="../sign_in.php">Sign in</a>/<a href="../createAccount.php">Create account</a></p>';
            }
            ?>
            <!-- <li><a href="#about">About</a></li>
                <li><a href="cart.php">Cart</a></li> -->
            </div>
        </nav>
    </header>
    <!-- -------------------------------------------------------------------------------------------------------------------------- -->
    <?php
    $email = "";
    if ($error == 0) {
        if (isset ($_POST['submit'])) {
            $email = $_POST['email'];
        }
    }
    ?>
    <div class="add-container">
    <!-- <h3>Add an Admin</h3> --><br><br>
    <p>Enter the email of the admin to send the form</p>
    <?php if(isset($message)) echo "
        <div class='error-container'>" . $message . " </div>
        " ; ?>
        <form action='addAdmin.php' method='POST'>
            <table>
                <tr>
                    <td>Admin email</td>
                    <td><input type='email' name='email' required value='<?php echo $email; ?>' class="add-input"></td>
                </tr>
            </table>
            <br>
            <input type='submit' name='submit' value='Add Admin' class="search-button">
            <!-- <button class="search-button"><a href='manageAdmins.php'>Back</a></button> -->
        </form>
        <br><button class="text-button"><a href='manageAdmins.php'>Back</a></button>
    </div>
    <footer>
        <!-- common footer -->
    </footer>
</body>

</html>