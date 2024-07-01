<?php
include_once ("include_all.php");
include_once ("connection.php");

$page = "";
$msg = "";
$next = "";
if (isset($_GET['page'])) {
    $page .= $_GET['page'] . ".php";
    if ($_GET['page'] == "viewOrderDetails") {
        if ($_GET['isAdmin'] == 0) {
            $next = "viewOrders.php";
        } else
            $next = "admin/viewOrders.php";

            $msg=$dbHelper->getInvoiceByOrderId($_GET['order_id']);

    //     $orderItems = $dbHelper->getOrderItemsByOrderId($_GET['order_id']);
    //     if($orderItems==null){
    //         $msg="<table><tr><th>No available details for this order, this is because some of the orderitems in this order are deleted from the database by admins  </th></tr></table>";
    //     }
    //     else{
    //     $msg = "<h2>Order Details</h2>
    //    <div class='order-table-container'>
    //        <table>
    //        <thead>
    //        <tr><th>Item</th>
    //        <th>Quantity</th>
    //        <th>Price(Per Unit)</th>
    //        <th>Total Price</th></tr></thead><tbody>";
    //     $total = 0;
    //     foreach ($orderItems as $key => $value) {

    //         $msg .= '<tr><td label="Item">' . ($dbHelper->getProductById($value->getProductId()))->getName() . '</td>
    //        <td label="Quantity">' . $value->getQuantity() . '</td>
    //        <td label="Price (Per Unit)">$' . number_format(($dbHelper->getProductById($value->getProductId()))->getPrice() * ((100 - ($dbHelper->getProductById($value->getProductId()))->getDiscount()) / 100), 2) . '</td>
    //        <td label="Total Price">$' . number_format((($dbHelper->getProductById($value->getProductId()))->getPrice() * ((100 - ($dbHelper->getProductById($value->getProductId()))->getDiscount()) / 100) * $value->getQuantity()), 2) . '</td>
    //        </tr>';
    //         // $total+=(($dbHelper->getProductById($value->getProductId()))->getPrice()*(100-($dbHelper->getProductById($value->getProductId()))->getDiscount()*$value->getQuantity());
    //         $total += (($dbHelper->getProductById($value->getProductId()))->getPrice() * ((100 - ($dbHelper->getProductById($value->getProductId()))->getDiscount()) / 100) * $value->getQuantity());
    //     }
    //     // $final=($dbHelper->getOrderById($_GET['order_id']))->getTotalAmount()-(($dbHelper->getCountryByName(($dbHelper->getShipmentInfoByShipmentId(($dbHelper->getOrderById($_GET['order_id']))->getSelectedShipmentInfoId()))->getCountryName()))->getDeliveryFees()+$total);
    //     // $final = (int) ((($dbHelper->getCountryByName(($dbHelper->getShipmentInfoByShipmentId(($dbHelper->getOrderById($_GET['order_id']))->getSelectedShipmentInfoId()))->getCountryName()))->getDeliveryFees() + $total) - ($dbHelper->getOrderById($_GET['order_id']))->getTotalAmount());
    //     // if ($final <= 0 || $final > ($dbHelper->getOrderById($_GET['order_id']))->getTotalAmount())
    //     //     $final = 0;
    //     $final=((($dbHelper->getCountryByName(($dbHelper->getShipmentInfoByShipmentId(($dbHelper->getOrderById($_GET['order_id']))->getSelectedShipmentInfoId()))->getCountryName()))->getDeliveryFees()+$total)-($dbHelper->getOrderById($_GET['order_id']))->getTotalAmount());
    //     //$final=ceil($final);
    //     if((int)$final<=0 || $final>($dbHelper->getOrderById($_GET['order_id']))->getTotalAmount()) $final=0;
    //     //else $final=$final/(-1);
    //     //if($final<0) $final=0;
    //     $msg .= '<tr><td colspan="3"><b>Subtotal</b></td><td>$' . number_format($total, 2) . '</td></tr>
    //    <tr><td colspan="3"><b>Delivery Fees</b></td>
    //    <td>$' . number_format(($dbHelper->getCountryByName(($dbHelper->getShipmentInfoByShipmentId(($dbHelper->getOrderById($_GET['order_id']))->getSelectedShipmentInfoId()))->getCountryName()))->getDeliveryFees(), 2) .
    //         '</td></tr>
    //    <tr><td colspan="3"><b>Points</b></td>
    //    <td>$' . number_format($final,2)/*(-1)*(($dbHelper->getOrderById($_GET['order_id']))->getTotalAmount()-(($dbHelper->getCountryByName(($dbHelper->getShipmentInfoByShipmentId(($dbHelper->getOrderById($_GET['order_id']))->getSelectedShipmentInfoId()))->getCountryName()))->getDeliveryFees()+$total))*/ .
    //         '</td></tr>
    //    <tr><td colspan="3"><b>Total Price</b></td>
    //    <td><b>$' . number_format(($dbHelper->getOrderById($_GET['order_id']))->getTotalAmount(),2) . '</b></td>
    //    </tr></tbody></table></div>';
    // }
}
}

    echo '<script>';
    echo 'var next="'.$next.'";';
    echo 'console.log(next);';
    echo '</script>';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="admin/styles.css"> -->
    <link rel="stylesheet" href="style/orders_popup.css">
    <link rel="icon" type="image/x-icon" href="logos/primary_icon.jpeg" /> <!-- modified -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Order Details</title>

    <style>
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
            color: black;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 600px;
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            padding: 20px;
            z-index: 1000;
            overflow-y: auto;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .popup-content button {
            margin-top: 20px;
        }

        .popup-content h2 {
            color: var(--navy-blue);
        }

        @media screen and (max-width: 768px) {
            .popup-content {
                width: 75%;
                height: 90%;
            }
        }
    </style>
</head>

<body>
    <div class="popup-overlay" id="popup-overlay">
        <div class="popup-content" id="popup-content">
            <h2><?php echo $msg; ?></h2>
            <!-- <p>This is a popup message.</p> -->
            <!-- <button onclick="closePopup(<?php echo $next ?>)">OK</button> -->
            <button class='add-button' onclick="closePopup()">OK</button>
        </div>
    </div>

    <script>
        // Function to close the popup
        function closePopup() {
            document.getElementById('popup-overlay').style.display = 'none';
            // window.location.href="sign_in.php";
            window.location.href = next;
        }

        // Function to open the popup when the page loads
        window.onload = function () {
            document.getElementById('popup-overlay').style.display = 'block';
        };
    </script>
</body>

</html>