<?php
include_once ($_SERVER['DOCUMENT_ROOT']."/connection.php");
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
    <title>View Shipments Information</title>
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
                    // echo '<p><a href="#about">About</a></p></div>';
                    // Account
                    // Sign Out
                    echo '<div class="account-select">
                    <i class="bi bi-person-fill-gear"></i>
                    <select id="accountSelect" onchange="goToPage2(this.value)">
                        <option disabled selected>--choose--</option>
                        <option value="../manageAccount.php?forward=updateProfile">Update Profile</option>
                        <option value="../manageAccount.php?forward=changePassword">Change Password</option>
                        <option value="delete">Delete Account</option>
                    </select></div>';
                    echo '<div><a href="../sign_out.php?isAdmin=1"><i class="bi bi-box-arrow-right"></i> </a></div></div>';


                    // echo '<div class="account-select">
                    // <i class="bi bi-person-fill-gear"></i>
                    // <select id="accountSelect" onchange="function f(){
                    //  //if(this.value=="delete"){
                    //   //console.log("delete");
                    //   //openPopUp();  
                    //  //}
                    //  //else{
                    //     window.location.href=this.value;
                    //  //}
                    // }">
                    //     <option disabled selected>--choose--</option>
                    //     <option value="../manageAccount.php?forward=updateProfile">Update Profile</option>
                    //     <option value="../manageAccount.php?forward=changePassword">Change Password</option>
                    //     <option value="delete">Delete Account</option>
                    // </select></div>';
                    // echo '<div><a href="../sign_out.php?isAdmin=1"><i class="bi bi-box-arrow-right"></i> </a></div></div>';
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
    <div class="overlay" onclick="closePopup()"></div>
    <div class="table-container">
    <?php
    if (isset ($_GET['client_id'])) {  //view accounts --> specific account info
        $shipments = $dbHelper->getShipmentInfoByClientId($_GET['client_id']);
        if (is_array($shipments) & $shipments != null) {
            echo "<h3>Choose a shipment to display</h3>
            <table border=1>
            <tr><th>Shipment ID</th>
            <th>Shipment Info</th>
            </tr>";
            foreach ($shipments as $value) {
                echo "<tr><td>" . $value->getShipmentInfoId() . "</td>
                <td><button class='i-button' onclick='loadData(" . $value->getShipmentInfoId() . ")'>More Info</button></td></tr>";
            }
            // echo "</table><button class='search-button'><a href='viewAccounts.php'>Back</a></button>";
            echo "</table><a href='viewAccounts.php' class='back-button'><button>Back</button></a>";
        }
    }
    ?>
    </div>
    <div id="popup" class="popup"> </div>

    <div class="popup-overlay" id="popup-overlay" style="display:none;">
            <div class="popup-content" id="popup-content">
                <h2>Are you sure you want to delete this account?</h2>
                <button class='add-button' onclick="close3()">Yes</button>
                <script>
                    function close3(){
                        document.getElementById('popup-overlay').style.display = 'none';
                    window.location.href = '../manageAccount.php?forward=deleteAccount';
                    }
                    </script>
                <button class='add-button' onclick="document.getElementById('popup-overlay').style.display = 'none';">No</button>
            </div>
        </div>


    <script>
        function loadData(shipment_id) {
            var url = 'showShipment_client.php?shipment_id=' + encodeURIComponent(shipment_id);
            // Set the content of the popup iframe to the constructed URL
            document.getElementById('popup').innerHTML = '<iframe src="' + url + '" width="100%" height="100%" frameborder="0"></iframe>';
            /*An <iframe> (short for "inline frame") is an HTML element that allows you to embed another HTML document
             within the current document. It's like a window or frame that contains a separate web page, 
             displayed within the current web page*/
            document.getElementById('popup').style.display = 'block'; // Display the popup
            document.querySelector('.overlay').style.display = 'block';
        }
        function closePopup() {
            document.getElementById('popup').innerHTML = '';
            document.querySelector('.popup').style.display = 'none';
            document.querySelector('.overlay').style.display = 'none';


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



        }
    </script>
<script>
function goToPage2(src) {
    if (src == "delete") {
        document.getElementById('popup-overlay').style.display = 'block';
        // openPopUp();
    } else {
        window.location.href = src;
    }
}
    </script>
    <footer>
        <!-- common footer -->
    </footer>
</body>

</html>