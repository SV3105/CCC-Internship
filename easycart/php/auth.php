<?php
$title = "Login / Sign Up - EasyCart";
$base_path = "../";
$page = "auth";
$extra_css = "auth.css";
include '../includes/header.php';
include '../data/users_data.php';


// Prepare data for JS validation
$users_json = json_encode($users);
?>

    <div class="auth-page-wrapper">
        <div class="auth-container">
            <!-- Left Side: Branding/Image -->
            <div class="auth-left">
                <div class="auth-content">
                    <h2>Welcome Back!</h2>
                    <p>Access your orders, wishlist, and recommendations.</p>
                </div>
            </div>

            <!-- Right Side: Forms -->
            <div class="auth-right">
                <div class="auth-toggle">
                    <button id="loginBtn" class="active" onclick="showLogin()">Login</button>
                    <button id="signupBtn" onclick="showSignup()">Sign Up</button>
                </div>

                <!-- Login Form -->
                <form id="loginForm" class="auth-form">
                    <h3>Sign In</h3>
                    <!-- Social Login Removed -->
                    
                    <div class="form-group">
                        <input type="email" id="loginEmail" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" placeholder="Password" required id="loginPass">
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('loginPass', this)"></i>
                    </div>
                    <button type="submit" class="btn">Sign In</button>
                </form>

                <!-- Signup Form -->
                <form id="signupForm" class="auth-form" style="display: none;">
                    <h3>Create Account</h3>
                    <!-- Social Login Removed -->

                    <div class="form-group">
                        <input type="text" id="signupName" name="name" placeholder="Full Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" id="signupEmail" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="signupPass" name="password" placeholder="Password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('signupPass', this)"></i>
                    </div>
                    <div class="form-group">
                        <input type="password" id="signupConfirmPass" placeholder="Confirm Password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('signupConfirmPass', this)"></i>
                    </div>
                    <button type="submit" class="btn">Sign Up</button>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/auth.js"></script>
    <script>
        const usersData = <?php echo $users_json; ?>;
    </script>

<?php include '../includes/footer.php'; ?>
