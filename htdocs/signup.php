<?php
session_start();
include("include/db.php");

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Define allowed packages and their prices
$allowed_packages = [
    'General' => 500.00,
    'Couple' => 1000.00,
    'Premium' => 1500.00
];

// Retrieve and validate package from GET parameter
$package = isset($_GET['id']) ? trim($_GET['id']) : '';
if (!array_key_exists($package, $allowed_packages)) {
    echo "<script>alert('Invalid package selected. Please choose a valid package.'); window.location.href = 'pricing.php';</script>";
    exit;
}

// Handle form submission
if (isset($_POST['register']) && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    // Sanitize inputs
    $user_email = filter_var(trim($_POST['user_email']), FILTER_SANITIZE_EMAIL);
    $user_name = filter_var(trim($_POST['user_name']), FILTER_SANITIZE_STRING);
    $user_surname = filter_var(trim($_POST['user_surname']), FILTER_SANITIZE_STRING);
    $user_contact = filter_var(trim($_POST['user_contact']), FILTER_SANITIZE_STRING);
    $user_pass = trim($_POST['user_pass']);
    $user_pass_confirm = trim($_POST['user_pass_confirm']);
    $user_weight = filter_var(trim($_POST['user_weight']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $user_age = filter_var(trim($_POST['user_age']), FILTER_SANITIZE_NUMBER_INT);
    $packages = htmlspecialchars($package);
    // Server-side validation
    $errors = [];

    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token.';
    }

    // Validate package
    if (!array_key_exists($package, $allowed_packages)) {
        $errors[] = 'Invalid package selected.';
    }

    // Validate email
    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL) || strlen($user_email) > 100) {
        $errors[] = 'Invalid or too long email address.';
    }

    // Validate name
    if (empty($user_name) || strlen($user_name) > 50 || !preg_match('/^[a-zA-Z\s]+$/', $user_name)) {
        $errors[] = 'Name is required, must be 50 characters or less, and contain only letters.';
    }

    // Validate surname
    if (empty($user_surname) || strlen($user_surname) > 50 || !preg_match('/^[a-zA-Z\s]+$/', $user_surname)) {
        $errors[] = 'Surname is required, must be 50 characters or less, and contain only letters.';
    }

    // Validate contact
    if (!preg_match('/^\d{10}$/', $user_contact)) {
        $errors[] = 'Contact number must be exactly 10 digits.';
    }

    // Validate password
    if (empty($user_pass) || strlen($user_pass) < 6 || strlen($user_pass) > 10) {
        $errors[] = 'Password must be between 6 and 10 characters.';
    }

    // Validate password confirmation
    if ($user_pass !== $user_pass_confirm) {
        $errors[] = 'Passwords do not match.';
    }

    // Validate weight
    if (!is_numeric($user_weight) || $user_weight <= 0 || $user_weight > 500) {
        $errors[] = 'Body weight must be a positive number up to 500 kg.';
    }

    // Validate age
    if (!is_numeric($user_age) || $user_age <= 0 || $user_age > 150) {
        $errors[] = 'Age must be a valid number between 1 and 150.';
    }

    // Check if email already exists
    $stmt = $con->prepare("SELECT user_id FROM users WHERE user_email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($bursts = $result->num_rows > 0) {
        $errors[] = 'Email already exists, please log in.';
    }
    $stmt->close();

    // Check if contact number already exists
    $stmt = $con->prepare("SELECT user_id FROM users WHERE user_contact = ?");
    $stmt->bind_param("s", $user_contact);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = 'Contact number already registered.';
    }
    $stmt->close();

    if (!empty($errors)) {
        echo "<script>alert('" . implode("\\n", array_map('addslashes', $errors)) . "'); window.history.back();</script>";
        exit;
    }

    // Calculate dates and pricing
    $join_date = date('Y-m-d');
    $membership_expiry = date('Y-m-d', strtotime('+1 month'));
    $price = $allowed_packages[$package];
    $total_paid = $price; // Initial payment assumed to be made

    // Hash password
    $hashed_password = password_hash($user_pass, PASSWORD_DEFAULT);

    // Start transaction
    $con->begin_transaction();

    try {
        $con->begin_transaction();

        // Insert user data
        $stmt = $con->prepare("INSERT INTO users (user_package,user_email, user_name, user_surname, user_age, user_pass, user_contact, user_weight, join_date, membership_expiry, price, total_paid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssisidssdd", 
        $packages,
            $user_email,
            $user_name,
            $user_surname,
            $user_age,
            $user_pass,
            $user_contact,
            $user_weight,
            $join_date,
            $membership_expiry,
            $price,
            $total_paid
        );
        if (!$stmt->execute()) {
            throw new Exception("Registration failed: " . $stmt->error);
        }
        $stmt->close();

        // Insert payment record (assumed payments table exists)
        $payment_method = 'Initial Signup';
        $payment_status = 'Pending'; // Payment to be confirmed via paycard.php
        $stmt = $con->prepare("INSERT INTO payments (user_email, amount, payment_date, payment_method, payment_status) VALUES (?, ?, NOW(), ?, ?)");
        $stmt->bind_param("sdss", $user_email, $price, $payment_method, $payment_status);
        if (!$stmt->execute()) {
            throw new Exception("Payment record creation failed: " . $stmt->error);
        }
        $stmt->close();


        

        // Assign default workout plan
        $plan_name = ($package === 'Premium') ? 'Advanced Cardio' : 'Beginner Strength';
        $stmt = $con->prepare("SELECT plan_id FROM workout_plans WHERE plan_name = ?");
        $stmt->bind_param("s", $plan_name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $plan_id = $row['plan_id'];
            // Optionally, insert into a user_plans table (not provided in schema)
            // For now, assume plan is associated via user_package
        }
        $stmt->close();

        // Commit transaction
        $con->commit();

        // Regenerate session ID after successful registration
        session_regenerate_id(true);

        echo "<script>alert('Registration successful! A payment of R" . number_format($price, 2) . " is pending. Please complete payment to activate your membership.'); window.location.href = 'login.php';</script>";
    } catch (Exception $e) {
        $con->rollback();
        echo "<script>alert('Registration failed. Please try again. Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sign up for FitZone Gym and choose a fitness package to start your journey.">
    <meta name="author" content="Dikotope T">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="generator" content="Hugo 0.84.0">
    <link rel="icon" type="image/png" href="../images/icons8-gym-50.png" sizes="16x16">
    <title>FitZone - Sign Up</title>
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
            --bg-color: #111;
            --second-bg-color: #1c1c1c;
            --text-color: #fff;
            --main-color: #45ffca;
        }
        html {
            font-size: 62.5%;
            overflow-x: hidden;
        }
        body {
            background: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s, color 0.3s;
        }
        body.light-mode {
            --bg-color: #f4f4f4;
            --second-bg-color: #fff;
            --text-color: #333;
            --main-color: #33ccaa;
        }
        section {
            min-height: 100vh;
            padding: 10rem 8% 2rem;
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
            color: var(--text-color);
            font-weight: 800;
            transition: 0.3s ease-in-out;
        }
        .logo:hover {
            transform: scale(1.1);
        }
        .logo span {
            color: var(--main-color);
        }
        .navbar {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 1.5rem;
        }
        .navbar a {
            font-size: 1.8rem;
            font-weight: 500;
            color: var(--text-color);
            text-decoration: none;
            transition: color 0.3s;
        }
        .navbar a:hover,
        .navbar a.active {
            color: var(--main-color);
        }
        #menu-icon {
            font-size: 3.6rem;
            color: var(--main-color);
            cursor: pointer;
            display: none;
        }
        .top-btn {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .nav-btn, .nav-btn1 {
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            background-color: var(--main-color);
            color: var(--bg-color);
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .nav-btn:hover, .nav-btn1:hover {
            background-color: #33ccaa;
        }
        .theme-toggle {
            background: none;
            border: none;
            color: var(--text-color);
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s;
        }
        .signup {
            background: var(--second-bg-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 10rem 8% 2rem;
        }
        .form-signin {
            background: var(--bg-color);
            padding: 3rem;
            border-radius: 1.5rem;
            box-shadow: 0 0 20px rgba(69, 255, 202, 0.2);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        .form-signin img {
            margin-bottom: 2rem;
        }
        .form-signin h2 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .form-signin .form-group {
            margin-bottom: 2rem;
            text-align: left;
        }
        .form-signin label {
            font-size: 1.4rem;
            color: var(--main-color);
            margin-bottom: 0.5rem;
            display: block;
        }
        .form-signin .input-group {
            position: relative;
        }
        .form-signin .input-group-addon {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: var(--main-color);
            font-size: 1.6rem;
        }
        .form-signin input,
        .form-signin input[readonly] {
            width: 100%;
            padding: 1rem 1rem 1rem 3.5rem;
            background: transparent;
            border: 1px solid var(--main-color);
            color: var(--text-color);
            border-radius: 0.5rem;
            font-size: 1.6rem;
        }
        .form-signin input:focus {
            border-color: #33ccaa;
            box-shadow: 0 0 5px var(--main-color);
        }
        .form-signin button {
            width: 100%;
            padding: 1rem;
            background: var(--main-color);
            color: var(--bg-color);
            font-size: 1.6rem;
            font-weight: bold;
            border-radius: 0.5rem;
            transition: background-color 0.3s;
        }
        .form-signin button:hover {
            background: #33ccaa;
            box-shadow: 0 0 10px var(--main-color);
        }
        .form-signin .error_strings {
            color: #ff5555;
            font-size: 1.2rem;
            margin-top: 0.5rem;
        }
        .form-signin .login-link {
            font-size: 1.4rem;
            margin-top: 2rem;
            display: inline-block;
            color: var(--main-color);
        }
        .footer {
            position: relative;
            bottom: 0;
            width: 100%;
            padding: 40px 0;
            background-color: var(--second-bg-color);
            text-align: center;
        }
        .footer .social a {
            font-size: 25px;
            color: var(--main-color);
            border: 2px solid var(--main-color);
            width: 42px;
            height: 42px;
            line-height: 42px;
            display: inline-block;
            text-align: center;
            border-radius: 50%;
            margin: 0 10px;
            transition: 0.3s ease-in-out;
        }
        .footer .social a:hover {
            transform: scale(1.2) translateY(-10px);
            background-color: var(--main-color);
            color: #131313;
            box-shadow: 0 0 25px var(--main-color);
        }
        .footer .copyright {
            margin-top: 20px;
            font-size: 16px;
            color: var(--text-color);
        }
        ::-webkit-scrollbar {
            width: 15px;
        }
        ::-webkit-scrollbar-thumb {
            background-color: var(--main-color);
            width: 50px;
        }
        .modal-content {
            background-color: var(--second-bg-color);
            color: var(--text-color);
        }
        .modal-content .table {
            width: 100%;
            margin-bottom: 0;
        }
        .modal-content .table th,
        .modal-content .table td {
            border-color: var(--main-color);
            padding: 0.75rem;
        }
        @media (max-width: 1200px) {
            html {
                font-size: 55%;
            }
        }
        @media (max-width: 991px) {
            #menu-icon {
                display: block;
            }
            .navbar {
                position: fixed;
                top: 100%;
                right: -100%;
                width: 255px;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                background: var(--bg-color);
                transition: all 0.5s ease;
                padding: 2rem;
            }
            .navbar.active {
                right: 0;
            }
            .navbar a {
                display: block;
                padding: 17px;
                font-size: 22px;
            }
            header {
                padding: 2rem 3%;
            }
            section {
                padding: 10rem 3%;
            }
        }
        @media (max-width: 450px) {
            html {
                font-size: 50%;
            }
            .form-signin {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1 class="logo">FitZone <span>Gym</span></h1>
        <div class="bx bx-menu" id="menu-icon"><i class="fas fa-bars"></i></div>
        <ul class="navbar">
            <li><a href="index.php" aria-label="Home">Home</a></li>
            <li><a href="services.php" aria-label="Services">Services</a></li>
            <li><a href="contactus.php" aria-label="Contact Us">Contact Us</a></li>
        </ul>
        <div class="top-btn">
            <a href="login.php" class="nav-btn1" aria-label="Log In">Log in</a>
        </div>
    </header>

    <section class="signup" id="signup">
        <main class="form-signin" data-aos="zoom-in-up">
            <form action="" method="post" id="signupForm" name="signupForm">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <img class="mb-4" src="../images/icons8-gym-50.png" alt="FitZone Logo" width="72" height="57">
                <h2>Join FitZone</h2>
                <h1 class="logo">Complete your <span>registration!</span></h1>

                <div class="form-group packageinput">
                    <label for="package">Selected Package</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-gift"></i></div>
                        <input type="text" id="package" value="<?php echo htmlspecialchars($package); ?>" readonly placeholder="Selected Package" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="user_email">Email</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-envelope"></i></div>
                        <input type="email" name="user_email" id="user_email" placeholder="Enter Your Email" required maxlength="100" />
                    </div>
                    <div id="user_email_error" class="error_strings"></div>
                </div>

                <div class="form-group">
                    <label for="user_name">Name</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-user"></i></div>
                        <input type="text" name="user_name" id="user_name" placeholder="Enter Your Name" required maxlength="50" pattern="[a-zA-Z\s]+" />
                    </div>
                    <div id="user_name_error" class="error_strings"></div>
                </div>

                <div class="form-group">
                    <label for="user_surname">Surname</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-user"></i></div>
                        <input type="text" name="user_surname" id="user_surname" placeholder="Enter Your Surname" required maxlength="50" pattern="[a-zA-Z\s]+" />
                    </div>
                    <div id="user_surname_error" class="error_strings"></div>
                </div>

                <div class="form-group">
                    <label for="user_contact">Contact Number</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-phone"></i></div>
                        <input type="tel" name="user_contact" id="user_contact" placeholder="Enter Your Contact Number" required pattern="[0-9]{10}" maxlength="10" />
                    </div>
                    <div id="user_contact_error" class="error_strings"></div>
                </div>

                <div class="form-group">
                    <label for="user_weight">Current Body Weight (kg)</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-balance-scale"></i></div>
                        <input type="number" name="user_weight" id="user_weight" placeholder="Enter Your Body Weight" required min="1" max="500" step="0.1" />
                    </div>
                    <div id="user_weight_error" class="error_strings"></div>
                </div>

                <div class="form-group">
                    <label for="user_age">Your Age</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-birthday-cake"></i></div>
                        <input type="number" name="user_age" id="user_age" placeholder="Enter Your Age" required min="1" max="150" />
                    </div>
                    <div id="user_age_error" class="error_strings"></div>
                </div>

                <div class="form-group">
                    <label for="user_pass">Password</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-lock"></i></div>
                        <input type="password" name="user_pass" id="user_pass" placeholder="Enter Your Password" required minlength="6" maxlength="10" />
                    </div>
                    <div id="user_pass_error" class="error_strings"></div>
                </div>

                <div class="form-group">
                    <label for="user_pass_confirm">Confirm Password</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-lock"></i></div>
                        <input type="password" name="user_pass_confirm" id="user_pass_confirm" placeholder="Confirm Your Password" required minlength="6" maxlength="10" />
                    </div>
                    <div id="user_pass_confirm_error" class="error_strings"></div>
                </div>

                <button type="submit" name="register" class="btn btn-info">Register</button>
                <div class="login-link">
                    Already have an account? <a href="login.php">Login</a>
                </div>
            </form>
        </main>
    </section>

    <!-- Schedule Modal -->
    <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleModalLabel">Class Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="scheduleModalDesc" class="visually-hidden">Weekly class schedule for FitZone Gym, including days, class types, and times.</p>
                    <table class="table table-striped table-bordered" style="color: var(--text-color);">
                        <thead>
                            <tr>
                                <th scope="col">Day</th>
                                <th scope="col">Class</th>
                                <th scope="col">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Monday</td>
                                <td>Yoga</td>
                                <td>6:00 PM - 7:00 PM</td>
                            </tr>
                            <tr>
                                <td>Wednesday</td>
                                <td>Strength Training</td>
                                <td>5:30 PM - 6:30 PM</td>
                            </tr>
                            <tr>
                                <td>Friday</td>
                                <td>Cardio Blast</td>
                                <td>7:00 PM - 8:00 PM</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn nav-btn" data-bs-dismiss="modal" aria-label="Close modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="social">
            <a href="https://instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="https://facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
        </div>
        <div class="copyright">
            © 2025 FitZone Gym. All rights reserved.
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" integrity="sha384-YZq3gR2gP1SUk5R5vO/jV6zM4uHU8W+0Ck0W7qACWkA5A1OUsIip5NZLtwvHR0A" crossorigin="anonymous"></script>
    <script>
        // CDN fallback
        if (typeof bootstrap === 'undefined') {
            console.warn('Bootstrap not loaded, attempting fallback...');
            document.write('<script src="../assets/dist/js/bootstrap.bundle.min.js"><\/script>');
        }
        if (typeof AOS === 'undefined') {
            console.warn('AOS not loaded, attempting fallback...');
            document.write('<script src="assets/aos.min.js"><\/script>');
        }
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 1000,
                    once: true
                });
            } else {
                console.warn('AOS not available, animations disabled.');
            }
            if (typeof bootstrap !== 'undefined') {
                const scheduleModal = document.getElementById('scheduleModal');
                if (scheduleModal) {
                    new bootstrap.Modal(scheduleModal);
                }
            } else {
                console.warn('Bootstrap not available, modal may not function.');
            }
        });
        const menuIcon = document.getElementById('menu-icon');
        const navbar = document.querySelector('.navbar');
        menuIcon.addEventListener('click', () => {
            navbar.classList.toggle('active');
            const icon = menuIcon.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        });
        const themeToggle = document.querySelector('.theme-toggle');
        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('light-mode');
            const icon = themeToggle.querySelector('i');
            icon.classList.toggle('fa-moon');
            icon.classList.toggle('fa-sun');
            localStorage.setItem('theme', document.body.classList.contains('light-mode') ? 'light' : 'dark');
        });
        if (localStorage.getItem('theme') === 'light') {
            document.body.classList.add('light-mode');
            themeToggle.querySelector('i').classList.replace('fa-moon', 'fa-sun');
        }
        const signupForm = document.getElementById('signupForm');
        signupForm.addEventListener('submit', (e) => {
            let isValid = true;
            const errors = {
                user_email: document.getElementById('user_email_error'),
                user_name: document.getElementById('user_name_error'),
                user_surname: document.getElementById('user_surname_error'),
                user_contact: document.getElementById('user_contact_error'),
                user_weight: document.getElementById('user_weight_error'),
                user_age: document.getElementById('user_age_error'),
                user_pass: document.getElementById('user_pass_error'),
                user_pass_confirm: document.getElementById('user_pass_confirm_error')
            };
            Object.values(errors).forEach(error => error.textContent = '');
            const email = document.getElementById('user_email').value;
            if (!email) {
                errors.user_email.textContent = 'Please enter your Email Address';
                isValid = false;
            } else if (email.length > 100) {
                errors.user_email.textContent = 'Max length for Email Address is 100 Characters';
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                errors.user_email.textContent = 'Please enter a valid Email Address';
                isValid = false;
            }
            const name = document.getElementById('user_name').value;
            if (!name) {
                errors.user_name.textContent = 'Please enter your Name';
                isValid = false;
            } else if (name.length > 50) {
                errors.user_name.textContent = 'Name must be 50 characters or less';
                isValid = false;
            } else if (!/^[a-zA-Z\s]+$/.test(name)) {
                errors.user_name.textContent = 'Name must contain only letters';
                isValid = false;
            }
            const surname = document.getElementById('user_surname').value;
            if (!surname) {
                errors.user_surname.textContent = 'Please enter your Surname';
                isValid = false;
            } else if (surname.length > 50) {
                errors.user_surname.textContent = 'Surname must be 50 characters or less';
                isValid = false;
            } else if (!/^[a-zA-Z\s]+$/.test(surname)) {
                errors.user_surname.textContent = 'Surname must contain only letters';
                isValid = false;
            }
            const contact = document.getElementById('user_contact').value;
            if (!contact) {
                errors.user_contact.textContent = 'Please enter your Contact Number';
                isValid = false;
            } else if (!/^\d{10}$/.test(contact)) {
                errors.user_contact.textContent = 'Contact Number must be exactly 10 digits';
                isValid = false;
            }
            const weight = document.getElementById('user_weight').value;
            if (!weight) {
                errors.user_weight.textContent = 'Please enter your Body Weight';
                isValid = false;
            } else if (isNaN(weight) || weight <= 0 || weight > 500) {
                errors.user_weight.textContent = 'Body Weight must be a positive number up to 500 kg';
                isValid = false;
            }
            const age = document.getElementById('user_age').value;
            if (!age) {
                errors.user_age.textContent = 'Please enter your Age';
                isValid = false;
            } else if (isNaN(age) || age <= 0 || age > 150) {
                errors.user_age.textContent = 'Age must be a valid number between 1 and 150';
                isValid = false;
            }
            const password = document.getElementById('user_pass').value;
            if (!password) {
                errors.user_pass.textContent = 'Please enter your Password';
                isValid = false;
            } else if (password.length < 6 || password.length > 10) {
                errors.user_pass.textContent = 'Password must be between 6 and 10 characters';
                isValid = false;
            }
            const passwordConfirm = document.getElementById('user_pass_confirm').value;
            if (!passwordConfirm) {
                errors.user_pass_confirm.textContent = 'Please confirm your Password';
                isValid = false;
            } else if (password !== passwordConfirm) {
                errors.user_pass_confirm.textContent = 'Passwords do not match';
                isValid = false;
            }
            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>