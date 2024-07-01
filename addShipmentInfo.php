<?php
//include_once ('payment.php');
?>

<body>
    <div class="popup-overlay" id="popup-shipment">
        <div class="popup-content" id="popup-content">
            <!-- <form action="payment.php" method="post"> -->
            <div class="form" style="display: block;">
                <h2>Add shipment Information</h2>
                <table>
                    <tr>
                        <td><span>Country: </span></td>
                        <td><select name="country">
                                <?php
                                $countries = $dbHelper->getAllCountries("country_name");
                                foreach ($countries as $country) {
                                    $countries_js[$country->getCountryName()] = $country->getDeliveryFees();
                                    echo '<option>' . $country->getCountryName() . '</option>';
                                }
                                ?>
                            </select></td>
                    </tr>
                    <tr>
                        <td><span>Full Name: </span></td>
                        <td><input type="text" name="fullName"></td>
                    </tr>
                    <tr>
                        <td><span>Street Number: </span></td>
                        <td><input type="text" name="street_nb"></td>
                    </tr>
                    <tr>
                        <td><span>Building (Optional): </span></td>
                        <td><input type="text" name="building" placeholder="Optional Field"><br>
                    <tr>
                        <td><span>City: </span></td>
                        <td><input type="text" name="city"></td>
                    </tr>
                    <tr>
                        <td><span>State: </span></td>
                        <td><input type="text" name="state"></td>
                    </tr>
                    <tr>
                        <td><span>Zip Code: </span></td>
                        <td><input type="text" name="zipCode" placeholder="Examples: 1300, 13001, 1333-5555"
                                title="XXXX or XXXXX or XXXX-XXXX or XXXXX-XXXX" pattern="\d{4,5}(-\d{4})?"></td>
                    </tr>
                    <tr>
                        <td><span>Phone Number: </span></td>
                        <td><input type="text" name="phoneNumber" placeholder="Ex: +961-81888888"
                                title="+XXX-XXXXXXX..." pattern="\+\d{3}-\d{8,20}"></td>
                    </tr>
                </table>
                <br>
            </div>
            <input type="submit" value="Add" name="addShip">
            <?php if (!empty($errormsg_1))
                echo "<script>document.getElementById('popup-shipment').style.display = 'block';</script>
                          <div><p class='error-message'>" . $errormsg_1 . "</p></div>"; ?>
            <!-- </form> -->
            <button id="cancelShipment">Cancel</button>
        </div>
    </div>
    <script>var countries = <?php echo json_encode($countries_js); ?></script>
    <script type="text/javascript" src="payment.js"></script>
</body>