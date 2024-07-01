<?php
include_once ($_SERVER['DOCUMENT_ROOT']."/connection.php");
session_start();
if(!isset($_SESSION['admin']['searched'])) $_SESSION['admin']['searched'] = 0;
//$back = "<button class='search-button'><a href='admin.php'>Back</a></button>";
$back = "<a href='admin.php' class='back-button'><button>Back</button></a>";
if(isset($_COOKIE['isOwner']) && $_COOKIE['isOwner'] == "true") $back = "<a href='owner.php' class='back-button'><button>Back</button></a>";
//$back = "<button class='search-button'><a href='owner.php'>Back</a></button>";
include_once("../check_login.php");
if($_SESSION['signed_in']==true){
if((!isset($_COOKIE['isOwner']) || $_COOKIE['isOwner'] == "false") && 
(!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] == "false")) header("location:../index.php");
}
else header("Location:../index.php");
//setting sessions
if (!isset ($_SESSION['admin']['accountsFilter'])) $_SESSION['admin']['accountsFilter'] = "order_id";
if (!isset ($_SESSION['admin']['accountsSearch'])) $_SESSION['admin']['accountsSearch'] = "";  
//assigning sessions new search values
if (isset ($_POST['submitFilter'])){
    $_SESSION['admin']['accountsFilter'] = $_POST['toFilter'];
    $_SESSION['admin']['searched'] = 1;
} 
if (isset ($_POST['submitSearch'])) $_SESSION['admin']['accountsSearch'] = htmlspecialchars($_POST['toSearch']);
if (!isset ($_SESSION['admin']['current'])) $_SESSION['admin']['current'] = 1;
if (isset ($_GET['current'])) $_SESSION['admin']['current'] = $_GET['current'];
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
    <title>View Accounts</title>
</head>

