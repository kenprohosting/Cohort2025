<?php
session_start();
$isLoggedIn = isset($_SESSION['employer_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="/favicon.png">
  <meta charset="UTF-8">
  <title>Contact Us - Homeworker Connect</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="styles.css">


</head>
<body>
<header>
  <div class="logo">
    <img src="bghse.png" alt="Logo" style="height: 40px;">
  </div>
  <div id="hamburger">☰</div>
 <div id="navLinks">
  <nav class="main-nav">
    <ul class="nav-links">
      <li><a class="nav-btn" href="index.php">Home</a></li>
      <li><a class="nav-btn" href="about.php">About</a></li>
      <li><a class="nav-btn" href="resources.php">Resources</a></li>
      <li><a class="nav-btn" href="contact.php">Contact Us</a></li>
      <li><a class="nav-btn" href="faq.php">FAQ</a></li>
    </ul>
  </nav>
 </div>
</header>

<main>
  <div class="faq-container">
    <div class="faq-title">Contact Us</div>
    <div class="contact-details">
      <div>Email: <a href="mailto:support@homeworker.info">support@homeworker.info</a></div>
      <div>Phone: +254 725 788 400</div>
      <div>Address: Nairobi, Kenya</div>
    </div>
    <form class="contact-form" method="post" action="#">
      <label for="name">Name</label>
      <input type="text" id="name" name="name" required>
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required>
      <label for="message">Message</label>
      <textarea id="message" name="message" required></textarea>
      <button type="submit">Send Message</button>
    </form>
  </div>
</main>
<footer>
  <p>&copy; <?= date("Y") ?> KenPro. All rights reserved.</p> | <a href="privacy_policy.php" style="text-decoration: none; color: inherit;">Privacy Policy</a>
</footer>
<script src="hamburger.js"></script>
</body>
</html> 