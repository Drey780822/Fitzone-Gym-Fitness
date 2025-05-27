<?php
ob_start(); // Start output buffering
session_start();
include("../include/db.php");

// Enable error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Secure session settings
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Redirect if already logged in
if (isset($_SESSION['admin_email'])) {
    session_write_close();
    header('Location: index.php?view=dashboard');
    exit();
}

// Initialize variables
$error = '';
$admin_email = '';
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

// Rate limiting setup
$max_attempts = 5;
$lockout_time = 15 * 60; // 15 minutes
$ip_address = $_SERVER['REMOTE_ADDR'];

// Create login_attempts table if not exists
mysqli_query($con, "
    CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        admin_email VARCHAR(255) NOT NULL,
        attempt_time DATETIME NOT NULL,
        ip_address VARCHAR(45) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $admin_email = filter_var($_POST['admin_email'], FILTER_SANITIZE_EMAIL);
    $admin_pass = $_POST['admin_pass'];

    // Check rate limiting
    $query = "SELECT COUNT(*) as attempts FROM login_attempts WHERE admin_email = ? AND attempt_time > NOW() - INTERVAL 15 MINUTE";
    $stmt = mysqli_prepare($con, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $admin_email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $attempts = mysqli_fetch_assoc($result)['attempts'];
        mysqli_stmt_close($stmt);

        if ($attempts >= $max_attempts) {
            $error = "Too many login attempts. Please try again in 15 minutes.";
        } else {
            // Verify admin credentials
            $query = "SELECT admin_email, admin_pass, admin_name FROM admin WHERE admin_email = ?";
            $stmt = mysqli_prepare($con, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $admin_email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if ($admin = mysqli_fetch_assoc($result)) {
                    if (password_verify($admin_pass, $admin['admin_pass'])) {
                        // Successful login
                        session_regenerate_id(true);
                        $_SESSION['admin_email'] = $admin['admin_email'];
                        $_SESSION['admin_name'] = $admin['admin_name'];

                        // Handle "Remember me"
                        if (isset($_POST['remember_me'])) {
                            $token = bin2hex(random_bytes(32));
                            setcookie('admin_remember', $token, time() + (30 * 24 * 60 * 60), '/', '', isset($_SERVER['HTTPS']), true);
                            $query = "UPDATE admin SET remember_token = ? WHERE admin_email = ?";
                            $stmt = mysqli_prepare($con, $query);
                            if ($stmt) {
                                mysqli_stmt_bind_param($stmt, "ss", $token, $admin_email);
                                mysqli_stmt_execute($stmt);
                                mysqli_stmt_close($stmt);
                            }
                        }

                        // Clear login attempts
                        $query = "DELETE FROM login_attempts WHERE admin_email = ?";
                        $stmt = mysqli_prepare($con, $query);
                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "s", $admin_email);
                            mysqli_stmt_execute($stmt);
                            mysqli_stmt_close($stmt);
                        }

                        // Ensure session is saved
                        session_write_close();
                        // Use absolute URL for redirection
                        $host = $_SERVER['HTTP_HOST'];
                        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                        header("Location: http://$host$uri/index.php?view=dashboard");
                        exit();
                    } else {
                        $error = "Invalid email or password.";
                    }
                } else {
                    $error = "Invalid email or password.";
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = "Database query failed: " . mysqli_error($con);
                error_log("Database query failed: " . mysqli_error($con));
            }

            // Log failed attempt
            $query = "INSERT INTO login_attempts (admin_email, attempt_time, ip_address) VALUES (?, NOW(), ?)";
            $stmt = mysqli_prepare($con, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ss", $admin_email, $ip_address);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    } else {
        $error = "Rate limiting check failed: " . mysqli_error($con);
        error_log("Rate limiting check failed: " . mysqli_error($con));
    }
}

// Handle "Remember me" cookie
if (isset($_COOKIE['admin_remember']) && !isset($_SESSION['admin_email'])) {
    $token = filter_var($_COOKIE['admin_remember'], FILTER_SANITIZE_STRING);
    $query = "SELECT admin_email, admin_name FROM admin WHERE remember_token = ?";
    $stmt = mysqli_prepare($con, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($admin = mysqli_fetch_assoc($result)) {
            session_regenerate_id(true);
            $_SESSION['admin_email'] = $admin['admin_email'];
            $_SESSION['admin_name'] = $admin['admin_name'];
            session_write_close();
            $host = $_SERVER['HTTP_HOST'];
            $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            header("Location: http://$host$uri/index.php?view=dashboard");
            exit();
        }
        mysqli_stmt_close($stmt);
    }
}

//Login Script Start
if (isset($_POST['Admin_login'])){
  $admin_email= ($_POST['admin_email']);
  $admin_password= ($_POST['admin_pass']);
  $select_admin="SELECT * FROM admin WHERE admin_email='$admin_email' AND admin_pass='$admin_password'";
  $run_admin=mysqli_query($con, $select_admin);
  $row_count=mysqli_num_rows($run_admin);
  if ($row_count==1) {
    $_SESSION['admin_email']=$admin_email; //create session variable
    header('location: index.php?view_users');
  }
  else{
    echo "<script>alert('Invalid Email or Password')</script>";
  }
}  //Login Script End

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="FitZone Gym admin login page">
    <meta name="author" content="Dikotope T">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="icon" type="image/png" href="../images/icons8-gym-50.png" sizes="16x16">
    <title>FitZone - Admin Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- AOS for animations -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" integrity="sha384-6sV1dW9H4eQhJ8fW6a/bW0JqD7N9D7fW6sV1dW9H4eQhJ8fW6a/bW0JqD7N9D7fW6" crossorigin="anonymous">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            text-decoration: none;
            list-style: none;
            border: none;
            outline: none;
            scroll-behavior: smooth;
            font-family: Arial, sans-serif;
        }

        :root {
            --bg-color: #121212;
            --second-bg-color: #1e1e1e;
            --text-color: #e0e0e0;
            --main-color: #26a69a;
        }

        html {
            font-size: 62.5%;
            overflow-x: hidden;
        }

        body {
            background: var(--bg-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            transition: background-color 0.3s, color 0.3s;
        }

        body.light-mode {
            --bg-color: #f5f5f5;
            --second-bg-color: #ffffff;
            --text-color: #212121;
            --main-color: #26a69a;
        }

        .login-container {
            background: var(--second-bg-color);
            border: 1px solid var(--main-color);
            border-radius: 1rem;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 0 15px rgba(38, 166, 154, 0.3);
            text-align: center;
            position: relative;
        }

        .login-container img {
            width: 7.2rem;
            height: 5.7rem;
            margin-bottom: 2rem;
        }

        .login-container h1 {
            font-size: 2.8rem;
            color: var(--main-color);
            margin-bottom: 2rem;
            font-weight: 800;
        }

        .form-group {
            margin-bottom: 2rem;
            text-align: left;
        }

        .form-label {
            font-size: 1.6rem;
            color: var(--text-color);
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control {
            background: var(--bg-color);
            border: 1px solid var(--main-color);
            color: var(--text-color);
            border-radius: 0.5rem;
            font-size: 1.6rem;
            padding: 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #00897b;
            box-shadow: 0 0 5px rgba(38, 166, 154, 0.5);
        }

        .form-control::placeholder {
            color: rgba(224, 224, 224, 0.5);
        }

        .checkbox {
            font-size: 1.6rem;
            color: var(--text-color);
            margin-bottom: 2rem;
            text-align: left;
        }

        .checkbox input {
            margin-right: 1rem;
        }

        .btn-primary {
            background: var(--main-color);
            border-color: var(--main-color);
            color: #ffffff;
            font-size: 1.6rem;
            font-weight: bold;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #00897b;
            box-shadow: 0 0 10px rgba(38, 166, 154, 0.5);
        }

        .forgot-password {
            font-size: 1.4rem;
            color: var(--main-color);
            text-decoration: none;
            display: block;
            margin-top: 1rem;
            transition: color 0.3s;
        }

        .forgot-password:hover {
            color: #00897b;
        }

        .alert {
            background: rgba(211, 47, 47, 0.15);
            border: 1px solid #d32f2f;
            color: var(--text-color);
            border-radius: 0.5rem;
            font-size: 1.6rem;
            padding: 1rem;
            margin-bottom: 2rem;
        }

        .theme-toggle {
            position: absolute;
            top: 2rem;
            right: 2rem;
            background: none;
            border: none;
            color: var(--text-color);
            font-size: 2rem;
            cursor: pointer;
            transition: color 0.3s;
        }

        .theme-toggle:hover {
            color: var(--main-color);
        }

        .footer-text {
            font-size: 1.4rem;
            color: var(--text-color);
            opacity: 0.7;
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            .login-container {
                padding: 2rem;
                max-width: 90%;
            }

            .login-container h1 {
                font-size: 2.4rem;
            }

            .form-label, .form-control, .checkbox, .btn-primary, .forgot-password {
                font-size: 1.4rem;
            }

            .login-container img {
                width: 6rem;
                height: 4.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container" data-aos="fade-up">
        <button class="theme-toggle" aria-label="Toggle theme">
            <i class="fas fa-moon"></i>
        </button>
        <img src="../images/icons8-gym-50.png" alt="FitZone Gym Logo" aria-hidden="true">
        <h1>Admin Login</h1>
        <?php if ($error): ?>
            <div class="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" id="loginForm">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <div class="form-group">
                <label for="admin_email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="admin_email" name="admin_email" placeholder="Enter your email" value="<?php echo htmlspecialchars($admin_email); ?>" required aria-describedby="emailHelp">
                <div id="emailHelp" class="form-text" style="display: none;">Please enter a valid email address.</div>
            </div>
            <div class="form-group">
                <label for="admin_pass" class="form-label">Password</label>
                <input type="password" class="form-control" id="admin_pass" name="admin_pass" placeholder="Enter your password" required aria-describedby="passwordHelp">
                <div id="passwordHelp" class="form-text" style="display: none;">Password must be at least 8 characters.</div>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="remember_me" value="1"> Remember me
                </label>
            </div>
            <button type="submit" class="btn-primary" name="Admin_login">Sign In</button>
            <a href="forgot-password.php" class="link-small">Forgot Password?</a>
        </form>
        <p class="footer-text">© 2025 FitZone Gym</p>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" integrity="sha384-YZq3gR2gP1SUk5R5vO/jV6zM4uHU8W+0Ck0W7qACWkA5A1OUsIip5NZLtwvHR0A" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Theme toggle
        const themeToggle = document.querySelector('.theme-toggle');
        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('light-mode');
            const icon = themeToggle.querySelector('i');
            icon.classList.toggle('fa-moon');
            icon.classList.toggle('fa-sun');
            localStorage.setItem('theme', document.body.classList.contains('light-mode') ? 'light' : 'dark');
        });

        // Load saved theme
        if (localStorage.getItem('theme') === 'light') {
            document.body.classList.add('light-mode');
            themeToggle.querySelector('i').classList.replace('fa-moon', 'fa-sun');
        }

        // Form validation
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                let valid = true;
                const email = $('#admin_email').val();
                const password = $('#admin_pass').val();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                // Reset help text
                $('#emailHelp, #passwordHelp').hide();

                if (!emailRegex.test(email)) {
                    $('#emailHelp').css('display', 'block').css('color', '#d32f2f');
                    valid = false;
                }

                if (password.length < 8) {
                    $('#passwordHelp').css('display', 'block').css('color', '#d32f2f');
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>