<?php
session_start();
include("include/db.php");

if (!isset($_SESSION['user_email']) || empty($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}

$userLogged = $_SESSION['user_email'];

if (isset($_POST['unregister'])) {
    $stmt = $con->prepare("DELETE FROM users WHERE user_email = ?");
    $stmt->bind_param("s", $userLogged);
    if ($stmt->execute()) {
        session_destroy();
        echo "<script>alert('Your account and package have been unregistered.'); window.location.href = 'logout.php';</script>";
    } else {
        echo "<script>alert('Error unregistering. Please try again.');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Confirm deregistration from FitZone Gym package.">
    <meta name="author" content="Dikotope T">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="generator" content="Hugo 0.84.0">
    <link rel="icon" type="image/png" href="../images/icons8-gym-50.png" sizes="16x16">
    <title>FitZone - Deregister</title>
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

        /* Header Section */
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

        .nav-btn {
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            background-color: var(--main-color);
            color: var(--bg-color);
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .nav-btn:hover {
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

        /* Deregister Section */
        .deregister {
            background: var(--second-bg-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 10rem 8% 2rem;
        }

        .form-deregister {
            background: var(--bg-color);
            padding: 3rem;
            border-radius: 1.5rem;
            box-shadow: 0 0 20px rgba(69, 255, 202, 0.2);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        .form-deregister img {
            margin-bottom: 2rem;
        }

        .form-deregister h2 {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--main-color);
        }

        .form-deregister p {
            font-size: 1.6rem;
            margin-bottom: 2rem;
            color: var(--text-color);
        }

        .form-deregister .btn {
            padding: 1rem 2rem;
            font-size: 1.6rem;
            font-weight: bold;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            margin: 0.5rem;
        }

        .form-deregister .btn-danger {
            background: #ff5555;
            border-color: #ff5555;
            color: #fff;
        }

        .form-deregister .btn-danger:hover {
            background: #cc4444;
            box-shadow: 0 0 10px #ff5555;
        }

        .form-deregister .btn-secondary {
            background: transparent;
            border: 1px solid var(--main-color);
            color: var(--main-color);
        }

        .form-deregister .btn-secondary:hover {
            background: var(--main-color);
            color: var(--bg-color);
            box-shadow: 0 0 10px var(--main-color);
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
            color: #131313;
            box-shadow: 0 0 25px var(--main-color);
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

        @media (max-width: 450px) {
            html {
                font-size: 50%;
            }

            .form-deregister {
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
            <li><a href="home.php" aria-label="Dashboard">Dashboard</a></li>
            <li><a href="home.php#attendance" aria-label="Attendance">Attendance</a></li>
            <li><a href="home.php#payments" aria-label="Payments">Payments</a></li>
            <li><a href="home.php#notifications" aria-label="Notifications">Notifications</a></li>
            <li><a href="home.php#fitness-tracker" aria-label="Fitness Tracker">Fitness Tracker</a></li>
            <li><a href="home.php#class-schedule" aria-label="Class Schedule">Class Schedule</a></li>
            <li><a href="#" data-bs-toggle="modal" data-bs-target="#scheduleModal" aria-label="View class schedule">Schedule</a></li>
            <li><a href="logout.php" aria-label="Sign Out">Sign Out</a></li>
        </ul>
        <div class="top-btn">
            <a href="home.php" class="nav-btn" aria-label="Back to Dashboard">Back</a>
            <button class="theme-toggle" aria-label="Toggle theme">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </header>

    <section class="deregister" id="deregister">
        <main class="form-deregister" data-aos="zoom-in-up">
            <img class="mb-4" src="../images/icons8-gym-50.png" alt="FitZone Logo" width="72" height="57">
            <h2>Deregister from Package</h2>
            <p>Are you sure you want to deregister? This will delete your account and cannot be undone.</p>
            <form action="" method="post" onsubmit="return confirm('Are you sure you want to deregister? This action cannot be undone.');">
                <button type="submit" name="unregister" class="btn btn-danger">Confirm Deregistration</button>
                <a href="home.php" class="btn btn-secondary">Cancel</a>
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
    </script>
</body>
</html>