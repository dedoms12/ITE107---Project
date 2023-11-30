<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Security headers
header("Strict-Transport-Security: max-age=63072000; includeSubDomains; preload"); // Added security header
header("X-Content-Type-Options: nosniff"); // Added security header
header("X-Frame-Options: DENY"); // Added security header
header("X-XSS-Protection: 1; mode=block"); // Added security header
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://kit.fontawesome.com; style-src 'self' https://fonts.googleapis.com 'unsafe-inline'; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:;"); // Added Content Security Policy (CSP)

session_start();
if (isset($_SESSION['SESSION_EMAIL'])) {
    header("Location: welcome.php");
    die();
}

// Load Composer's autoloader
require 'vendor/autoload.php';

include 'config.php';
$msg = "";

if (isset($_POST['submit'])) {
    // Input validation and output encoding
    $name = mysqli_real_escape_string($conn, htmlspecialchars(trim($_POST['name']))); // Added input validation and output encoding
    $email = mysqli_real_escape_string($conn, htmlspecialchars(trim($_POST['email']))); // Added input validation and output encoding
    $password = mysqli_real_escape_string($conn, md5($_POST['password']));
    $confirm_password = mysqli_real_escape_string($conn, md5($_POST['confirm-password']));
    $code = mysqli_real_escape_string($conn, md5(rand()));
    $role = mysqli_real_escape_string($conn, $_POST['role']); // Added role handling


    // Basic email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "<div class='alert alert-danger'>Invalid email address.</div>";
    } elseif (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE email='{$email}'")) > 0) {
        $msg = "<div class='alert alert-danger'>{$email} - This email address already exists.</div>";
    } else {
        // Password length validation
        if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $_POST['password'])) {
            $msg = "<div class='alert alert-danger' style='font-size: 17px;'>
                Password should be at least 8 characters long and include:
                    <ul style='margin-left: 17px;'>
                        <li>One lowercase letter (a-z)</li>
                        <li>One uppercase letter (A-Z)</li>
                        <li>One digit (0-9)</li>
                        <li>One special character (@$!%*?&)</li>
                    </ul>
                </div>";
        } else {
            // Password match validation
            if ($password === $confirm_password) {
                $sql = "INSERT INTO users (name, email, password, code, role) VALUES ('{$name}', '{$email}', '{$password}', '{$code}', '{$role}')";
                $result = mysqli_query($conn, $sql);
                if ($result) {
                    echo "<div style='display: none;'>";
                    //Create an instance; passing `true` enables exceptions
                    $mail = new PHPMailer(true);

                    try {
                        //Server settings
                        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                        $mail->isSMTP();                                            //Send using SMTP
                        $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
                        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                        $mail->Username   = 'videogamestore.2023@gmail.com';                      //SMTP username
                        $mail->Password   = 'boosybhaqaxsosce';                      //SMTP password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                        //Recipients
                        $mail->setFrom('randed22@gmail.com');
                        $mail->addAddress($email);

                        //Content
                        $mail->isHTML(true);                                  //Set email format to HTML
                        $mail->Subject = 'no reply';
                        $mail->Body    = 'Here is the verification link <b><a href="http://localhost/milestone-107/?verification=' . $code . '">http://localhost/milestone/?verification=' . $code . '</a></b>';

                        $mail->send();
                        echo 'Message has been sent';

                        // Redirect to admin verification page if the role is "admin"
                        if ($role === 'admin') {
                            header("Location: admin_verification.php?email=" . urlencode($email));
                            exit();
                        }
                    } catch (Exception $e) {
                        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }
                    echo "</div>";
                    $msg = "<div class='alert alert-info'>Verification link has been sent to your email address.</div>";
                } else {
                    $msg = "<div class='alert alert-danger'>Something went wrong.</div>";
                }
            } else {
                $msg = "<div class='alert alert-danger'>Password and Confirm Password do not match.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Register</title>
    <!-- Meta tag Keywords -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords" content="Login Form" />
    <!-- //Meta tag Keywords -->
    <style>
        body{
            background-image: url('images/backg.png');
            background-size: cover;
            background-position: center;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100%;
        }
    </style>

    <!--/Style-CSS -->
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />

    <script src="https://kit.fontawesome.com/af562a2a63.js" crossorigin="anonymous"></script>



</head>

<body>

    <!-- form section start -->
    <section class="w3l-mockup-form">
        <div class="container">
            <!-- /form -->
            <div class="workinghny-form-grid">
                <div class="main-mockup">
                    <div class="w3l_form align-self" style="background: rgba(30, 0, 110, 1)">
                        <div class="left_grid_info">
                            <img src="images/glogo.png" alt="" width="310px">
                        </div>
                    </div>
                    <div class="content-wthree">
                        <h2>Register</h2>
                        <?php echo $msg; ?>
                        <form action="" method="post">
                            <input type="text" class="name" name="name" style="background:#fff;" placeholder="Enter Your Name" value="<?php if (isset($_POST['submit'])) {
                                                                                                                    echo $name;
                                                                                                                } ?>" required>
                            <input type="email" class="email" name="email" style="background:#fff;" placeholder="Enter Your Email" value="<?php if (isset($_POST['submit'])) {
                                                                                                                        echo $email;
                                                                                                                    } ?>" required>
                            <input type="password" class="password" name="password" style="background:#fff;" placeholder="Enter Your Password" required>
                            <input type="password" class="confirm-password" name="confirm-password" style="background:#fff;" placeholder="Enter Your Confirm Password" required>

                            <p><label for="role">Select Role:</label></p>
                            <select name="role" id="role" class="custom-select" style="margin-top: 10px;" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>

                            <button name="submit" class="btn" type="submit" style="margin-top: 16px;">Register</button>
                        </form>
                        <div class="social-icons">
                            <p>Have an account? <a href="index.php">Login</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- //form -->
        </div>
    </section>

    <script src="js/jquery.min.js"></script>
    <script src="script.js"></script>

</body>

</html>