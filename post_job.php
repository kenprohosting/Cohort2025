<?php
session_start();
if (!isset($_SESSION['employer_id'])) {
    header("Location: employer_login.php");
    exit();
}

require_once('db_connect.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $required_skills = trim($_POST['required_skills']);
    $location = trim($_POST['location']);
    $salary_min = !empty($_POST['salary_min']) ? $_POST['salary_min'] : null;
    $salary_max = !empty($_POST['salary_max']) ? $_POST['salary_max'] : null;
    $job_type = $_POST['job_type'];
    $start_date = $_POST['start_date'];
    $duration_hours = !empty($_POST['duration_hours']) ? $_POST['duration_hours'] : null;
    $special_requirements = trim($_POST['special_requirements']);
    $expiry_date = $_POST['expiry_date'];
    
    // Validate expiry date (must be in the future)
    if (strtotime($expiry_date) <= time()) {
        $message = '<div style="color: red; margin: 10px 0;">Expiry date must be in the future.</div>';
    } else {
        $stmt = $conn->prepare("
            INSERT INTO jobs (Employer_ID, Title, Description, Required_skills, Location, 
                             Salary_min, Salary_max, Job_type, Start_date, Duration_hours, 
                             Special_requirements, Expiry_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([
            $_SESSION['employer_id'], $title, $description, $required_skills, $location,
            $salary_min, $salary_max, $job_type, $start_date, $duration_hours,
            $special_requirements, $expiry_date
        ])) {
            $message = '<div style="color: green; margin: 10px 0;">Job posted successfully!</div>';
        } else {
            $message = '<div style="color: red; margin: 10px 0;">Error posting job. Please try again.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post a Job - Homeworker Connect</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <style>
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: rgb(24, 123, 136);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        input[type="text"], input[type="number"], input[type="date"], 
        textarea, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        
        textarea {
            height: 100px;
            resize: vertical;
        }
        
        .salary-group {
            display: flex;
            gap: 10px;
        }
        
        .salary-group input {
            flex: 1;
        }
        
        .required {
            color: red;
        }
        
        .help-text {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .user-greeting {
            color: white;
            font-weight: 500;
            padding: 10px 16px;
        }
        
        /* Ensure navigation buttons remain visible and functional */
        .nav-links {
            display: flex !important;
            list-style: none !important;
            gap: 20px;
            align-items: center;
            margin: 0;
            padding: 0;
        }
        
        .nav-btn {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        .hamburger {
          display: none;
          flex-direction: column;
          justify-content: center;
          align-items: center;
          width: 44px;
          height: 44px;
          background: none;
          border: none;
          cursor: pointer;
          margin-left: auto;
          z-index: 1002;
        }
        .hamburger .bar {
          width: 28px;
          height: 3px;
          background: #fff;
          margin: 4px 0;
          border-radius: 2px;
          transition: 0.3s;
          display: block;
        }
        @media (max-width: 900px) {
          .hamburger {
            display: flex;
          }
          .main-nav {
            width: 100%;
          }
          .nav-links {
            display: none !important;
            flex-direction: column;
            position: absolute;
            top: 60px;
            right: 0;
            left: 0;
            background: #0A4A70;
            z-index: 1001;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            padding: 18px 0 10px 0;
            border-radius: 0 0 12px 12px;
            gap: 0;
            margin: 0;
            width: 100vw;
            min-width: 0;
          }
          .nav-links.show {
            display: flex !important;
          }
          .nav-links li {
            width: 100%;
            text-align: center;
            margin: 0;
            padding: 0;
          }
          .nav-links li a, .nav-links li span {
            display: block;
            width: 100%;
            padding: 14px 0;
            margin: 0;
            border-radius: 0;
            border-bottom: 1px solid #197b88;
          }
          .nav-links li:last-child a {
            border-bottom: none;
          }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <img src="bghse.png" alt="Logo" style="height: 40px;">
    </div>
    <button class="hamburger" id="hamburgerBtn" aria-label="Open navigation" aria-expanded="false" aria-controls="mainNav" type="button">
      <span class="bar"></span>
      <span class="bar"></span>
      <span class="bar"></span>
    </button>
    <nav class="main-nav">
        <ul class="nav-links" id="mainNav">
            <li><span class="user-greeting">Hello, <?= htmlspecialchars($_SESSION['employer_name']) ?></span></li>
            <li><a class="nav-btn" href="employer_dashboard.php">Dashboard</a></li>
            <li><a class="nav-btn" href="manage_jobs.php">My Jobs</a></li>
            <li><a class="nav-btn" href="employer_logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <h1>Post a New Job</h1>
    
    <?php echo $message; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="title">Job Title <span class="required">*</span></label>
            <input type="text" id="title" name="title" required 
                   placeholder="e.g., House Cleaner, Cook, Gardener">
        </div>
        
        <div class="form-group">
            <label for="description">Job Description <span class="required">*</span></label>
            <textarea id="description" name="description" required 
                      placeholder="Describe the job responsibilities, working conditions, and what you're looking for..."></textarea>
        </div>
        
        <div class="form-group">
            <label for="required_skills">Required Skills <span class="required">*</span></label>
            <input type="text" id="required_skills" name="required_skills" required 
                   placeholder="e.g., Cleaning, Cooking, Childcare, Gardening">
            <div class="help-text">Separate multiple skills with commas</div>
        </div>
        
        <div class="form-group">
            <label for="location">Location <span class="required">*</span></label>
            <input type="text" id="location" name="location" required 
                   placeholder="e.g., Nairobi, Westlands">
        </div>
        
        <div class="form-group">
            <label for="salary_min">Salary Range (KSH)</label>
            <div class="salary-group">
                <input type="number" id="salary_min" name="salary_min" 
                       placeholder="Minimum" min="0">
                <input type="number" id="salary_max" name="salary_max" 
                       placeholder="Maximum" min="0">
            </div>
            <div class="help-text">Leave empty if salary is negotiable</div>
        </div>
        
        <div class="form-group">
            <label for="job_type">Job Type <span class="required">*</span></label>
            <select id="job_type" name="job_type" required>
                <option value="">Select job type</option>
                <option value="one-time">One-time</option>
                <option value="part-time">Part-time</option>
                <option value="full-time">Full-time</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="start_date">Start Date <span class="required">*</span></label>
            <input type="date" id="start_date" name="start_date" required 
                   min="<?= date('Y-m-d') ?>">
        </div>
        
        <div class="form-group">
            <label for="duration_hours">Duration (Hours)</label>
            <input type="number" id="duration_hours" name="duration_hours" 
                   placeholder="e.g., 8" min="1">
            <div class="help-text">For one-time jobs, specify how many hours the job will take</div>
        </div>
        
        <div class="form-group">
            <label for="special_requirements">Special Requirements</label>
            <textarea id="special_requirements" name="special_requirements" 
                      placeholder="Any special requirements, preferences, or additional information..."></textarea>
        </div>
        
        <div class="form-group">
            <label for="expiry_date">Job Expiry Date <span class="required">*</span></label>
            <input type="date" id="expiry_date" name="expiry_date" required 
                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
            <div class="help-text">After this date, the job will no longer be visible to employees</div>
        </div>
        
        <button type="submit" class="btn">Post Job</button>
    </form>
</div>

<footer>
    <p>&copy; <?= date("Y") ?> Homeworker Connect. All rights reserved.</p> | <a href="privacy_policy.php" style="text-decoration: none; color: inherit;">Privacy Policy</a>
</footer>

<script>
// Hamburger menu toggle
const hamburgerBtn = document.getElementById('hamburgerBtn');
const navLinks = document.getElementById('mainNav');
hamburgerBtn.addEventListener('click', function() {
  const expanded = hamburgerBtn.getAttribute('aria-expanded') === 'true';
  hamburgerBtn.setAttribute('aria-expanded', !expanded);
  navLinks.classList.toggle('show');
});
// Close menu when clicking outside (mobile only)
document.addEventListener('click', function(e) {
  if (window.innerWidth <= 900 && navLinks.classList.contains('show')) {
    if (!navLinks.contains(e.target) && e.target !== hamburgerBtn && !hamburgerBtn.contains(e.target)) {
      navLinks.classList.remove('show');
      hamburgerBtn.setAttribute('aria-expanded', 'false');
    }
  }
});
</script>
</body>
</html> 