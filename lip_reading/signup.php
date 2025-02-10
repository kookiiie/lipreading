<?php
// Secure Headers
header("X-Frame-Options: DENY");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; frame-ancestors 'none';");

// Secure session cookies
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF Token Setup
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require 'db.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // ✅ 1️⃣ CSRF Protection
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception("Invalid request.");
        }

        // ✅ 2️⃣ Validate Inputs
        if (!isset($_POST['Username']) || !isset($_POST['Pass']) || !isset($_POST['ID']) || !isset($_POST['DOB'])) {
            throw new Exception("Invalid input.");
        }

        $username = trim($_POST['Username']);
        $password = trim($_POST['Pass']);
        $id = trim($_POST['ID']);
        $dob = trim($_POST['DOB']);

        if (mb_strlen($username) > 30) {
            throw new Exception("Invalid username.");
        }
        if (mb_strlen($password) > 72) {
            throw new Exception("Invalid password.");
        }
        if (!preg_match("/^\d{1,10}$/", $id)) {
            throw new Exception("Invalid ID.");
        }
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $dob)) {
            throw new Exception("Invalid date format.");
        }

        // ✅ 3️⃣ Sanitize Inputs
        $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // ✅ 4️⃣ Check if username is taken
        $stmt = $pdo->prepare("SELECT id FROM users WHERE Username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            throw new Exception("Username already taken.");
        }

        // ✅ 5️⃣ Insert User
        $stmt = $pdo->prepare("INSERT INTO users (Username, Pass, ID, DOB) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $hashed_password, $id, $dob])) {
            header("Location: login.php");
            exit();
        } else {
            throw new Exception("Unable to create account.");
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        $error_message = "Sign-up failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Sign Up</h1>
        </header>

        <main class="content">
            <h2>Create a New Account</h2>

            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="signup.php">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <label for="Username">Username</label>
                <input type="text" id="Username" name="Username" placeholder="Create a username" maxlength="30" required>

                <label for="Pass">Password</label>
                <input type="password" id="Pass" name="Pass" placeholder="Create a password" maxlength="72" required>

                <label for="ID">ID</label>
                <input type="number" id="ID" name="ID" placeholder="Enter your PWD ID" max="9999999999" required>

                <label for="DOB">Date of Birth</label>
                <input type="date" id="DOB" name="DOB" required>

                <button type="submit" class="primary-btn">Sign Up</button>
            </form>

            <a href="login.php">Already have an account? Login</a>
        </main>

        <footer class="footer">
            <p>&copy; 2024 Lip Reading Transcription</p>
        </footer>
    </div>
</body>
</html>
