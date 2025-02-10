<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user) {
    die("User not found.");
}

$update_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $address = htmlspecialchars(trim($_POST['address']));
    
    $stmt = $pdo->prepare("UPDATE users SET name = ?, address = ? WHERE id = ?");
    if ($stmt->execute([$name, $address, $user_id])) {
        header("Location: home.php");
        exit();
    } else {
        $update_message = "Error updating profile.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$user_id])) {
        session_destroy();  
        header("Location: index.php");
        exit();
    } else {
        $update_message = "Error deleting profile.";
    }
}
?>

<!DOCTYPE html>
<script nonce="ABC123">
    console.log("Secure script!");
</script>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>My Profile</h1>
        </header>

        <main class="content">
            <a href="home.php" class="primary-btn home-btn">Home</a>
            <h2>Profile Information</h2>
            <div class="profile-info">
                <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
                <p><strong>User ID:</strong> <?php echo htmlspecialchars($user['id']); ?></p>
                <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['dob']); ?></p>
            </div>

            <form method="POST" action="profile.php">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
                <button type="submit" name="update" class="primary-btn">Update Profile</button>
            </form>
            <form method="POST" action="profile.php" onsubmit="return confirm('Are you sure you want to delete your account?');">
                <button type="submit" name="delete" class="delete-btn">Delete Account</button>
            </form>
        </main>
        <footer class="footer">
            <p>&copy; 2024 Lip Reading Transcription</p>
        </footer>
    </div>
</body>
</html>
