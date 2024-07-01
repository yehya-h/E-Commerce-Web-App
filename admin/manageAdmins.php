
<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include_once ($path . "/connection.php");
session_start();
include_once("../check_login.php");
if(!isset($_SESSION['admin']['searched'])) $_SESSION['admin']['searched'] = 0;
if($_SESSION['signed_in']==true){
if (!isset($_COOKIE['isOwner'])) {
    if (isset($_COOKIE['isAdmin']))
        header("location:admin.php");
    else
        header("location:../index.php");
}
else{
    //owner
}
}
else header("Location:../index.php");

$id_filter=-1;
$holder = "placeholder='search ...'";  //for the search bar
if (!isset($_SESSION['admin']['adminFilter']))
    $_SESSION['admin']['adminFilter'] = "firstName";
if (isset($_POST['submitFilter'])){
    $_SESSION['admin']['adminFilter'] = $_POST['toFilter'];
    $_SESSION['admin']['searched'] = 1;
}
if (!isset($_SESSION['admin']['adminSearch']))
    $_SESSION['admin']['adminSearch'] = "search ...";
if (isset($_POST['submitSearch'])) {
    $_SESSION['admin']['adminSearch'] = htmlspecialchars($_POST['toSearch']);
    $holder = "value=" . htmlspecialchars($_POST['toSearch']);
}
if (!isset($_SESSION['admin']['current']))
    $_SESSION['admin']['current'] = 1;      //for the pagination 
