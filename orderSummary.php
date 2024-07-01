<?php
session_start();
include_once ("check_login.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <link rel="stylesheet" href="style\main.css" />
    <link rel="stylesheet" href="style/header.css" />
    <link rel="stylesheet" href="style/footer.css" />
    <link rel="icon" type="image/x-icon" href="../logos/primary_icon.jpeg" />
    <style>
        .message-container {
            text-align: center;
            padding: 8% 0 15% 0;
        }

        .message-container h2 {
            color: var(--navy-blue);
        }

        .message-container p {
            color: var(--blue);
        }

        .message-container a {
            background-color: var(--red);
            padding: 0.8rem 1.2rem 0.8rem 1.2rem;
            margin: 1rem 0.2rem 1rem 0.2rem;
            color: white;
            border-radius: 5px;
            font-size: medium;
            /* font-weight: bold; */
            border: none;
            text-decoration: none;
        }

        .message-container a:hover {
            background-color: var(--bordo);
            color: white;
            cursor: pointer;

        }

        @media only screen and (max-width: 768px) {

            .message-container {
            text-align: center;
            padding: 20% 0 30% 0;
        }
        .search-bar .search-input{
        width: 65%;
    }
}
    </style>
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

        <div class="message-container">
            <h2>Order placed successfully</h2>
            <p>Order Summary Email sent</p><br>
            <a href="index.php">Continue Shopping</a>
            <a href="cart.php">Back to Cart</a>
        </div>
    </div> <!-- close the middle section div -->

    <footer>
        <div class="footer">
            <div class="contact">
                <div style="text-align:center; ">
                    <p>Need Help?
                        <br>Contact us through any of these support channels
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
    //this code must be at last
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