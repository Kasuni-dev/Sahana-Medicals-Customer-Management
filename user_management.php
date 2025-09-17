<?php
session_start();
require_once 'db_connection.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
        $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        $password = password_hash(mysqli_real_escape_string($conn, $_POST['password']), PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (firstName, lastName, email, phone, address, status, password) 
                VALUES ('$firstName', '$lastName', '$email', '$phone', '$address', '$status', '$password')";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success_message'] = "User added successfully!";
        } else {
            $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        }
        
        // Redirect to prevent form resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    
    if (isset($_POST['update_user'])) {
        $id = (int)$_POST['user_id'];
        $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
        $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        $sql = "UPDATE users SET 
                firstName='$firstName', 
                lastName='$lastName', 
                email='$email', 
                phone='$phone', 
                address='$address', 
                status='$status' 
                WHERE id=$id";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success_message'] = "User updated successfully!";
        } else {
            $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        }
        
        // Redirect to prevent form resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    
    if (isset($_POST['delete_user'])) {
        $id = (int)$_POST['user_id'];
        $sql = "DELETE FROM users WHERE id = $id";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success_message'] = "User deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        }
        
        // Redirect to prevent form resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Fetch user data for editing if ID is provided
$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id = $edit_id");
    if ($result && mysqli_num_rows($result) > 0) {
        $edit_user = mysqli_fetch_assoc($result);
    }
}

