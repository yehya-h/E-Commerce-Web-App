<?php
session_start();
include_once("include_all.php");
include_once("connection.php");

include_once ("check_login.php");
if ($isAdmin == true)
    header("Location:admin/admin.php");
else if ($isOwner == true)
    header("Location:admin/owner.php");
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/viewOrders.css">
    <link rel="stylesheet" href="style/header.css">
    <link rel="stylesheet" href="style/footer.css">
    <link rel="icon" type="image/x-icon" href="logos/primary_icon.jpeg" /> <!-- modified -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>View Orders</title>
</head>
<body>
<header>
        <nav class="nav-container">
            <div class='left-nav'>
                <div class="pc-logo">
                    <a href="../index.php"><img src="../logos/primary_logo.png" alt="logo" width='220rem'
                            height='90rem'></a>
                </div>
                <div class="phone-logo">
                    <a href="../index.php"><img src="logos/primary_logo_phone_2.png" alt="logo" width='70em'
                            height='60em'></a>
                </div>
            </div>
            <div class="center-nav">
                <div class="search-bar">
                    <form id="filter" action="index.php" method="POST">
                        <!-- <label for="categoryFilter">Select Categories</label> -->
                        <div class="filter-wrapper">
                            <select id="categoryFilter" name="categoryFilter" class="filter-select">
                                <?php
                                $categories = $dbHelper->getAllCategories("name");
                                foreach ($categories as $category) {
                                    echo "<option value='" . $category->getCategoryId() . "'>" . $category->getName() . "</option>";
                                }
                                ?>
                            </select>
                            <input type="submit" name="filterByCategory" value="ok" class='filter-button' />
                        </div>
                    </form>
                    <form action="index.php" method="post">
                        <input type="text" placeholder="What are you looking for?" name="searchField" required
                            class='search-input' />
                        <button type="submit" name="search" class='search-button'><i class="bi bi-search"></i></button>
                    </form>
                </div>
            </div>
            <?php
            if ($_SESSION['signed_in'] == true && $isClient == true) {
                $client = $dbHelper->getClientByToken($_COOKIE['token']);
                if ($client == null) {
                    //account not founed depending on token => somebody signed in from another device
                    //in this case, sign out from this device to avoid errors
                    header("Location:sign_out.php?isClient=1");
                }
                $cart_id = $client->getCartId();
                $cartItems = 0;
                if ($dbHelper->getNbCartItems($cart_id) != null) {
                    $cartItems = $dbHelper->getNbCartItems($cart_id);
                }
                echo '<div class="right-nav"><h3>&nbsp<div class="account-select" onclick="showOptions()">
                    ' . $client->getFirstName() . '
                    <i class="bi bi-person-fill-gear"></i>
                    <select id="accountSelect" onchange="goToPage(this.value)">
                            <option disabled selected>--choose--</option>
                            <option value="manageAccount.php?forward=viewOrders">View Orders</option>
                            <option value="manageAccount.php?forward=updateProfile">Update Profile</option>
                            <option value="manageAccount.php?forward=changePassword">Change Password</option>
                            <option value="delete">Delete Account</option>
                        </select>
                        </div>
                    ';
                echo '&nbsp|&nbspPoints:' . $client->getPoints();
                echo '&nbsp<h2 style="background-color:transparent;"><div class="cart-icon"><a href="cart.php"><i class="bi bi-bag-fill"></i> </a>
                                   <span id="nbItems">' . $cartItems . '</span></div></h2>
                                ';
                // echo '&nbsp<a href="#about">About</a>';
                echo '&nbsp&nbsp<h2 style="background-color:transparent;">
                <a href="sign_out.php?isClient=1"><i class="bi bi-box-arrow-right"></i> </a></h2></h3></div>';
            } else {
                echo '<div class="right-nav">
                    <h3><a href="sign_in.php">Sign In&nbsp<i class="bi bi-person-fill"></i></a></h3>&nbsp &nbsp
                    <div class="cart-icon"><a href="cart.php"><i class="bi bi-bag-fill"></i> </a>
                                   <span id="nbItems">0</span></div></div>';
                // &nbsp &nbsp<a href="createAccount.php">Create account&nbsp<i class="bi bi-person-fill-add"></i></a>;
            }
            ?>
            <!-- <li><a href="#about">About</a></li> -->
            <!-- <li><a href="cart.php">Cart</a></li> -->
            </ul>
        </nav>
    </header>

    <div class="middle-section">
        <div class="popup-overlay" id="popup-overlay" style="display:none;">
            <div class="popup-content" id="popup-content">
                <h2>Are you sure you want to delete this account?</h2>
                <button class='add-button' onclick="deleteAccount()">Yes</button> <button class='add-button'
                    onclick="closePopUp()">No</button>
            </div>
        </div>
