<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="images/icons8-gym-50.png" sizes="16x16">
  <title>Classes - FitZone Gym</title>
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

    /* Classes Section */
    .classes {
      padding: 4rem 2rem;
      background: var(--second-bg-color);
    }

    .classes h1 {
      text-align: center;
      font-size: 3rem;
      margin-bottom: 2rem;
    }

    .filter-container {
      text-align: center;
      margin-bottom: 2rem;
    }

    .filter-container select {
      padding: 0.5rem;
      font-size: 1rem;
      border-radius: 0.5rem;
      background: var(--bg-color);
      color: var(--text-color);
      border: 1px solid var(--main-color);
    }

    .class-table {
      background: var(--bg-color);
      border-radius: 0.5rem;
      overflow: hidden;
    }

    .class-table table {
      width: 100%;
      border-collapse: collapse;
    }

    .class-table th,
    .class-table td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid var(--main-color);
    }

    .class-table th {
      background: var(--main-color);
      color: var(--bg-color);
    }

    @media (max-width: 768px) {
      .navbar-collapse {
        background: #000;
        padding: 1rem;
      }

      .class-table table,
      .class-table thead,
      .class-table tbody,
      .class-table th,
      .class-table td,
      .class-table tr {
        display: block;
      }

      .class-table th {
        display: none;
      }

      .class-table td {
        position: relative;
        padding-left: 50%;
        text-align: right;
      }

      .class-table td:before {
        content: attr(data-label);
        position: absolute;
        left: 1rem;
        width: 45%;
        font-weight: bold;
        text-align: left;
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
            <li class="nav-item"><a class="nav-link active" href="classes.php" aria-label="Classes">Classes</a></li>
            <li class="nav-item"><a class="nav-link" href="trainers.php" aria-label="Trainers">Trainers</a></li>
            <li class="nav-item"><a class="nav-link" href="blog.php" aria-label="Blog">Blog</a></li>
            <li class="nav-item"><a class="nav-link" href="faqs.php" aria-label="FAQs">FAQs</a></li>
            <li class="nav-item"><a class="nav-link" href="contactus.php" aria-label="Contact us">Contact us</a></li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <section class="classes" data-aos="fade-up">
    <h1>Our Classes</h1>
    <div class="filter-container">
      <label for="classFilter" class="visually-hidden">Filter by class type</label>
      <select id="classFilter" class="form-select">
        <option value="all">All Classes</option>
        <option value="Yoga">Yoga</option>
        <option value="Strength Training">Strength Training</option>
        <option value="Cardio Blast">Cardio Blast</option>
        <option value="Pilates">Pilates</option>
      </select>
    </div>
    <div class="class-table" data-aos="zoom-in">
      <table>
        <thead>
          <tr>
            <th scope="col">Day</th>
            <th scope="col">Class</th>
            <th scope="col">Time</th>
            <th scope="col">Action</th>
          </tr>
        </thead>
        <tbody id="classTable">
          <tr data-class="Yoga">
            <td data-label="Day">Monday</td>
            <td data-label="Class">Yoga</td>
            <td data-label="Time">6:00 PM - 7:00 PM</td>
            <td data-label="Action"><a href="booking.php" class="nav-btn">Reserve</a></td>
          </tr>
          <tr data-class="Strength Training">
            <td data-label="Day">Wednesday</td>
            <td data-label="Class">Strength Training</td>
            <td data-label="Time">5:30 PM - 6:30 PM</td>
            <td data-label="Action"><a href="booking.php" class="nav-btn">Reserve</a></td>
          </tr>
          <tr data-class="Cardio Blast">
            <td data-label="Day">Friday</td>
            <td data-label="Class">Cardio Blast</td>
            <td data-label="Time">7:00 PM - 8:00 PM</td>
            <td data-label="Action"><a href="booking.php" class="nav-btn">Reserve</a></td>
          </tr>
          <tr data-class="Pilates">
            <td data-label="Day">Tuesday</td>
            <td data-label="Class">Pilates</td>
            <td data-label="Time">6:30 PM - 7:30 PM</td>
            <td data-label="Action"><a href="booking.php" class="nav-btn">Reserve</a></td>
          </tr>
        </tbody>
      </table>
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

      const classFilter = document.getElementById('classFilter');
      const classTable = document.getElementById('classTable').querySelectorAll('tr');
      classFilter.addEventListener('change', () => {
        const selected = classFilter.value;
        classTable.forEach(row => {
          row.style.display = selected === 'all' || row.getAttribute('data-class') === selected ? '' : 'none';
        });
      });
    });
  </script>
</body>
</html>