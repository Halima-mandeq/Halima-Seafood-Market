<?php
// Auth/reset_password.php
include '../Includes/db.php';
session_start();

$error = "";
$success = "";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$user_id = sanitize($conn, $_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'";
        
        if (mysqli_query($conn, $sql)) {
            $success = "Password updated successfully! <a href='index.php' class='underline'>Login here</a>";
        } else {
            $error = "Error updating password: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Halima Seafood Market</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../Assets/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f8fafc]">
    <div class="centered-layout">
        <div class="auth-card animate-fade">
            <img src="../Images/Logo.png" alt="Logo" class="h-24 mx-auto mb-8 object-contain">
            
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Reset Password</h1>
            <p class="text-gray-500 text-sm mb-10">Please enter your new security credentials below</p>

            <?php if($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-semibold border border-red-100 italic text-left">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 text-sm font-semibold border border-green-100 text-left">
                    <i class="fa-solid fa-circle-check mr-2"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form action="reset_password.php?id=<?php echo $user_id; ?>" method="POST" class="space-y-6 text-left">
                <div class="auth-input-group">
                    <label class="text-gray-900 !font-bold !text-sm mb-2">New Password</label>
                    <div class="auth-input-wrapper">
                        <input type="password" name="new_password" id="new_password" class="auth-input !bg-white !pl-12" placeholder="Enter new password" required>
                        <i class="fa-solid fa-lock auth-input-icon"></i>
                        <i class="fa-solid fa-eye auth-input-icon-right" onclick="toggleVisibility('new_password')"></i>
                    </div>
                </div>

                <div class="auth-input-group">
                    <label class="text-gray-900 !font-bold !text-sm mb-2">Confirm Password</label>
                    <div class="auth-input-wrapper">
                        <input type="password" name="confirm_password" id="confirm_password" class="auth-input !bg-white !pl-12" placeholder="Confirm your new password" required>
                        <i class="fa-solid fa-lock auth-input-icon"></i>
                        <i class="fa-solid fa-eye auth-input-icon-right" onclick="toggleVisibility('confirm_password')"></i>
                    </div>
                </div>

                <button type="submit" class="auth-btn uppercase tracking-wider bg-[#0284c7] hover:bg-[#0369a1] py-4 rounded-xl">
                    Update Password <i class="fa-solid fa-rotate-right ml-2 text-sm"></i>
                </button>
            </form>

            <div class="mt-8">
                <a href="index.php" class="text-[#0284c7] font-bold text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>

    <script>
        function toggleVisibility(id) {
            const pwd = document.getElementById(id);
            const icon = event.target;
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>