<h1>Order History</h1>
    <div class='order-table-container'>
    <div class="table-container-display">
        <table border=1>
        <thead>
            <tr>
                <th>Order Id</th>
                <th>Date</th>
                <th>Details</th>
                <th>Shipment Info</th>
                <th>Total Amount</th>
            </tr></thead>
            <tbody>
        <?php
        $perpage=5;
        $current=(isset($_GET['page']))?$_GET['page']:1;
        $offset=(($current-1)*$perpage);
        
        
    $orders=$dbHelper->getOrdersByClientId(($dbHelper->getClientByToken($_COOKIE['token']))->getClientId());
   // $totalPages=ceil(count($orders)/$perpage); 
    if(!empty($orders) && $orders!=null){
        $totalPages=ceil(count($orders)/$perpage); 
        $ordersPerPage=array_slice( $orders , $offset , $perpage );
        foreach($ordersPerPage as $key=>$value){

                // echo '<tr><td>'.$value->getOrderId() .'</td><td>'.$value->getOrderDate().
                // '</td><td><a href="popup.php?page=viewOrders&shipmentInfo_id='.$value->getSelectedShipmentInfoId().
                // '">'.$value->getSelectedShipmentInfoId().'</a></td><td>'.$value->getTotalAmount().'</td></tr>'; 
            //     echo '<tr>
            //     <td label="Order Id"><a href="popup.php?page=viewOrderDetails&order_id='.$value->getOrderId().'">' . $value->getOrderId() . '</a></td>
            //     <td label="Date">' . $value->getOrderDate() . '</td>
            //     <td label="Shipment Info"><button class="i-button"><a href="popup.php?page=viewOrders&shipmentInfo_id=' . $value->getSelectedShipmentInfoId() .
            // '">shipment info</a></button></td>
            //     <td label="Total Amount">' . $value->getTotalAmount() . '</td></tr>';

            echo '<tr>
            <td label="Order Id">' . $value->getOrderId() . '</td>
            <td label="Date">' . $value->getOrderDate() . '</td>
            <td label="Details"><button class="i-button"><a href="orders_popup.php?page=viewOrderDetails&order_id='.$value->getOrderId().
                                '&isAdmin=0">View Details</a></button></td>
            <td label="Shipment Info"><button class="i-button"><a href="popup.php?page=viewOrders&shipmentInfo_id=' . $value->getSelectedShipmentInfoId() .
                                '">shipment info</a></button></td>
            <td label="Total Amount">' . number_format($value->getTotalAmount(),2) . '</td></tr>';





        }

    }
    else{
        echo '<p style="color:red;">No available orders to display</p>';
    }
    echo '</tbody></table>';
    if($current>1) 
    echo '<button  class="page-button"><a href="viewOrders.php?page=' . ($current - 1) . '"><</a></button>';
     if(isset($totalPages)) echo "Page " . $current . "/" . $totalPages;
    if( isset($totalPages)&&$current<$totalPages) 
    echo '<button  class="page-button"><a href="viewOrders.php?page=' . ($current + 1) . '">></a></button>';
            echo "<br><br>";

