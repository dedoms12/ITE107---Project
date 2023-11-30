<?php
include 'config.php';

$email = isset($_GET['email']) ? urldecode($_GET['email']) : '';
$adminKey = isset($_POST['admin_key']) ? $_POST['admin_key'] : '';
$verificationMessage = '';

if (empty($email)) {
    // Handle invalid or missing email parameter
    echo "Invalid request.";
    exit();
}

// Check if the email exists in the database with the admin role
$result = mysqli_query($conn, "SELECT * FROM users WHERE email='{$email}' AND role='admin'");

if (mysqli_num_rows($result) > 0) {
    // Admin email found in the database
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verify admin key
        $correctAdminKey = 'admin_123'; // Replace with your actual admin key

        if ($adminKey === $correctAdminKey) {
            $verificationMessage = "Verification code has been sent to your email address.";
        } else {
            $verificationMessage = "Incorrect admin key.";
        }
    }
} else {
    // Admin email not found in the database with the admin role
    echo "Invalid request.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Admin Verification</title>
    <!-- Meta tag Keywords -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <!-- //Meta tag Keywords -->

    <!--/Style-CSS -->
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
</head>

<body>

    <section class="w3l-mockup-form">
        <div class="container">
            <div class="workinghny-form-grid">
                <div class="main-mockup">
                    <div class="content-wthree" style="margin-left: 200px;">
                        <h2>Admin Verification</h2>
                        <?php if (!empty($verificationMessage)) : ?>
                            <div class="alert alert-info" style="margin-bottom: -20px;"><?php echo $verificationMessage; ?></div>
                        <?php endif; ?>
                        <form action="" method="post">
                            <p style="padding-bottom: 10px;">Please enter the admin key to verify your admin status:</p>
                            <input type="password" name="admin_key" placeholder="Enter Admin Key" required>
                            <button type="submit" class="btn">Verify</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>

</html>