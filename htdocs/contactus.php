<?php
session_start();
include("include/db.php");

// Fetch stats for the Stats Section
$stats = [
    'users' => $con->query("SELECT COUNT(*) FROM users")->fetch_row()[0],
    'trainers' => $con->query("SELECT COUNT(*) FROM trainer")->fetch_row()[0],
    'classes' => $con->query("SELECT COUNT(*) FROM class_schedules")->fetch_row()[0] // Assuming 'classes' table exists
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Contact FitZone Gym for inquiries, feedback, or to learn more about our fitness services and membership packages.">
    <meta name="author" content="Dikotope T">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="generator" content="Hugo 0.84.0">
    <link rel="icon" type="image/png" href="../images/icons8-gym-50.png" sizes="16x16">
    <title>FitZone - Contact Us</title>
    <link href="../styles.css" rel="stylesheet">
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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
            transition: background-color 0.3s, color 0.3s;
        }

        body.light-mode {
            --bg-color: #f4f4f4;
            --second-bg-color: #fff;
            --text-color: #333;
            --main-color: #33ccaa;
        }

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

        .nav-btn {
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            background-color: var(--main-color);
            color: var(--bg-color);
            text-decoration: none;
            font-weight: bold;
            transition: transform 0.2s, background-color 0.3s;
        }

        .nav-btn:hover {
            background-color: #33ccaa;
            transform: scale(1.05);
        }

        .scroll-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background-color: var(--main-color);
            color: var(--bg-color);
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            text-decoration: none;
            transition: opacity 0.3s;
        }

        .scroll-to-top.show {
            display: flex;
        }

        footer {
            margin-top: 2rem;
            text-align: center;
            padding: 2rem;
            background-color: var(--bg-color);
            color: #aaa;
        }

        .social-icons a {
            color: var(--main-color);
            font-size: 1.5rem;
            margin: 0 0.5rem;
            transition: color 0.3s;
        }

        .social-icons a:hover {
            color: #33ccaa;
        }

        /* Hero Section */
        .hero {
            position: relative;
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow: hidden;
        }

        .hero-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
            opacity: 0.7;
        }

        .hero-content {
            text-align: center;
            max-width: 700px;
            background: rgba(0, 0, 0, 0.5);
            padding: 2rem;
            border-radius: 1rem;
        }

        .hero-content h1 {
            font-size: 3rem;
            margin: 0.5rem 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero-content p {
            font-size: 1.6rem;
            margin: 0.5rem 0;
        }

        /* Contact Section */
        .contact {
            padding: 4rem 2rem;
            background: var(--second-bg-color);
            text-align: center;
        }

        .contact h2 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
        }

        .contact p {
            font-size: 1.4rem;
            margin-bottom: 2rem;
        }

        .contact form {
            max-width: 600px;
            margin: 0 auto;
        }

        .contact .form-label {
            font-size: 1.4rem;
            color: var(--text-color);
        }

        .contact .form-control {
            background: var(--bg-color);
            color: var(--text-color);
            border: 1px solid var(--main-color);
            font-size: 1.4rem;
            margin-bottom: 1.5rem;
        }

        .contact .form-control:focus {
            border-color: #33ccaa;
            box-shadow: 0 0 5px var(--main-color);
        }

        .contact .btn-primary {
            background: var(--main-color);
            color: var(--bg-color);
            font-size: 1.4rem;
            padding: 0.8rem 2rem;
            border-radius: 0.5rem;
            transition: background-color 0.3s;
        }

        .contact .btn-primary:hover {
            background: #33ccaa;
        }

        .contact .btn-primary:disabled {
            background: #555;
            cursor: not-allowed;
        }

        .alert {
            font-size: 1.4rem;
            margin-top: 1rem;
        }

        /* Contact Info Section */
        .contact-info {
            padding: 4rem 2rem;
            background: var(--bg-color);
            text-align: center;
        }

        .contact-info h2 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
        }

        .contact-info .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .info-item {
            padding: 1.5rem;
            background: var(--second-bg-color);
            border-radius: 0.5rem;
        }

        .info-item i {
            font-size: 2rem;
            color: var(--main-color);
            margin-bottom: 0.5rem;
        }

        .info-item p {
            font-size: 1.4rem;
            margin: 0;
        }

        /* Map Section */
        .map {
            padding: 4rem 2rem;
            background: var(--second-bg-color);
            text-align: center;
        }

        .map h2 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
        }

        .map iframe {
            width: 100%;
            max-width: 1200px;
            height: 400px;
            border: 0;
            border-radius: 0.5rem;
        }

        /* FAQ Section */
        .faq {
            padding: 4rem 2rem;
            background: var(--bg-color);
            text-align: center;
        }

        .faq h2 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
        }

        .accordion-item {
            background: var(--second-bg-color);
            border: 1px solid var(--main-color);
            margin-bottom: 1rem;
            border-radius: 0.5rem;
        }

        .accordion-button {
            background: var(--bg-color);
            color: var(--text-color);
            font-size: 1.4rem;
        }

        .accordion-button:not(.collapsed) {
            background: var(--main-color);
            color: var(--bg-color);
        }

        .accordion-body {
            font-size: 1.4rem;
            color: var(--text-color);
        }

        /* Stats Section */
        .stats {
            padding: 4rem 2rem;
            background: var(--second-bg-color);
            text-align: center;
        }

        .stats h2 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
        }

        .stat-item {
            display: inline-block;
            margin: 1rem 2rem;
        }

        .stat-number {
            font-size: 3rem;
            color: var(--main-color);
            font-weight: bold;
        }

        /* Testimonial Carousel */
        .testimonials {
            padding: 4rem 2rem;
            background: var(--bg-color);
        }

        .testimonials h2 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 2rem;
        }

        .carousel-item img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto;
        }

        .carousel-caption {
            background: rgba(0, 0, 0, 0.7);
            border-radius: 0.5rem;
            padding: 1rem;
        }

        /* Instagram Feed */
        .insta-feed {
            padding: 4rem 2rem;
            background: var(--second-bg-color);
            text-align: center;
        }

        .insta-feed h2 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
        }

        .insta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .insta-grid img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 0.5rem;
        }

        /* Progress Tracker Preview */
        .progress-preview {
            padding: 4rem 2rem;
            background: var(--bg-color);
            text-align: center;
        }

        .progress-bar {
            width: 80%;
            height: 20px;
            background: #333;
            border-radius: 10px;
            margin: 1rem auto;
            overflow: hidden;
        }

        .progress-fill {
            width: 70%;
            height: 100%;
            background: var(--main-color);
            transition: width 1s ease-in-out;
        }

        /* Modal Styles */
        .modal-content {
            background-color: var(--second-bg-color);
            color: var(--text-color);
        }

        .modal-content .table th,
        .modal-content .table td {
            border-color: var(--main-color);
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

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .map iframe {
                height: 300px;
            }
        }
    </style>
