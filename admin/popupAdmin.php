<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
</head>

<?php
include_once ("../include_all.php");
include_once ("../connection.php");


$page = "";
$msg = "";
$next = "";
if (isset($_GET['page'])) {
    $page .= $_GET['page'] . ".php";

    switch ($_GET['page']) {

        case "manageAdmins":
            $msg = "Are you certain you wish to delete this Admin?";
            $next = "manageAdmins.php?admin_id=" . $_GET['admin_id'];
            $next_no = "manageAdmins.php";
            break;

        case "viewAccounts":
            $msg = "Are you certain you wish to delete this Account?";
            $next = "viewAccounts.php?client_id=" . $_GET['client_id'];
            $next_no = "viewAccounts.php";
            break;

        case "manageCategories":
            $msg = "Are you certain you wish to delete this Category?";
            $next = "manageCategories.php?id=" . $_GET['id'];
            $next_no = "manageCategories.php";
            break;

        case "manageCountries":
            $msg = "Are you certain you wish to delete this Country?";
            $next = "manageCountries.php?name=" . $_GET['name'];
            $next_no = "manageCountries.php";
            break;

        case "manageProducts":
            $msg = "Are you certain you wish to delete this Product?";
            $next = "manageProducts.php?id=" . $_GET['id'];
            $next_no = "manageProducts.php";
            break;
        default:
            break;
    }
    echo '<script>';
    echo 'var next="' . $next . '";';
    echo 'var next_no="' . $next_no . '";';
    echo 'console.log(next);';
    echo '</script>';
    // echo $next;
    // echo $msg;
    // echo '<br>'.json_encode($next);

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automatic Popup</title>
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
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            /* Center-align content */
        }

        .popup-content button {
            margin-top: 20px;
            /* Add some space between the form and the button */
        }

        /* button{
            display: flex;
        }
         Styles for the popup 
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); 
            z-index: 999; 
        }
        .popup-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }  */
    </style>
</head>

<body>
    <?php //include_once($page); ?>
    <div class="popup-overlay" id="popup-overlay">
        <div class="popup-content" id="popup-content">
            <h2><?php echo $msg; ?></h2>
            <!-- <p>This is a popup message.</p> -->
            <!-- <button onclick="closePopup(<?php echo $next ?>)">OK</button> -->
            <button class='search-button' onclick="closePopup()">Delete</button>
            <button class='search-button' onclick="cancel()">Cancel</button>
        </div>
    </div>

    <script>
        // Function to close the popup
        function closePopup() {
            document.getElementById('popup-overlay').style.display = 'none';
            // window.location.href="sign_in.php";
            window.location.href = next;
        }

        function cancel() {
            document.getElementById('popup-overlay').style.display = 'none';
            window.location.href = next_no;
        }

        // Function to open the popup when the page loads
        window.onload = function () {
            document.getElementById('popup-overlay').style.display = 'block';
        };
    </script>

</body>

</html>