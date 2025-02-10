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

?>


<!DOCTYPE html>
<script nonce="ABC123">
    console.log("Secure script!");
</script>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Lip Reading Transcription</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Welcome to Lip Reading Transcription</h1>
            <p>For People with Unilateral Cleft Palate</p>
        </header>

        <main class="content">
            <section class="auth-options">
                <?php
                session_start();
                    echo '<a href="signup.php" class="green-btn">Sign Up</a>';
                    echo '<a href="login.php" class="green-btn">Login</a>';
                
                ?>
            </section>
        </main>

        <footer class="footer">
            <p>&copy; 2024 Lip Reading Transcription</p>
        </footer>
    </div>
</body>
</html>