</head>
<body>
    <header role="banner">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <h1 class="logo">FitZone <span>Gym</span></h1>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php" aria-label="Home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="aboutus.php" aria-label="About Us">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="services.php" aria-label="Services">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="classes.php" aria-label="Classes">Classes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="trainers.php" aria-label="Trainers">Trainers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="blog.php" aria-label="Blog">Blog</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="faqs.php" aria-label="FAQs">FAQs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="contactus.php" aria-label="Contact us">Contact us</a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#scheduleModal" aria-label="View class schedule">Schedule</a>
                        </li> -->
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section id="hero" class="hero">
        <video class="hero-video" autoplay muted loop playsinline poster="../images/gv4.jpeg">
            <source src="../images/gymvid3.mp4" type="video/mp4">
            <img src="../images/gv3.jpeg" alt="FitZone Gym Contact Hero Image">
        </video>
        <div class="hero-content" data-aos="zoom-in">
            <h1>Contact Us</h1>
            <p>Reach out to start your fitness journey or get answers to your questions.</p>
            <a href="#contact" class="nav-btn" aria-label="Contact us now">Get in Touch</a>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact" id="contact">
        <h2 data-aos="zoom-in-down">Contact <span>Us</span></h2>
        <p data-aos="fade-up">We’re here to help! Send us a message, and our team will respond promptly.</p>
        <form id="contactForm" data-aos="zoom-in-up">
            <div class="mb-3">
                <label class="form-label" for="name">Name</label>
                <input class="form-control" id="name" type="text" placeholder="Enter your name" required aria-describedby="nameHelp" />
                <div id="nameHelp" class="form-text">Your full name.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="emailAddress">Email Address</label>
                <input class="form-control" id="emailAddress" type="email" placeholder="Enter your email" required aria-describedby="emailHelp" />
                <div id="emailHelp" class="form-text">We'll use this to reply to you.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="phone">Phone Number</label>
                <input class="form-control" id="phone" type="tel" placeholder="Enter your phone number" aria-describedby="phoneHelp" />
                <div id="phoneHelp" class="form-text">Optional, for faster communication.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="message">Message</label>
                <textarea class="form-control" id="message" placeholder="Enter your message" style="height: 10rem;" required aria-describedby="messageHelp"></textarea>
                <div id="messageHelp" class="form-text">Tell us how we can assist you.</div>
            </div>
            <div class="d-grid">
                <button class="btn btn-primary btn-lg" type="submit" id="submitButton">
                    <span id="submitText">Submit</span>
                    <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
            <div id="formMessage" class="alert d-none" role="alert"></div>
        </form>
    </section>

    <!-- Contact Info Section -->
    <section class="contact-info" data-aos="fade-up">
        <h2>Our Contact Details</h2>
        <div class="info-grid">
            <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <p>123 Fitness Street, Cape Town, South Africa</p>
            </div>
            <div class="info-item">
                <i class="fas fa-phone"></i>
                <p>+27 79 769 2900</p>
            </div>
            <div class="info-item">
                <i class="fas fa-envelope"></i>
                <p>info@fitzonegym.co.za</p>
            </div>
            <div class="info-item">
                <i class="fas fa-clock"></i>
                <p>Mon-Fri: 6AM-9PM<br>Sat: 8AM-6PM<br>Sun: 8AM-2PM</p>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map" data-aos="fade-up">
        <h2>Find Us</h2>
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3309.961110155396!2d18.423145315207885!3d-33.91886198064567!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1dcc5d7e8c4e4b7b%3A0x3b1e8c1e8c4e4b7b!2sCape%20Town%2C%20South%20Africa!5e0!3m2!1sen!2sza!4v1634567890123" allowfullscreen="" loading="lazy" aria-label="FitZone Gym Location"></iframe>
    </section>

    <!-- FAQ Section -->
    <section class="faq" data-aos="fade-up">
        <h2>Frequently Asked Questions</h2>
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="faq1">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                        What are your membership options?
                    </button>
                </h2>
                <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        We offer three packages: General (R500/month), Couple (R1000/month), and Premium (R1500/month). Visit our <a href="pricing.php">pricing page</a> for details.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faq2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                        How can I book a consultation?
                    </button>
                </h2>
                <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Use the contact form above or call us at +27 79 769 2900 to schedule a free consultation with our trainers.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faq3">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                        Do you offer trial sessions?
                    </button>
                </h2>
                <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes, we offer a free trial class. Contact us to book your session!
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats" data-aos="fade-up">
        <h2>Why Choose FitZone?</h2>
        <div class="stat-item">
            <div class="stat-number"><?php echo $stats['users']; ?></div>
            <p>Happy Members</p>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo $stats['trainers']; ?></div>
            <p>Expert Trainers</p>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo $stats['classes']; ?></div>
            <p>Weekly Classes</p>
        </div>
    </section>

    <!-- Testimonial Carousel -->

    <!-- Instagram Feed 
    <section class="insta-feed" data-aos="fade-up">
        <h2>Join Our Community</h2>
        <div class="insta-grid">
            <img src="images/bg1.jpg" alt="Gym workout" loading="lazy">
            <img src="images/bg2.jpg" alt="Group class" loading="lazy">
            <img src="images/bg3.jpg" alt="Member transformation" loading="lazy">
            <img src="images/Fat Lose.jpg" alt="Gym equipment" loading="lazy">
        </div>
    </section>-->

    <!-- Progress Tracker Preview 
    <section class="progress-preview" data-aos="fade-up">
        <h2>Track Your Progress</h2>
        <p>Monitor your workouts and goals in our member portal!</p>
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
        <a href="pricing.php" class="nav-btn" aria-label="Sign up to track progress">Start Tracking Now</a>
    </section>-->

    <!-- Schedule Modal
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
                                <td>Tuesday</td>
                                <td>HIIT</td>
                                <td>5:00 PM - 6:00 PM</td>
                            </tr>
                            <tr>
                                <td>Wednesday</td>
                                <td>Strength Training</td>
                                <td>5:30 PM - 6:30 PM</td>
                            </tr>
                            <tr>
                                <td>Thursday</td>
                                <td>Spin</td>
                                <td>6:30 PM - 7:30 PM</td>
                            </tr>
                            <tr>
                                <td>Friday</td>
                                <td>Cardio Blast</td>
                                <td>7:00 PM - 8:00 PM</td>
                            </tr>
                            <tr>
                                <td>Saturday</td>
                                <td>Zumba</td>
                                <td>10:00 AM - 11:00 AM</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <a href="classes.php" class="nav-btn" aria-label="View full schedule">View Full Schedule</a>
                    <button type="button" class="nav-btn" data-bs-dismiss="modal" aria-label="Close modal">Close</button>
                </div>
            </div>
        </div>
    </div>-->

    <!-- Scroll-to-Top Button -->
    <a href="#" class="scroll-to-top" aria-label="Scroll to top">
        <i class="fas fa-chevron-up"></i>
    </a>

    <footer>
        <p>© 2025 FitZone Gym. All rights reserved.</p>
        <div class="social-icons">
            <a href="https://instagram.com/fitzonegym" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://twitter.com/fitzonegym" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="https://facebook.com/fitzonegym" target="_blank" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous" defer></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12" defer></script>
    <script>
        // Initialize AOS
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 1000,
                    once: true
                });
            }

            // Scroll-to-top button
            const scrollToTopBtn = document.querySelector('.scroll-to-top');
            window.addEventListener('scroll', () => {
                scrollToTopBtn.classList.toggle('show', window.scrollY > 300);
            });

            // Contact form handling
            const contactForm = document.getElementById('contactForm');
            const submitButton = document.getElementById('submitButton');
            const submitText = document.getElementById('submitText');
            const spinner = document.getElementById('spinner');
            const formMessage = document.getElementById('formMessage');

            contactForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                submitButton.disabled = true;
                submitText.textContent = 'Submitting...';
                spinner.classList.remove('d-none');
                formMessage.classList.add('d-none');

                // Client-side validation
                const name = document.getElementById('name').value.trim();
                const email = document.getElementById('emailAddress').value.trim();
                const message = document.getElementById('message').value.trim();

                if (name.length < 2) {
                    formMessage.classList.remove('d-none', 'alert-success');
                    formMessage.classList.add('alert-danger');
                    formMessage.textContent = 'Name must be at least 2 characters long.';
                    submitButton.disabled = false;
                    submitText.textContent = 'Submit';
                    spinner.classList.add('d-none');
                    return;
                }

                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    formMessage.classList.remove('d-none', 'alert-success');
                    formMessage.classList.add('alert-danger');
                    formMessage.textContent = 'Please enter a valid email address.';
                    submitButton.disabled = false;
                    submitText.textContent = 'Submit';
                    spinner.classList.add('d-none');
                    return;
                }

                try {
                    // Simulate form submission (replace with actual API call)
                    await new Promise(resolve => setTimeout(resolve, 2000));
                    
                    // Reset form
                    contactForm.reset();
                    formMessage.classList.remove('d-none', 'alert-danger');
                    formMessage.classList.add('alert-success');
                    formMessage.textContent = 'Thank you for your message! We will get back to you soon.';
                } catch (error) {
                    formMessage.classList.remove('d-none', 'alert-success');
                    formMessage.classList.add('alert-danger');
                    formMessage.textContent = 'An error occurred. Please try again later.';
                } finally {
                    submitButton.disabled = false;
                    submitText.textContent = 'Submit';
                    spinner.classList.add('d-none');
                }
            });
        });
    </script>
</body>
</html>