<?php
$title = "Login / Sign Up - EasyCart";
$base_path = "../";
$page = "auth";
$extra_css = "auth.css";
include '../includes/header.php';
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
                        <input type="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" placeholder="Password" required id="loginPass">
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('loginPass', this)"></i>
                    </div>
                    <a href="#" class="forgot-pass">Forgot your password?</a>
                    <button type="submit" class="btn">Sign In</button>
                </form>

                <!-- Signup Form -->
                <form id="signupForm" class="auth-form" style="display: none;">
                    <h3>Create Account</h3>
                    <!-- Social Login Removed -->

                    <div class="form-group">
                        <input type="text" placeholder="Full Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" placeholder="Password" required id="signupPass">
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('signupPass', this)"></i>
                    </div>
                    <div class="form-group">
                        <input type="password" placeholder="Confirm Password" required id="signupConfirmPass">
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('signupConfirmPass', this)"></i>
                    </div>
                    <button type="submit" class="btn">Sign Up</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const signupForm = document.getElementById('signupForm');
        const loginBtn = document.getElementById('loginBtn');
        const signupBtn = document.getElementById('signupBtn');
        const authLeftH2 = document.querySelector('.auth-left h2');
        const authLeftP = document.querySelector('.auth-left p');

        function showLogin() {
            loginForm.style.display = 'block';
            signupForm.style.display = 'none';
            loginBtn.classList.add('active');
            signupBtn.classList.remove('active');
            
            // Update Text on Left Side with animation
            updateLeftContent("Welcome Back!", "To keep connected with us please login with your personal info");
        }

        function showSignup() {
            loginForm.style.display = 'none';
            signupForm.style.display = 'block';
            signupBtn.classList.add('active');
            loginBtn.classList.remove('active');

            // Update Text on Left Side with animation
             updateLeftContent("Hello, Friend!", "Enter your personal details and start your journey with us");
        }

        function updateLeftContent(title, text) {
            authLeftH2.style.opacity = 0;
            authLeftP.style.opacity = 0;
            setTimeout(() => {
                authLeftH2.innerText = title;
                authLeftP.innerText = text;
                authLeftH2.style.opacity = 1;
                authLeftP.style.opacity = 1;
            }, 200);
        }

        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
<?php include '../includes/footer.php'; ?>
