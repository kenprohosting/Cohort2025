<?php
session_start();
require_once('@pdo.php');

// Connect to the database using PDO helper
PDO_Connect('mysql:host=localhost;dbname=esoma_homeworker;charset=utf8mb4', 'esoma_homeworker', 'Kenyan@254'); // LIVE DB credentials

if (!isset($_SESSION['employer_id'])) {
    header("Location:employer_login.php");
    exit();
}

$employer_id = $_SESSION['employer_id'];
$booking_id = isset($_GET['bid']) ? intval($_GET['bid']) : 0;
$errors = [];
$success = '';
$employee = null;
$payment_success = false;

// IntaSend API keys
$publishable_key = "ISPubKey_live_40f25458-716c-47c5-b049-786fd1f3a1ce";

// IntaSend returns a 'checkout_request_id' in the callback URL
$checkout_request_id = isset($_GET['checkout_request_id']) ? $_GET['checkout_request_id'] : null;

if ($booking_id) {
    // 1. Verify payment status with IntaSend API
    if ($checkout_request_id) {
        $ch = curl_init("https://api.intasend.com/api/v1/checkout/" . urlencode($checkout_request_id) . "/");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $publishable_key",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($response, true);
        if (isset($result['status']) && strtolower($result['status']) === 'complete') {
            $payment_success = true;
        } else {
            $errors[] = "Payment failed or not completed. Please try again.";
        }
    } else {
        $errors[] = "No payment reference found. Please try again.";
    }

    // 2. If payment is successful, fetch employee details
    if ($payment_success) {
        $employee = PDO_FetchRow(
            "SELECT emp.* FROM bookings b JOIN employees emp ON b.Employee_ID = emp.ID WHERE b.ID = ? AND b.Homeowner_ID = ?",
            [$booking_id, $employer_id]
        );
        if (!$employee) {
            $errors[] = "Invalid booking or employee not found.";
        }
    }
} else {
    $errors[] = "No booking selected.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Contact Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <div class="logo"></div>
    <nav>
        <ul class="nav-links">
            <li><a href="employer_dashboard.php">← Back</a></li>
            <li><a href="employer_logout.php">Logout</a></li>
        </ul>
    </nav>
</header>
<div class="form-container">
    <h2>Employee Contact Details</h2>
    <?php
    if ($errors) foreach ($errors as $e) echo "<p class='error'>$e</p>";
    ?>
    <?php if ($payment_success && $employee): ?>
        <p><strong>Name:</strong> <?= htmlspecialchars($employee['Name']) ?></p>
        <p><strong>Gender:</strong> <?= htmlspecialchars($employee['Gender']) ?></p>
        <p><strong>Age:</strong> <?= htmlspecialchars($employee['Age']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($employee['phone']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($employee['email']) ?></p>
        <p><strong>Country:</strong> <?= htmlspecialchars($employee['country']) ?></p>
        <p><strong>County/Province:</strong> <?= htmlspecialchars($employee['county_province']) ?></p>
        <p><strong>Skills:</strong> <?= htmlspecialchars($employee['Skills']) ?></p>
        <p><strong>Education Level:</strong> <?= htmlspecialchars($employee['Education_level']) ?></p>
        <p><strong>Social Referee:</strong> <?= htmlspecialchars($employee['Social_referee']) ?></p>
    <?php elseif (!$payment_success): ?>
        <p style="color:red;font-weight:bold;">Payment was not successful. Please try again.</p>
        <a href="employer_payment.php?bid=<?= $booking_id ?>" class="btn">Retry Payment</a>
    <?php endif; ?>
</div>
</body>
</html> 