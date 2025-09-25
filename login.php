<?php
session_start();
require_once 'db_connection.php';

// Redirect to appropriate page if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header("Location: user_management.php");
    } else {
        header("Location: profile.php");
    }
    exit();
}

// Handle login form submission
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = mysqli_real_escape_string($conn, $_POST['identifier']);
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($identifier) || empty($password)) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Check if identifier is email or phone
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $sql = "SELECT * FROM users WHERE email = '$identifier'";
        } else {
            $sql = "SELECT * FROM users WHERE phone = '$identifier'";
        }
        
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['firstName'] . ' ' . $user['lastName'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: user_management.php");
                } else {
                    header("Location: profile.php");
                }
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "User not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sahana Medicals</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Your existing CSS styles from the login.html */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
        }

        /* Header Styles */
        .top-header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 8px 0;
            font-size: 14px;
        }

        .header-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-contact {
            display: flex;
            gap: 20px;
        }

        .header-contact span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Navigation Styles */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            padding: 15px 0;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-size: 2.8rem;
            font-weight: bold;
            background: linear-gradient(135deg, #4a69bd, #1e3799);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 3px;
        }

        .brand-subtitle {
            font-size: 0.8rem;
            color: #666;
            margin-left: -10px;
            letter-spacing: 4px;
            font-weight: 300;
        }

        .navbar-nav .nav-link {
            color: #2c3e50 !important;
            font-weight: 600;
            margin: 0 15px;
            font-size: 1.1rem;
            position: relative;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #4a69bd !important;
            transform: translateY(-2px);
        }

        .navbar-nav .nav-link.active {
            color: #4a69bd !important;
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 50%;
            background: linear-gradient(135deg, #4a69bd, #1e3799);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .navbar-nav .nav-link:hover::after,
        .navbar-nav .nav-link.active::after {
            width: 100%;
        }

        /* Cart/Wishlist Icons */
        .nav-icons {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-left: 20px;
        }

        .nav-icon {
            color: #2c3e50 !important;
            font-size: 1.3rem;
            position: relative;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .nav-icon:hover {
            color: #4a69bd !important;
            transform: scale(1.1);
        }

        .cart-count,
        .wishlist-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Main Content Styles */
        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 120px);
            padding: 40px 20px;
        }

        .forms-wrapper {
            display: flex;
            gap: 30px;
            max-width: 1000px;
            width: 100%;
        }

        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            flex: 1;
            min-height: 500px;
            transition: transform 0.3s ease;
        }

        .form-container:hover {
            transform: translateY(-5px);
        }

        .login-section {
            padding: 40px;
            background: #f8f9fa;
            height: 100%;
        }

        .registration-section {
            padding: 40px;
            background: linear-gradient(135deg, #4a69bd, #1e3799);
            color: white;
            height: 100%;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .section-subtitle {
            font-size: 0.9rem;
            margin-bottom: 30px;
            color: #666;
        }

        .registration-section .section-subtitle {
            color: rgba(255, 255, 255, 0.9);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .registration-section .form-label {
            color: white;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1.rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #4a69bd;
            box-shadow: 0 0 0 0.2rem rgba(74, 105, 189, 0.25);
            outline: none;
        }

        .registration-section .form-control {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .registration-section .form-control:focus {
            background: white;
            border-color: rgba(255, 255, 255, 0.8);
        }

        .btn-primary {
            background: linear-gradient(135deg, #4a69bd, #1e3799);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            color: white;
            width: 100%;
            margin-top: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 105, 189, 0.4);
        }

        .registration-section .btn-primary {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.5);
            color: white;
        }

        .registration-section .btn-primary:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.8);
        }

        .required {
            color: #e74c3c;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        /* Additional form enhancements */
        .password-toggle {
            position: relative;
        }

        .password-toggle-icon {
            position: absolute;
            right: 15px;
            top: 42px;
            cursor: pointer;
            color: #6c757d;
        }

        .form-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9rem;
        }

        .form-footer a {
            color: #4a69bd;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .registration-section .form-footer a {
            color: rgba(255, 255, 255, 0.9);
        }

        /* Response Messages */
        .response-message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        .response-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .response-error {
            background-color: 'f8d7da';
            color: '721c24';
            border: 1px solid 'f5c6cb';
        }

        /* Footer Styles */
        .footer {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 30px 0 15px 0;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            margin-bottom: 20px;
        }

        .footer-section h4 {
            color: #4a69bd;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .footer-section p,
        .footer-section li {
            color: #bdc3c7;
            line-height: 1.5;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li a {
            color: #bdc3c7;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: #4a69bd;
        }

        .social-links {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .social-links a {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: #4a69bd;
            transform: translateY(-2px);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 15px;
            text-align: center;
            color: #95a5a6;
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .forms-wrapper {
                flex-direction: column;
                gap: 20px;
                margin: 20px;
            }

            .login-section,
            .registration-section {
                padding: 30px;
            }

            .navbar-brand {
                font-size: 2rem;
            }

            .navbar-nav .nav-link {
                margin: 0 10px;
                font-size: 1rem;
            }

            .nav-icons {
                gap: 10px;
                margin-left: 10px;
            }

            .header-contact {
                flex-direction: column;
                gap: 5px;
            }
        }

        @media (max-width: 576px) {
            .forms-wrapper {
                margin: 10px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Skip to main content link for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <div class="navbar-brand-wrapper">
                <a class="navbar-brand" href="home.html">SAHANA</a>
                <div class="brand-subtitle">MEDICALS</div>
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="home.html">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="shop.html">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about_us.html">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact_us.html">Contact Us</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link active" href="login.php"><i class="fas fa-user"></i> Login</a>
                    </li>
                </ul>

                <div class="nav-icons">
                    <a href="#" class="nav-icon" title="Wishlist">
                        <i class="fas fa-heart"></i>
                        <span class="wishlist-count">0</span>
                    </a>
                    <a href="cart.html" class="nav-icon" title="Cart">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">0</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content - Login and Registration Forms -->
    <div class="main-container" id="main-content">
        <div class="forms-wrapper">
            <!-- Login Box -->
            <div class="form-container">
                <div class="login-section">
                    <div class="section-title">Already a customer?</div>
                    <div class="section-subtitle">If you have an account with us, log in using your email address.</div>

                    <?php if (!empty($error_message)): ?>
                        <div class="response-message response-error">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="loginEmail" class="form-label">Email / Phone number <span
                                    class="required">*</span></label>
                            <input type="text" class="form-control" id="loginEmail" name="identifier" required>
                        </div>

                        <div class="form-group password-toggle">
                            <label for="loginPassword" class="form-label">Password <span
                                    class="required">*</span></label>
                            <input type="password" class="form-control" id="loginPassword" name="password" required>
                            <span class="password-toggle-icon" id="loginPasswordToggle">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>

                        <button type="submit" class="btn btn-primary">Sign in ></button>

                        <div class="form-footer">
                            <a href="#forgot-password">Forgot your password?</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Registration Box -->
            <div class="form-container">
                <div class="registration-section">
                    <div class="section-title">Is this your first time?</div>
                    <div class="section-subtitle">Fill in following information to upload Prescription.</div>

                    <form action="register.php" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstName" class="form-label">First Name <span
                                        class="required">*</span></label>
                                <input type="text" class="form-control" id="firstName" name="first_name" required>
                            </div>
                            <div class="form-group">
                                <label for="lastName" class="form-label">Last Name <span
                                        class="required">*</span></label>
                                <input type="text" class="form-control" id="lastName" name="last_name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mobileNumber" class="form-label">Mobile number <span
                                    class="required">*</span></label>
                            <input type="tel" class="form-control" id="mobileNumber" name="mobile" required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email <span class="required">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="address" class="form-label">Address <span class="required">*</span></label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>

                        <div class="form-group password-toggle">
                            <label for="password" class="form-label">Password <span class="required">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <span class="password-toggle-icon" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>

                        <div class="form-group password-toggle">
                            <label for="confirmPassword" class="form-label">Confirm Password <span
                                    class="required">*</span></label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password"
                                required>
                            <span class="password-toggle-icon" id="confirmPasswordToggle">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>

                        <button type="submit" class="btn btn-primary">Sign up ></button>

                        <div class="form-footer">
                            By registering, you agree to our <a href="#terms">Terms of Service</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer - Site links and information -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>SAHANA MEDICALS</h4>
                    <p>Your trusted healthcare partner providing quality medicines and pharmaceutical services.</p>
                    <p><i class="fas fa-phone"></i> +1 (555) 123-4567</p>
                    <p><i class="fas fa-envelope"></i> info@sahanamedicals.com</p>
                </div>

                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="home.html">Home</a></li>
                        <li><a href="shop.html">Shop</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="about_us.html">About Us</a></li>
                        <li><a href="contact_us.html">Contact Us</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Online Pharmacy</a></li>
                        <li><a href="#">Prescription Upload</a></li>
                        <li><a href="#">Medical Consultation</a></li>
                        <li><a href="#">Home Delivery</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Connect With Us</h4>
                    <p>Follow us on social media for health tips and updates</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 Sahana Medicals. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Document ready function
        document.addEventListener('DOMContentLoaded', function () {
            // Password toggle functionality
            function setupPasswordToggle(toggleId, inputId) {
                const toggle = document.getElementById(toggleId);
                const input = document.getElementById(inputId);

                if (toggle && input) {
                    toggle.addEventListener('click', function () {
                        if (input.type === 'password') {
                            input.type = 'text';
                            toggle.innerHTML = '<i class="fas fa-eye-slash"></i>';
                        } else {
                            input.type = 'password';
                            toggle.innerHTML = '<i class="fas fa-eye"></i>';
                        }
                    });
                }
            }

            // Setup password toggles
            setupPasswordToggle('loginPasswordToggle', 'loginPassword');
            setupPasswordToggle('passwordToggle', 'password');
            setupPasswordToggle('confirmPasswordToggle', 'confirmPassword');

            // Real-time password validation
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');

            if (confirmPassword) {
                confirmPassword.addEventListener('input', function () {
                    if (password.value !== confirmPassword.value) {
                        confirmPassword.setCustomValidity('Passwords do not match');
                        confirmPassword.style.borderColor = '#e74c3c';
                    } else {
                        confirmPassword.setCustomValidity('');
                        confirmPassword.style.borderColor = '#28a745';
                    }
                });
            }

            // Navbar scroll effect
            window.addEventListener('scroll', () => {
                const navbar = document.querySelector('.navbar');
                if (window.scrollY > 100) {
                    navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                    navbar.style.backdropFilter = 'blur(15px)';
                } else {
                    navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                    navbar.style.backdropFilter = 'blur(10px)';
                }
            });

            // Page load animation
            document.body.style.opacity = '1';
            document.body.style.transform = 'translateY(0)';
        });

        // Initial body styling for loading animation
        document.body.style.opacity = '0';
        document.body.style.transform = 'translateY(20px)';
        document.body.style.transition = 'all 0.5s ease-out';
    </script>
</body>
</html>