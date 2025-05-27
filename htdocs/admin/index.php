<?php
ob_start();
session_start();
include("../include/db.php");
// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Check database connection
if (!$con) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Database connection failed: " . mysqli_connect_error());
} else {
    mysqli_set_charset($con, 'utf8mb4');
}

// Regenerate session ID
session_regenerate_id(true);

// Check admin session
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit();
}

// CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize variables
$error = '';
$success = '';
$view = $_GET['view'] ?? 'dashboard';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    // Add User
    if (isset($_POST['add_user'])) {
        $email = filter_var($_POST['user_email'], FILTER_SANITIZE_EMAIL);
        $name = filter_var($_POST['user_name'], FILTER_SANITIZE_STRING);
        $surname = filter_var($_POST['user_surname'], FILTER_SANITIZE_STRING);
        $password = $_POST['password'];
        $package = filter_var($_POST['package'], FILTER_SANITIZE_STRING);
        $join_date = date('Y-m-d');
        $expiry_date = date('Y-m-d', strtotime('+1 month'));

        $query = "INSERT INTO users (user_email, user_name, user_surname, user_pass, user_package, join_date, membership_expiry) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssssss", $email, $name, $surname, $password, $package, $join_date, $expiry_date);
            if (mysqli_stmt_execute($stmt)) {
                $success = "User added successfully.";
                // Log activity
                $log_query = "INSERT INTO activity_log (user_email, action_type, action_details, timestamp) VALUES (?, ?, ?, NOW())";
                $log_stmt = mysqli_prepare($con, $log_query);
                if ($log_stmt) {
                    $action_type = "User Added";
                    $action_details = "Added user: $email";
                    mysqli_stmt_bind_param($log_stmt, "sss", $email, $action_type, $action_details);
                    mysqli_stmt_execute($log_stmt);
                    mysqli_stmt_close($log_stmt);
                }
            } else {
                $error = "Failed to add user: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Bulk User Import
    if (isset($_FILES['user_csv']) && isset($_POST['import_users'])) {
        $file = $_FILES['user_csv']['tmp_name'];
        if (($handle = fopen($file, "r")) !== FALSE) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row++;
                if ($row == 1) continue; // Skip header
                if (count($data) < 5) {
                    $error = "Invalid CSV format at row $row.";
                    break;
                }
                $email = filter_var($data[0], FILTER_SANITIZE_EMAIL);
                $name = filter_var($data[1], FILTER_SANITIZE_STRING);
                $surname = filter_var($data[2], FILTER_SANITIZE_STRING);
                $password = $data[3];
                $package = filter_var($data[4], FILTER_SANITIZE_STRING);
                $join_date = date('Y-m-d');
                $expiry_date = date('Y-m-d', strtotime('+1 month'));

                if (!in_array($package, ['General', 'Couple', 'Premium'])) {
                    $error = "Invalid package at row $row.";
                    break;
                }

                $query = "INSERT INTO users (user_email, user_name, user_surname, user_pass, user_package, join_date, membership_expiry) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($con, $query);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sssssss", $email, $name, $surname, $password, $package, $join_date, $expiry_date);
                    if (!mysqli_stmt_execute($stmt)) {
                        $error = "Failed to add user at row $row: " . mysqli_stmt_error($stmt);
                        mysqli_stmt_close($stmt);
                        break;
                    }
                    mysqli_stmt_close($stmt);
                    // Log activity
                    $log_query = "INSERT INTO activity_log (user_email, action_type, action_details, timestamp) VALUES (?, ?, ?, NOW())";
                    $log_stmt = mysqli_prepare($con, $log_query);
                    if ($log_stmt) {
                        $action_type = "User Imported";
                        $action_details = "Imported user: $email via CSV";
                        mysqli_stmt_bind_param($log_stmt, "sss", $email, $action_type, $action_details);
                        mysqli_stmt_execute($log_stmt);
                        mysqli_stmt_close($log_stmt);
                    }
                }
            }
            fclose($handle);
            if (!$error) {
                $success = "Users imported successfully.";
            }
        } else {
            $error = "Failed to open CSV file.";
        }
    }

    // Update User Package
    if (isset($_POST['update_package'])) {
        $user_email = filter_var($_POST['user_email'], FILTER_SANITIZE_EMAIL);
        $package = filter_var($_POST['package'], FILTER_SANITIZE_STRING);
        $expiry_date = filter_var($_POST['membership_expiry'], FILTER_SANITIZE_STRING);

        $query = "UPDATE users SET user_package = ?, membership_expiry = ? WHERE user_email = ?";
        $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $package, $expiry_date, $user_email);
            if (mysqli_stmt_execute($stmt)) {
                $success = "User package updated successfully.";
                // Log activity
                $log_query = "INSERT INTO activity_log (user_email, action_type, action_details, timestamp) VALUES (?, ?, ?, NOW())";
                $log_stmt = mysqli_prepare($con, $log_query);
                if ($log_stmt) {
                    $action_type = "Package Updated";
                    $action_details = "Updated package to $package for $user_email";
                    mysqli_stmt_bind_param($log_stmt, "sss", $user_email, $action_type, $action_details);
                    mysqli_stmt_execute($log_stmt);
                    mysqli_stmt_close($log_stmt);
                }
            } else {
                $error = "Failed to update package: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Add Trainer
    if (isset($_POST['add_trainer'])) {
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $specialization = filter_var($_POST['specialization'], FILTER_SANITIZE_STRING);

        $query = "INSERT INTO trainer (tran_name, tran_email, tran_class) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $specialization);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Trainer added successfully.";
            } else {
                $error = "Failed to add trainer: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Add Exercise
    if (isset($_POST['add_exercise'])) {
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
        $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);

        $query = "INSERT INTO exercises (exercise_name, exer_img, category) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $name, $description, $category);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Exercise added successfully.";
            } else {
                $error = "Failed to add exercise: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Add Class Schedule
    if (isset($_POST['add_class'])) {
        $class_name = filter_var($_POST['class_name'], FILTER_SANITIZE_STRING);
        $schedule_time = filter_var($_POST['schedule_time'], FILTER_SANITIZE_STRING);
        $max_slots = intval($_POST['max_slots']);
        $package_access = filter_var($_POST['package_access'], FILTER_SANITIZE_STRING);
        $trainer_id = intval($_POST['trainer_id']);

        $query = "INSERT INTO class_schedules (class_name, schedule_time, max_slots, package_access, tran_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssisi", $class_name, $schedule_time, $max_slots, $package_access, $trainer_id);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Class schedule added successfully.";
            } else {
                $error = "Failed to add class: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Send Notification
    if (isset($_POST['send_notification'])) {
        $user_email = filter_var($_POST['user_email'], FILTER_SANITIZE_EMAIL);
        $type = filter_var($_POST['type'], FILTER_SANITIZE_STRING);
        $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

        if ($user_email === 'all') {
            $query = "INSERT INTO notifications (user_email, type, message, created_at) 
                      SELECT user_email, ?, ?, NOW() FROM users WHERE user_email IS NOT NULL AND user_email != ''";
            $stmt = mysqli_prepare($con, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ss", $type, $message);
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Notification sent to all users.";
                } else {
                    $error = "Failed to send notification: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            $checkQuery = "SELECT user_email FROM users WHERE user_email = ?";
            $checkStmt = mysqli_prepare($con, $checkQuery);
            if ($checkStmt) {
                mysqli_stmt_bind_param($checkStmt, "s", $user_email);
                mysqli_stmt_execute($checkStmt);
                mysqli_stmt_store_result($checkStmt);

                if (mysqli_stmt_num_rows($checkStmt) > 0) {
                    $query = "INSERT INTO notifications (user_email, type, message, created_at) VALUES (?, ?, ?, NOW())";
                    $stmt = mysqli_prepare($con, $query);
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "sss", $user_email, $type, $message);
                        if (mysqli_stmt_execute($stmt)) {
                            $success = "Notification sent successfully.";
                        } else {
                            $error = "Failed to send notification: " . mysqli_stmt_error($stmt);
                        }
                        mysqli_stmt_close($stmt);
                    }
                } else {
                    $error = "User email does not exist. Notification not sent.";
                }
                mysqli_stmt_close($checkStmt);
            }
        }
    }

    // Approve Payment
    if (isset($_POST['approve_payment'])) {
        $payment_id = intval($_POST['payment_id']);
        $status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);

        $query = "UPDATE payments SET payment_status = ? WHERE payment_id = ?";
        $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $status, $payment_id);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Payment status updated.";
                $query = "UPDATE users u SET total_paid = (SELECT SUM(amount) FROM payments p WHERE p.user_email = u.user_email AND p.payment_status = 'Approved') WHERE user_email = (SELECT user_email FROM payments WHERE payment_id = ?)";
                $stmt2 = mysqli_prepare($con, $query);
                if ($stmt2) {
                    mysqli_stmt_bind_param($stmt2, "i", $payment_id);
                    mysqli_stmt_execute($stmt2);
                    mysqli_stmt_close($stmt2);
                }
            } else {
                $error = "Failed to update payment: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Update Trainer
    if (isset($_POST['update_trainer'])) {
        $trainer_id = intval($_POST['trainer_id']);
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $specialization = filter_var($_POST['specialization'], FILTER_SANITIZE_STRING);

        $query = "UPDATE trainer SET tran_name = ?, tran_email = ?, tran_class = ? WHERE tran_id = ?";
        $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $specialization, $trainer_id);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Trainer updated successfully.";
                header('Location: index.php?view=trainers');
                exit();
            } else {
                $error = "Failed to update trainer: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Update Exercise
    if (isset($_POST['update_exercise'])) {
        $exercise_id = intval($_POST['exercise_id']);
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
        $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);

        $query = "UPDATE exercises SET exercise_name = ?, exer_img = ?, category = ? WHERE exer_id = ?";
        $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssi", $name, $description, $category, $exercise_id);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Exercise updated successfully.";
                header('Location: index.php?view=exercises');
                exit();
            } else {
                $error = "Failed to update exercise: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Update Class
    if (isset($_POST['update_class'])) {
        $class_id = intval($_POST['class_id']);
        $class_name = filter_var($_POST['class_name'], FILTER_SANITIZE_STRING);
        $schedule_time = filter_var($_POST['schedule_time'], FILTER_SANITIZE_STRING);
        $max_slots = intval($_POST['max_slots']);
        $package_access = filter_var($_POST['package_access'], FILTER_SANITIZE_STRING);
        $trainer_id = intval($_POST['trainer_id']);

        $query = "UPDATE class_schedules SET class_name = ?, schedule_time = ?, max_slots = ?, package_access = ?, trainer_id = ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssisii", $class_name, $schedule_time, $max_slots, $package_access, $trainer_id, $class_id);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Class updated successfully.";
                header('Location: index.php?view=classes');
                exit();
            } else {
                $error = "Failed to update class: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="FitZone Gym admin dashboard to manage users, trainers, classes, and more.">
    <meta name="author" content="Dikotope T">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="icon" type="image/png" href="../images/icons8-gym-50.png" sizes="16x16">
    <title>FitZone - Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- AOS for animations -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" integrity="sha384-6sV1dW9H4eQhJ8fW6a/bW0JqD7N9D7fW6sV1dW9H4eQhJ8fW6a/bW0JqD7N9D7fW6" crossorigin="anonymous">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous">
    <!-- Chart.js for analytics -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.css" integrity="sha256-WXB9W0MrtM4y3KxU8KxXb9V6f2v+/+zq9eK3fR0y0qU=" crossorigin="anonymous">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
            transition: background-color 0.3s, color 0.3s;
        }

        body.light-mode {
            --bg-color: #f5f5f5;
            --second-bg-color: #ffffff;
            --text-color: #212121;
            --main-color: #26a69a;
        }

        header {
            background-color: #0a0a0a;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
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

        .nav-btn {
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            background-color: var(--main-color);
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .nav-btn:hover {
            background-color: #00897b;
        }

        .theme-toggle {
            background: none;
            border: none;
            color: var(--text-color);
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s;
        }

        .sidebar {
            background: var(--second-bg-color);
            min-height: 100vh;
            padding: 2rem;
            border-right: 1px solid rgba(38, 166, 154, 0.3);
        }

        .sidebar .nav-link {
            font-size: 1.8rem;
            color: var(--text-color);
            padding: 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: var(--main-color);
            color: #ffffff;
        }

        .sidebar .nav-link [data-feather] {
            margin-right: 1rem;
        }

        .main-content {
            padding: 4rem;
            background: var(--bg-color);
        }

        .main-content h1 {
            font-size: 4rem;
            color: var(--main-color);
            margin-bottom: 2rem;
        }

        .card {
            background: var(--second-bg-color);
            color: var(--text-color);
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
            background: var(--second-bg-color);
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

        .btn-primary, .btn-success, .btn-danger, .btn-warning {
            background: var(--main-color);
            border-color: var(--main-color);
            color: #ffffff;
            font-size: 1.6rem;
            padding: 0.8rem 1.6rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover, .btn-success:hover, .btn-danger:hover, .btn-warning:hover {
            background: #00897b;
            box-shadow: 0 0 10px rgba(38, 166, 154, 0.5);
        }

        .form-control {
            background: var(--bg-color);
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

        .alert-danger {
            background: rgba(211, 47, 47, 0.15);
            border: 1px solid #d32f2f;
        }

        .chart-container {
            background: var(--second-bg-color);
            padding: 2rem;
            border-radius: 0.5rem;
            margin-top: 2rem;
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

            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 255px;
                height: 100vh;
                transition: all 0.5s ease;
                z-index: 1000;
            }

            .sidebar.active {
                left: 0;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 2rem;
            }

            .table th, .table td {
                font-size: 1.4rem;
                padding: 0.8rem;
            }

            .btn-primary, .btn-success, .btn-danger, .btn-warning {
                font-size: 1.4rem;
                padding: 0.6rem 1.2rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1 class="logo">FitZone <span>Gym</span> - Admin</h1>
        <div class="bx bx-menu" id="menu-icon"><i class="fas fa-bars"></i></div>
        <ul class="navbar">
            <li><a href="index.php?view=dashboard" class="<?php echo $view === 'dashboard' ? 'active' : ''; ?>" aria-label="Dashboard">Dashboard</a></li>
            <li><a href="index.php?view=users" class="<?php echo $view === 'users' ? 'active' : ''; ?>" aria-label="Users">Users</a></li>
            <li><a href="index.php?view=trainers" class="<?php echo $view === 'trainers' ? 'active' : ''; ?>" aria-label="Trainers">Trainers</a></li>
            <li><a href="index.php?view=exercises" class="<?php echo $view === 'exercises' ? 'active' : ''; ?>" aria-label="Exercises">Exercises</a></li>
            <li><a href="index.php?view=classes" class="<?php echo $view === 'classes' ? 'active' : ''; ?>" aria-label="Classes">Classes</a></li>
            <li><a href="index.php?view=notifications" class="<?php echo $view === 'notifications' ? 'active' : ''; ?>" aria-label="Notifications">Notifications</a></li>
            <li><a href="index.php?view=payments" class="<?php echo $view === 'payments' ? 'active' : ''; ?>" aria-label="Payments">Payments</a></li>
            <li><a href="index.php?view=attendance" class="<?php echo $view === 'attendance' ? 'active' : ''; ?>" aria-label="Attendance">Attendance</a></li>
            <li><a href="index.php?view=activity_log" class="<?php echo $view === 'activity_log' ? 'active' : ''; ?>" aria-label="Activity Log">Activity Log</a></li>
            <li><a href="logout.php" aria-label="Sign Out">Sign Out</a></li>
        </ul>
        <div class="top-btn">
            <span class="nav-btn"><?php echo htmlspecialchars($_SESSION['admin_email']); ?></span>
            <button class="theme-toggle" aria-label="Toggle theme">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $view === 'dashboard' ? 'active' : ''; ?>" href="index.php?view=dashboard">
                                <span data-feather="home"></span> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $view === 'users' ? 'active' : ''; ?>" href="index.php?view=users">
                                <span data-feather="users"></span> Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $view === 'trainers' ? 'active' : ''; ?>" href="index.php?view=trainers">
                                <span data-feather="users"></span> Manage Trainers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $view === 'exercises' ? 'active' : ''; ?>" href="index.php?view=exercises">
                                <span data-feather="activity"></span> Manage Exercises
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $view === 'classes' ? 'active' : ''; ?>" href="index.php?view=classes">
                                <span data-feather="calendar"></span> Manage Classes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $view === 'notifications' ? 'active' : ''; ?>" href="index.php?view=notifications">
                                <span data-feather="bell"></span> Manage Notifications
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $view === 'payments' ? 'active' : ''; ?>" href="index.php?view=payments">
                                <span data-feather="dollar-sign"></span> Manage Payments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $view === 'attendance' ? 'active' : ''; ?>" href="index.php?view=attendance">
                                <span data-feather="check-square"></span> Manage Attendance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $view === 'activity_log' ? 'active' : ''; ?>" href="index.php?view=activity_log">
                                <span data-feather="activity"></span> Activity Log
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <h1>Admin Dashboard</h1>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

               <?php if ($view === 'dashboard'): ?>
    <div class="card" data-aos="fade-up">
        <h4>Analytics Overview</h4>
        <div class="row">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5>Total Users</h5>
                        <p class="fs-4">
                            <?php
                            $query = "SELECT COUNT(*) as count FROM users";
                            $result = mysqli_query($con, $query);
                            echo mysqli_fetch_assoc($result)['count'];
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5>Total Trainers</h5>
                        <p class="fs-4">
                            <?php
                            $query = "SELECT COUNT(*) as count FROM trainer";
                            $result = mysqli_query($con, $query);
                            echo mysqli_fetch_assoc($result)['count'];
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5>Active Memberships</h5>
                        <p class="fs-4">
                            <?php
                            $query = "SELECT COUNT(*) as count FROM users WHERE membership_expiry >= CURDATE()";
                            $result = mysqli_query($con, $query);
                            echo mysqli_fetch_assoc($result)['count'];
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5>Total Revenue</h5>
                        <p class="fs-4">R
                            <?php
                            $query = "SELECT SUM(amount) as total FROM payments WHERE payment_status = 'Approved'";
                            $result = mysqli_query($con, $query);
                            echo number_format(mysqli_fetch_assoc($result)['total'] ?? 0, 2);
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5>Payments Processed</h5>
                        <p class="fs-4">
                            <?php
                            $query = "SELECT COUNT(*) as count FROM payments WHERE payment_status = 'Approved'";
                            $result = mysqli_query($con, $query);
                            echo mysqli_fetch_assoc($result)['count'];
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5>Class Bookings</h5>
                        <p class="fs-4">
                            <?php
                            $query = "SELECT COUNT(*) as count FROM class_bookings";
                            $result = mysqli_query($con, $query);
                            echo mysqli_fetch_assoc($result)['count'];
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5>Upcoming Classes</h5>
                        <p class="fs-4">
                            <?php
                            $query = "SELECT COUNT(*) as count FROM class_schedules WHERE schedule_time >= NOW()";
                            $result = mysqli_query($con, $query);
                            echo mysqli_fetch_assoc($result)['count'];
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5>Pending Payments</h5>
                        <p class="fs-4">
                            <?php
                            $query = "SELECT COUNT(*) as count FROM payments WHERE payment_status = 'Pending'";
                            $result = mysqli_query($con, $query);
                            echo mysqli_fetch_assoc($result)['count'];
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <h5>Revenue Trend</h5>
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h5>Membership Distribution</h5>
                    <canvas id="membershipChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h5>Trainer Specialization</h5>
                    <canvas id="trainerChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h5>Attendance Trend</h5>
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activity Log -->
        <div class="card mt-4">
            <h4>Recent Activity</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT user_email, action_type, action_details, timestamp FROM activity_log ORDER BY timestamp DESC LIMIT 5";
                    $result = mysqli_query($con, $query);
                    while ($row = mysqli_fetch_assoc($result)):
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                            <td><?php echo htmlspecialchars($row['action_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['action_details']); ?></td>
                            <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="index.php?view=activity_log" class="btn btn-primary">View Full Log</a>
        </div>

        <!-- Pending Actions -->
        <div class="card mt-4">
            <h4>Pending Actions</h4>
            <h5>Expiring Memberships (Next 7 Days)</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Package</th>
                        <th>Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT user_email, user_package, membership_expiry FROM users WHERE membership_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) LIMIT 5";
                    $result = mysqli_query($con, $query);
                    while ($row = mysqli_fetch_assoc($result)):
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                            <td><?php echo htmlspecialchars($row['user_package']); ?></td>
                            <td><?php echo htmlspecialchars($row['membership_expiry']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="index.php?view=users" class="btn btn-primary">Manage Users</a>
        </div>
    </div>
                <?php elseif ($view === 'users'): ?>
                    <div class="card" data-aos="fade-up">
                        <h4>Manage Users</h4>
                        <form method="post" class="mb-4">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <input type="email" name="user_email" class="form-control" placeholder="Email" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <input type="text" name="user_name" class="form-control" placeholder="First Name" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <input type="text" name="user_surname" class="form-control" placeholder="Surname" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <select name="package" class="form-control" required>
                                        <option value="General">General</option>
                                        <option value="Couple">Couple</option>
                                        <option value="Premium">Premium</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                                </div>
                            </div>
                        </form>
                        <form method="post" enctype="multipart/form-data" class="mb-4">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="file" name="user_csv" class="form-control" accept=".csv" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <button type="submit" name="import_users" class="btn btn-primary">Import Users</button>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="data:text/csv;charset=utf-8,email,first_name,surname,password,package%0Auser@example.com,John,Doe,pass123,General" download="user_import_template.csv" class="btn btn-warning">Download CSV Template</a>
                                </div>
                            </div>
                        </form>
                        <button class="btn btn-success mb-3" onclick="exportTableToCSV('usersTable', 'users_export.csv')">Export to CSV</button>
                        <table class="table table-striped" id="usersTable">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Name</th>
                                    <th>Package</th>
                                    <th>Expiry</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT user_email, user_name, user_surname, user_package, membership_expiry FROM users";
                                $result = mysqli_query($con, $query);
                                while ($row = mysqli_fetch_assoc($result)):
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['user_name'] . ' ' . $row['user_surname']); ?></td>
                                        <td><?php echo htmlspecialchars($row['user_package']); ?></td>
                                        <td><?php echo htmlspecialchars($row['membership_expiry']); ?></td>
                                        <td>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="user_email" value="<?php echo $row['user_email']; ?>">
                                                <select name="package" class="form-control d-inline w-auto">
                                                    <option value="General" <?php echo $row['user_package'] === 'General' ? 'selected' : ''; ?>>General</option>
                                                    <option value="Couple" <?php echo $row['user_package'] === 'Couple' ? 'selected' : ''; ?>>Couple</option>
                                                    <option value="Premium" <?php echo $row['user_package'] === 'Premium' ? 'selected' : ''; ?>>Premium</option>
                                                </select>
                                                <input type="date" name="membership_expiry" class="form-control d-inline w-auto" value="<?php echo $row['membership_expiry']; ?>">
                                                <button type="submit" name="update_package" class="btn btn-success">Update</button>
                                            </form>
                                            <a href="index.php?view=delete_user&email=<?php echo urlencode($row['user_email']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user? This will also delete related records.');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php elseif ($view === 'trainers'): ?>
                    <div class="card" data-aos="fade-up">
                        <h4>Manage Trainers</h4>
                        <form method="post" class="mb-4">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <input type="text" name="name" class="form-control" placeholder="Name" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <input type="text" name="specialization" class="form-control" placeholder="Specialization" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <button type="submit" name="add_trainer" class="btn btn-primary">Add Trainer</button>
                                </div>
                            </div>
                        </form>
                        <table class="table table-striped" id="trainersTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Specialization</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT tran_id, tran_name, tran_email, tran_class FROM trainer";
                                $result = mysqli_query($con, $query);
                                while ($row = mysqli_fetch_assoc($result)):
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['tran_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tran_email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tran_class']); ?></td>
                                        <td>
                                            <a href="index.php?view=edit_trainer&id=<?php echo $row['tran_id']; ?>" class="btn btn-warning">Edit</a>
                                            <a href="index.php?view=delete_trainer&id=<?php echo $row['tran_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this trainer? This will also delete related class schedules.');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php elseif ($view === 'exercises'): ?>
                    <div class="card" data-aos="fade-up">
                        <h4>Manage Exercises</h4>
                        <form method="post" class="mb-4">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <input type="text" name="name" class="form-control" placeholder="Exercise Name" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <input type="text" name="description" class="form-control" placeholder="Description" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <input type="text" name="category" class="form-control" placeholder="Category" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <button type="submit" name="add_exercise" class="btn btn-primary">Add Exercise</button>
                                </div>
                            </div>
                        </form>
                        <table class="table table-striped" id="exercisesTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT exer_id, exercise_name, exer_img, category FROM exercises";
                                $result = mysqli_query($con, $query);
                                while ($row = mysqli_fetch_assoc($result)):
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['exercise_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['exer_img']); ?></td>
                                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                                        <td>
                                            <a href="index.php?view=edit_exercise&id=<?php echo $row['exer_id']; ?>" class="btn btn-warning">Edit</a>
                                            <a href="index.php?view=delete_exercise&id=<?php echo $row['exer_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this exercise?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php elseif ($view === 'classes'): ?>
                    <div class="card" data-aos="fade-up">
                        <h4>Manage Class Schedules</h4>
                        <form method="post" class="mb-4">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <input type="text" name="class_name" class="form-control" placeholder="Class Name" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <input type="datetime-local" name="schedule_time" class="form-control" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <input type="number" name="max_slots" class="form-control" placeholder="Max Slots" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <input type="text" name="package_access" class="form-control" placeholder="Packages (e.g., General,Premium)" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <select name="trainer_id" class="form-control" required>
                                        <option value="">Select Trainer</option>
                                        <?php
                                        $query = "SELECT tran_id, tran_name FROM trainer";
                                        $result = mysqli_query($con, $query);
                                        while ($row = mysqli_fetch_assoc($result)):
                                        ?>
                                            <option value="<?php echo $row['tran_id']; ?>"><?php echo htmlspecialchars($row['tran_name']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <button type="submit" name="add_class" class="btn btn-primary">Add Class</button>
                                </div>
                            </div>
                        </form>
                        <table class="table table-striped" id="classesTable">
                            <thead>
                                <tr>
                                    <th>Class Name</th>
                                    <th>Time</th>
                                    <th>Slots</th>
                                    <th>Packages</th>
                                    <th>Trainer</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT cs.class_id, cs.class_name, cs.schedule_time, cs.max_slots, cs.package_access, t.tran_name FROM class_schedules cs LEFT JOIN trainer t ON cs.tran_id = t.tran_id";
                                $result = mysqli_query($con, $query);
                                while ($row = mysqli_fetch_assoc($result)):
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['schedule_time']); ?></td>
                                        <td><?php echo htmlspecialchars($row['max_slots']); ?></td>
                                        <td><?php echo htmlspecialchars($row['package_access']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tran_name'] ?? 'N/A'); ?></td>
                                        <td>
                                            <a href="index.php?view=edit_class&id=<?php echo $row['class_id']; ?>" class="btn btn-warning">Edit</a>
                                            <a href="index.php?view=delete_class&id=<?php echo $row['class_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this class?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php elseif ($view === 'notifications'): ?>
                    <div class="card" data-aos="fade-up">
                        <h4>Send Notifications</h4>
                        <form method="post" class="mb-4">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <select name="user_email" class="form-control" required>
                                        <option value="all">All Users</option>
                                        <?php
                                        $query = "SELECT user_email FROM users";
                                        $result = mysqli_query($con, $query);
                                        while ($row = mysqli_fetch_assoc($result)):
                                        ?>
                                            <option value="<?php echo $row['user_email']; ?>"><?php echo htmlspecialchars($row['user_email']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <input type="text" name="type" class="form-control" placeholder="Type (e.g., Alert)" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <input type="text" name="message" class="form-control" placeholder="Message" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <button type="submit" name="send_notification" class="btn btn-primary">Send</button>
                                </div>
                            </div>
                        </form>
                        <table class="table table-striped" id="notificationsTable">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Read</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT notification_id, user_email, type, message, created_at, is_read FROM notifications";
                                $result = mysqli_query($con, $query);
                                while ($row = mysqli_fetch_assoc($result)):
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['type']); ?></td>
                                        <td><?php echo htmlspecialchars($row['message']); ?></td>
                                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                        <td><?php echo $row['is_read'] ? 'Yes' : 'No'; ?></td>
                                        <td>
                                            <a href="index.php?view=delete_notification&id=<?php echo $row['notification_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this notification?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php elseif ($view === 'payments'): ?>
                    <div class="card" data-aos="fade-up">
                        <h4>Manage Payments</h4>
                        <button class="btn btn-success mb-3" onclick="exportTableToCSV('paymentsTable', 'payments_export.csv')">Export to CSV</button>
                        <table class="table table-striped" id="paymentsTable">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT payment_id, user_email, amount, payment_date, payment_method, payment_status FROM payments";
                                $result = mysqli_query($con, $query);
                                while ($row = mysqli_fetch_assoc($result)):
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                                        <td>R <?php echo number_format($row['amount'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($row['payment_date']); ?></td>
                                        <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                                        <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                                        <td>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="payment_id" value="<?php echo $row['payment_id']; ?>">
                                                <select name="status" class="form-control d-inline w-auto">
                                                    <option value="Pending" <?php echo $row['payment_status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="Approved" <?php echo $row['payment_status'] === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                                    <option value="Rejected" <?php echo $row['payment_status'] === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                                </select>
                                                <button type="submit" name="approve_payment" class="btn btn-success">Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php elseif ($view === 'attendance'): ?>
                    <div class="card" data-aos="fade-up">
                        <h4>Manage Attendance</h4>
                        <button class="btn btn-success mb-3" onclick="exportTableToCSV('attendanceTable', 'attendance_export.csv')">Export to CSV</button>
                        <table class="table table-striped" id="attendanceTable">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT attendance_id, user_email, check_in, check_out FROM attendance";
                                $result = mysqli_query($con, $query);
                                while ($row = mysqli_fetch_assoc($result)):
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['check_in']); ?></td>
                                        <td><?php echo htmlspecialchars($row['check_out'] ?? 'N/A'); ?></td>
                                        <td>
                                            <a href="index.php?view=delete_attendance&id=<?php echo $row['attendance_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this attendance record?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <h4>Attendance Analytics</h4>
                        <div class="chart-container">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                <?php elseif ($view === 'activity_log'): ?>
                    <div class="card" data-aos="fade-up">
                        <h4>User Activity Log</h4>
                        <table class="table table-striped" id="activityLogTable">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Action Type</th>
                                    <th>Details</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT user_email, action_type, action_details, timestamp FROM activity_log ORDER BY timestamp DESC";
                                $result = mysqli_query($con, $query);
                                while ($row = mysqli_fetch_assoc($result)):
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['action_type']); ?></td>
                                        <td><?php echo htmlspecialchars($row['action_details']); ?></td>
                                        <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php elseif ($view === 'edit_trainer'): ?>
                    <div class="card" data-aos="fade-up">
                        <h4>Edit Trainer</h4>
                        <?php
                        $trainer_id = intval($_GET['id']);
                        $query = "SELECT tran_name, tran_email, tran_class FROM trainer WHERE tran_id = ?";
                        $stmt = mysqli_prepare($con, $query);
                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "i", $trainer_id);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $trainer = mysqli_fetch_assoc($result);
                            mysqli_stmt_close($stmt);
                        }
                        ?>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="trainer_id" value="<?php echo $trainer_id; ?>">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($trainer['tran_name']); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($trainer['tran_email']); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <input type="text" name="specialization" class="form-control" value="<?php echo htmlspecialchars($trainer['tran_class']); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <button type="submit" name="update_trainer" class="btn btn-primary">Update Trainer</button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php elseif ($view === 'edit_exercise'): ?>
                    <div class="card" data-aos="fade-up">
                        <h4>Edit Exercise</h4>
                        <?php
                        $exercise_id = intval($_GET['id']);
                        $query = "SELECT exercise_name, exer_img, category FROM exercises WHERE exer_id = ?";
                        $stmt = mysqli_prepare($con, $query);
                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "i", $exercise_id);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $exercise = mysqli_fetch_assoc($result);
                            mysqli_stmt_close($stmt);
                        }
                        ?>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="exercise_id" value="<?php echo $exercise_id; ?>">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($exercise['exercise_name']); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($exercise['exer_img']); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($exercise['category']); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <button type="submit" name="update_exercise" class="btn btn-primary">Update Exercise</button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php elseif ($view === 'edit_class'): ?>
                    <div class="card" data-aos="fade-up">
                        <h4>Edit Class Schedule</h4>
                        <?php
                        $class_id = intval($_GET['id']);
                        $query = "SELECT class_name, schedule_time, max_slots, package_access, trainer_id FROM class_schedules WHERE id = ?";
                        $stmt = mysqli_prepare($con, $query);
                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "i", $class_id);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $class = mysqli_fetch_assoc($result);
                            mysqli_stmt_close($stmt);
                        }
                        ?>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <input type="text" name="class_name" class="form-control" value="<?php echo htmlspecialchars($class['class_name']); ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <input type="datetime-local" name="schedule_time" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($class['schedule_time'])); ?>" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <input type="number" name="max_slots" class="form-control" value="<?php echo $class['max_slots']; ?>" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <input type="text" name="package_access" class="form-control" value="<?php echo htmlspecialchars($class['package_access']); ?>" required>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <select name="trainer_id" class="form-control" required>
                                        <option value="">Select Trainer</option>
                                        <?php
                                        $query = "SELECT tran_id, tran_name FROM trainer";
                                        $result = mysqli_query($con, $query);
                                        while ($row = mysqli_fetch_assoc($result)):
                                        ?>
                                            <option value="<?php echo $row['tran_id']; ?>" <?php echo $class['trainer_id'] == $row['tran_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['tran_name']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <button type="submit" name="update_class" class="btn btn-primary">Update Class</button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php elseif ($view === 'delete_user'): ?>
                    <?php
                    $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
                    $tables = ['attendance', 'notifications', 'payments', 'class_bookings'];
                    foreach ($tables as $table) {
                        $query = "DELETE FROM $table WHERE user_email = ?";
                        $stmt = mysqli_prepare($con, $query);
                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "s", $email);
                            mysqli_stmt_execute($stmt);
                            mysqli_stmt_close($stmt);
                        } else {
                            $error = "Failed to prepare statement for deleting from $table: " . mysqli_error($con);
                            break;
                        }
                    }
                    if (!$error) {
                        $query = "DELETE FROM users WHERE user_email = ?";
                        $stmt = mysqli_prepare($con, $query);
                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "s", $email);
                            if (mysqli_stmt_execute($stmt)) {
                                $success = "User and related records deleted successfully.";
                                // Log activity
                                $log_query = "INSERT INTO activity_log (user_email, action_type, action_details, timestamp) VALUES (?, ?, ?, NOW())";
                                $log_stmt = mysqli_prepare($con, $log_query);
                                if ($log_stmt) {
                                    $action_type = "User Deleted";
                                    $action_details = "Deleted user: $email";
                                    mysqli_stmt_bind_param($log_stmt, "sss", $email, $action_type, $action_details);
                                    mysqli_stmt_execute($log_stmt);
                                    mysqli_stmt_close($log_stmt);
                                }
                            } else {
                                $error = "Failed to delete user: " . mysqli_stmt_error($stmt);
                            }
                            mysqli_stmt_close($stmt);
                        } else {
                            $error = "Failed to prepare statement for deleting user: " . mysqli_error($con);
                        }
                    }
                    header('Location: index.php?view=users');
                    ob_flush();
                    ?>
                <?php elseif ($view === 'delete_trainer'): ?>
                    <?php
                    $id = intval($_GET['id']);
                    $query = "DELETE FROM class_schedules WHERE trainer_id = ?";
                    $stmt = mysqli_prepare($con, $query);
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "i", $id);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                    } else {
                        $error = "Failed to prepare statement for deleting class schedules: " . mysqli_error($con);
                    }
                    if (!$error) {
                        $query = "DELETE FROM trainer WHERE tran_id = ?";
                        $stmt = mysqli_prepare($con, $query);
                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "i", $id);
                            if (mysqli_stmt_execute($stmt)) {
                                $success = "Trainer and related class schedules deleted successfully.";
                            } else {
                                $error = "Failed to delete trainer: " . mysqli_stmt_error($stmt);
                            }
                            mysqli_stmt_close($stmt);
                        } else {
                            $error = "Failed to prepare statement for deleting trainer: " . mysqli_error($con);
                        }
                    }
                    header('Location: index.php?view=trainers');
                    ob_flush();
                    ?>
                <?php elseif ($view === 'delete_exercise'): ?>
                    <?php
                    $id = intval($_GET['id']);
                    $query = "DELETE FROM exercises WHERE exer_id = ?";
                    $stmt = mysqli_prepare($con, $query);
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "i", $id);
                        if (mysqli_stmt_execute($stmt)) {
                            $success = "Exercise deleted successfully.";
                        } else {
                            $error = "Failed to delete exercise: " . mysqli_stmt_error($stmt);
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        $error = "Failed to prepare statement for deleting exercise: " . mysqli_error($con);
                    }
                    header('Location: index.php?view=exercises');
                    ob_flush();
                    ?>
                <?php elseif ($view === 'delete_class'): ?>
                    <?php
                    $id = intval($_GET['id']);
                    $query = "DELETE FROM class_schedules WHERE class_id = ?";
                    $stmt = mysqli_prepare($con, $query);
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "i", $id);
                        if (mysqli_stmt_execute($stmt)) {
                            $success = "Class deleted successfully.";
                        } else {
                            $error = "Failed to delete class: " . mysqli_stmt_error($stmt);
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        $error = "Failed to prepare statement for deleting class: " . mysqli_error($con);
                    }
                    header('Location: index.php?view=classes');
                    ob_flush();
                    ?>
                <?php elseif ($view === 'delete_notification'): ?>
                    <?php
                    $id = intval($_GET['id']);
                    $query = "DELETE FROM notifications WHERE notification_id = ?";
                    $stmt = mysqli_prepare($con, $query);
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "i", $id);
                        if (mysqli_stmt_execute($stmt)) {
                            $success = "Notification deleted successfully.";
                        } else {
                            $error = "Failed to delete notification: " . mysqli_stmt_error($stmt);
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        $error = "Failed to prepare statement for deleting notification: " . mysqli_error($con);
                    }
                    header('Location: index.php?view=notifications');
                    ob_flush();
                    ?>
                <?php elseif ($view === 'delete_attendance'): ?>
                    <?php
                    $id = intval($_GET['id']);
                    $query = "DELETE FROM attendance WHERE attendance_id = ?";
                    $stmt = mysqli_prepare($con, $query);
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "i", $id);
                        if (mysqli_stmt_execute($stmt)) {
                            $success = "Attendance record deleted successfully.";
                        } else {
                            $error = "Failed to delete attendance: " . mysqli_stmt_error($stmt);
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        $error = "Failed to prepare statement for deleting attendance: " . mysqli_error($con);
                    }
                    header('Location: index.php?view=attendance');
                    ob_flush();
                    ?>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" integrity="sha384-YZq3gR2gP1SUk5R5vO/jV6zM4uHU8W+0Ck0W7qACWkA5A1OUsIip5NZLtwvHR0A" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha256-4oxtNMLboDsc1vFNiVFL6vR3OQGgQJCrLZuY2+HrjJI=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha256-t9UJPrESBeG2ojKTIcFLPGF7nHi2vEc7f5A2ZpH/91E=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Navbar and sidebar toggle
        const menuIcon = document.getElementById('menu-icon');
        const navbar = document.querySelector('.navbar');
        const sidebar = document.querySelector('.sidebar');
        menuIcon.addEventListener('click', () => {
            navbar.classList.toggle('active');
            sidebar.classList.toggle('active');
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
            updateCharts();
        });

        // Load saved theme
        if (localStorage.getItem('theme') === 'light') {
            document.body.classList.add('light-mode');
            themeToggle.querySelector('i').classList.replace('fa-moon', 'fa-sun');
        }

  
    