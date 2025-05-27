<?php
session_start();
include("../include/db.php");

// Generate CAPTCHA if not set
if (!isset($_SESSION['captcha'])) {
    $_SESSION['captcha'] = sprintf("%04d", rand(0, 9999));
}

$error = "";
if (isset($_POST['reset_password'])) {
    $user_email = mysqli_real_escape_string($con, $_POST['email']);
    $captcha = $_POST['captcha'];

    // Validate CAPTCHA
    if (empty($captcha) || $captcha !== $_SESSION['captcha']) {
        $error = "Invalid CAPTCHA code.";
        $_SESSION['captcha'] = sprintf("%04d", rand(0, 9999)); // Regenerate CAPTCHA
    } else {
        // Check if email exists
        $query = "SELECT * FROM admin WHERE admin_email = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "s", $user_email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            // Generate and store reset token
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token valid for 1 hour
            $insert_query = "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)";
            $insert_stmt = mysqli_prepare($con, $insert_query);
            mysqli_stmt_bind_param($insert_stmt, "sss", $user_email, $token, $expires_at);
            
            if (mysqli_stmt_execute($insert_stmt)) {
                // Redirect to reset-password.php with token
                header("Location: reset-password.php?token=" . urlencode($token));
                exit();
            } else {
                $error = "Failed to process reset request. Please try again.";
                $_SESSION['captcha'] = sprintf("%04d", rand(0, 9999));
            }
            mysqli_stmt_close($insert_stmt);
        } else {
            $error = "No account found with that email address.";
            $_SESSION['captcha'] = sprintf("%04d", rand(0, 9999));
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FitZone Gym - Forgot Password</title>
    <link href="styles.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: var(--bg-color, #111);
            color: var(--text-color, #fff);
        }

        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding-top: 6rem;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .form-signin {
            background-color: var(--second-bg-color, #1c1c1c);
            padding: 3rem;
            border-radius: 1.5rem;
            box-shadow: 0 0 20px rgba(69, 255, 202, 0.2);
            width: 100%;
            max-width: 500px;
            color: var(--text-color, #fff);
        }

        .form-signin input[type="email"],
        .form-signin input[type="text"] {
            width: 100%;
            padding: 1rem;
            margin-top: 0.5rem;
            margin-bottom: 1.5rem;
            background: transparent;
            border: 1px solid var(--main-color, #45ffca);
            color: var(--text-color, #fff);
            border-radius: 0.5rem;
        }

        .form-signin label {
            font-size: 1.4rem;
            display: block;
            color: var(--main-color, #45ffca);
        }

        .form-signin button {
            width: 100%;
            padding: 1rem;
            background-color: var(--main-color, #45ffca);
            color: var(--bg-color, #111);
            font-size: 1.6rem;
            font-weight: bold;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease;
            border: none;
        }

        .form-signin button:hover {
            background-color: #33ccaa;
            box-shadow: 0 0 10px var(--main-color, #45ffca);
        }

        .link-small {
            color: #45ffca;
            text-decoration: none;
            font-size: 1rem;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .link-small:hover {
            text-decoration: underline;
        }

        .centered-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        header {
            background-color: #000;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo {
            font-size: 2rem;
            color: white;
        }

        .logo span {
            color: var(--main-color, #45ffca);
        }

        .navbar {
            list-style: none;
            display: flex;
            gap: 1.5rem;
        }

        .navbar li a {
            text-decoration: none;
            color: white;
        }

        .top-btn {
            display: flex;
            gap: 1rem;
        }

        .nav-btn,
        .nav-btn1 {
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            background-color: var(--main-color, #45ffca);
            color: black;
            text-decoration: none;
            font-weight: bold;
        }

        footer {
            margin-top: 2rem;
            text-align: center;
            padding: 2rem;
            background-color: #111;
            color: #aaa;
        }

        .captcha-group {
            margin-bottom: 1.5rem;
        }

        .captcha-group label {
            font-size: 1.4rem;
            color: var(--main-color, #45ffca);
            display: block;
            margin-bottom: 0.5rem;
        }

        .captcha-wrapper {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: center;
        }

        .captcha-code {
            background-color: #fff;
            color: #000;
            padding: 0.5rem 1rem;
            border: 1px solid var(--main-color, #45ffca);
            border-radius: 0.5rem;
            font-size: 1.2rem;
            font-weight: bold;
            letter-spacing: 2px;
            user-select: none;
        }

        .reload-captcha {
            padding: 0.8rem 1.5rem;
            background-color: transparent;
            border: 1px solid var(--main-color, #45ffca);
            color: var(--main-color, #45ffca);
            font-size: 1rem;
            font-weight: bold;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .reload-captcha:hover {
            background-color: var(--main-color, #45ffca);
            color: var(--bg-color, #111);
            box-shadow: 0 0 10px var(--main-color, #45ffca);
        }

        @media (max-width: 480px) {
            .captcha-wrapper {
                gap: 0.8rem;
            }
            
            .captcha-code {
                font-size: 1rem;
                padding: 0.4rem 0.8rem;
            }
            
            .reload-captcha {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1 class="logo">FitZone <span>Gym</span></h1>
        <div class="navbar">
            <li><a href="index.php">Home</a></li>
            <li><a href="services.php">Services</a></li>
            <li><a href="contactus.php">Contact Us</a></li>
        </div>
        <div class="top-btn">
            <a href="pricing.php" class="nav-btn">Join Us</a>
            <a href="login.php" class="nav-btn1">Log in</a>
        </div>
    </header>

    <div class="register-container">
        <main class="form-signin">
            <?php if (!empty($error)): ?>
                <p style="color: #ff6b6b; font-weight: bold;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form action="" method="post">
                <div class="centered-content">
                    <img class="mb-4" src="../images/icons8-gym-50.png" alt="" width="72" height="57">
                    <h1 class="logo">Reset <span>Password</span></h1>
                </div>

                <div class="form-group">
                    <label for="floatingInput">Email address</label>
                    <input type="email" id="floatingInput" name="email" placeholder="name@example.com" required autofocus />
                </div>

                <div class="form-group captcha-group">
                    <label for="captcha">Enter the code below:</label>
                    <div class="captcha-wrapper">
                        <span class="captcha-code"><?php echo $_SESSION['captcha']; ?></span>
                        <input type="text" id="captcha" name="captcha" placeholder="Enter CAPTCHA" required />
                        <button type="button" class="reload-captcha" onclick="refreshCaptcha()">Refresh Code</button>
                    </div>
                </div>

                <div class="form-options">
                    <a href="login.php" class="link-small">Back to Login</a>
                </div>

                <button type="submit" name="reset_password">Submit</button>
            </form>
        </main>
    </div>

    <footer class="container">
        <?php include('../footerp.php'); ?>
        <p class="float-end"><a href="index.php">Back to top</a></p>
    </footer>

    <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function refreshCaptcha() {
            window.location.reload();
        }
    </script>
</body>
</html>