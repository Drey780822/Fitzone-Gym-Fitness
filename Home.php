<?php
session_start();
include("include/db.php");
include("include/functions.php");
// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Check database connection
if (!$con) {
    $error = "Database connection failed: " . mysqli_connect_error();
    error_log($error);
    die($error);
} else {
    mysqli_set_charset($con, 'utf8mb4');
}

// Regenerate session ID
session_regenerate_id(true);

if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$userLogged = $_SESSION['user_email'];
$results = getUser($con, $userLogged);
if (!$results) {
    $error = "Failed to fetch user data.";
    error_log("getUser failed for $userLogged");
}

// Fetch user data
$join_date = $results['join_date'] ?? date('Y-m-d');
$package = $results['package'] ?? 'General';
$total_paid = $results['total_paid'] ?? 0.00;
$expiry_date = $results['expiry_date'] ?? date('Y-m-d', strtotime($join_date . ' + 1 month'));
$payment_history = [];
$attendance_records = [];
$notifications = [];
$fitness_metrics = [];
$class_schedules = [];

// CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Define package prices for payment due check
$allowed_packages = [
    'General' => 500.00,
    'Couple' => 1000.00,
    'Premium' => 1500.00
];
$required_amount = isset($allowed_packages[$package]) ? $allowed_packages[$package] : 0;
$payment_pending = ($total_paid < $results['price']);

// Check for active check-in (for checkout button)
$has_active_checkin = false;
$query = "SELECT COUNT(*) as active FROM attendance WHERE user_email = ? AND check_out IS NULL LIMIT 1";
$stmt = mysqli_prepare($con, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $userLogged);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $has_active_checkin = mysqli_fetch_assoc($result)['active'] > 0;
    } else {
        error_log("Active check-in query failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

// Fetch payment history
$query = "SELECT amount, payment_date, payment_method, payment_status FROM payments WHERE user_email = ? ORDER BY payment_date DESC";
$stmt = mysqli_prepare($con, query: $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $userLogged);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $payment_history[] = $row;
        }
    } else {
        error_log("Payment history query failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

// Fetch attendance records
$query = "SELECT check_in, check_out FROM attendance WHERE user_email = ? ORDER BY check_in DESC";
$stmt = mysqli_prepare($con, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $userLogged);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $attendance_records[] = $row;
        }
    } else {
        $error = "Failed to fetch attendance records: " . mysqli_stmt_error($stmt);
        error_log($error);
    }
    mysqli_stmt_close($stmt);
} else {
    $error = "Failed to prepare attendance query: " . mysqli_error($con);
    error_log($error);
}

