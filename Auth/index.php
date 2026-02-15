<?php
// Auth/index.php
include '../Includes/db.php';
session_start();

$error = "";
$success = "";
$active_form = $_GET['form'] ?? "login"; // Default to login but respect GET param

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == 'login') {
        $active_form = "login";
        $username = sanitize($conn, $_POST['username']);
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];

                if ($user['role'] == 'admin') {
                    header("Location: ../Admin/index.php");
                } else {
                    header("Location: ../User/index.php");
                }
                exit();
            } else { $error = "Invalid username or password."; }
        } else { $error = "Invalid username or password."; }
    } 
    else if ($action == 'register') {
        $active_form = "register";
        $full_name = sanitize($conn, $_POST['full_name']);
        $email = sanitize($conn, $_POST['email']);
        $phone = sanitize($conn, $_POST['phone']);
        $username = sanitize($conn, $_POST['username']);
        $password = $_POST['password'];

        if (empty($full_name) || empty($email) || empty($phone) || empty($username) || empty($password)) {
            $error = "All fields are required.";
        } else {
            $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' OR username = '$username'");
            if (mysqli_num_rows($check) > 0) {
                $error = "Email or Username already exists.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (full_name, email, phone_number, username, password) 
                        VALUES ('$full_name', '$email', '$phone', '$username', '$hashed_password')";
                if (mysqli_query($conn, $sql)) {
                    $new_id = mysqli_insert_id($conn);

                    // AUTO-MESSAGE: Welcome the new user to the chat system
                    $welcome_msg = "Welcome to Halima Seafood Market! 🐟\nHow can we help you today?";
                    // Assuming Admin ID 1 is the system sender
                    $admin_sender_id = 1; 
                    
                    $msg_sql = "INSERT INTO messages (sender_id, receiver_id, message, is_read) 
                                VALUES ($admin_sender_id, $new_id, '$welcome_msg', 0)";
                    mysqli_query($conn, $msg_sql);

                    $success = "Account created successfully! You can now login.";
                    $active_form = "login";
                } else { $error = "Error creating account: " . mysqli_error($conn); }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication | Halima Seafood Market</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../Assets/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .hidden-form { display: none !important; }
        .auth-toggle-btn { cursor: pointer; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="auth-container">
        <!-- Sidebar -->
        <div class="auth-sidebar md:flex hidden animate-slide-left">
            <div class="flex flex-col items-end w-full">
                <div id="btn-login" onclick="showForm('login')" class="auth-toggle-btn <?php echo $active_form == 'login' ? 'border-r-4 border-[#0284c7]' : 'inactive'; ?>">
                    <i class="fa-solid fa-arrow-right-to-bracket mr-2"></i> LOGIN
                </div>
                <div id="btn-register" onclick="showForm('register')" class="auth-toggle-btn <?php echo $active_form == 'register' ? 'border-r-4 border-[#0284c7]' : 'inactive'; ?>">
                    <i class="fa-solid fa-user-plus mr-2"></i> REGISTRATION
                </div>
            </div>
            <div class="absolute bottom-10 right-10 text-white/50 text-right">
                <p class="text-sm">Join our premium seafood<br>community for exclusive daily<br>catches and wholesale pricing.</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="auth-main animate-fade">
            <div class="auth-card">
                
                <?php if($error): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-semibold border border-red-100 italic">
                        <i class="fa-solid fa-circle-exclamation mr-2"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if($success): ?>
                    <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 text-sm font-semibold border border-green-100">
                        <i class="fa-solid fa-circle-check mr-2"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <!-- LOGIN FORM -->
                <div id="form-login" class="<?php echo $active_form != 'login' ? 'hidden-form' : ''; ?>">
                    <img src="../Images/Logo.png" alt="Logo" class="h-32 mx-auto mb-8 object-contain">
                    <h1 class="text-4xl font-black text-[#0284c7] tracking-tight mb-2 uppercase">Login</h1>
                    <p class="text-gray-500 font-medium mb-10">Halima Seafood Market</p>

                    <form action="index.php" method="POST" class="space-y-6">
                        <input type="hidden" name="action" value="login">
                        <div class="auth-input-group">
                            <div class="auth-input-wrapper">
                                <input type="text" name="username" class="auth-input" placeholder="Username" required>
                                <i class="fa-solid fa-user auth-input-icon"></i>
                            </div>
                        </div>
                        <div class="auth-input-group">
                            <div class="auth-input-wrapper">
                                <input type="password" name="password" class="auth-input" placeholder="Password" required>
                                <i class="fa-solid fa-lock auth-input-icon"></i>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <a href="forgot_password.php" class="auth-link">Forgot Password?</a>
                            <button type="submit" class="auth-btn w-auto px-12 rounded-full uppercase tracking-wider bg-[#0284c7] hover:bg-[#0369a1]">LOGIN</button>
                        </div>
                    </form>
                </div>

                <!-- REGISTRATION FORM -->
                <div id="form-register" class="<?php echo $active_form != 'register' ? 'hidden-form' : ''; ?>">
                    <div class="bg-[#0284c7]/5 p-8 rounded-full inline-block mb-6">
                        <img src="../Images/Logo.png" alt="Logo" class="h-24 w-24 object-contain">
                    </div>
                    <h1 class="text-4xl font-black text-[#0284c7] tracking-tight mb-2 uppercase">Registration</h1>
                    
                    <form action="index.php" method="POST" class="space-y-5 text-left mt-6">
                        <input type="hidden" name="action" value="register">
                        <div class="auth-input-group">
                            <label>Full Name</label>
                            <div class="auth-input-wrapper">
                                <input type="text" name="full_name" class="auth-input" placeholder="Full Name" required>
                                <i class="fa-solid fa-user auth-input-icon"></i>
                            </div>
                        </div>
                        <div class="auth-input-group">
                            <label>Email</label>
                            <div class="auth-input-wrapper">
                                <input type="email" name="email" class="auth-input" placeholder="Email" required>
                                <i class="fa-solid fa-envelope auth-input-icon"></i>
                            </div>
                        </div>
                        <div class="auth-input-group flex gap-4">
                            <div class="flex-1">
                                <label>Phone</label>
                                <div class="auth-input-wrapper">
                                    <input type="text" name="phone" class="auth-input !pl-6" placeholder="Phone" required>
                                </div>
                            </div>
                            <div class="flex-1">
                                <label>Username</label>
                                <div class="auth-input-wrapper">
                                    <input type="text" name="username" class="auth-input !pl-6" placeholder="Username" required>
                                </div>
                            </div>
                        </div>
                        <div class="auth-input-group">
                            <label>Password</label>
                            <div class="auth-input-wrapper">
                                <input type="password" name="password" id="reg-password" class="auth-input" placeholder="........." required>
                                <i class="fa-solid fa-lock auth-input-icon"></i>
                            </div>
                        </div>
                        <button type="submit" class="auth-btn uppercase tracking-wider bg-[#0284c7] hover:bg-[#0369a1]">
                            CREATE ACCOUNT <i class="fa-solid fa-arrow-right ml-2"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            showForm('<?php echo $active_form; ?>');
        };

        function showForm(type) {
            const loginForm = document.getElementById('form-login');
            const registerForm = document.getElementById('form-register');
            const loginBtn = document.getElementById('btn-login');
            const registerBtn = document.getElementById('btn-register');

            if (type === 'login') {
                loginForm.classList.remove('hidden-form');
                registerForm.classList.add('hidden-form');
                loginBtn.classList.add('border-r-4', 'border-[#0284c7]');
                loginBtn.classList.remove('inactive');
                registerBtn.classList.add('inactive');
                registerBtn.classList.remove('border-r-4', 'border-[#0284c7]');
            } else if (type === 'register') {
                loginForm.classList.add('hidden-form');
                registerForm.classList.remove('hidden-form');
                registerBtn.classList.add('border-r-4', 'border-[#0284c7]');
                registerBtn.classList.remove('inactive');
                loginBtn.classList.add('inactive');
                loginBtn.classList.remove('border-r-4', 'border-[#0284c7]');
            }
        }
    </script>
</body>
</html>
