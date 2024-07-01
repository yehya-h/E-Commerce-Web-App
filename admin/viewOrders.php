<?php
include_once ($_SERVER['DOCUMENT_ROOT']."/connection.php");
session_start();
if(!isset($_SESSION['admin']['searched'])) $_SESSION['admin']['searched'] = 0;
//$back = "<button class='search-button'><a href='admin.php'>Back</a></button>";
$back = "<a href='admin.php' class='back-button'><button>Back</button></a>";
include_once("../check_login.php");
if($_SESSION['signed_in']==true){
if(isset($_COOKIE['isOwner']) && $_COOKIE['isOwner'] == "true")  $back = "<a href='owner.php' class='back-button'><button>Back</button></a>";
//$back = "<button class='search-button'><a href='owner.php'>Back</a></button>";
if((!isset($_COOKIE['isOwner']) || $_COOKIE['isOwner'] == "false") && 
(!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] == "false")) header("location:../index.php");
}
else header("Location:../index.php");
//if(!isset($_COOKIE['isOwner']) || !isset($_COOKIE['isAdmin'])) header("location:sign_in.php");

//setting sessions
if (!isset ($_SESSION['admin']['ordersFilter'])) $_SESSION['admin']['ordersFilter'] = "order_id";
if (!isset ($_SESSION['admin']['ordersSearch'])) $_SESSION['admin']['ordersSearch'] = "";
//assigning sessions new search values
if (isset ($_POST['submitFilter'])){
    $_SESSION['admin']['ordersFilter'] = $_POST['toFilter'];
    $_SESSION['admin']['searched'] = 1;
}  
if (isset ($_POST['submitSearch']) && isset ($_POST['toSearch']))  $_SESSION['admin']['ordersSearch'] = htmlspecialchars($_POST['toSearch']);
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
    <title>View Orders</title>
</head>

