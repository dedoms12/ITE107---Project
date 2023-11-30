function showCreditCardModal(gameName, gamePrice) {
    // Show the credit card entry modal
    document.getElementById('creditCardModal').style.display = 'block';

    document.getElementById('modalGameName').textContent = gameName;
    document.getElementById('modalGamePrice').textContent = gamePrice.toFixed(2);
}

function closeCreditCardModal() {
    // Close the credit card entry modal
    document.getElementById('creditCardModal').style.display = 'none';
}

function processPayment() {
    // Get the credit card number
    var creditCardNumber = document.getElementById('creditCardNumber').value;

    // Add a confirmation prompt
    var confirmation = confirm('Are you sure you want to make the purchase?');

    if (confirmation) {
        alert('Payment successful!');
        closeCreditCardModal(); // Close the modal after successful payment
    } else {
        // If the user cancels, you can add additional logic if needed
        alert('Payment canceled.');
    }
}

$(document).ready(function(c) {
    $('.alert-close').on('click', function(c) {
        $('.main-mockup').fadeOut('slow', function(c) {
            $('.main-mockup').remove();
        });
    });
});


function openTab(tabName) {
    var i, tabContent, tabLinks;
    tabContent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabContent.length; i++) {
        tabContent[i].classList.remove("active");
    }
    document.getElementById(tabName).classList.add("active");
}