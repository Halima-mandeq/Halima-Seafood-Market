<?php
// Auth/forgot_password.php
include '../Includes/db.php';
session_start();

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize($conn, $_POST['username']);
    $email = sanitize($conn, $_POST['email']);

    $sql = "SELECT id FROM users WHERE username = '$username' AND email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        // In a real system, send email. For this prototype, we redirect to reset page.
        header("Location: reset_password.php?id=" . $user['id']);
        exit();
    } else {
        $error = "No account found with that username and email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Halima Seafood Market</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../Assets/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f8fafc]">
    <div class="centered-layout">
        <div class="auth-card animate-fade">
            <img src="../Images/Logo.png" alt="Logo" class="h-24 mx-auto mb-8 object-contain">
            
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Forgot Password</h1>
            <p class="text-gray-500 text-sm mb-10">Enter your details to reset your account password.</p>

            <?php if($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-semibold border border-red-100 italic text-left">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="forgot_password.php" method="POST" class="space-y-6 text-left">
                <div class="auth-input-group">
                    <label class="text-gray-900 !font-bold !text-sm mb-2">Username</label>
                    <div class="auth-input-wrapper">
                        <input type="text" name="username" class="auth-input !bg-white !pl-12" placeholder="Enter your username" required>
                        <i class="fa-solid fa-user auth-input-icon"></i>
                    </div>
                </div>

                <div class="auth-input-group">
                    <label class="text-gray-900 !font-bold !text-sm mb-2">Email</label>
                    <div class="auth-input-wrapper">
                        <input type="email" name="email" class="auth-input !bg-white !pl-12" placeholder="Enter your email address" required>
                        <i class="fa-solid fa-envelope auth-input-icon"></i>
                    </div>
                </div>

                <button type="submit" class="auth-btn uppercase tracking-wider bg-[#0284c7] hover:bg-[#0369a1] py-4 rounded-xl">
                    CONFIRM <i class="fa-solid fa-rotate-right ml-2 text-sm"></i>
                </button>
            </form>

            <div class="mt-8">
                <a href="index.php" class="text-[#0284c7] font-bold text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>
