<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Explore the fitness services offered by FitZone Gym, including strength training, weight management, and personalized membership packages.">
    <meta name="author" content="Dikotope T">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="generator" content="Hugo 0.84.0">
    <link rel="icon" type="image/png" href="../images/icons8-gym-50.png" sizes="16x16">
    <title>FitZone - Services</title>
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
    }

    .navbar-nav .nav-link:hover {
      color: var(--main-color);
    }

    .top-btn {
      display: flex;
      gap: 1rem;
      align-items: center;
    }

    .nav-btn,
    .nav-btn1 {
      padding: 0.6rem 1.2rem;
      border-radius: 0.5rem;
      background-color: var(--main-color);
      color: var(--bg-color);
      text-decoration: none;
      font-weight: bold;
      transition: transform 0.2s, background-color 0.3s;
    }

    .nav-btn:hover,
    .nav-btn1:hover {
      background-color: #33ccaa;
      transform: scale(1.05);
    }

    .theme-toggle {
      background: none;
      border: none;
      color: var(--text-color);
      font-size: 1.5rem;
      cursor: pointer;
      transition: color 0.3s;
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


    .navbar-nav .nav-link {
      color: var(--text-color);
      transition: color 0.3s;
    }

    .navbar-nav .nav-link:hover {
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

        .top-btn {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .nav-btn,
        .nav-btn1 {
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            background-color: var(--main-color);
            color: var(--bg-color);
            text-decoration: none;
            font-weight: bold;
            transition: transform 0.2s, background-color 0.3s;
        }

        .nav-btn:hover,
        .nav-btn1:hover {
            background-color: #33ccaa;
            transform: scale(1.05);
        }

        .theme-toggle {
            background: none;
            border: none;
            color: var(--text-color);
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s;
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

        /* Hero Section with Video Background */
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

        /* Services Section */
        .services {
            padding: 4rem 2rem;
            background: var(--second-bg-color);
        }

        .services h2 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 2rem;
        }

        .services-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .row {
            background: var(--bg-color);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 0 5px var(--main-color);
            transition: transform 0.3s;
        }

        .row img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .row h4 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .row p {
            font-size: 1.4rem;
            margin-bottom: 1rem;
        }

        .row a {
            padding: 0.6rem 1.2rem;
            background: var(--main-color);
            color: var(--bg-color);
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: bold;
        }

        .row:hover {
            transform: translateY(-5px);
        }

        .filter-bar {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .filter-btn {
            padding: 0.6rem 1.2rem;
            background: transparent;
            color: var(--main-color);
            border: 2px solid var(--main-color);
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--main-color);
            color: var(--bg-color);
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

        /* Responsive Navbar */
        @media (max-width: 991px) {
            .header-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar {
                width: 100%;
                margin-top: 1rem;
            }

            .top-btn {
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

            .nav-btn,
            .nav-btn1 {
                padding: 0.5rem 1rem;
            }

            .services-content {
                grid-template-columns: 1fr;
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
            <li class="nav-item"><a class="nav-link active" href="services.php" aria-label="Services">Services</a></li>
            <li class="nav-item"><a class="nav-link" href="trainers.php" aria-label="Trainers">Trainers</a></li>
            <li class="nav-item"><a class="nav-link" href="blog.php" aria-label="Blog">Blog</a></li>
            <li class="nav-item"><a class="nav-link" href="faqs.php" aria-label="FAQs">FAQs</a></li>
            <li class="nav-item"><a class="nav-link" href="contactus.php" aria-label="Contact us">Contact us</a></li>
          </ul>
        </div>
      
      </div>
    </nav>
  </header>

    <!-- Hero Section -->
    <section id="hero" class="hero">
        <video class="hero-video" autoplay muted loop playsinline poster="images/gv4.jpeg">
            <source src="images/gymvid2.mp4" type="video/mp4">
            <img src="images/gv3.jpeg" alt="FitZone Gym Services Hero Image">
        </video>
        <div class="hero-content" data-aos="zoom-in">
            <h1>Our Services</h1>
            <p>Discover tailored fitness programs and membership packages to achieve your goals.</p>
            <a href="#services" class="nav-btn" aria-label="Explore services">Explore Now</a>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
        <h2 data-aos="zoom-in-down">Our <span>Services</span></h2>
        <div class="filter-bar" data-aos="fade-up">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="strength">Strength</button>
            <button class="filter-btn" data-filter="cardio">Cardio</button>
            <button class="filter-btn" data-filter="weight">Weight Management</button>
        </div>
        <div class="services-content" data-aos="zoom-in-up">
            <div class="row" data-category="strength">
                <img src="images/Physical Fitness.jpg" alt="Physical Fitness Training" loading="lazy">
                <h4>Physical Fitness</h4>
                <p>Comprehensive fitness programs to improve endurance, flexibility, and overall health.</p>
                <a href="service-details.php?id=physical-fitness" aria-label="Learn more about Physical Fitness">Learn More</a>
            </div>
            <div class="row" data-category="weight">
                <img src="images/Weight Gain.jpg" alt="Weight Gain Program" loading="lazy">
                <h4>Weight Gain</h4>
                <p>Personalized nutrition and training plans to help you gain muscle mass safely.</p>
                <a href="service-details.php?id=weight-gain" aria-label="Learn more about Weight Gain">Learn More</a>
            </div>
            <div class="row" data-category="strength">
                <img src="images/Strength Training.jpg" alt="Strength Training Sessions" loading="lazy">
                <h4>Strength Training</h4>
                <p>Targeted workouts to build muscle strength and power with expert guidance.</p>
                <a href="service-details.php?id=strength-training" aria-label="Learn more about Strength Training">Learn More</a>
            </div>
            <div class="row" data-category="weight">
                <img src="images/Fat Lose.jpg" alt="Fat Loss Program" loading="lazy">
                <h4>Fat Loss</h4>
                <p>Effective strategies combining cardio, strength, and diet to shed unwanted fat.</p>
                <a href="service-details.php?id=fat-loss" aria-label="Learn more about Fat Loss">Learn More</a>
            </div>
            <div class="row" data-category="strength">
                <img src="images/Weightlifting.jpg" alt="Weightlifting Classes" loading="lazy">
                <h4>Weightlifting</h4>
                <p>Advanced weightlifting techniques to enhance performance and build muscle.</p>
                <a href="service-details.php?id=weightlifting" aria-label="Learn more about Weightlifting">Learn More</a>
            </div>
            <div class="row" data-category="cardio">
                <img src="images/Running.jpg" alt="Running and Cardio Training" loading="lazy">
                <h4>Running</h4>
                <p>Guided running sessions to boost cardiovascular health and stamina.</p>
                <a href="service-details.php?id=running" aria-label="Learn more about Running">Learn More</a>
            </div>
        </div>
    </section>

    
    <!-- Stats Section -->
    <!-- Testimonial Carousel -->
 

    <!-- Instagram Feed -->


    <!-- Progress Tracker Preview -->

    <!-- Schedule Modal -->

    <!-- Consultation Modal -->
    <div class="modal fade" id="consultationModal" tabindex="-1" aria-labelledby="consultationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="consultationModalLabel">Book a Free Consultation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Fill out the form to schedule a free consultation with our trainers!</p>
                    <form id="consultationForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" required aria-describedby="nameHelp">
                            <div id="nameHelp" class="form-text">Enter your full name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" required aria-describedby="emailHelp">
                            <div id="emailHelp" class="form-text">We'll use this to contact you.</div>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" required aria-describedby="phoneHelp">
                            <div id="phoneHelp" class="form-text">For scheduling purposes.</div>
                        </div>
                        <div class="mb-3">
                            <label for="goal" class="form-label">Fitness Goal</label>
                            <select class="form-select" id="goal" required aria-describedby="goalHelp">
                                <option value="" disabled selected>Select a goal</option>
                                <option value="weight-loss">Weight Loss</option>
                                <option value="muscle-gain">Muscle Gain</option>
                                <option value="endurance">Endurance</option>
                                <option value="general">General Fitness</option>
                            </select>
                            <div id="goalHelp" class="form-text">Your primary fitness objective.</div>
                        </div>
                        <button type="submit" class="nav-btn">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

            // Theme toggle
            const themeToggle = document.querySelector('.theme-toggle');
            const themeIcon = themeToggle.querySelector('i');
            const savedTheme = localStorage.getItem('theme') || 'dark';
            if (savedTheme === 'light') {
                document.body.classList.add('light-mode');
                themeIcon.classList.replace('fa-moon', 'fa-sun');
            }

            themeToggle.addEventListener('click', () => {
                document.body.classList.toggle('light-mode');
                const isLightMode = document.body.classList.contains('light-mode');
                themeIcon.classList.toggle('fa-moon', !isLightMode);
                themeIcon.classList.toggle('fa-sun', isLightMode);
                localStorage.setItem('theme', isLightMode ? 'light' : 'dark');
            });

            // Scroll-to-top button
            const scrollToTopBtn = document.querySelector('.scroll-to-top');
            window.addEventListener('scroll', () => {
                scrollToTopBtn.classList.toggle('show', window.scrollY > 300);
            });

            // Service filter
            const filterButtons = document.querySelectorAll('.filter-btn');
            const serviceRows = document.querySelectorAll('.row');
            filterButtons.forEach(button => {
                button.addEventListener('click', () => {
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    const filter = button.getAttribute('data-filter');
                    serviceRows.forEach(row => {
                        const category = row.getAttribute('data-category');
                        row.style.display = (filter === 'all' || category === filter) ? 'block' : 'none';
                    });
                });
            });

            // Animated Stats Counter
            const stats = document.querySelectorAll('.stat-number');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = entry.target;
                        const count = parseInt(target.getAttribute('data-count'));
                        let current = 0;
                        const increment = count / 100;
                        const updateCount = () => {
                            current += increment;
                            target.textContent = Math.ceil(current);
                            if (current < count) {
                                requestAnimationFrame(updateCount);
                            } else {
                                target.textContent = count;
                            }
                        };
                        updateCount();
                        observer.unobserve(target);
                    }
                });
            }, { threshold: 0.5 });

            stats.forEach(stat => observer.observe(stat));

            // Consultation Form Submission
            const consultationForm = document.getElementById('consultationForm');
            consultationForm.addEventListener('submit', (e) => {
                e.preventDefault();
                alert('Thank you for your submission! We will contact you soon.');
                consultationForm.reset();
                bootstrap.Modal.getInstance(document.getElementById('consultationModal')).hide();
            });
        });
    </script>
</body>
</html>