// Fetch all users
$users = [];
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
if ($result) {
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Get messages from session and then clear them
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | Sahana Medicals</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }

        /* Header */
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

        /* Admin Content */
        .admin-container {
            padding: 40px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .admin-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .admin-title {
            font-size: 2.5rem;
            font-weight: bold;
            background: linear-gradient(135deg, #4a69bd, #1e3799);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .admin-subtitle {
            color: #666;
            font-size: 1.1rem;
        }

        /* User Table */
        .user-table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 30px;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .search-box {
            display: flex;
            gap: 15px;
        }

        .search-input {
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            width: 300px;
        }

        .search-input:focus {
            border-color: #4a69bd;
            outline: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4a69bd, #1e3799);
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 105, 189, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #27ae60, #229954);
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
        }

        .user-table th,
        .user-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .user-table th {
            background-color: #f0f5ff;
            font-weight: 600;
            color: #1e3799;
        }

        .user-table tr:hover {
            background-color: #f8f9fa;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
        }

        .action-btn {
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .edit-btn {
            background: #4a69bd;
            color: white;
        }

        .edit-btn:hover {
            background: #1e3799;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
        }

        .delete-btn:hover {
            background: #c0392b;
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            display: none;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            width: 600px;
            max-width: 90%;
            padding: 30px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
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

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #4a69bd;
            box-shadow: 0 0 0 0.2rem rgba(74, 105, 189, 0.25);
            outline: none;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 30px 0 15px 0;
            margin-top: 50px;
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

        /* Response Messages */
        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .search-box {
                width: 100%;
            }

            .search-input {
                width: 100%;
            }

            .user-table {
                display: block;
                overflow-x: auto;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 8px;
            }
        }

        @media (max-width: 576px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .admin-title {
                font-size: 2rem;
            }
            
            .admin-container {
                padding: 20px 15px;
            }
            
            .user-table-container {
                padding: 15px;
            }
            
            .modal-content {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <div class="navbar-brand-wrapper">
                <a class="navbar-brand" href="#">SAHANA</a>
                <div class="brand-subtitle">MEDICALS</div>
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="user_management.php">User</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="employee_management.php">Employee</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product_management.php">Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="supplier_management.php">Supplier</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-user"></i> Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Admin Content -->
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">User Management</h1>
            <p class="admin-subtitle">Admin panel for managing user accounts</p>
        </div>

        <!-- Display Messages -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- User Table -->
        <div class="user-table-container">
            <div class="table-header">
                <h2 class="table-title">Registered Users</h2>
                <div class="search-box">
                    <input type="text" class="search-input" id="searchInput" placeholder="Search users...">
                    <button class="btn-primary" id="addUserBtn">
                        <i class="fas fa-plus"></i> Add New User
                    </button>
                </div>
            </div>

            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td><?php echo htmlspecialchars($user['address']); ?></td>
                                <td>
                                    <?php 
                                    $status_class = '';
                                    if ($user['status'] == 'active') $status_class = 'bg-success';
                                    elseif ($user['status'] == 'inactive') $status_class = 'bg-warning';
                                    elseif ($user['status'] == 'suspended') $status_class = 'bg-danger';
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($user['status']); ?></span>
                                </td>
                                <td class="action-buttons">
                                    <a href="?edit=<?php echo $user['id']; ?>" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="action-btn delete-btn" 
                                                onclick="return confirm('Are you sure you want to delete this user?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-users fa-2x mb-3 text-muted"></i>
                                <h5 class="text-muted">No users found</h5>
                                <p class="text-muted">Add your first user using the button above</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div class="modal-overlay" id="userModal" <?php echo $edit_user ? 'style="display: flex;"' : ''; ?>>
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle"><?php echo $edit_user ? 'Edit User' : 'Add New User'; ?></h3>
                <button class="close-modal">&times;</button>
            </div>

            <form method="POST" id="userForm">
                <?php if ($edit_user): ?>
                    <input type="hidden" name="user_id" value="<?php echo $edit_user['id']; ?>">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="firstName" value="<?php echo $edit_user ? htmlspecialchars($edit_user['firstName']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="lastName" value="<?php echo $edit_user ? htmlspecialchars($edit_user['lastName']) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo $edit_user ? htmlspecialchars($edit_user['email']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" name="phone" value="<?php echo $edit_user ? htmlspecialchars($edit_user['phone']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-control" name="address" value="<?php echo $edit_user ? htmlspecialchars($edit_user['address']) : ''; ?>" required>
                </div>

                <?php if (!$edit_user): ?>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select class="form-control" name="status" required>
                        <option value="active" <?php echo $edit_user && $edit_user['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $edit_user && $edit_user['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        <option value="suspended" <?php echo $edit_user && $edit_user['status'] == 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-danger close-modal">Cancel</button>
                    <button type="submit" name="<?php echo $edit_user ? 'update_user' : 'add_user'; ?>" class="btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
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
                        <li><a href="index.php">Home</a></li>
                        <li><a href="shop.php">Shop</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="aboutus.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Online Pharmacy</a></li>
                        <li><a href="#">Prescription Upload</a></li>
                        <li><a href="#">Home Delivery</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Connect With Us</h4>
                    <p>Follow us on social media for health tips and updates</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
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
        // DOM Elements
        const userModal = document.getElementById('userModal');
        const modalTitle = document.getElementById('modalTitle');
        const userForm = document.getElementById('userForm');
        const addUserBtn = document.getElementById('addUserBtn');

        
        function openModal(modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        
        function closeModals() {
            userModal.style.display = 'none';
            document.body.style.overflow = 'auto';
            
            // Redirect to clear edit parameters
            <?php if ($edit_user): ?>
                window.location.href = 'user_management.php';
            <?php endif; ?>
        }

        // Add new user
        addUserBtn.addEventListener('click', () => {
            modalTitle.textContent = 'Add New User';
            userForm.reset();
            openModal(userModal);
        });

        // Close modals 
        document.querySelectorAll('.close-modal').forEach(btn => {
            btn.addEventListener('click', closeModals);
        });

        userModal.addEventListener('click', (e) => {
            if (e.target === userModal) closeModals();
        });

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('keyup', () => {
            const searchTerm = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('.user-table tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

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
        
        // Automatically show modal 
        <?php if ($edit_user): ?>
            document.addEventListener('DOMContentLoaded', function() {
                openModal(userModal);
            });
        <?php endif; ?>
    </script>
</body>

</html>