<body>
     <!-- -------------------------------------------------------------------------------------------------------------------------- -->
     <header>
        <nav class="nav-container">
            <div class='left-nav'>
                <div>
                    <a href="../index.php"><img src="../logos/primary_logo.png" alt="logo" width='220rem'
                            height='90rem'></a>
                </div>
            </div>

            <?php
            include_once ("../check_login.php");
            // if ($isAdmin == true)
            //     header("Location:admin.php");
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
                    echo '<div class="center-nav"><div><h1>Accounts Administration</h1></div></div>
                    <div class="right-nav"><div><h3>Admin : ' . $admin->getFirstName() . ' </h3></div>';
                    // echo '<div><a href="../manageAccount.php?user=admin"><i class="bi bi-person-fill-gear"></i></a></div>
                    // <div><a href="../sign_out.php?isAdmin=1"><i class="bi bi-box-arrow-right"></i></a></div></div>';
                    //echo '<p><a href="#about">About</a></p></div>';
                    // Account
                    // Sign Out

                    echo '<div class="account-select">
                    <i class="bi bi-person-fill-gear"></i>
                    <select id="accountSelect" onchange="goToPage(this.value)">
                        <option disabled selected>--choose--</option>
                        <option value="../manageAccount.php?forward=updateProfile">Update Profile</option>
                        <option value="../manageAccount.php?forward=changePassword">Change Password</option>
                        <option value="delete">Delete Account</option>
                    </select></div>';
                    echo '<div><a href="../sign_out.php?isAdmin=1"><i class="bi bi-box-arrow-right"></i> </a></div></div>';
                } else if ($isOwner == true) {
                    echo '<div class="center-nav"><div><h1>Accounts Administration</h1></div></div>
                        <div class="right-nav"><div><h3>Owner : ' . OWNER_NAME . ' </h3></div>';
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
    if(isset($_GET['client_id'])){
        $client_id=$_GET['client_id'];
        //$dbHelper->removeClient($client_id);
        if($dbHelper->deleteAccount(($dbHelper->getClientByClientId($client_id))->getAccountId()))
        header("location:viewAccounts.php");
        else  echo "Error deleting account.";
    }
    ?>
    <div class="bar-container">
        <!-- <h3>Accounts Administration</h3> -->
        <div class="search-bar">
            <form action="viewAccounts.php" method="POST">
            <div class="filter-wrapper">
                <select name="toFilter" class='filter-select'>
                    <?php
                    $selectOptions = array("client_id", "account_id"); //fixed
                    foreach ($selectOptions as $value) {
                        $selected = ($_SESSION['admin']['accountsFilter'] == $value) ? 'selected' : '';
                        echo "<option name='" . $value . "' value='" . $value . "' " . $selected . ">" . $value;
                    }
                    ?>
                </select>
                <input type="submit" name="submitFilter" class='search-button' value="Filter">
                </div>
            </form>
            <?php
            if (isset ($_POST['submitFilter']) || isset ($_POST['submitSearch']) || (isset($_GET['current']) && $_SESSION['admin']['searched']==1)) {
                if ($_SESSION['admin']['accountsFilter'] == "account_id" || $_SESSION['admin']['accountsFilter'] == "client_id") {
                    //displaying search bar
                    if (isset ($_POST['submitSearch']) && !empty ($_POST['toSearch'])) {
                        $holder = "value='" . htmlspecialchars($_POST['toSearch']) . "'";
                    } else
                        $holder = "placeholder='search ...'";
                    echo "<form action='viewAccounts.php' method='POST'>
                    <input type='text' class='search-input' name='toSearch' " . $holder . "'>
                    <input type='submit' name='submitSearch' class='search-button' value='search'></form>";
                }
            }
            ?>
        </div>
    </div>
    <?php
    if (isset ($_POST['submitSearch'])) {
        if ($_SESSION['admin']['accountsFilter'] == "account_id") {
            $accounts = $dbHelper->getClientInfoByAccountId($_SESSION['admin']['accountsSearch']);
        } else if ($_SESSION['admin']['accountsFilter'] == "client_id") {
            $accounts = $dbHelper->getClientInfoByClientId($_SESSION['admin']['accountsSearch']);
        }
    } else {
        $accounts = $dbHelper->getClientInfo(); //default
    }
    if (is_array($accounts))
        $count = count($accounts);
    else
        $count = 0;
    if (empty ($accounts)) {
        echo "<div class='error-container'><p style='color:red ; font-weight:bold'>No accounts</p>".$back . "</div>";
    } else {
        //echo "<div class='total-box'>total number of accounts<br>". count($accounts) ."</div>";
        //pagination
        $perPage = 6;
        $totalPages = ceil($count / $perPage);
        $first = ($_SESSION['admin']['current'] * $perPage) - $perPage;
        if (is_array($accounts) && !empty ($accounts)) {
            //$accounts is an array (default since the retrieved elements is an array and not a single elements , values from 2 tables)
            $temp = array_slice($accounts, $first, $perPage);
            echo " <div class='table-container'>
        <table border=1>
                   <tr>
                     <th>Account ID</th>
                     <th>Client ID</th>
                     <th>First Name</th>
                     <th>Last Name</th>
                     <th>Phone Number</th>
                     <th>Email</th>
                     <th>Shipment Information</th>
                     <th>Payment Information</th>"; 
                     if(isset($_COOKIE['isOwner']) && $_COOKIE['isOwner'] == "true") 
                      echo "<th>remove client</th>";
            foreach ($temp as $key => $value) {
                $phone = ($value['phone_number']!=null)? $value['phone_number'] : 'N/A';
                echo "<tr>
                    <td>" . $value['account_id'] . "</td> 
                    <td>" . $value['client_id'] . "</td>
                    <td>" . $value['firstName'] . "</td>
                    <td>" . $value['lastName'] . "</td>
                    <td>" . $phone . "</td>
                    <td>" . $value['email'] . "</td>
                    <td>";
                    // $shipment=($dbHelper->getShipmentInfoByClientId($value['client_id']))!=null ? "<button 
                    // class='i-button'><a href='viewClientShipments.php?client_id=" . $value['client_id'] . "'>view shipments</a></button></td>" : "N/A" ;
                    $shipment=($dbHelper->getShipmentInfoByClientId($value['client_id']))!=null ? "<a href='viewClientShipments.php?client_id=" . $value['client_id'] . "'>
                    <button class='i-button'>view shipments</button></a></td>" : "N/A" ;
                    echo $shipment . "</td><td>";
                    // $payment=($dbHelper->getPaymentInfoByClientId($value['client_id']))!=null ? "<button 
                    // class='i-button'><a href='viewClientPayments.php?client_id=" . $value['client_id'] . "'>view payments</a></button></td>" : "N/A" ;
                    $payment=($dbHelper->getPaymentInfoByClientId($value['client_id']))!=null ? "<a href='viewClientPayments.php?client_id=" . $value['client_id'] . "'>
                    <button class='i-button'>view payments</button></a></td>" : "N/A" ;
                    echo $payment . "</td>";
                    if(isset($_COOKIE['isOwner']) && $_COOKIE['isOwner'] == "true") 
                    //   echo "<td><button class='i-button'><a href='popupAdmin.php?client_id=" . $value['client_id'] . "&page=viewAccounts'>remove account</a></button></td></tr>";
                    echo "<td><a href='popupAdmin.php?client_id=" . $value['client_id'] . "&page=viewAccounts'><button class='i-button'>remove account</button></a></td></tr>";
            
            }
            echo "</table>";
        } else {
            echo "<div class='error-container'><p style='color:red ; font-weight:bold'>No accounts to display</p> <br>" . $back . "</div>";
        }
        //pagination
        if ($_SESSION['admin']['current'] > 1)
            echo "<button class='page-button'><a href='viewAccounts.php?current=" . ($_SESSION['admin']['current'] - 1) . "'><</a></button>";
        echo " Page " . $_SESSION['admin']['current'] . "/" . $totalPages;
        if ($_SESSION['admin']['current'] < $totalPages)
            echo " <button class='page-button'><a href='viewAccounts.php?current=" . ($_SESSION['admin']['current'] + 1) . "'>></a></button>";
        echo "<br><br>".$back."
            </div>";
    }
    ?>
    <div class="popup-overlay" id="popup-overlay" style="display:none;">
            <div class="popup-content" id="popup-content">
                <h2>Are you sure you want to delete this account?</h2>
                <button class='add-button' onclick="deleteAccount()">Yes</button>
                <button class='add-button' onclick="closePopUp()">No</button>
            </div>
        </div>
     <script type="text/javascript" src="script.js"></script>
    <footer>
        <!-- common footer -->
    </footer>
</body>

</html>