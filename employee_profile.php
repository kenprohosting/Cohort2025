<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header("Location: employee_login.php");
    exit();
}
require_once("db_connect.php");

$employee_id = $_SESSION['employee_id'];
$success = '';
$errors = [];

// Get employee data
$stmt = $conn->prepare("SELECT * FROM employee WHERE ID = ?");
$stmt->execute([$employee_id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $skills = $_POST['skills'];
    $location = $_POST['location'];
    $language = $_POST['language'];
    $education = $_POST['education'];
    $residence = $_POST['residence_type'];

    $profile_pic = $employee['Profile_pic'] ?? null;

    // Upload new picture
    if (!empty($_FILES['profile_pic']['name'])) {
        $targetDir = "uploads/";
        $filename = basename($_FILES["profile_pic"]["name"]);
        $uniqueName = time() . "_" . $filename;
        $targetFile = $targetDir . $uniqueName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
            $errors[] = "Only JPG, JPEG, and PNG files are allowed.";
        } elseif (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFile)) {
            $profile_pic = $targetFile;
        } else {
            $errors[] = "Failed to upload profile picture.";
        }
    }

    if (empty($errors)) {
        $update = $conn->prepare("UPDATE employee SET Name = ?, Age = ?, Skills = ?, Location = ?, Language = ?, Education_level = ?, residence_type = ?, Profile_pic = ? WHERE ID = ?");
        $result = $update->execute([$name, $age, $skills, $location, $language, $education, $residence, $profile_pic, $employee_id]);

        if ($result) {
            $success = "Profile updated successfully!";
            $stmt->execute([$employee_id]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC); // Refresh
        } else {
            $errors[] = "Update failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Profile</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; background: #f0f0f0; }
        form { background: white; padding: 20px; max-width: 600px; margin: auto; border-radius: 8px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 4px; }
        button { margin-top: 15px; padding: 10px 20px; background: #00695c; color: white; border: none; border-radius: 4px; }
        .success { color: green; }
        .error { color: red; }
        img.profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 10px auto;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Update Your Profile</h2>

<?php if ($success) echo "<p class='success' style='text-align:center;'>$success</p>"; ?>
<?php foreach ($errors as $e) echo "<p class='error' style='text-align:center;'>$e</p>"; ?>

<?php
$profilePath = $employee['Profile_pic'] ?? '';
if (!empty($profilePath) && file_exists($profilePath)):
?>
    <img src="<?= htmlspecialchars($profilePath) ?>" alt="Profile Picture" class="profile-pic">
<?php else: ?>
    <img src="uploads/default.jpg" class="profile-pic" alt="Default Picture">
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Name:</label>
    <input name="name" value="<?= htmlspecialchars($employee['Name']) ?>" required>

    <label>Age:</label>
    <input type="number" name="age" value="<?= htmlspecialchars($employee['Age']) ?>" required>

    <label>Skills:</label>
    <textarea name="skills" required><?= htmlspecialchars($employee['Skills']) ?></textarea>

    <label>Location:</label>
    <input name="location" value="<?= htmlspecialchars($employee['Location']) ?>" required>

    <label>Languages:</label>
    <select name="language" required>
        <option <?= $employee['Language'] == 'English' ? 'selected' : '' ?>>English</option>
        <option <?= $employee['Language'] == 'Kiswahili' ? 'selected' : '' ?>>Kiswahili</option>
        <option <?= $employee['Language'] == 'Both' ? 'selected' : '' ?>>Both</option>
    </select>

    <label>Education Level:</label>
    <input name="education" value="<?= htmlspecialchars($employee['Education_level']) ?>" required>

    <label>Residence Type:</label>
    <select name="residence_type" required>
        <option <?= $employee['residence_type'] == 'urban' ? 'selected' : '' ?>>urban</option>
        <option <?= $employee['residence_type'] == 'rural' ? 'selected' : '' ?>>rural</option>
    </select>

    <label>Profile Picture:</label>
    <input type="file" name="profile_pic">

    <button type="submit">Update Profile</button>
</form>

</body>
</html>
