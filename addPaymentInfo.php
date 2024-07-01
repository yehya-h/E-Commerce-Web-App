<?php
//include_once ('payment.php');
?>
<body>
    <div class="popup-overlay" id="popup-payment">
        <div class="popup-content" id="popup-content">
            <!-- <form action="payment.php" method="post"> -->
            <div class="form" style="display: block;">
                <h2>Add payment Information</h2>
                <table>
                    <tr>
                        <td><span>Card Number: </span></td>
                        <td><input type="text" name="cardNumber" title="Only digits (8 to 19)" pattern="\d{8,19}"></td>
                    </tr>
                    <tr>
                        <td><span>Name On Card: </span></td>
                        <td><input type="text" name="nameOnCard"></td>
                    </tr>
                    <tr>
                        <td><span>Expiry Date: </span></td>
                        <td><select name="expiryDate">
                                <br>
                                <?php
                                for ($i = 24; $i < 30; $i++) {

                                    $start = ($i == 24) ? 6 : 1;
                                    for ($j = $start; $j < 13; $j++) {

                                        $date = "";
                                        if ($j < 10)
                                            $date .= "0";
                                        $date .= $j . "/" . $i;

                                        echo '<option name="' . $date . '">' . $date . '</option>';
                                    }
                                }
                                ?>
                            </select></td>
                    </tr>
                    <tr>
                        <td><span>Security Code: </span></td>
                        <td><input type="text" name="securityCode" minlength="3" maxlength="4" pattern="\d{3,4}"></td>
                    </tr>
                </table>
                <br>
            </div>
            <input type="submit" value="Add" name="addPay">
            <?php if (!empty($errormsg_2))
                echo "<script>document.getElementById('popup-payment').style.display = 'block';</script>
                          <div><p class='error-message'>" . $errormsg_2 . "</p></div>"; ?>
            <!-- </form> -->
            <button id="cancelPayment">Cancel</button>
        </div>
    </div>
    <script type="text/javascript" src="payment.js"></script>
</body>