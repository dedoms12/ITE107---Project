<?php
session_start();
if (isset($_SESSION['SESSION_EMAIL'])) {
  // Set a timestamp for the session start
  if (!isset($_SESSION['SESSION_START_TIME'])) {
    $_SESSION['SESSION_START_TIME'] = time();
  } else {
    // Check if the session has exceeded the timeout
    $sessionTimeout = 9999; // 15 seconds
    if (time() - $_SESSION['SESSION_START_TIME'] > $sessionTimeout) {
      // Session has expired, destroy it
      session_unset();
      session_destroy();
      header("Location: session-timeout-alert.php");
      die();
    }
  }
} else {
  $msg = "<div class='alert alert-danger'>You are not logged in. Please log in.</div>";
  header("Location: index.php");
  die();
}

include 'config.php';

$query = mysqli_query($conn, "SELECT * FROM users WHERE email='{$_SESSION['SESSION_EMAIL']}'");

if (mysqli_num_rows($query) > 0) {
  $row = mysqli_fetch_assoc($query);

  echo "<div class='nav-bar'>";
  echo "<img src='images/game-no-bg.png' width='100px' style='margin-right: 1200px;'>"; // Adjust margin as needed
  echo "<div class='user-info'>";
  echo "<span>" . $row['name'] . "</span> <a href='logout.php'>Logout</a>";
  echo "</div>";
  echo "</div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['creditCardNumber'])) {
  // Assuming you have validated and sanitized the credit card number
  $creditCardNumber = $_POST['creditCardNumber'];

  // Hash the credit card number
  $hashedCreditCardNumber = password_hash($creditCardNumber, PASSWORD_DEFAULT);

  // Store the hashed credit card number in the user's record in the database
  $email = $_SESSION['SESSION_EMAIL'];

  $updateQuery = "UPDATE users SET hashed_credit_card = ? WHERE email = ?";
  $stmt = mysqli_prepare($conn, $updateQuery);

  if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ss", $hashedCreditCardNumber, $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Video Game Homepage</title>
  <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
</head>

<body>

  <?php
  // Fetch registered users from the database
  $result = mysqli_query($conn, "SELECT * FROM games");

  if (mysqli_num_rows($result) > 0) {
    echo '<div class="games">';

    while ($row = mysqli_fetch_assoc($result)) {
      echo '<div class="game">
                <img src="' . $row['game_image'] . '" alt="' . $row['game_name'] . '" class="game__image">
                <div class="game__details">
                    <h3 class="game__title">' . $row['game_name'] . '</h3>
                    <p class="game__description">' . $row['game_description'] . '</p>
                    <p class="game__price">₱ ' . $row['game_price'] . '</p>
                    <button class="game__buy-button" onclick="showCreditCardModal(\'' . htmlspecialchars($row['game_name'], ENT_QUOTES) . '\', ' . $row['game_price'] . ')">Buy</button>
                </div>
            </div>';
    }
    echo '</div>';
  } else {
    echo '<p style="font-size: 80px; text-align: center; margin-top: 250px">DEVELOPING...</p>';
  }
  ?>
  <!-- Modal for credit card entry -->
  <div class="modal" id="creditCardModal">
    <div class="modal-header">Enter Credit Card Details</div>
    <div class="modal-body">
      <h3>Game: <span id="modalGameName"></span></h3>
      <h3>Price: ₱<span id="modalGamePrice"></span></h3>
      <label for="creditCardNumber">Credit Card Number:</label>
      <input type="text" id="creditCardNumber" name="creditCardNumber" placeholder="Enter credit card number">
      <button class="modal-button modal-confirm-button" onclick="processPayment()">Purchase</button>
      <button class="modal-button modal-cancel-button" onclick="closeCreditCardModal()">Cancel</button>
    </div>
  </div>

  <script src="script.js"></script>
</body>

</html>