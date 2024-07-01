/////////////////////////////////////<CART>
// Get all items with class "item"
var items = document.querySelectorAll('.item');
var alltems = document.getElementById('allItems');
var checkedItems = document.getElementById('checkedItems');
// window.onload = calculateTotal();
//var total = document.getElementById('Total');
var subTotal = document.getElementById('subTotal');
function increment(index) {
    event.preventDefault();
    //var checkbox = document.getElementById('cb' + index);
    // var total = document.getElementById('Total');
    var quantity = document.getElementById('quantity' + index);
    var currentValue = parseInt(quantity.value);
    var totalitem = document.getElementById("totalitem" + index);
    var price = parseFloat(document.getElementById("price" + index).innerHTML);
    quantity.value = currentValue + 1;
    //alltems.innerHTML = parseInt(alltems.innerHTML) + 1;
    newtotalitem = quantity.value * price
    totalitem.innerHTML = newtotalitem.toFixed(2);
    // if (checkbox.checked) {
    this.calculateTotal();
    // }
    this.modified();
};
function decrement(index) {
    event.preventDefault();
    var quantity = document.getElementById('quantity' + index);
    var currentValue = parseInt(quantity.value);
    if (currentValue > 1) {
        // var checkbox = document.getElementById('cb' + index);
        var totalitem = document.getElementById("totalitem" + index);
        var price = parseFloat(document.getElementById("price" + index).innerHTML);
        quantity.value = currentValue - 1;
        //alltems.innerHTML = parseInt(alltems.innerHTML) - 1;
        newtotalitem = quantity.value * price
        totalitem.innerHTML = newtotalitem.toFixed(2);
        // if (checkbox.checked) {
        this.calculateTotal();
        // }
    }
    this.modified();
};
function remove(index) {
    event.preventDefault();
    document.getElementById(index).remove();
    var error = document.getElementById('error' + index);
    if (error) error.remove();
    items = document.querySelectorAll("div[name='item']");
    this.calculateTotal();
    this.modified();
}
function modified() {
    document.getElementById('update').hidden = false;
}
// function editTotal(index) {
//     var checkbox = document.getElementById('cb' + index);
//     var price = parseFloat(document.getElementById("price" + index).innerHTML);
//     var quantity = parseInt(document.getElementById('quantity' + index).value);
//     // var checkedItems = document.getElementById('checkedItems');
//     // var total = document.getElementById('Total');
//     // var subTotal = document.getElementById('subTotal');

//     if (checkbox.checked) {
//         checkedItems.innerHTML = parseInt(checkedItems.innerHTML) + 1;
//         var newTotal = parseFloat(total.innerHTML) + price * quantity;
//         var newSubTotal = parseFloat(subTotal.innerHTML) + price;
//         total.innerHTML = newTotal.toFixed(2) + ' $';
//         subTotal.innerHTML = newSubTotal.toFixed(2) + ' $';
//     }

//     if (!checkbox.checked) {
//         checkedItems.innerHTML = parseInt(checkedItems.innerHTML) - 1;
//         var newTotal = parseFloat(total.innerHTML) - price * quantity;
//         var newSubTotal = parseFloat(subTotal.innerHTML) - price;
//         total.innerHTML = newTotal.toFixed(2) + ' $';
//         subTotal.innerHTML = newSubTotal.toFixed(2) + ' $';
//     }
// }
document.addEventListener("DOMContentLoaded", calculateTotal);
// Get all items with class "item"
var items = document.querySelectorAll("div[name='item']");
// Add event listener to each checkbox
items.forEach(function (item) {
    checkbox = document.getElementById('cb' + item.id);
    checkbox.addEventListener('change', function () {
        calculateTotal();
        modified();
    });
    // checkbox.addEventListener('load', function () {
    //     calculateTotal();
    //     modified();
    // });
    itemQuantity = document.getElementById('quantity' + item.id);
    itemQuantity.addEventListener('change', function () {
        calculateTotal();
        modified();
    });
});

// Function to calculate total price
function calculateTotal() {
    var totalPrice = 0;
    var totalQte = 0;
    var totalCart = 0;
    // Iterate through each checked checkbox
    items.forEach(function (item) {
        var id = item.id;
        checkbox = document.getElementById('cb' + id);
        var quantity = parseInt(document.getElementById('quantity' + id).value);
        totalCart += quantity;
        if (checkbox.checked) {
            var checkbox = document.getElementById('cb' + id);
            var price = parseFloat(document.getElementById("price" + id).innerHTML);
            // Add the value of the checked checkbox to the total
            totalQte += quantity;
            totalPrice += price * quantity;
        }
    });

    // Display the total price in the total price field
    var nbItems = document.getElementsByName("nbItems");
    nbItems.forEach(function(nbItem){
        nbItem.innerHTML = totalCart;
    });
    alltems.innerHTML = totalCart;
    subTotal.innerHTML = totalPrice.toFixed(2) + ' $';
    checkedItems.innerHTML = totalQte;
    //modified();
}
/////////////////////////////////////</CART>
