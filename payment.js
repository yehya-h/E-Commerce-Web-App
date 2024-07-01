
/////////////////////////////////////<PAYMENT>
/////////////////<ADDSHIPMENTINFO>
// Function to close the popup
function closePopup(id) {
    document.getElementById(id).style.display = 'none';
    //window.location.href = "payment.php";
}

// Function to open the popup when the page loads
/* window.onload = */ function displayForm(id) {
    document.getElementById(id).style.display = 'block';
}
var cancelShipForm = document.getElementById("cancelShipment");
if (cancelShipForm != null)
    cancelShipForm.addEventListener("click", function () {
        event.preventDefault();
        closePopup('popup-shipment');
    });

var cancelpayForm = document.getElementById("cancelPayment");
if (cancelpayForm != null)
    cancelpayForm.addEventListener("click", function () {
        event.preventDefault();
        closePopup('popup-payment');
    });
/////////////////</ADDSHIPMENTINFO>
var shipForm = document.getElementById("shipForm");
if (shipForm != null)
    shipForm.addEventListener("click", function () {
        event.preventDefault();
        displayForm('popup-shipment');
    });
var payForm = document.getElementById("payForm");
if (payForm != null)
    payForm.addEventListener("click", function () {
        event.preventDefault();
        displayForm('popup-payment');
    });

var credit = document.getElementById("credit");
var cash = document.getElementById("cash");
var allPayments = document.getElementById('allPayments');

if (credit != null)
    credit.addEventListener("change", function () {
        if (this.checked)
            allPayments.style.display = 'block';
    });

if (cash != null)
    cash.addEventListener("change", function () {
        if (this.checked)
            allPayments.style.display = 'none';
    });

var order = document.getElementById('order');
if (order != null)
    order.addEventListener("click", function () {
        if (credit.checked) {
            event.preventDefault();
            alert("Credit card payment is not available right now. Please try again later or choose an alternative payment method.");
            cash.checked = true;
            cash.dispatchEvent(new Event("change"));
        }
    });

var points = document.getElementById('points');
var deliveryElement = document.getElementById("deliveryFee");
var subTotalElement = document.getElementById("subTotal");

// Get the element where the selected value will be displayed
var selectedValueSpan = document.getElementById('selectedpts');
var coupon = document.getElementById('coupon');
// Update the displayed value when the range input value changes
points.addEventListener('input', function () {
    selectedValueSpan.textContent = points.value;
    coupon.textContent = parseFloat(points.value) / -10 + " $";
    updateTolal();
});
//console.log(clientPoints);
//points.max = 50;
//console.log(parseFloat(document.getElementById("subTotal").innerHTML) *10);
function checkMaxPoints() {
    var delivery = parseFloat(deliveryElement.innerText.replace(/[^\d.]/g, '')); // Remove non-numeric characters
    var subTotal = parseFloat(subTotalElement.innerText.replace(/[^\d.]/g, '')); // Remove non-numeric characters

    var totalwithdelivery = isNaN(delivery) ? subTotal : subTotal + delivery;

    var x = Math.floor(totalwithdelivery * 10); // Use Math.floor to convert to integer
    var y = parseInt(clientPoints); // Ensure clientPoints is numeric

    // console.log("Total with delivery:", totalwithdelivery);
    // console.log("X:", x);
    // console.log("Y:", y);

    
    // Set max points based on condition
    points.max = (x < y) ? x : y;

    selectedValueSpan.textContent = points.value;
    coupon.textContent = parseFloat(points.value) / -10 + " $";
    updateTolal();
}
function updateTolal(){
    var deliveryfee = isNaN(parseFloat(deliveryElement.innerText)) ? 0 : parseFloat(deliveryElement.innerText);
    var couponSale = isNaN(parseFloat(coupon.innerText)) ? 0 : parseFloat(coupon.innerText);
    var total = parseFloat(subTotalElement.innerHTML) + parseFloat(deliveryfee) + parseFloat(couponSale);
    document.getElementById("total").innerHTML = parseFloat(total).toFixed(1) + " $";
}
// Retrieve country and delivery fees data from PHP
//  var countries = <?php echo json_encode($countries_js); ?>;
// console.log(countries);
//Function to update delivery fee in order summary
function updateDeliveryFee() {
    var selectedShipment = document.querySelector("input[name='ship']:checked");
    if (selectedShipment == null) return null;
    var div = selectedShipment.parentElement.querySelector("div");
    var selectedCountry = div.querySelector("div li[name='country']").id;
    var deliveryFee = countries[selectedCountry];
    var deliveryFee = parseFloat(countries[selectedCountry]).toFixed(2);
    deliveryElement.innerHTML = deliveryFee + " $";
    // var total = parseFloat(subTotalElement.innerHTML) + parseFloat(deliveryFee);
    // document.getElementById("total").innerHTML = total + " $";
    checkMaxPoints();
}

// Add event listeners to radio buttons
var radioButtons = document.querySelectorAll("input[name='ship']");
radioButtons.forEach(function (radioButton) {

    radioButton.addEventListener("change", function () {
        updateDeliveryFee();
    });
});

// Initial update of delivery fee
document.addEventListener("DOMContentLoaded", function () {
    updateDeliveryFee();
    checkMaxPoints();
});
/////////////////////////////////////<PAYMENT>