// Fetch notifications
$query = "SELECT message, created_at, is_read FROM notifications WHERE user_email = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($con, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $userLogged);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $notifications[] = $row;
        }
    } else {
        error_log("Notifications query failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

// Fetch fitness metrics
$query = "SELECT weight, height, bmi, recorded_at FROM fitness_metrics WHERE user_email = ? ORDER BY recorded_at DESC";
$stmt = mysqli_prepare($con, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $userLogged);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $fitness_metrics[] = $row;
        }
    } else {
        error_log("Fitness metrics query failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

// Fetch class schedules
$query = "SELECT class_id, class_name, schedule_time, max_slots, package_access FROM class_schedules WHERE schedule_time > NOW()";
$stmt = mysqli_prepare($con, $query);
if ($stmt) {
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $class_schedules[] = $row;
        }
    } else {
        error_log("Class schedules query failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

// Handle attendance check-in
if (isset($_POST['check_in']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $query = "INSERT INTO attendance (user_email, check_in) VALUES (?, NOW())";
    $stmt = mysqli_prepare($con, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $userLogged);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: home.php');
            echo "<script>window.location.href='home.php';</script>";
            exit();
        } else {
            $error = "Failed to record check-in.";
            error_log("Check-in failed: " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle attendance check-out
if (isset($_POST['check_out']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    if ($has_active_checkin) {
        $query = "UPDATE attendance SET check_out = NOW() WHERE user_email = ? AND check_out IS NULL ORDER BY check_in DESC LIMIT 1";
        $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $userLogged);
            if (mysqli_stmt_execute($stmt)) {
                header('Location: home.php');
                echo "<script>window.location.href='home.php';</script>";
                exit();
            } else {
                $error = "Failed to record check-out.";
                error_log("Check-out failed: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $error = "No active check-in found to check out.";
    }
}

// Handle fitness metrics submission
if (isset($_POST['submit_metrics']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $weight = floatval($_POST['weight'] ?? 0);
    $height = floatval($_POST['height'] ?? 0);
    if ($weight <= 0 || $height <= 0) {
        $error = "Weight and height must be positive numbers.";
    } else {
        $bmi = $weight / ($height * $height) * 10000; // Height in cm
        $query = "INSERT INTO fitness_metrics (user_email, weight, height, bmi, recorded_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sddd", $userLogged, $weight, $height, $bmi);
            if (mysqli_stmt_execute($stmt)) {
                header('Location: home.php');
                echo "<script>window.location.href='home.php';</script>";
                exit();
            } else {
                $error = "Failed to save fitness metrics.";
                error_log("Fitness metrics insert failed: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Handle class booking
if (isset($_POST['book_class']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $class_id = intval($_POST['class_id'] ?? 0);
    $query = "SELECT package_access, max_slots, (SELECT COUNT(*) FROM class_bookings WHERE class_id = ?) as booked_slots FROM class_schedules WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $class_id, $class_id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $class = mysqli_fetch_assoc($result);
            $allowed_packages = explode(',', $class['package_access']);
            if (!in_array($package, $allowed_packages)) {
                $error = "Your package does not allow booking this class.";
            } elseif ($class['booked_slots'] >= $class['max_slots']) {
                $error = "This class is fully booked.";
            } else {
                $query = "INSERT INTO class_bookings (user_email, class_id, booked_at) VALUES (?, ?, NOW())";
                $stmt2 = mysqli_prepare($con, $query);
                if ($stmt2) {
                    mysqli_stmt_bind_param($stmt2, "si", $userLogged, $class_id);
                    if (mysqli_stmt_execute($stmt2)) {
                        header('Location: home.php');
                        echo "<script>window.location.href='home.php';</script>";
                        exit();
                    } else {
                        $error = "Failed to book class.";
                        error_log("Class booking failed: " . mysqli_stmt_error($stmt2));
                    }
                    mysqli_stmt_close($stmt2);
                }
            }
        } else {
            $error = "Failed to verify class availability.";
            error_log("Class availability query failed: " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="FitZone Gym member dashboard to track attendance, payments, fitness metrics, and class schedules.">
    <meta name="author" content="Dikotope T">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="generator" content="Hugo 0.84.0">
    <link rel="icon" type="image/png" href="../images/icons8-gym-50.png" sizes="16x16">
    <title>FitZone - Member Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- AOS for animations -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" integrity="sha384-6sV1dW9H4eQhJ8fW6a/bW0JqD7N9D7fW6sV1dW9H4eQhJ8fW6a/bW0JqD7N9D7fW6" crossorigin="abulous">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous">
    <!-- Chart.js for BMI chart -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.css" integrity="sha256-WXB9W0MrtM4y3KxU8KxXb9V6f2v+/+zq9eK3fR0y0qU=" crossorigin="anonymous">
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
        .row{
            background-color: white;
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
            transition: background-color 0.3s, color 0.3s;
        }

        /* Light mode styles */
        body.light-mode {
            --bg-color: #f5f5f5;
            --second-bg-color: #ffffff;
            --text-color: #212121;
            --main-color: #26a69a;
        }

        section {
            padding: 10rem 8% 2rem;
        }

        /* Header Section */
        header {
            background-color: #0a0a0a;
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
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .nav-btn:hover, .nav-btn1:hover {
            background-color: #00897b;
        }

        /* Theme toggle button */
        .theme-toggle {
            background: none;
            border: none;
            color: var(--text-color);
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s;
        }

        /* Dashboard Section */
        .dashboard {
            background: var(--second-bg-color);
            min-height: 100vh;
            padding: 10rem 8% 2rem;
        }

        .dashboard h2 {
            font-size: 4rem;
            color: var(--main-color);
            margin-bottom: 2rem;
        }

        .card {
            background: var(--bg-color);
            border: 1px solid var(--main-color);
            border-radius: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 0 10px rgba(38, 166, 154, 0.3);
            padding: 2rem;
        }

        .card h4 {
            font-size: 2.5rem;
            color: var(--main-color);
            margin-bottom: 1.5rem;
        }

        .table {
            color: var(--text-color);
            background: var(--bg-color);
            width: 100%;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(38, 166, 154, 0.1);
        }

        .table th, .table td {
            border-color: rgba(38, 166, 154, 0.3);
            padding: 1rem;
            font-size: 1.6rem;
        }

        .btn-primary, .btn-success, .btn-info, .btn-warning {
            background: var(--main-color);
            border-color: var(--main-color);
            color: #ffffff;
            font-size: 1.6rem;
            padding: 0.8rem 1.6rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover, .btn-success:hover, .btn-info:hover, .btn-warning:hover {
            background: #00897b;
            box-shadow: 0 0 10px rgba(38, 166, 154, 0.5);
        }

        .form-control {
            background: var(--second-bg-color);
            border: 1px solid var(--main-color);
            color: var(--text-color);
            border-radius: 0.5rem;
            font-size: 1.6rem;
            padding: 0.8rem;
        }

        .form-control:focus {
            border-color: #00897b;
            box-shadow: 0 0 5px rgba(38, 166, 154, 0.5);
        }

        .alert {
            background: rgba(38, 166, 154, 0.15);
            border: 1px solid var(--main-color);
            color: var(--text-color);
            border-radius: 0.5rem;
            font-size: 1.6rem;
            padding: 1rem;
        }

        .notification-unread {
            background: rgba(211, 47, 47, 0.15) !important;
            border-left: 4px solid #d32f2f;
        }

        .chart-container {
            background: var(--bg-color);
            padding: 2rem;
            border-radius: 0.5rem;
            margin-top: 2rem;
        }

        .profile-img {
            border: 2px solid var(--main-color);
            padding: 0.2rem;
            border-radius: 50%;
        }

        /* Footer */
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
            color: #ffffff;
            box-shadow: 0 0 15px rgba(38, 166, 154, 0.5);
        }

        .footer .copyright {
            margin-top: 20px;
            font-size: 16px;
            color: var(--text-color);
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 15px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: var(--main-color);
            width: 50px;
        }

        /* Modal styles */
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

        /* Responsive design */
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

        @media (max-width: 768px) {
            .card {
                padding: 1.5rem;
            }

            .table th, .table td {
                font-size: 1.4rem;
                padding: 0.8rem;
            }

            .btn-primary, .btn-success, .btn-info, .btn-warning {
                font-size: 1.4rem;
                padding: 0.6rem 1.2rem;
            }
        }

        @media (max-width: 450px) {
            html {
                font-size: 50%;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1 class="logo">FitZone <span>Gym</span></h1>
        <div class="bx bx-menu" id="menu-icon"><i class="fas fa-bars"></i></div>
        <ul class="navbar">
            <li><a href="home.php" class="active" aria-label="Dashboard">Dashboard</a></li>
            <li><a href="#attendance" aria-label="Attendance">Attendance</a></li>
            <li><a href="#payments" aria-label="Payments">Payments</a></li>
            <li><a href="#notifications" aria-label="Notifications">Notifications</a></li>
            <li><a href="#fitness-tracker" aria-label="Fitness Tracker">Fitness Tracker</a></li>
            <li><a href="#class-schedule" aria-label="Class Schedule">Class Schedule</a></li>
            <li><a href="#" data-bs-toggle="modal" data-bs-target="#scheduleModal" aria-label="View class schedule">Schedule</a></li>
            <li><a href="logout.php" aria-label="Sign Out">Sign Out</a></li>
        </ul>
        <div class="top-btn">
            <a href="member_edit.php" class="nav-btn" aria-label="Edit Profile">Edit Profile</a>
            <a href="unregister_code.php" class="nav-btn" aria-label="Edit Profile">Deregister</a>
            <button class="theme-toggle" aria-label="Toggle theme">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </header>

    <section class="dashboard" id="dashboard">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <h2>Welcome <strong><?php echo htmlspecialchars($results['user_name']); ?></strong></h2>

        <!-- Profile -->
        <h2 id="profile">Profile</h2>
        <div class="card" data-aos="fade-up">
            <div class="row">
                <div class="col-md-4 text-center">
                    <img src="../images/userimg.png" alt="Profile" class="profile-img" width="150">
                    <h4><?php echo htmlspecialchars($results['user_name']); ?></h4>
                    <p>Package: <?php echo htmlspecialchars($package); ?></p>
                    <p>Location: TUT, Witbank, RSA</p>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-sm-3"><strong>Full Name</strong></div>
                        <div class="col-sm-9"><?php echo htmlspecialchars($results['user_name'] . ' ' . $results['user_surname']); ?></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3"><strong>Email</strong></div>
                        <div class="col-sm-9"><?php echo htmlspecialchars($results['user_email']); ?></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3"><strong>Weight</strong></div>
                        <div class="col-sm-9"><?php echo htmlspecialchars($results['user_weight'] ?? 'N/A'); ?> kg</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3"><strong>Contact</strong></div>
                        <div class="col-sm-9"><?php echo htmlspecialchars($results['user_contact'] ?? 'N/A'); ?></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3"><strong>Join Date</strong></div>
                        <div class="col-sm-9"><?php echo htmlspecialchars($join_date); ?></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3"><strong>Expiry Date</strong></div>
                        <div class="col-sm-9"><?php echo htmlspecialchars($expiry_date); ?></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3"><strong>Total Paid</strong></div>
                        <div class="col-sm-9">R <?php echo number_format($total_paid, 2); ?></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3"><strong>Status</strong></div>
                        <div class="col-sm-9"><?php echo (date('Y-m-d') > $expiry_date) ? 'Expired' : 'Active'; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Tracker -->
        <h2 id="attendance">Attendance Tracker</h2>
        <div class="card" data-aos="fade-up">
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit" name="check_in" class="btn btn-primary">Check In</button>
                <button type="submit" name="check_out" class="btn btn-primary" <?php echo $has_active_checkin ? '' : 'disabled'; ?>>Check Out</button>
            </form>
            <h4>Recent Attendance</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Check In</th>
                        <th>Check Out</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_records as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['check_in']); ?></td>
                            <td><?php echo htmlspecialchars($record['check_out'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h4>Weekly Summary</h4>
            <p>Visits this week: <?php
                $week_start = date('Y-m-d', strtotime('monday this week'));
                $count = 0;
                foreach ($attendance_records as $record) {
                    if (strtotime($record['check_in']) >= strtotime($week_start)) {
                        $count++;
                    }
                }
                echo $count;
            ?></p>
        </div>

        <!-- Payments -->
        <h2 id="payments">Payments</h2>
        <div class="card" data-aos="fade-up">
            <?php if ($payment_pending): ?>
                <div class="alert alert-warning">Payment of R<?php echo number_format($required_amount, 2); ?> is due for your <?php echo htmlspecialchars($package); ?> package.</div>
                <a href="paycard.php" class="btn btn-success">Make Payment</a>
            <?php else: ?>
                <div class="alert alert-success">Your membership payment is up to date.</div>
            <?php endif; ?>
            <h4>Payment History</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payment_history as $payment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                            <td>R <?php echo number_format($payment['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                            <td><?php echo htmlspecialchars($payment['payment_status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Notifications -->
        <h2 id="notifications">Notifications</h2>
        <div class="card" data-aos="fade-up">
            <?php foreach ($notifications as $notification): ?>
                <div class="alert alert-info <?php echo $notification['is_read'] ? '' : 'notification-unread'; ?>">
                    <strong><?php echo htmlspecialchars($notification['type'] ?? 'Notification'); ?>:</strong> <?php echo htmlspecialchars($notification['message']); ?>
                    <br><small><?php echo htmlspecialchars($notification['created_at']); ?></small>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Fitness Tracker -->
        <h2 id="fitness-tracker">Fitness Tracker</h2>
        <div class="card" data-aos="fade-up">
            <form method="post" class="mb-4">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group mb-3">
                    <label for="weight">Weight (kg)</label>
                    <input type="number" step="0.1" name="weight" class="form-control" min="1" required>
                </div>
                <div class="form-group mb-3">
                    <label for="height">Height (cm)</label>
                    <input type="number" step="0.1" name="height" class="form-control" min="1" required>
                </div>
                <button type="submit" name="submit_metrics" class="btn btn-primary">Submit</button>
            </form>
            <h4>Recent Metrics</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Weight (kg)</th>
                        <th>Height (cm)</th>
                        <th>BMI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fitness_metrics as $metric): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($metric['recorded_at']); ?></td>
                            <td><?php echo number_format($metric['weight'], 1); ?></td>
                            <td><?php echo number_format($metric['height'], 1); ?></td>
                            <td><?php echo number_format($metric['bmi'], 1); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="chart-container">
                <canvas id="bmiChart"></canvas>
            </div>
        </div>

        <!-- Class Schedule -->
        <h2 id="class-schedule">Class Schedule</h2>
        <div class="card" data-aos="fade-up">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Time</th>
                        <th>Slots Available</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($class_schedules as $schedule): ?>
                        <?php
                        $allowed_packages = explode(',', $schedule['package_access']);
                        if (!in_array($package, $allowed_packages)) continue;
                        $query = "SELECT COUNT(*) as booked FROM class_bookings WHERE class_id = ?";
                        $stmt = mysqli_prepare($con, $query);
                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "i", $schedule['id']);
                            if (mysqli_stmt_execute($stmt)) {
                                $result = mysqli_stmt_get_result($stmt);
                                $booked = mysqli_fetch_assoc($result)['booked'];
                                $slots_available = $schedule['max_slots'] - $booked;
                            } else {
                                error_log("Class bookings count failed: " . mysqli_stmt_error($stmt));
                                $slots_available = 0;
                            }
                            mysqli_stmt_close($stmt);
                        }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($schedule['class_name']); ?></td>
                            <td><?php echo htmlspecialchars($schedule['schedule_time']); ?></td>
                            <td><?php echo $slots_available; ?></td>
                            <td>
                                <?php if ($slots_available > 0): ?>
                                    <form method="post">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="class_id" value="<?php echo $schedule['id']; ?>">
                                        <button type="submit" name="book_class" class="btn btn-primary">Book</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-danger">Full</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($package !== 'General'): ?>
            <h4>
                <label>You can schedule a training session with one of our trainers:</label>
                <a href="schedule.php"><button type="button" class="btn btn-warning">Schedule</button></a>
            </h4>
        <?php endif; ?>
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
                    <table class="table table-striped table-bordered">
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
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha256-4oxtNMLboDsc1vFNiVFL6vR3OQGgQJCrLZuY2+HrjJI=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha256-t9UJPrESBeG2ojKTIcFLPGF7nHi2vEc7f5A2ZpH/91E=" crossorigin="anonymous"></script>
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
        if (typeof feather === 'undefined') {
            console.warn('Feather Icons not loaded, attempting fallback...');
            document.write('<script src="../assets/feather.min.js"><\/script>');
        }
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js not loaded, attempting fallback...');
            document.write('<script src="../assets/chart.min.js"><\/script>');
        }

        // Initialize AOS and modal
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

            if (typeof feather !== 'undefined') {
                feather.replace();
            } else {
                console.warn('Feather Icons not available.');
            }
        });

        // Navbar toggle
        const menuIcon = document.getElementById('menu-icon');
        const navbar = document.querySelector('.navbar');
        menuIcon.addEventListener('click', () => {
            navbar.classList.toggle('active');
            const icon = menuIcon.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
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

        // BMI Chart
        const bmiData = <?php
            $labels = [];
            $data = [];
            foreach (array_reverse($fitness_metrics) as $metric) {
                $labels[] = substr($metric['recorded_at'], 0, 10);
                $data[] = $metric['bmi'];
            }
            echo json_encode(['labels' => $labels, 'data' => $data]);
        ?>;
        try {
            const isLightMode = document.body.classList.contains('light-mode');
            const gridColor = isLightMode ? '#cccccc' : '#444444';
            const tickColor = isLightMode ? '#212121' : '#e0e0e0';
            const ctx = document.getElementById('bmiChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: bmiData.labels,
                    datasets: [{
                        label: 'BMI',
                        data: bmiData.data,
                        borderColor: '#26a69a',
                        backgroundColor: 'rgba(38, 166, 154, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: { color: tickColor }
                        },
                        x: {
                            grid: { color: gridColor },
                            ticks: { color: tickColor }
                        }
                    },
                    plugins: {
                        legend: { labels: { color: tickColor } }
                    }
                }
            });

            // Update chart on theme change
            themeToggle.addEventListener('click', () => {
                const chart = Chart.getChart('bmiChart');
                const isLightMode = document.body.classList.contains('light-mode');
                const gridColor = isLightMode ? '#cccccc' : '#444444';
                const tickColor = isLightMode ? '#212121' : '#e0e0e0';
                chart.options.scales.y.grid.color = gridColor;
                chart.options.scales.x.grid.color = gridColor;
                chart.options.scales.y.ticks.color = tickColor;
                chart.options.scales.x.ticks.color = tickColor;
                chart.options.plugins.legend.labels.color = tickColor;
                chart.update();
            });
        } catch (e) {
            console.error('Chart.js failed to render:', e);
        }
    </script>
</body>
</html>