<?php
session_start();
include("include/db.php");

$error = "";
$success = "";
$token = isset($_GET['token']) ? $_GET['token'] : '';

if (empty($token)) {
    $error = "Invalid or missing reset token.";
}

// Check token validity
if (empty($error)) {
    $query = "SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        $error = "Invalid or expired reset token.";
    } else {
        $row = mysqli_fetch_assoc($result);
        $user_email = $row['email'];
    }
    mysqli_stmt_close($stmt);
}

if (isset($_POST['reset_password']) && empty($error)) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords
    if (empty($password) || strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Update password (plain text)
        $query = "UPDATE users SET user_pass = ? WHERE user_email = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ss", $password, $user_email);
        
        if (mysqli_stmt_execute($stmt)) {
            // Delete used token
            $delete_query = "DELETE FROM password_resets WHERE token = ?";
            $delete_stmt = mysqli_prepare($con, $delete_query);
            mysqli_stmt_bind_param($delete_stmt, "s", $token);
            mysqli_stmt_execute($delete_stmt);
            mysqli_stmt_close($delete_stmt);
            
            $success = "Your password has been successfully reset. <a href='login.php' class='link-small'>Log in now</a>.";
        } else {
            $error = "Failed to update password. Please try again.";
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
    <title>FitZone Gym - Reset Password</title>
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

        .form-signin input[type="password"] {
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

        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-wrapper input {
            width: 100%;
            padding-right: 40px;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            cursor: pointer;
            user-select: none;
            font-size: 1.2rem;
            color: #45ffca;
        }

        .success-message {
            color: #45ffca;
            font-weight: bold;
            margin-bottom: 1rem;
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
            <?php if (!empty($success)): ?>
                <p class="success-message"><?php echo $success; ?></p>
            <?php endif; ?>

            <?php if (empty($success) && empty($error)): ?>
                <form action="" method="post">
                    <div class="centered-content">
                        <img class="mb-4" src="../images/icons8-gym-50.png" alt="" width="72" height="57">
                        <h1 class="logo">Reset <span>Password</span></h1>
                    </div>

                    <div class="form-group">
                        <label for="password">New Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" placeholder="New Password" required />
                            <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required />
                            <span class="toggle-password" onclick="togglePassword('confirm_password')">👁️</span>
                        </div>
                    </div>

                    <div class="form-options">
                        <a href="login.php" class="link-small">Back to Login</a>
                    </div>

                    <button type="submit" name="reset_password">Reset Password</button>
                </form>
            <?php endif; ?>
        </main>
    </div>

    <footer class="container">
        <?php include('../footerp.php'); ?>
        <p class="float-end"><a href="index.php">Back to top</a></p>
    </footer>

    <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
        }
    </script>
</body>
</html>