?>
    <!-- </table> --><br>
    <!-- <a href="manageAccount.php">Back to Account Management</a>  -->
    <button class='add-button'><a href="index.php">Back</a></button>
    </div>
</div>
</div>

<footer>
        <div class="footer">
            <div class="contact">
                <div style="text-align:center; ">
                    <p>Need Help?
                        <br>Contact us through any of these support chanels
                    </p>
                </div>
                <div class="contact-us">
                    <div class="margin-right">
                        <p><a href="mailto:shopandshiponlinetrading@gmail.com" class="linkFooter">Contact us via
                                Mail<br>
                                <i class="bi-envelope-fill"></i></a></p>
                    </div>

                    <div class="margin-left">
                        <p><a href="https://wa.me/+96106388426" class="linkFooter">Whatsapp Us<br>
                                <i class="bi-whatsapp"></i></a></p>
                    </div>
                </div>
            </div>
            <p style="font-size:0.8rem; ">&copy; 2024 S&S. All Rights Reserved</p>
        </div>
    </footer>
<?php
        if ($_SESSION['signed_in'] == true && $isClient == true) {
            echo '<div class="bottom-bar-phone">
                            <div class="bar-phone-items">
                                <div><h2 style="color:var(--navly-blue);"><div class="account-select">
                                <i class="bi bi-person-fill-gear"></i>
                                <select id="accountSelect" onchange="goToPage(this.value)">
                                        <option disabled selected>--choose--</option>
                                        <option value="manageAccount.php?forward=viewOrders">View Orders</option>
                                        <option value="manageAccount.php?forward=updateProfile">Update Profile</option>
                                        <option value="manageAccount.php?forward=changePassword">Change Password</option>
                                        <option value="delete">Delete Account</option>
                                    </select>
                                    </div></h2></div>
                                    
                                    <div><h4 style="color:var(--navy-blue);">Points:' . $client->getPoints() . '</h4></div>

                                <div><h2 ><div class="cart-icon"><a href="cart.php" style="color:var(--navy-blue);"><i class="bi bi-bag-fill"></i> </a>
                                    <span id="nbItems" style="background-color:var(--bordo);">' . $cartItems . '</span></div></h2></div>

                                

                                <div><h2 ><a href="sign_out.php?isClient=1" style="color:var(--navy-blue);"><i class="bi bi-box-arrow-right"></i> </a>   </h2></div>
                                </div>
                        </div>';

        } else {
            echo '<div class="bottom-bar-phone">
                            <div class="bar-phone-items">

                                <div><h2 ><div class="cart-icon"><a href="cart.php" style="color:var(--navy-blue);"><i class="bi bi-bag-fill"></i> </a>
                                    <span id="nbItems" style="background-color:var(--bordo);">0</span></div></h2></div>

                                <div><h3><a href="sign_in.php" style="color:var(--navy-blue);">Sign In&nbsp<i class="bi bi-person-fill"></i></a></h3></h2></div>
                                </div>
                        </div>';
        }


        ?>

        <script>

            function showOptions() {
                var select = document.getElementById('accountSelect');
                select.focus(); // Focus on the select element to show its options
            }

            function goToPage(src) {
                if (src == "delete") {
                    openPopUp();
                } else {
                    window.location.href = src;
                }
            }

            // Function to close the popup
            function closePopUp() {
                document.getElementById('popup-overlay').style.display = 'none';
                // window.location.href="sign_in.php";
                //window.location.href=next;
            }

            // Function to open the popup when the page loads
            function openPopUp() {
                document.getElementById('popup-overlay').style.display = 'block';
            }

            function deleteAccount() {
                document.getElementById('popup-overlay').style.display = 'none';
                //window.location.href = "deleteAccount.php?del=1";
                window.location.href="manageAccount.php?forward=deleteAccount";
            }

        </script> 
</body>
</html>