<body>

    <div class="overlay" onclick="closePopup()"></div>

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
                    echo '<div class="center-nav"><div><h1>Orders Administration</h1></div></div>
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
                    echo '<div class="center-nav"><div><h1>Orders Administration</h1></div></div>
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
    <div class="bar-container">
        <!-- <h3>Orders Administration</h3> -->
        <div class="search-bar">
            <form action="viewOrders.php" method="POST">
            <div class="filter-wrapper">
                <select name="toFilter" class='filter-select'>
                    <?php
                    $selectOptions = array("order_id", "client_id", "order_date", "total_amount"); //fixed
                    foreach ($selectOptions as $value) {
                        $selected = ($_SESSION['admin']['ordersFilter'] == $value) ? 'selected' : '';
                        echo "<option name='" . $value . "' value='" . $value . "' " . $selected . ">" . $value;
                    }
                    ?>
                </select>
                <input type="submit" name="submitFilter" class='search-button' value="Filter">
                </div>
            </form>
            <?php
            if (isset ($_POST['submitFilter']) || isset ($_POST['submitSearch']) || (isset($_GET['current']) && $_SESSION['admin']['searched']==1)) {
                if (
                    $_SESSION['admin']['ordersFilter'] == "order_id" || $_SESSION['admin']['ordersFilter'] == "client_id"
                    || $_SESSION['admin']['ordersFilter'] == "total_amount"
                ) { //case search bar
                    if (isset ($_POST['submitSearch']) && isset ($_POST['toSearch']) && !empty ($_POST['toSearch'])) {
                        $holder = "value='" . htmlspecialchars($_POST['toSearch']) . "'";
                    } else
                        $holder = "placeholder='search ...'";

                    echo "<form action='viewOrders.php' method='POST'>
                    <input type='text' class='search-input' name='toSearch' " . $holder . "'>
                    <input type='submit' name='submitSearch' class='search-button' value='search'></form>";

                } else if ($_SESSION['admin']['ordersFilter'] == "order_date") { //case date
                    $date = '';
                    if (isset ($_POST['submitSearch'])) {
                        $date = $_SESSION['admin']['ordersSearch'];
                    }
                    echo "<form action='viewOrders.php' method='POST'>
                        <input type='date' class='date-input' name='toSearch' value='" . $date . "' required>
                        <input type='submit' name='submitSearch' class='search-button' value='search'></form>";
                }
            }
            ?>
        </div>
    </div>
    <?php
    //get orders by search
    if(isset ($_POST['submitFilter'])){
        $orders=$dbHelper->getAllOrders($_SESSION['admin']['ordersFilter']);
    }
    else if (isset ($_POST['submitSearch'])) {
        if ($_SESSION['admin']['ordersFilter'] == "order_id") {
            $orders = $dbHelper->getOrderById($_SESSION['admin']['ordersSearch']);
        } else if ($_SESSION['admin']['ordersFilter'] == "client_id") {
            $orders = $dbHelper->getOrdersByClientId($_SESSION['admin']['ordersSearch']);
        } else if ($_SESSION['admin']['ordersFilter'] == "order_date") {
            echo (getType($_SESSION['admin']['ordersSearch']));
            
           // $orders = $dbHelper->getOrdersByDate(date('Y-m-d',strtotime($_SESSION['admin']['ordersSearch'])));
           $date = DateTime::createFromFormat("Y-m-d", $_SESSION['admin']['ordersSearch']);
           $orders=$dbHelper->getOrdersByDate($date);
            //$orders = $dbHelper->getOrdersByDate(date($_SESSION['admin']['ordersSearch']));
           
        } else if ($_SESSION['admin']['ordersFilter'] == "total_amount") {
            $orders = $dbHelper->getOrdersByAmount($_SESSION['admin']['ordersSearch']);
        }
    } else {
        $orders = $dbHelper->getAllOrders($_SESSION['admin']['ordersFilter']); 
    }
    //print_r($orders);
    if (is_array($orders))
        $count = count($orders);
    else
        $count = 0;
    if (empty ($orders)) {
        echo "<div class='error-container'><p style='color:red ; font-weight:bold'>No orders</p>".$back . "</div>";
    } else {
        //echo "<div class='total-box'>total number of orders<br>". count($orders) ."</div>";

        //pagination
        $perPage = 6;
        $totalPages = ceil($count / $perPage);
        $first = ($_SESSION['admin']['current'] * $perPage) - $perPage;
        if (is_array($orders) && $orders != null) {  //case array of orders (default)
            $temp = array_slice($orders, $first, $perPage);
            echo " <div class='table-container'>
                 <table border=1>
                   <tr>
                     <th>Order ID</th>
                     <th>Client ID</th>
                     <th>Date</th>
                     <th>Amount</th>
                     <th>Details</th>
                     <th>Shipment Information</th>
                   </tr>";
            foreach ($temp as $key => $value) {
                if($value->getClientId()!=null){
                        $client_id = "<td>" . $value->getClientId() . "</td>";
                        // $shipment_info = "<td><button class='i-button' onclick='loadData(" .  $value->getOrderId() . ")'>shipment info</button></td>";
                    }else{
                        $client_id = '<td>N/A</td>';
                     //   $shipment_info = '<td>N/A</td>';
                    }
                    $shipment_info = "<td><button class='i-button' onclick='loadData(" .  $value->getOrderId() . ")'>shipment info</button></td>";
                echo "<tr>
                    <td>" . $value->getOrderId() . "</td>";
                    // echo  $client_id . "
                    // <td>" . $value->getOrderDate() . "</td>
                    // <td>" . $value->getTotalAmount() . "</td>";
                    echo  $client_id . "
                    <td>" . $value->getOrderDate() . "</td>
                    <td>" . number_format($value->getTotalAmount(),2) . "</td>
                    <td label='Details'><button class='i-button'><a href='../orders_popup.php?page=viewOrderDetails&order_id=".$value->getOrderId().
                                    "&isAdmin=1'>View Details</a></button></td>";
                    
                    echo $shipment_info;
            echo "</tr>";
            }
            echo "</table>";
        } else if ($orders != null) {        //case single order element
            $temp = $orders;
            $totalPages = ceil(1 / $perPage);
            if($temp->getClientId()!=null){
                $client_id = "<td>" . $temp->getClientId() . "</td>";
                // $shipment_info = "<td><button class='i-button' onclick='loadData(" .  $temp->getOrderId() . ")'>shipment info</button></td>";
            }else{
                $client_id = '<td style="padding: 5px 20px;">N/A</td>';
               // $shipment_info = '<td style="padding: 5px 20px;">N/A</td>';
            }
            $shipment_info = "<td><button class='i-button' onclick='loadData(" .  $temp->getOrderId() . ")'>shipment info</button></td>";
            
            // echo " <div class='table-container'>
            //      <table border=1>
            //        <tr>
            //          <th>Order ID</th>
            //          <th>Client ID</th>
            //          <th>Date</th>
            //          <th>Amount</th>
            //          <th>Details</th>
            //          <th>Shipment Information</th>
            //        </tr>
            //        <tr>
            //         <td>" . $temp->getOrderId() . "</td>";
            //         echo  $client_id . "
            //         <td>" . $temp->getOrderDate() . "</td>
            //         <td>" . $temp->getTotalAmount() . "</td>";
            echo " <div class='table-container'>
            <table border=1>
              <tr>
                <th>Order ID</th>
                <th>Client ID</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Details</th>
                <th>Shipment Information</th>
              </tr>
              <tr>
               <td>" . $temp->getOrderId() . "</td>";
               echo  $client_id . "
               <td>" . $temp->getOrderDate() . "</td>
               <td>" . number_format($temp->getTotalAmount(),2) . "</td>
               <td label='Details'><button class='i-button'><a href='../orders_popup.php?page=viewOrderDetails&order_id=".$temp->getOrderId().
               "&isAdmin=1'>View Details</a></button></td>";

                    echo $shipment_info;
           echo "</tr></table>";
        } else {
            echo "<div class='error-container'><p style='color:red ; font-weight:bold'>No orders to display</p><br>".$back . "</div>";
        }
        //pagination
        if ($_SESSION['admin']['current'] > 1)
            echo "<button  class='page-button'><a href='viewOrders.php?current=" . ($_SESSION['admin']['current'] - 1) . "'><</a></button>";
        echo " Page " . $_SESSION['admin']['current'] . "/" . $totalPages;
        if ($_SESSION['admin']['current'] < $totalPages)
            echo " <button class='page-button'><a href='viewOrders.php?current=" . ($_SESSION['admin']['current'] + 1) . "'>></a></button>";
        echo "<br><br>".$back."
            </div>";
    }
    ?>
    <div id="popup" class="popup"> </div>
    <div class="popup-overlay" id="popup-overlay" style="display:none;">
            <div class="popup-content" id="popup-content">
                <h2>Are you sure you want to delete this account?</h2>
                <button class='add-button' onclick="deleteAccount()">Yes</button>
                <button class='add-button' onclick="closePopUp2()">No</button>
            </div>
        </div>
    <script>
        function loadData(order_id) {
            var url = 'showShipment_order.php?order_id=' + encodeURIComponent(order_id);
            // Set the content of the popup iframe to the constructed URL
            document.getElementById('popup').innerHTML = '<iframe src="' + url + '" width="100%" height="100%" frameborder="0"></iframe>';
            document.getElementById('popup').style.display = 'block'; // Display the popup
            document.querySelector('.overlay').style.display = 'block';
        }
        function closePopup() {
            document.getElementById('popup').innerHTML = '';
            document.querySelector('.popup').style.display = 'none';
            document.querySelector('.overlay').style.display = 'none';
        }

        function getLink(link) {
    window.location.href = link;
}

function goToPage(src) {
    if (src == "delete") {
        openPopUp();
    } else {
        window.location.href = src;
    }
}

// Function to close the popup
function closePopUp2() {
    document.getElementById('popup-overlay').style.display = 'none';
}

// Function to open the popup when the page loads
function openPopUp() {
    document.getElementById('popup-overlay').style.display = 'block';
}

function deleteAccount() {
    document.getElementById('popup-overlay').style.display = 'none';
    window.location.href = "../manageAccount.php?forward=deleteAccount";
}

    </script>
    <footer>
        <!-- common footer -->
    </footer>
</body>

</html>