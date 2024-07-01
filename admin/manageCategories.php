<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include_once ($path."/connection.php");

session_start();
include_once("../check_login.php");
//$back = "<button class='search-button'><a href='admin.php'>Back</a></button>";
$back = "<a href='admin.php' class='back-button'><button>Back</button></a>";
if($_SESSION['signed_in']==true){
if(isset($_COOKIE['isOwner']) && $_COOKIE['isOwner'] == "true") $back = "<a href='owner.php' class='back-button'><button>Back</button></a>";
 //$back = "<button class='search-button'><a href='owner.php'>Back</a></button>";
if((!isset($_COOKIE['isOwner']) || $_COOKIE['isOwner'] == "false") && 
(!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] == "false")) header("location:../index.php");
}
else header("Location:../index.php");

$holder = "placeholder='search category...'";  //for the search bar
if (!isset ($_SESSION['admin']['categorySearch'])) $_SESSION['admin']['categorySearch'] = "search category...";
if (isset ($_POST['submit']) && !empty($_POST['toSearch'])) {
    $_SESSION['admin']['categorySearch'] = htmlspecialchars($_POST['toSearch']);
    $holder = "value=" . htmlspecialchars($_POST['toSearch']);
}
if (!isset ($_SESSION['admin']['current'])) $_SESSION['admin']['current'] = 1;      //for the pagination
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
    <title>Manage Categories</title>
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
                    echo '<div class="center-nav"><div><h1>Category Administration</h1></div></div>
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
                    echo '<div class="center-nav"><div><h1>Category Administration</h1></div></div>
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
        <!-- <h3>Category Administration</h3> -->
        <!-- <button class="i-button"><a href="addCategory.php">Add Category</a></button> -->
        <a href="addCategory.php"><button class="i-button">Add Category</button></a>
        <div class="search-bar">
            <form action="manageCategories.php" method="POST">
                <input type="text" name="toSearch" <?php echo $holder; ?> class='search-input'>
                <input type="submit" name="submit" value="search" class='search-button'>
            </form>
        </div>
    </div>
    <?php

    //delete category
    if (isset ($_GET['id'])) {
        $category = $dbHelper->getCategoryById($_GET['id']);
        $nb = $category->getNbProducts();
        if ($nb != 0) {
            echo "<p style='color:red ; font-weight:bold'>Can't delete category since there exist products from that category</p>";
        } else {
            $dbHelper->removeCategory($_GET['id']);
            header("location:manageCategories.php");
        }
    }

    //search category
    if (isset ($_POST['submit'])) {
        $categories = $dbHelper->getCategoriesByName($_SESSION['admin']['categorySearch']);
    } else {
        $categories = $dbHelper->getAllCategories("category_id");
    }
    if ($categories != null)
        $count = count($categories);
    else
        $count = 0;

    if (empty ($categories)) {
        echo "<div class='error-container'><p style='color:red ; font-weight:bold'>No categories</p>
        " . $back . " </div>";
    } else {
        //echo "<div class='total-box'>total number of categories<br>". count($categories) ."</div>";

        //pagination
        $perPage = 5;
        $first = ($_SESSION['admin']['current'] * $perPage) - $perPage;
        if (is_array($categories)) {
            $temp = array_slice($categories, $first, $perPage);
            $totalPages = ceil($count / $perPage);
            echo " <div class='table-container'>
        <table border=1>
                   <tr>
                     <th>ID</th>
                     <th>Category Name</th>
                     <th>Number of Products</th>
                     <th>Update</th>
                     <th>Delete</th>
                   </tr>";
            foreach ($temp as $key => $value) {
                if ($value != null) {
            //         echo "<tr>
            //         <td>" . $value->getCategoryId() . "</td>
            //         <td>" . $value->getName() . "</td>
            //         <td>" . $value->getNbProducts() . "</td>
            //         <td><button class='i-button'><a href='updateCategory.php?id=" . $value->getCategoryId() . "'>update</a></button></td>
            //         <td><button class='i-button'><a href='popupAdmin.php?id=" . $value->getCategoryId() . "&page=manageCategories'>delete</a></button></td>
            // </tr>";

            echo "<tr>
            <td>" . $value->getCategoryId() . "</td>
            <td>" . $value->getName() . "</td>
            <td>" . $value->getNbProducts() . "</td>
            <td><a href='updateCategory.php?id=" . $value->getCategoryId() . "'><button class='i-button'>update</button></a></td>
            <td><a href='popupAdmin.php?id=" . $value->getCategoryId() . "&page=manageCategories'><button class='i-button'>delete</button></a></td>
             </tr>";

                }
            }
            echo "</table>";
        }
        //pagination
        if ($_SESSION['admin']['current'] > 1)
            echo "<button class='page-button'><a href='manageCategories.php?current=" . ($_SESSION['admin']['current'] - 1) . "'><</a></button>";
        echo " Page " . $_SESSION['admin']['current'] . "/" . $totalPages;
        if ($_SESSION['admin']['current'] < $totalPages)
            echo " <button class='page-button'><a href='manageCategories.php?current=" . ($_SESSION['admin']['current'] + 1) . "'>></a></button>";
        echo "<br><br>" . $back ."
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
    <script type="text/javascript" src="script.js"></script>
</body>

</html>