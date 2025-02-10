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
        if (!isset($_POST['Username']) || !isset($_POST['Pass'])) {
            throw new Exception("Invalid input.");
        }

        $username = trim($_POST['Username']);
        $password = trim($_POST['Pass']);

        if (mb_strlen($username) > 30) {
            throw new Exception("Invalid username.");
        }
        if (mb_strlen($password) > 72) {
            throw new Exception("Invalid password.");
        }

        // ✅ 3️⃣ Sanitize input
        $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

        // ✅ 4️⃣ Check if user exists
        $stmt = $pdo->prepare("SELECT id, Pass FROM users WHERE Username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['Pass'])) {
            throw new Exception("Invalid credentials.");
        }

        // ✅ 5️⃣ Store session
        $_SESSION['user_id'] = $user['id'];
        header("Location: profile.php");
        exit();
    } catch (Exception $e) {
        error_log($e->getMessage()); // Log error internally
        $error_message = "Login failed. Please check your credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Login</h1>
        </header>

        <main class="content">
            <h2>Access Your Account</h2>

            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <label for="Username">Username</label>
                <input type="text" id="Username" name="Username" placeholder="Enter your username" maxlength="30" required>

                <label for="Pass">Password</label>
                <input type="password" id="Pass" name="Pass" placeholder="Enter your password" maxlength="72" required>

                <button type="submit" class="primary-btn">Login</button>
            </form>

            <a href="signup.php">Don't have an account? Sign up</a>
        </main>

        <footer class="footer">
            <p>&copy; 2024 Lip Reading Transcription</p>
        </footer>
    </div>
</body>
</html>