if (isset($_GET['current']))
    $_SESSION['admin']['current'] = $_GET['current'];
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
    <title>Manage Admins</title>
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
    if (isset($_GET['admin_id'])) {
        $admin_id = $_GET['admin_id'];
       // $dbHelper->removeAdmin($admin_id);
       foreach($dbHelper->getAdminByAdminID($admin_id) as $key=>$value){
      if( $dbHelper->deleteAccount($value->getAccountId()))
        header("location:manageAdmins.php");
    else echo "FAILED TO DELETE ACCOUNT";
       }
    }
    ?>

    <div class="bar-container">
        <!-- <h3>Admins Administration</h3> -->
        <!-- <button class="i-button"><a href="addAdmin.php">Add Admin</a></button> -->
        <a href="addAdmin.php"><button class="i-button">Add Admin</button></a>
        <div class="search-bar">
            <form action="manageAdmins.php" method="POST">
                <div class="filter-wrapper">
                    <select name="toFilter" class="filter-select">
                        <?php
                        $selectOptions = array(  "firstName", "account_id", "admin_id", "lastName", "email"); //fixed search options
                        foreach ($selectOptions as $value) {
                            $selected = ($_SESSION['admin']['adminFilter'] == $value) ? 'selected' : '';
                            echo "<option name='" . $value . "' value='" . $value . "' " . $selected . ">" . $value;
                        }
                        ?>
                    </select>
                    <input type="submit" name="submitFilter" value="Filter" class="search-button">
                </div>
            </form>
            <?php
            if (isset($_POST['submitFilter']) || isset($_POST['submitSearch']) || (isset($_GET['current']) && $_SESSION['admin']['searched']==1)) {
                if (isset($_POST['submitSearch']) && !empty($_POST['toSearch'])) {
                    $holder = "value='" . htmlspecialchars($_POST['toSearch']) . "'";
                } else
                    $holder = "placeholder='search ...'";
                echo "<form action='manageAdmins.php' method='POST'>
                    <input type='text' name='toSearch' " . $holder . "' class='search-input'>
                    <input type='submit' name='submitSearch' value='search' class='search-button'></form>";
            }
            echo "</div></div>";

            $admins = null;
            //get admins according to the SESSION containing the current to filter & search &order
            if (isset($_POST['submitFilter'])) {
                //echo "<br>478<br>";
                $admins = $dbHelper->getAllAdmins($_SESSION['admin']['adminFilter']);
                $id_filter=0;
            } else if (isset($_POST['submitSearch'])) {
                if ($_SESSION['admin']['adminFilter'] == "admin_id") {
                   // echo "<br>479 Admin id <br>admins : ";
                    $admins = $dbHelper->getAdminByAdminID($_SESSION['admin']['adminSearch']);
                  //  print_r($admins); echo "<br>";
                    $id_filter=1;
                } else if ($_SESSION['admin']['adminFilter'] == "account_id") {
                   // echo "<br>485 Acc id <br> admins: ";
                    $admins = $dbHelper->getAdminByAccountId($_SESSION['admin']['adminSearch']);
                   // print_r($admins); echo "<br>";
                    $id_filter=1;
                } else if ($_SESSION['admin']['adminFilter'] == "firstName") {
                    $id_filter=0;
                  //  echo "<br>489<br>";
                    $admins = $dbHelper->getAdminsByFirstName($_SESSION['admin']['adminSearch']);
                } else if ($_SESSION['admin']['adminFilter'] == "lastName") {
                    $id_filter=0;
                    //echo "<br>493<br>";
                    $admins = $dbHelper->getAdminsByLastName($_SESSION['admin']['adminSearch']);
                } else if ($_SESSION['admin']['adminFilter'] == "email") {
                    $id_filter=0;
                    //echo "<br>496<br>";
                    $admins = $dbHelper->getAdminsByEmail($_SESSION['admin']['adminSearch']);
                }
            } else {
                $id_filter=0;
                //echo "<br>500<br>";
                $admins = $dbHelper->getAllAdmins($_SESSION['admin']['adminFilter']);
            }

            if (is_array($admins))
                $count = count($admins);
            else
                $count = 0;

            echo " <div class='table-container'>";

            if (empty($admins)) {
            //     echo "<p style='color:red ; font-weight:bold'>No Admins</p>
            // <button class='search-button'><a href='owner.php'>Back</a></button>";

            echo "<p style='color:red ; font-weight:bold'>No Admins</p>
            <a href='owner.php' class='back-button'><button>Back</button></a>";
            } else {
                //echo "<div class='total-box'>total number of admins<br>" . count($admins) . "</div>";

                //pagination
                $perPage = 5;
                $totalPages = ceil($count / $perPage);
                $first = ($_SESSION['admin']['current'] * $perPage) - $perPage;

                    // if($_SESSION['admin']['adminFilter'] == "admin_id" ||
                    //  $_SESSION['admin']['adminFilter'] == "account_id" && $id_filter==1 
                    //  && isset($_POST['submitSearch']) && !empty($_POST['toSearch'])) {
                    if(!isset($_POST['submitFilter']) && ($_SESSION['admin']['adminFilter'] == "admin_id" ||
                      $_SESSION['admin']['adminFilter'] == "account_id") && $id_filter==1){

                        //echo "<br>535<br>";
                        //print_r($admins);
                        //echo "<br>";
                        echo "<table border=1>
                        <tr>
                          <th>Admin ID</th>
                          <th>Account ID</th>
                          <th>First Name</th>
                          <th>Last Name</th>
                          <th>Email</th>
                          <th>Address</th>
                          <th>Phone Number</th>
                          <th>Remove Admin</th>
                        </tr>";

                        foreach($admins as $key=>$value){
                            $phone = ($value->getPhoneNumber()!=null)? $value->getPhoneNumber() : 'N/A';
                            //$phone="N/A";
                            echo "<tr>
                            <td>" . $value->getAdminId() . "</td>
                            <td>" . $value->getAccountId() . "</td>     
                    <td>" . $value->getFirstName() . "</td>
                    <td>" . $value->getLastName() . "</td>
                    <td>" . $key . "</td>
                    <td>" . $value->getAddress() . "</td>
                    <td>" . $phone . "</td>
                    <td><a href='popupAdmin.php?admin_id=" . $value->getAdminId() . "&page=manageAdmins'><button class='i-button'>remove</button></a></td>
                    </tr>";

                        }
                        echo "</table>";
                    }

               else  if (is_array($admins) && $admins != null) {   //case where $admins is an array of elements (default or by search)
                    $temp = array_slice($admins, $first, $perPage);

                    echo "<table border=1>
                       <tr>
                         <th>Admin ID</th>
                         <th>Account ID</th>
                         <th>First Name</th>
                         <th>Last Name</th>
                         <th>Email</th>
                         <th>Address</th>
                         <th>Phone Number</th>
                         <th>Remove Admin</th>
                       </tr>";
                    foreach ($temp as $key => $value) {
                        if ($value != null) {
                            $phone = ($value['phone_number']!=null)? $value['phone_number'] : 'N/A';
                    //         echo "<tr>
                    //         <td>" . $value['admin_id'] . "</td>
                    //         <td>" . $value['account_id'] . "</td>     
                    // <td>" . $value['firstName'] . "</td>
                    // <td>" . $value['lastName'] . "</td>
                    // <td>" . $value['email'] . "</td>
                    // <td>" . $value['address'] . "</td>
                    // <td>" . $phone . "</td>
                    // <td><button class='i-button'><a href='popupAdmin.php?admin_id=" . $value['admin_id'] . "&page=manageAdmins'>remove</a></button></td>
                    // </tr>";

                    echo "<tr>
                    <td>" . $value['admin_id'] . "</td>
                    <td>" . $value['account_id'] . "</td>     
            <td>" . $value['firstName'] . "</td>
            <td>" . $value['lastName'] . "</td>
            <td>" . $value['email'] . "</td>
            <td>" . $value['address'] . "</td>
            <td>" . $phone . "</td>
            <td><a href='popupAdmin.php?admin_id=" . $value['admin_id'] . "&page=manageAdmins'><button class='i-button'>remove</button></a></td>
            </tr>";

                        }
                    }
                    echo "</table>";
                } else {
                //     echo "<p style='color:red ; font-weight:bold'><br>No Admins to display</p><br> 
                // <button class='search-button'><a href='owner.php'>Back</a></button>";

                echo "<p style='color:red ; font-weight:bold'><br>No Admins to display</p><br> 
                <a href='owner.php' class='back-button'><button>Back</button></a>";
                }
                //pagination
                if ($_SESSION['admin']['current'] > 1)
                    echo "<button class='page-button'><a href='manageAdmins.php?current=" . ($_SESSION['admin']['current'] - 1) . "'><</a></button>";
                echo " Page " . $_SESSION['admin']['current'] . "/" . $totalPages;
                if ($_SESSION['admin']['current'] < $totalPages)
                    echo " <button class='page-button'><a href='manageAdmins.php?current=" . ($_SESSION['admin']['current'] + 1) . "'>></a></button>";
                    echo "<br><br><a href='owner.php' class='back-button'><button>Back</button></a></div>";
                    //echo "<br><br><button class='search-button'><a href='owner.php'>Back</a></button>
        
            }
            ?>
            
            <footer>
                <!-- common footer -->
            </footer>
</body>

</html>