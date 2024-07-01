

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
function closePopUp() {
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
