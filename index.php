<?php
    session_start();
    if (isset($_SESSION['SESSION_EMAIL'])) {
        header("Location: welcome.php");
        die();
    }

    include 'config.php';
    $msg = "";

    if (isset($_GET['verification'])) {
        if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE code='{$_GET['verification']}'")) > 0) {
            $query = mysqli_query($conn, "UPDATE users SET code='' WHERE code='{$_GET['verification']}'");
            
            if ($query) {
                $msg = "<div class='alert alert-success'>Account verification has been successfully completed. Please enter your email and password once again.</div>";
            }
        } else {
            header("Location: index.php");
        }
    }

    if (isset($_POST['submit'])) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, md5($_POST['password']));

        $sql = "SELECT * FROM users WHERE email='{$email}' AND password='{$password}'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);

            if (empty($row['code'])) {
                // Validate reCAPTCHA
                $recaptchaResponse = $_POST['g-recaptcha-response'];
                $recaptchaSecretKey = '6LdVIc4oAAAAAFRImqezIKWk_SvYjsOkXt0zhP86'; 

                // Verify reCAPTCHA
                $recaptchaVerify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecretKey&response=$recaptchaResponse");
                $recaptchaData = json_decode($recaptchaVerify);

                if (!$recaptchaData->success) {
                    $msg = "<div class='alert alert-danger'>Please verify that you are not a robot.</div>";
                } else {
                    // reCAPTCHA validation passed
                    $_SESSION['SESSION_EMAIL'] = $email;
                    // Redirect to admin page if the role is "admin"
                    if ($row['role'] === 'admin') {
                        header("Location: admin_page.php");
                        exit();
                    } else {
                        header("Location: welcome.php");
                        exit();
                    }
                    header("Location: welcome.php");
                    }
            } else {
                $msg = "<div class='alert alert-info'>First verify your account and try again.</div>";
            }
        } else {
            $msg = "<div class='alert alert-danger'>Email or password do not match.</div>";
        }
    }
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Login</title>
    <!-- Meta tag Keywords -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords"
        content="Login Form" />
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

    <link href="//fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!--/Style-CSS -->
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
    <!--//Style-CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/af562a2a63.js" crossorigin="anonymous"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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
                            <img src="images/glogo.png" alt="" width="300px">
                        </div>
                    </div>
                    <div class="content-wthree">
                        <h2>Login</h2>
                        <?php echo $msg; ?>
                        <form action="" method="post">
                            <input type="email" class="email" name="email" placeholder="Enter Your Email" style="background: #fff;" required>
                            <input type="password" class="password" name="password" placeholder="Enter Your Password" style="background: #fff; margin-bottom: 2px;" required>
                            <div class="social-icons">
                            <p class="pt-1"><a href="forgot-password.php" style="margin-bottom: 15px; display: block; text-align: right;">Forgot Password?</a></p>
                            </div>
                            <div class="form-group" style="margin-top: -30px;">
                                <div class="g-recaptcha" data-sitekey="6LdVIc4oAAAAAGnHXfdAGUf7cu_JVi-eO3iiJc7X"></div>
                            </div>
                            <button name="submit" class="btn" type="submit">Login</button>
                        </form>
                        <div class="social-icons">
                            <p>Create Account! <a href="register.php">Register</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- //form -->
        </div>
    </section>
    <!-- //form section start -->

    <script src="js/jquery.min.js"></script>
    <script>
        $(document).ready(function (c) {
            $('.alert-close').on('click', function (c) {
                $('.main-mockup').fadeOut('slow', function (c) {
                    $('.main-mockup').remove();
                });
            });
        });
    </script>
</body>
</html>
