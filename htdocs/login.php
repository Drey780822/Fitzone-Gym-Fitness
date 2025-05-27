<?php
session_start();
include("include/db.php");

// Login Script
$error = "";
if (isset($_POST['user_login'])) {
    $user_email = mysqli_real_escape_string($con, $_POST['user_email']);
    $user_password = mysqli_real_escape_string($con, $_POST['user_pass']);

    $select_user = "SELECT * FROM users WHERE user_email=? AND user_pass=?";
    $stmt = mysqli_prepare($con, $select_user);
    mysqli_stmt_bind_param($stmt, "ss", $user_email, $user_password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row_count = mysqli_num_rows($result);

    if ($row_count == 1) {
        $_SESSION['user_email'] = $user_email;
        header('Location: Home.php');
        exit();
    } else {
        $error = "Invalid email or password. Please try again.";
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FitZone Gym Login</title>
    <link href="styles.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous">
    <style>
        :root {
            --bg-color: #111;
            --second-bg-color: #1c1c1c;
            --text-color: #fff;
            --main-color: #45ffca;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        /* Header Section */
        header {
            background-color: #000;
            padding: 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            font-size: 2rem;
            color: var(--text-color);
        }

        .logo span {
            color: var(--main-color);
        }

        .navbar-nav .nav-link {
            color: var(--text-color);
            transition: color 0.3s;
            font-size: 1.1rem;
        }

        .navbar-nav .nav-link:hover {
            color: var(--main-color);
        }

        .navbar-nav .nav-item:last-child {
            display: flex;
            align-items: center; /* Vertically center the Login button */
        }

        .nav-btn {
            padding: 0.5rem 1rem; /* Adjusted for alignment */
            border-radius: 0.5rem;
            background-color: var(--main-color);
            color: var(--bg-color);
            text-decoration: none;
            font-weight: bold;
            transition: transform 0.2s, background-color 0.3s;
            margin-left: 0.5rem; /* Consistent spacing */
            line-height: 1.5; /* Match nav-link */
            font-size: 1.1rem; /* Match nav-link */
            vertical-align: middle; /* Inline alignment */
        }

        .nav-btn:hover {
            background-color: #33ccaa;
            transform: scale(1.02); /* Reduced scale */
        }

        .nav-btn.disabled {
            background-color: #555;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Login Form Styles */
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

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .forgot-password a {
            color: var(--main-color);
            text-decoration: none;
            font-size: 1rem;
            display: block;
            text-align: right;
            margin-top: -10px;
        }

        .form-signin {
            background-color: var(--second-bg-color);
            padding: 3rem;
            border-radius: 1.5rem;
            box-shadow: 0 0 20px rgba(69, 255, 202, 0.2);
            width: 100%;
            max-width: 500px;
            color: var(--text-color);
        }

        .form-signin input[type="email"],
        .form-signin input[type="password"],
        .form-signin input[type="text"] {
            width: 100%;
            padding: 1rem;
            margin-top: 0.5rem;
            margin-bottom: 1.5rem;
            background: transparent;
            border: 1px solid var(--main-color);
            color: var(--text-color);
            border-radius: 0.5rem;
        }

        .form-signin label {
            font-size: 1.4rem;
            display: block;
            color: var(--main-color);
        }

        .form-signin button {
            width: 100%;
            padding: 1rem;
            background-color: var(--main-color);
            color: var(--bg-color);
            font-size: 1.6rem;
            font-weight: bold;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease;
            border: none;
        }

        .form-check-group {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            gap: 0.5rem;
        }

        .form-check-group input[type="checkbox"] {
            margin: 0;
            transform: scale(1.2);
        }

        .form-check-group label {
            font-size: 1rem;
            color: var(--text-color);
        }

        .link-small {
            color: var(--main-color);
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

        .form-signin button:hover {
            background-color: #33ccaa;
            box-shadow: 0 0 10px var(--main-color);
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
            color: var(--main-color);
        }

        /* Footer */
        footer {
            margin-top: 2rem;
            text-align: center;
            padding: 2rem;
            background-color: var(--bg-color);
            color: #aaa;
        }

        /* Responsive Design */
        @media (max-width: 991px) {
            .navbar {
                width: 100%;
                margin-top: 1rem;
            }

            .navbar-collapse {
                background: #000;
                padding: 1rem;
            }
        }

        @media (max-width: 768px) {
            .logo {
                font-size: 1.5rem;
            }

            .navbar-nav .nav-link {
                font-size: 1rem;
            }

            .nav-btn {
                padding: 0.5rem 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <h1 class="logo">FitZone <span>Gym</span></h1>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php" aria-label="Home">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="aboutus.php" aria-label="About Us">About Us</a></li>
                        <li class="nav-item"><a class="nav-link" href="services.php" aria-label="Services">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="trainers.php" aria-label="Trainers">Trainers</a></li>
                        <li class="nav-item"><a class="nav-link" href="blog.php" aria-label="Blog">Blog</a></li>
                        <li class="nav-item"><a class="nav-link" href="faqs.php" aria-label="FAQs">FAQs</a></li>
                        <li class="nav-item"><a class="nav-link" href="contactus.php" aria-label="Contact us">Contact us</a></li>
                        <li class="nav-item"><a class="nav-link" href="pricing.php" aria-label="Pricing">Pricing</a></li>
                        <li class="nav-item">
                            <a class="nav-btn disabled" href="#" aria-label="Login">Login</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="register-container">
        <main class="form-signin">
            <?php if (!empty($error)): ?>
                <p style="color: #ff6b6b; font-weight: bold;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form action="" method="post">
                <div class="centered-content">
                    <img class="mb-4" src="images/icons8-gym-50.png" alt="" width="72" height="57">
                    <h1 class="logo">Login to <span>FitZone</span></h1>
                </div>

                <div class="form-group">
                    <label for="floatingInput">Email address</label>
                    <input type="email" id="floatingInput" name="user_email" placeholder="name@example.com" required autofocus />
                </div>

                <div class="form-group">
                    <label for="floatingPassword">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="floatingPassword" name="user_pass" placeholder="Password" required />
                        <span class="toggle-password" onclick="togglePassword()"></span>
                    </div>
                </div>

                <div class="form-options">
                    <div class="checkbox-group">
                        <input type="checkbox" name="remember_me" id="rememberMe" />
                        <label for="rememberMe">Remember Me</label>
                    </div>
                    <div class="forgot-password">
                        <a href="forgot-password.php" class="link-small">Forgot Password?</a>
                    </div>
                </div>

                <button type="submit" name="user_login">Log In</button>
            </form>
        </main>
    </div>

    <footer class="container">
        <?php include('../footerp.php'); ?>
        <p class="float-end"><a href="index.php">Back to top</a></p>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('floatingPassword');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
        }
    </script>
</body>
</html>