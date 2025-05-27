<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="images/icons8-gym-50.png" sizes="16x16">
  <title>FAQs - FitZone Gym</title>
  <link href="styles.css" rel="stylesheet">
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

    /* FAQs Section */
    .faqs {
      padding: 4rem 2rem;
      background: var(--second-bg-color);
    }

    .faqs h1 {
      text-align: center;
      font-size: 3rem;
      margin-bottom: 2rem;
    }

    .accordion-item {
      background: var(--bg-color);
      border: 1px solid var(--main-color);
      margin-bottom: 1rem;
    }

    .accordion-button {
      background: var(--bg-color);
      color: var(--text-color);
      font-size: 1.2rem;
      transition: background-color 0.3s;
    }

    .accordion-button:not(.collapsed) {
      background: var(--main-color);
      color: var(--bg-color);
    }

    .accordion-body {
      background: var(--second russa-bg-color);
      color: var(--text-color);
      font-size: 1rem;
    }

    @media (max-width: 768px) {
      .navbar-collapse {
        background: #000;
        padding: 1rem;
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
            <li class="nav-item"><a class="nav-link active" href="faqs.php" aria-label="FAQs">FAQs</a></li>
            <li class="nav-item"><a class="nav-link" href="contactus.php" aria-label="Contact us">Contact us</a></li>
          </ul>
        </div>
      
      </div>
    </nav>
  </header>

  <section class="faqs" data-aos="fade-up">
    <h1>Frequently Asked Questions</h1>
    <div class="accordion" id="faqAccordion">
      <div class="accordion-item" data-aos="zoom-in">
        <h2 class="accordion-header" id="faq1">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
            What are the membership options?
          </button>
        </h2>
        <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            We offer monthly memberships, with options for individual, couple, and family plans. Visit our <a href="pricing.php">Pricing</a> page for details.
          </div>
        </div>
      </div>
      <div class="accordion-item" data-aos="zoom-in">
        <h2 class="accordion-header" id="faq2">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
            Do I need to book classes in advance?
          </button>
        </h2>
        <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Yes, we recommend booking classes in advance through our <a href="classes.php">Classes</a> page to secure your spot.
          </div>
        </div>
      </div>
      <div class="accordion-item" data-aos="zoom-in">
        <h2 class="accordion-header" id="faq3">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
            What facilities does FitZone offer?
          </button>
        </h2>
        <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Our gym includes weight rooms, cardio areas, group class studios, locker rooms, and a smoothie bar. Check out our <a href="services.php">Services</a> page for more.
          </div>
        </div>
      </div>
      <div class="accordion-item" data-aos="zoom-in">
        <h2 class="accordion-header" id="faq4">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
            Can I try the gym before joining?
          </button>
        </h2>
        <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="faq4" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Yes! We offer a free trial day. Contact us via our <a href="contactus.php">Contact</a> page to schedule your visit.
          </div>
        </div>
      </div>
    </div>
  </section>

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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous" defer></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (typeof AOS !== 'undefined') {
        AOS.init({
          duration: 1000,
          once: true
        });
      }

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

      const scrollToTopBtn = document.querySelector('.scroll-to-top');
      window.addEventListener('scroll', () => {
        scrollToTopBtn.classList.toggle('show', window.scrollY > 300);
      });
    });
  </script>
</body>
</html>