<link rel="stylesheet" href="admin/styles.css"><!-- added -->

<?php
include_once("include_all.php");
include_once("connection.php");

$page="";
$msg="";
$next="";
if(isset($_GET['page'])){
    $page.=$_GET['page'].".php";
    // if($_GET['page']=="resetPassword"){
    //     $msg="You have successfully updated your password!!!\nYou will be forwarder to the sign in page :)";
    // }
    switch($_GET['page']){

        case "resetPassword":
            $msg="You have successfully updated your password!!!\nYou will be forwarded to the sign in page :)";
            $next="sign_in.php";
            break;
        
        case "verify":
            if($_GET['error']=='0' || $_GET['error']=="0" || $_GET['error']==0 || $_GET['error']=="false"){
                $msg="Congratulations, you have successfully created a new account.\n you will be forwaded to the sign in page";
                $next="sign_in.php";
            }
            else{
                $msg=$_GET['errormsg'];
                $next="index.php";
            }
            break;

        case "viewOrders":
            $shipmentInfo=$dbHelper->getShipmentInfoByShipmentId($_GET['shipmentInfo_id']);
            $building=($shipmentInfo->getBuilding()!=null && 
            !empty($shipmentInfo->getBuilding()))?$shipmentInfo->getBuilding():"N/A";
            // $msg='<table><tr><td><strong>ID: </strong>'.$shipmentInfo->getShipmentInfoId()."</td><td><strong>Country:
            //  </strong>".$shipmentInfo->getCountryName().'</td><td><strong>Full Name</strong>'.
            //  $shipmentInfo->getFullName()."</td></tr>
            // <tr><td><strong>Street Number: </strong>".$shipmentInfo->getStreetNb().
            // "</td><td><strong>Building:</strong>".$building."</td><td><strong>City: </strong>".
            // $shipmentInfo->getCity()."</td></tr>
            // <tr><td><strong>State: </strong>".$shipmentInfo->getState() . 
            // "</td><td><strong>Zip Code: </strong>".$shipmentInfo->getZipCode().
            // "</td><td><strong>Phone Number: </strong>".$shipmentInfo->getPhoneNumber()."</td>
            // </tr></table>"; 

            $msg = '<div class="order-table-container">
            <table>
            <tr><th>Full Name</th><td>' . $shipmentInfo->getFullName() . '</td></tr>
            <tr><th>Country</th><td>' . $shipmentInfo->getCountryName() . '</td></tr>
            <tr><th>ID </th><td>' . $shipmentInfo->getShipmentInfoId() . "</td></tr>
           <tr><th>Street Number</th><td>" . $shipmentInfo->getStreetNb() . "</td></tr>
           <tr><th>Building</th><td>" . $building . "</td></tr>
           <tr><th>City</th><td>" . $shipmentInfo->getCity() . "</td></tr>
           <tr><th>State</th><td>" . $shipmentInfo->getState() . "</td></tr>
           <tr><th>Zip Code</th><td>" . $shipmentInfo->getZipCode() . "</td></tr>
           <tr><th>Phone Number</th><td>" . $shipmentInfo->getPhoneNumber() . "</td></tr>
           </table></div>"; 

            $next="viewOrders.php";
            break;

        case "updateProfile":
            $next="index.php";
            $msg="You have successfully Updated Your profile !!!";
            break;


        case "changePassword":
            $next="index.php";
            $msg="You have successfully updated your password";
            break;

        case "deleteAccount":
            $next="index.php";
            $msg="Account deleted successfully, we hope you had a good time with us.";
            break;

        case "saveAdmin":
            $next = "index.php";
            $msg = "<p style='color:green ; font-weight:bold'>You're now officially an Admin. Please log in to access the Admin Panel</p>";
            break;

        case "displayProduct":
            $next="index.php";
            $msg="<p style='color:green ; font-weight:bold'>Product Added To Cart</p>";
            break;

        case "index":
            $next="index.php";
            $msg="<p style='color:red ; font-weight:bold'>Unfortunately, this product is currently not available (out of stock)</p>";
            break;
            //break;

        // case "viewOrderDetails":
        //         //$next="viewOrders.php";
        //         if($_GET['isAdmin']==0){
        //             $next="viewOrders.php";
        //        }else $next = "admin/viewOrders.php";

        //         $orderItems=$dbHelper->getOrderItemsByOrderId($_GET['order_id']);
        //         // $msg="
        //         //     <table>
        //         //     <tr><th>Item</th><th>Quantity</th><th>Price(Per Unit)</th>
        //         //     <th>Total Price</th></tr>";
        //         $msg="<div class='order-table-container'>
        //         <table>
        //         <tr><th>Item</th>
        //         <th>Quantity</th>
        //         <th>Price(Per Unit)</th>
        //         <th>Total Price</th></tr>";
        //             $total=0;
        //         foreach($orderItems as $key=>$value){

        //             $msg.='<tr><td>'.($dbHelper->getProductById($value->getProductId()))->getName().'</td>
        //             <td>'.$value->getQuantity().'</td>
        //             <td>$'.number_format(($dbHelper->getProductById($value->getProductId()))->getPrice()*((100-($dbHelper->getProductById($value->getProductId()))->getDiscount())/100),2).'</td>
        //             <td>$'.number_format((($dbHelper->getProductById($value->getProductId()))->getPrice()*((100-($dbHelper->getProductById($value->getProductId()))->getDiscount())/100)*$value->getQuantity()),2).'</td>
        //             </tr>';
        //             // $total+=(($dbHelper->getProductById($value->getProductId()))->getPrice()*(100-($dbHelper->getProductById($value->getProductId()))->getDiscount()*$value->getQuantity());
        //             $total+=(($dbHelper->getProductById($value->getProductId()))->getPrice()*((100-($dbHelper->getProductById($value->getProductId()))->getDiscount())/100)*$value->getQuantity());
        //         }
        //         // $final=($dbHelper->getOrderById($_GET['order_id']))->getTotalAmount()-(($dbHelper->getCountryByName(($dbHelper->getShipmentInfoByShipmentId(($dbHelper->getOrderById($_GET['order_id']))->getSelectedShipmentInfoId()))->getCountryName()))->getDeliveryFees()+$total);
        //         //$final=(int)((($dbHelper->getCountryByName(($dbHelper->getShipmentInfoByShipmentId(($dbHelper->getOrderById($_GET['order_id']))->getSelectedShipmentInfoId()))->getCountryName()))->getDeliveryFees()+$total)-($dbHelper->getOrderById($_GET['order_id']))->getTotalAmount());
        //         $final=((($dbHelper->getCountryByName(($dbHelper->getShipmentInfoByShipmentId(($dbHelper->getOrderById($_GET['order_id']))->getSelectedShipmentInfoId()))->getCountryName()))->getDeliveryFees()+$total)-($dbHelper->getOrderById($_GET['order_id']))->getTotalAmount());
        //         //$final=ceil($final);
        //         if((int)$final<=0 || $final>($dbHelper->getOrderById($_GET['order_id']))->getTotalAmount()) $final=0;
        //         //else $final=$final/(-1);
        //         //if($final<0) $final=0;
        //         $msg.='<tr><td colspan="3"><b>Subtotal</b></td><td>$'.$total.'</td></tr>
        //         <tr><td colspan="3"><b>Delivery Fees</b></td>
        //         <td>$'.number_format(($dbHelper->getCountryByName(($dbHelper->getShipmentInfoByShipmentId(($dbHelper->getOrderById($_GET['order_id']))->getSelectedShipmentInfoId()))->getCountryName()))->getDeliveryFees(),2).
        //         '</td></tr>
        //         <tr><td colspan="3"><b>Points</b></td>
        //         <td>$'.$final/*(-1)*(($dbHelper->getOrderById($_GET['order_id']))->getTotalAmount()-(($dbHelper->getCountryByName(($dbHelper->getShipmentInfoByShipmentId(($dbHelper->getOrderById($_GET['order_id']))->getSelectedShipmentInfoId()))->getCountryName()))->getDeliveryFees()+$total))*/.
        //         '</td></tr>
        //         <tr><td colspan="3"><b>Total Price</b></td>
        //         <td>'.number_format(($dbHelper->getOrderById($_GET['order_id']))->getTotalAmount(),2).'</td>
        //         </tr></table></div>';
                
        //         break;
            
        default: break;
    }
    echo '<script>';
    echo 'var next="'.$next.'";';
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
    <link rel="stylesheet" href="style/accountStyle.css">
    <link rel="icon" type="image/x-icon" href="logos/primary_icon.jpeg" /> <!-- modified -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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

        /*.popup-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: var(--white);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            /* width:30%; 
            width: 60%;
            height: 50%;
            overflow: hidden;

            /* Center-align content 
        }*/

        .popup-content {
            /* display: none; */
            position: fixed;
            color: black;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            /* width: 60%;
            height: 50%; */
            max-width: 600px;
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            padding: 20px;
            z-index: 1000;
            overflow-y: auto;
            /* overflow: hidden; */
            /* display: none; */
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .popup-content button {
            margin-top: 20px;
            /* Add some space between the form and the button */
        }

        .popup-content h2{
            color:var(--navy-blue);
        }

        @media screen and (max-width: 768px) {
            .popup-content {
                width: 75%;
            }
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
<?php //include_once($page);?>
<div class="popup-overlay" id="popup-overlay">
    <div class="popup-content" id="popup-content">
        <h2><?php echo $msg;?></h2>
        <!-- <p>This is a popup message.</p> -->
        <!-- <button onclick="closePopup(<?php echo $next?>)">OK</button> -->
        <button class='add-button' onclick="closePopup()">OK</button>
    </div>
</div>

<script>
    // Function to close the popup
    function closePopup() {
        document.getElementById('popup-overlay').style.display = 'none';
       // window.location.href="sign_in.php";
       window.location.href=next;
    }

    // Function to open the popup when the page loads
    window.onload = function() {
        document.getElementById('popup-overlay').style.display = 'block';
    };
</script>

</body>
</html>
