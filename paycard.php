<?php
session_start();
include("include/db.php");
include("include/functions.php");

// Redirect if not logged in
if (!isset($_SESSION['user_email'])) {
    header('location: login.php');
    exit();
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_email = $_SESSION['user_email'];
$results = getUser($con, $user_email);

// Define package prices
$allowed_packages = [
    'General' => 500.00,
    'Couple' => 1000.00,
    'Premium' => 1500.00
];

// Check payment status
$package = $results['user_package'];
$required_amount = isset($allowed_packages[$package]) ? $allowed_packages[$package] : 0;
$payment_pending = ($results['total_paid'] < $results['price']);

// Luhn algorithm for card number validation
function luhn_check($number) {
    $number = preg_replace('/\D/', '', $number);
    $sum = 0;
    $num_digits = strlen($number);
    $parity = $num_digits % 2;
    for ($i = 0; $i < $num_digits; $i++) {
        $digit = $number[$i];
        if ($i % 2 == $parity) {
            $digit *= 2;
            if ($digit > 9) {
                $digit -= 9;
            }
        }
        $sum += $digit;
    }
    return ($sum % 10) == 0;
}

// Handle payment submission
if (isset($_POST['pay']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $card_num = preg_replace('/\s+/', '', trim($_POST['card-num']));
    $exp = trim($_POST['exp']);
    $cvv = trim($_POST['cvv']);
    $amount = floatval($_POST['amount']);
    $save_card = isset($_POST['chk']) ? 1 : 0;

    $errors = [];

    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token.';
    }

    // Validate name
    if (empty($name) || strlen($name) > 50 || !preg_match('/^[a-zA-Z\s]+$/', $name)) {
        $errors[] = 'Name on card is required and must contain only letters.';
    }

    // Validate card number
    if (!preg_match('/^\d{16}$/', $card_num) || !luhn_check($card_num)) {
        $errors[] = 'Invalid card number. Must be 16 digits and pass validation.';
    }

    // Validate expiry date
    if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $exp)) {
        $errors[] = 'Invalid expiry date. Use MM/YY format.';
    } else {
        $exp_month = substr($exp, 0, 2);
        $exp_year = '20' . substr($exp, 3, 2);
        $exp_date = DateTime::createFromFormat('Y-m-d', "$exp_year-$exp_month-01");
        if (!$exp_date || $exp_date < new DateTime()) {
            $errors[] = 'Card has expired.';
        }
    }

    // Validate CVV
    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        $errors[] = 'Invalid CVV. Must be 3 or 4 digits.';
    }

    // Validate amount
    if ($amount != $required_amount) {
        $errors[] = 'Payment amount does not match the required package price.';
    }

    if (!empty($errors)) {
        echo "<script>alert(" . json_encode(implode('\n', $errors)) . "); window.history.back();</script>";
        exit;
    }

    // Start transaction
    $con->begin_transaction();
    try {
        // Update users table
        $total_paid = $results['total_paid'] + $amount;
        $payment_date = date('Y-m-d');
        $stmt = $con->prepare("UPDATE users SET total_paid = ?, paymentDate = ? WHERE user_email = ?");
        $stmt->bind_param("dss", $total_paid, $payment_date, $user_email);
        if (!$stmt->execute()) {
            throw new Exception("Error updating user payment: " . $stmt->error);
        }
        $stmt->close();

        // Insert payment record
        $payment_method = 'Credit Card';
        $payment_status = 'Completed';
        $transaction_id = 'TXN' . strtoupper(uniqid());
        $stmt = $con->prepare("INSERT INTO payments (user_email, amount, payment_date, payment_method, payment_status, transaction_id) VALUES (?, ?, NOW(), ?, ?, ?)");
        $stmt->bind_param("sdsss", $user_email, $amount, $payment_method, $payment_status, $transaction_id);
        if (!$stmt->execute()) {
            throw new Exception("Error recording payment: " . $stmt->error);
        }
        $stmt->close();

        // Save card details if requested
        if ($save_card) {
            $card_type = 'Unknown'; // Simplified; could detect Visa/Mastercard via regex
            $last_four = substr($card_num, -4);
            $stmt = $con->prepare("INSERT INTO saved_cards (user_email, card_type, last_four, expiry_date, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $user_email, $card_type, $last_four, $exp);
            if (!$stmt->execute()) {
                throw new Exception("Error saving card: " . $stmt->error);
            }
            $stmt->close();
        }

        // Simulate email notification
        $email_body = "Dear {$results['user_name']},\n\nYour payment of R$amount for the $package package has been successfully processed.\nTransaction ID: $transaction_id\nDate: $payment_date\n\nThank you for choosing FitZone Gym!\n\nBest regards,\nFitZone Team";
        error_log("Simulated email sent:\n$email_body");

        // Commit transaction
        $con->commit();

        // Regenerate session ID
        session_regenerate_id(true);

        $success_message = "Payment of R" . number_format($amount, 2) . " was successfully captured. Transaction ID: $transaction_id";
        echo "<script>alert(" . json_encode($success_message) . "); window.location.href = 'Home.php';</script>";
    } catch (Exception $e) {
        $con->rollback();
        $error_message = "Payment failed. Error: " . $e->getMessage();
        echo "<script>alert(" . json_encode($error_message) . "); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Make a secure payment for your FitZone Gym membership.">
    <meta name="author" content="Dikotope T">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="icon" type="image/png" href="../images/icons8-gym-50.png" sizes="16x16">
    <title>FitZone - Payment</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- AOS for animations -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" integrity="sha384-6sV1dW9H4eQhJ8fW6a/bW0JqD7N9D7fW6sV1dW9H4eQhJ8fW6a/bW0JqD7N9D7fW6" crossorigin="anonymous">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" integrity="sha512-3P8rXCuGJdNZOnUx/03c1jOTnMn3rP63nBip5gOP2qmUh5LKS1ETkyHjW+rCnohoOUH6NfYV6GEm1lV8/XaY2QA==" crossorigin="anonymous"></script>
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
        .payment {
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
        .form-signin .back-link {
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
    <script src="js/gen_validatorv4.js" type="text/javascript"></script>
</head>
<body>
    <header>
        <h1 class="logo">FitZone <span>Gym</span></h1>
        <div class="bx bx-menu" id="menu-icon"><i class="fas fa-bars"></i></div>
        <ul class="navbar">
            <li><a href="index.php" aria-label="Home">Home</a></li>
            <li><a href="services.php" aria-label="Services">Services</a></li>
            <li><a href="contactus.php" aria-label="Contact Us">Contact Us</a></li>
            <li><a href="#" data-bs-toggle="modal" data-bs-target="#scheduleModal" aria-label="View class schedule">Schedule</a></li>
        </ul>
        <div class="top-btn">
            <a href="pricing.php" class="nav-btn" aria-label="Join Us">Join Us</a>
            <a href="logout.php" class="nav-btn1" aria-label="Log Out">Log Out</a>
            <button class="theme-toggle" aria-label="Toggle theme">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </header>

    <section class="payment" id="payment">
        <main class="form-signin" data-aos="zoom-in-up">
            <form action="" method="post" id="paymentForm" name="paymentForm">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <img class="mb-4" src="../images/icons8-gym-50.png" alt="FitZone Logo" width="72" height="57">
                <h2>Make Payment</h2>
                <h1 class="logo">Complete your <span>payment!</span></h1>
                <?php if ($payment_pending): ?>
                    <p class="text-danger" style="font-size: 1.4rem;">Payment of R<?php echo number_format($required_amount, 2); ?> is required for your <?php echo htmlspecialchars($package); ?> membership.</p>
                <?php else: ?>
                    <p class="text-success" style="font-size: 1.4rem;">Your membership payment is up to date.</p>
                <?php endif; ?>

                <div class="form-group">
                    <label for="user_email">User Email</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-envelope"></i></div>
                        <input type="email" name="user_email" id="user_email" value="<?php echo htmlspecialchars($results['user_email']); ?>" readonly />
                    </div>
                </div>

                <div class="form-group">
                    <label for="amount">Payment Amount (R)</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-money-bill"></i></div>
                        <input type="text" name="amount" id="amount" value="<?php echo number_format($required_amount, 2); ?>" readonly />
                    </div>
                    <div id="amount_error" class="error_strings"></div>
                </div>

                <div class="form-group">
                    <label for="name">Name on Card</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-user"></i></div>
                        <input type="text" name="name" id="name" placeholder="Enter Name on Card" maxlength="50" required />
                    </div>
                    <div id="name_error" class="error_strings"></div>
                </div>

                <div class="form-group">
                    <label for="card-num">Card Number</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-credit-card"></i></div>
                        <input type="text" name="card-num" id="cr_no" placeholder="0000 0000 0000 0000" minlength="19" maxlength="19" required />
                    </div>
                    <div id="card-num_error" class="error_strings"></div>
                </div>

                <div class="form-group">
                    <label for="exp">Expiry Date</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="exp" id="exp" placeholder="MM/YY" minlength="5" maxlength="5" required />
                    </div>
                    <div id="exp_error" class="error_strings"></div>
                </div>

                <div class="form-group">
                    <label for="cvv">CVV/CVC</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-lock"></i></div>
                        <input type="password" name="cvv" id="cvv" placeholder="000" minlength="3" maxlength="4" required />
                    </div>
                    <div id="cvv_error" class="error_strings"></div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="chk" id="chk1" class="custom-control-input">
                        <label for="chk1" style="font-size: 1.4rem; color: var(--text-color);">Save card for future payments</label>
                    </div>
                </div>

                <button type="submit" name="pay" class="btn btn-info" <?php if (!$payment_pending) echo 'disabled'; ?>>Pay Now</button>
                <div class="back-link">
                    <a href="Home.php">Back to Home</a>
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
        const paymentForm = document.getElementById('paymentForm');
        paymentForm.addEventListener('submit', (e) => {
            let isValid = true;
            const errors = {
                name: document.getElementById('name_error'),
                card_num: document.getElementById('card-num_error'),
                exp: document.getElementById('exp_error'),
                cvv: document.getElementById('cvv_error'),
                amount: document.getElementById('amount_error')
            };
            Object.values(errors).forEach(error => error.textContent = '');
            const name = document.getElementById('name').value;
            if (!name) {
                errors.name.textContent = 'Please enter name on card';
                isValid = false;
            } else if (name.length > 50) {
                errors.name.textContent = 'Name must be 50 characters or less';
                isValid = false;
            } else if (!/^[a-zA-Z\s]+$/.test(name)) {
                errors.name.textContent = 'Name must contain only letters';
                isValid = false;
            }
            const cardNum = document.getElementById('cr_no').value.replace(/\s/g, '');
            if (!cardNum) {
                errors.card_num.textContent = 'Please enter card number';
                isValid = false;
            } else if (!/^\d{16}$/.test(cardNum)) {
                errors.card_num.textContent = 'Card number must be 16 digits';
                isValid = false;
            }
            const exp = document.getElementById('exp').value;
            if (!exp) {
                errors.exp.textContent = 'Please enter expiry date';
                isValid = false;
            } else if (!/^(0[1-9]|1[0-2])\/[0-9]{2}$/.test(exp)) {
                errors.exp.textContent = 'Expiry date must be in MM/YY format';
                isValid = false;
            }
            const cvv = document.getElementById('cvv').value;
            if (!cvv) {
                errors.cvv.textContent = 'Please enter CVV number';
                isValid = false;
            } else if (!/^\d{3,4}$/.test(cvv)) {
                errors.cvv.textContent = 'CVV must be 3 or 4 digits';
                isValid = false;
            }
            const amount = document.getElementById('amount').value;
            if (!amount || isNaN(parseFloat(amount)) || parseFloat(amount) <= 0) {
                errors.amount.textContent = 'Invalid payment amount';
                isValid = false;
            }
            if (!isValid) {
                e.preventDefault();
            }
        });
        // Card number formatting
        const cardNum = document.getElementById('cr_no');
        cardNum.onkeyup = function(e) {
            if (this.value == this.lastValue) return;
            var caretPosition = this.selectionStart;
            var sanitizedValue = this.value.replace(/[^0-9]/gi, '');
            var parts = [];
            for (var i = 0, len = sanitizedValue.length; i < len; i += 4) {
                parts.push(sanitizedValue.substring(i, i + 4));
            }
            for (var i = caretPosition - 1; i >= 0; i--) {
                var c = this.value[i];
                if (c < '0' || c > '9') {
                    caretPosition--;
                }
            }
            caretPosition += Math.floor(caretPosition / 4);
            this.value = this.lastValue = parts.join(' ');
            this.selectionStart = this.selectionEnd = caretPosition;
        }
        // Expiry date formatting
        const expDate = document.getElementById('exp');
        expDate.onkeyup = function(e) {
            if (this.value == this.lastValue) return;
            var caretPosition = this.selectionStart;
            var sanitizedValue = this.value.replace(/[^0-9]/gi, '');
            var parts = [];
            for (var i = 0, len = sanitizedValue.length; i < len; i += 2) {
                parts.push(sanitizedValue.substring(i, i + 2));
            }
            for (var i = caretPosition - 1; i >= 0; i--) {
                var c = this.value[i];
                if (c < '0' || c > '9') {
                    caretPosition--;
                }
            }
            caretPosition += Math.floor(caretPosition / 2);
            this.value = this.lastValue = parts.join('/');
            this.selectionStart = this.selectionEnd = caretPosition;
        }
    </script>
</body>
</html>