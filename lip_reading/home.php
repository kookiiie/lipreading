<?php
session_start();
include 'db.php';

// Logout 
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit(); 
}


// if (!isset($_SESSION['Username'])) {
//     echo "❌ Session variable 'Username' is not set.<br>";
//     echo "Debug: ";
//     print_r($_SESSION); 
//     exit();
// }

$username = trim($_SESSION['Username']);  


$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

// if (!$user) {
//     echo "❌ User not found in the database.";
//     exit();
// }
?>

<!DOCTYPE html>
<script nonce="ABC123">
    console.log("Secure script!");
</script>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>What would you like to do?</h1> 
        </header>

        <main class="content">
            <section class="user-actions">
                <a href="profile.php" class="primary-btn">Go to Profile</a>

                <form action="home.php" method="POST">
                    <button type="submit" name="logout" class="secondary-btn">Logout</button>
                </form>
            </section>
        </main>

        <footer class="footer">
            <p>&copy; 2024 Lip Reading Transcription</p>
        </footer>
    </div>
</body>
</html>
