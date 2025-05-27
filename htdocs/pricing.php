<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Explore FitZone Gym's pricing packages, including General, Couple, and Premium plans tailored to your fitness needs.">
    <meta name="author" content="Dikotope T">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="generator" content="Hugo 0.84.0">
    <link rel="icon" type="image/png" href="../images/icons8-gym-50.png" sizes="16x16">
    <title>FitZone - Pricing</title>
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
        }

        section {
            min-height: 100vh;
            padding: 10rem 8% 2rem;
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

        /* Pricing Section */
        .plans {
            background: var(--second-bg-color);
            text-align: center;
        }

        .plans h2 {
            font-size: 4.5rem;
            margin-bottom: 3rem;
        }

        .plans-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, auto));
            align-items: center;
            gap: 2.3rem;
            margin-top: 4.2rem;
        }

        .box {
            padding: 30px;
            background: var(--bg-color);
            border-radius: 28px;
            border: 1px solid transparent;
            box-shadow: 0 0 5px var(--main-color);
            transition: all 0.5s ease;
            cursor: pointer;
        }

        .box h3 {
            font-size: 3.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .box h2 {
            font-size: 4.3rem;
            font-weight: 700;
            margin-bottom: 2rem;
        }

        .box h2 span {
            font-size: 2rem;
            font-weight: 400;
        }

        .box ul {
            list-style: none;
            margin-bottom: 2rem;
        }

        .box ul li {
            font-size: 1.7rem;
            padding-bottom: 1.2rem;
            text-align: left;
        }

        .box ul li::before {
            content: '✔';
            color: var(--main-color);
            margin-right: 1rem;
        }

        .box a {
            display: inline-block;
            padding: 1rem 2.8rem;
            background: var(--main-color);
            color: var(--bg-color);
            border-radius: 0.8rem;
            font-size: 1.6rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .box a:hover {
            background: #33ccaa;
            box-shadow: 0 0 10px var(--main-color);
        }

        .box:hover {
            border: 1px solid var(--main-color);
            transform: translateY(-5px) scale(1.03);
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
            .navbar {
                width: 100%;
                margin-top: 1rem;
            }

            .navbar-collapse {
                background: #000;
                padding: 1rem;
            }
        }

        @media (max-width: 786px) {
            .plans-content {
                grid-template-columns: repeat(auto-fit, minmax(250px, auto));
            }
        }

        @media (max-width: 450px) {
            html {
                font-size: 50%;
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
                        <li class="nav-item"><a class="nav-link active" href="pricing.php" aria-label="Pricing">Pricing</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section class="plans" id="plans">
        <h2 class="heading" data-aos="zoom-in-down">Our <span>Packages</span></h2>
        <div class="plans-content" data-aos="zoom-in-up">
            <div class="box">
                <h3>General</h3>
                <h2>R500<span>/Month</span></h2>
                <ul>
                    <li>Smart workout plan</li>
                    <li>1 Person</li>
                </ul>
                <a href="signup.php?id=General" aria-label="Join General Package">
                    Join Now
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="box">
                <h3>Couple</h3>
                <h2>R1000<span>/Month</span></h2>
                <ul>
                    <li>Couple Gyms</li>
                    <li>Smart workout plan</li>
                </ul>
                <a href="signup.php?id=Couple" aria-label="Join Couple Package">
                    Join Now
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="box">
                <h3>Premium</h3>
                <h2>R1500<span>/Month</span></h2>
                <ul>
                    <li>Elite Gyms & Classes</li>
                    <li>Pro Gyms</li>
                    <li>Smart workout plan</li>
                    <li>1 Person + Personal Trainer</li>
                </ul>
                <a href="signup.php?id=Premium" aria-label="Join Premium Package">
                    Join Now
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
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
    </script>
</body>
</html>