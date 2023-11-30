<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['creditCardNumber'])) {
    // Simulate processing the credit card number (for educational purposes only)
    $creditCardNumber = $_POST['creditCardNumber'];
    $hashedCreditCardNumber = password_hash($creditCardNumber, PASSWORD_DEFAULT);

    // Log the hashed credit card number (in a real scenario, you wouldn't log it)
    file_put_contents('hashed_credit_cards.log', $hashedCreditCardNumber . PHP_EOL, FILE_APPEND);
}
