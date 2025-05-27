<?php
session_start();
include("include/db.php");

// Fetch stats for the Stats Section
$stats = [
    'users' => $con->query("SELECT COUNT(*) FROM users")->fetch_row()[0],
    'trainers' => $con->query("SELECT COUNT(*) FROM trainer")->fetch_row()[0],
    'classes' => $con->query("SELECT COUNT(*) FROM class_schedules")->fetch_row()[0] // Assuming 'classes' table exists
];

// Initialize error variable and login attempt flag
$error = "";
$login_attempted = false;

if (isset($_POST['user_login'])) {
    $login_attempted = true;
    $user_email = mysqli_real_escape_string($con, $_POST['user_email']);
    $user_password = mysqli_real_escape_string($con, $_POST['user_pass']);

    // Query to fetch user by email and plain-text password
    $select_user = "SELECT * FROM users WHERE user_email = ? AND user_pass = ?";
    $stmt = mysqli_prepare($con, $select_user);
    mysqli_stmt_bind_param($stmt, "ss", $user_email, $user_password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row_count = mysqli_num_rows($result);

    if ($row_count == 1) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        $_SESSION['user_email'] = $user_email;
        $_SESSION['user_id'] = $row['user_id'];
        if (isset($_POST['remember_me'])) {
            // Set a cookie for 30 days
            setcookie('user_email', $user_email, time() + (30 * 24 * 60 * 60), "/", "", false, true);
        }
        header('Location: Home.php');
        exit();
    } else {
        $error = "Invalid email or password. Please try again.";
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="images/icons8-gym-50.png" sizes="16x16">
  <title>FitZone Gym Home</title>
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

    /* Hero Section with Video Background */
    .home {
      position: relative;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
      overflow: hidden;
    }

    .home-video {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: -1;
      opacity: 0.7;
    }

    .home-content {
      text-align: center;
      max-width: 700px;
      background: rgba(0, 0, 0, 0.5);
      padding: 2rem;
      border-radius: 1rem;
    }

    .home-content h1 {
      font-size: 3.5rem;
      margin: 0.5rem 0;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .home-content h3 {
      font-size: 1.8rem;
      margin: 0.5rem 0;
    }

    /* Pricing Section */
    .pricing {
      padding: 4rem 2rem;
      background: var(--bg-color);
      text-align: center;
    }

    .pricing h2 {
      font-size: 2.5rem;
      margin-bottom: 2rem;
    }

    .pricing-table {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    .pricing-card {
      background: var(--second-bg-color);
      border-radius: 1rem;
      padding: 2rem;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
      transition: transform 0.3s;
    }

    .pricing-card:hover {
      transform: translateY(-5px);
    }

    .pricing-card h3 {
      font-size: 2rem;
      margin-bottom: 1rem;
    }

    .pricing-card .price {
      font-size: 2.5rem;
      color: var(--main-color);
      margin-bottom: 1rem;
    }

    .pricing-card ul {
      list-style: none;
      padding: 0;
      margin-bottom: 1rem;
    }

    .pricing-card ul li {
      font-size: 1.4rem;
      margin-bottom: 0.5rem;
    }
/* Align the Login button with nav links */
.navbar-nav .nav-item:last-child {
    display: flex;
    align-items: center; /* Vertically center the button */
}

.nav-btn {
    margin-left: 0.5rem; /* Consistent spacing with nav links */
    padding: 0.5rem 1rem; /* Slightly reduce padding to match nav-link height */
    line-height: 1.5; /* Match nav-link line height */
    font-size: 1.1rem; /* Match nav-link font size */
    vertical-align: middle; /* Ensure inline alignment */
}

/* Optional: Adjust hover to prevent shifting */
.nav-btn:hover {
    background-color: #33ccaa;
    transform: scale(1.02); /* Reduce scale to minimize visual shift */
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

    /* Login Modal */
    .login-modal .modal-content {
      background-color: var(--second-bg-color);
      color: var(--text-color);
    }

    .login-modal .form-control {
      background-color: var(--bg-color);
      color: var(--text-color);
      border: 1px solid var(--main-color);
    }

    .login-modal .form-control:focus {
      background-color: var(--bg-color);
      color: var(--text-color);
      border-color: var(--main-color);
      box-shadow: 0 0 5px var(--main-color);
    }

    .login-modal .btn-close {
      filter: invert(1);
    }

    .toggle-password {
      cursor: pointer;
      color: var(--main-color);
    }

    .toggle-password:hover {
      color: #33ccaa;
    }

    .form-control.is-invalid {
      border-color: #ff6b6b;
    }

    .alert-danger {
      background-color: rgba(255, 107, 107, 0.1);
      border-color: #ff6b6b;
      color: var(--text-color);
    }

    /* Responsive Navbar */
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
            <li class="nav-item"><a class="nav-link active" href="index.php" aria-label="Home">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="aboutus.php" aria-label="About Us">About Us</a></li>
            <li class="nav-item"><a class="nav-link" href="services.php" aria-label="Services">Services</a></li>
            <li class="nav-item"><a class="nav-link" href="trainers.php" aria-label="Trainers">Trainers</a></li>
            <li class="nav-item"><a class="nav-link" href="blog.php" aria-label="Blog">Blog</a></li>
            <li class="nav-item"><a class="nav-link" href="faqs.php" aria-label="FAQs">FAQs</a></li>
            <li class="nav-item"><a class="nav-link" href="contactus.php" aria-label="Contact us">Contact us</a></li>
            <br>
            <li class="nav-item">
              <a class="nav-btn" href="#" data-bs-toggle="modal" data-bs-target="#loginModal" aria-label="Login">Login</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    
  </header>

  <!-- Login Modal -->
  <div class="modal fade login-modal" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="loginModalLabel">Member Login</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>
          <form id="loginForm" action="index.php" method="post" novalidate>
            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="form-control" id="email" name="user_email" required
                     value="<?php echo isset($_COOKIE['user_email']) ? htmlspecialchars($_COOKIE['user_email']) : ''; ?>"
                     aria-describedby="emailHelp">
              <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
              <div class="invalid-feedback">Please enter a valid email address.</div>
            </div>
            <div class="mb-3 position-relative">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="user_pass" required>
              <span class="toggle-password position-absolute end-0 top-50 translate-middle-y me-3" onclick="togglePassword()" aria-label="Toggle password visibility">
                
              </span>
              <div class="invalid-feedback">Please enter your password.</div>
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me"
                     <?php echo isset($_COOKIE['user_email']) ? 'checked' : ''; ?>>
              <label class="form-check-label" for="rememberMe">Remember me</label>
            </div>
            <button type="submit" class="nav-btn w-100" name="user_login">Login</button>
          </form>
          <div class="mt-3 text-center">
            <a href="forgot-password.php" class="text-decoration-none">Forgot Password?</a> |
            <a href="pricing.php" class="text-decoration-none">Sign Up</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <section id="home" class="home">
    <video class="home-video" autoplay muted loop playsinline poster="images/gv4.jpeg">
      <source src="images/gymvid3.mp4" type="video/mp4">
      <img src="images/gv3.jpeg" alt="FitZone Gym Hero Image">
    </video>
    <div class="home-content" data-aos="zoom-in">
      <h3>Transform Your</h3>
      <h1>Body & Mind</h1>
      <h3><span class="multiple-text"></span></h3>
      <a href="pricing.php" class="nav-btn" aria-label="Join now">Join Us Today!</a>
      <!--<a href="classes.php" class="nav-btn mt-3" aria-label="View class schedule">View Classes</a>-->
    </div>
  </section>

  <!-- Pricing Section -->
  <section class="pricing" data-aos="fade-up">
    <h2>Membership Packages</h2>
    <div class="pricing-table">
      <div class="pricing-card">
        <h3>General</h3>
        <div class="price">R500/month</div>
        <ul>
          <li>Access to gym equipment</li>
          <li>Basic group classes</li>
          <li>Locker room access</li>
        </ul>
      </div>
      <div class="pricing-card">
        <h3>Couple</h3>
        <div class="price">R1000/month</div>
        <ul>
          <li>All General benefits for two</li>
          <li>Shared billing</li>
          <li>5 group classes/month each</li>
        </ul>
      </div>
      <div class="pricing-card">
        <h3>Premium</h3>
        <div class="price">R1500/month</div>
        <ul>
          <li>All General benefits</li>
          <li>Unlimited classes</li>
          <li>Personal training sessions</li>
        </ul>
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

  <!-- Testimonial Carousel 
  <section class="testimonials" data-aos="fade-up">
    <h2>Member Success Stories</h2>
    <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="images/mike-von-CX0zKCHOpJo-unsplash (2).jpg" alt="Member Jane D.">
          <div class="carousel-caption">
            <h5>Jane D.</h5>
            <p>"Lost 20 lbs and gained confidence with FitZone's trainers!"</p>
          </div>
        </div>
        <div class="carousel-item">
          <img src="images/hero.png" alt="Member Mike S.">
          <div class="carousel-caption">
            <h5>Mike S.</h5>
            <p>"The group classes keep me motivated every week!"</p>
          </div>
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </section>-->

  <!-- Fitness Goal Quiz Modal 
  <div class="modal fade" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content" style="background-color: var(--second-bg-color); color: var(--text-color);">
        <div class="modal-header">
          <h5 class="modal-title" id="quizModalLabel">Find Your Fitness Path</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Answer a few questions to discover the best FitZone plan for you!</p>
          <div id="quiz">
            <div class="quiz-question" data-question="1">
              <p>What's your primary fitness goal?</p>
              <button class="quiz-option nav-btn" data-value="weight-loss">Weight Loss</button>
              <button class="quiz-option nav-btn" data-value="muscle-gain">Muscle Gain</button>
              <button class="quiz-option nav-btn" data-value="endurance">Endurance</button>
            </div>
            <div class="quiz-question" data-question="2" style="display: none;">
              <p>How often can you train per week?</p>
              <button class="quiz-option nav-btn" data-value="1-2">1-2 Times</button>
              <button class="quiz-option nav-btn" data-value="3-4">3-4 Times</button>
              <button class="quiz-option nav-btn" data-value="5+">5+ Times</button>
            </div>
            <div class="quiz-result" style="display: none;">
              <p id="quizResult"></p>
              <a href="choosePackage.php" class="nav-btn" aria-label="View recommended plan">View Your Plan</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>-->

  <!-- Instagram Feed -->
  <section class="insta-feed" data-aos="fade-up">
    <h2>Join Our Community</h2>
    <div class="insta-grid">
      <img src="images/bg1.jpg" alt="Gym workout">
      <img src="images/bg2.jpg" alt="Group class">
      <img src="images/bg3.jpg" alt="Member transformation">
      <img src="images/Fat Lose.jpg" alt="Gym equipment">
    </div>
  </section>

  <!-- Progress Tracker Preview 
  <section class="progress-preview" data-aos="fade-up">
    <h2>Track Your Progress</h2>
    <p>Monitor your workouts and goals in our member portal!</p>
    <div class="progress-bar">
      <div class="progress-fill"></div>
    </div>
    <a href="pricing.php" class="nav-btn" aria-label="Sign up to track progress">Start Tracking Now</a>
  </section>
-->
  <!-- Scroll-to-Top Button -->
  <a href="#" class="scroll-to-top" aria-label="Scroll to top">
    <i class="fas fa-chevron-up"></i>
  </a>

  <footer>
    <p>© 2025 FitZone Gym. All rights reserved.</p>
    <div class="social-icons">
      <a href="https://instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
      <a href="https://twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
      <a href="https://facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
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

      // Typed.js for hero section
      if (typeof Typed !== 'undefined') {
        new Typed('.multiple-text', {
          strings: ['Get Stronger', 'Stay Motivated', 'Achieve Goals'],
          typeSpeed: 100,
          backSpeed: 50,
          loop: true
        });
      }

      // Scroll-to-top button
      const scrollToTopBtn = document.querySelector('.scroll-to-top');
      window.addEventListener('scroll', () => {
        scrollToTopBtn.classList.toggle('show', window.scrollY > 300);
      });

      // Quiz Logic
      const quiz = document.getElementById('quiz');
      const questions = quiz.querySelectorAll('.quiz-question');
      const resultDiv = quiz.querySelector('.quiz-result');
      const resultText = quiz.querySelector('#quizResult');
      let currentQuestion = 1;
      let answers = {};

      quiz.addEventListener('click', (e) => {
        if (e.target.classList.contains('quiz-option')) {
          answers[`q${currentQuestion}`] = e.target.getAttribute('data-value');
          questions[currentQuestion - 1].style.display = 'none';
          if (currentQuestion < questions.length) {
            questions[currentQuestion].style.display = 'block';
            currentQuestion++;
          } else {
            resultDiv.style.display = 'block';
            const goal = answers.q1;
            const frequency = answers.q2;
            let recommendation = '';
            if (goal === 'weight-loss') {
              recommendation = 'Our Weight Loss Program with cardio and nutrition plans is perfect for you!';
            } else if (goal === 'muscle-gain') {
              recommendation = 'Join our Muscle Building Program with strength training and protein guidance!';
            } else {
              recommendation = 'Our Endurance Training Program with HIIT and stamina classes suits you!';
            }
            resultText.textContent = `${recommendation} Based on your ${frequency} weekly commitment.`;
          }
        }
      });

      // Password Toggle
      function togglePassword() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.querySelector('.toggle-password i');
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        toggleIcon.classList.toggle('fa-eye', type === 'password');
        toggleIcon.classList.toggle('fa-eye-slash', type !== 'password');
      }

      // Client-side Form Validation
      const loginForm = document.getElementById('loginForm');
      loginForm.addEventListener('submit', (e) => {
        if (!loginForm.checkValidity()) {
          e.preventDefault();
          loginForm.classList.add('was-validated');
        }
      });

      // Show login modal only after an invalid login attempt
      <?php if ($login_attempted && !empty($error)): ?>
        const loginModal = new bootstrap.Modal(document.getElementById('loginModal'), {
          keyboard: false
        });
        loginModal.show();
      <?php endif; ?>
    });
  </script>
</body>
</html>