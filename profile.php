<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Initialize profile fields if they don't exist
$profile_fields = [
    'dateOfBirth' => '',
    'gender' => '',
    'bloodType' => '',
    'weight' => '',
    'emergencyContact' => '',
    'emergencyPhone' => '',
    'allergies' => '',
    'currentMedications' => '',
    'created_at' => date('Y-m-d H:i:s')
];

foreach ($profile_fields as $field => $default_value) {
    if (!isset($user[$field])) {
        $user[$field] = $default_value;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_personal'])) {
        $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
        $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $dateOfBirth = mysqli_real_escape_string($conn, $_POST['dateOfBirth']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        
        // Build dynamic SQL query based on available columns
        $update_fields = [
            "firstName='$firstName'",
            "lastName='$lastName'",
            "email='$email'",
            "phone='$phone'",
            "address='$address'"
        ];
        
        // Add optional fields if they exist in the database
        if ($dateOfBirth) {
            $update_fields[] = "dateOfBirth='$dateOfBirth'";
        }
        if ($gender) {
            $update_fields[] = "gender='$gender'";
        }
        
        $sql = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id=$user_id";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success_message'] = "Personal information updated successfully!";
            // Update session data
            $_SESSION['user_name'] = $firstName . ' ' . $lastName;
            $_SESSION['user_email'] = $email;
        } else {
            $_SESSION['error_message'] = "Error updating personal information: " . mysqli_error($conn);
        }
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    
    if (isset($_POST['update_medical'])) {
        $bloodType = mysqli_real_escape_string($conn, $_POST['bloodType']);
        $weight = mysqli_real_escape_string($conn, $_POST['weight']);
        $emergencyContact = mysqli_real_escape_string($conn, $_POST['emergencyContact']);
        $emergencyPhone = mysqli_real_escape_string($conn, $_POST['emergencyPhone']);
        $allergies = mysqli_real_escape_string($conn, $_POST['allergies']);
        $currentMedications = mysqli_real_escape_string($conn, $_POST['currentMedications']);
        
        // Build dynamic SQL query for medical fields
        $medical_fields = [];
        if ($bloodType) $medical_fields[] = "bloodType='$bloodType'";
        if ($weight) $medical_fields[] = "weight='$weight'";
        if ($emergencyContact) $medical_fields[] = "emergencyContact='$emergencyContact'";
        if ($emergencyPhone) $medical_fields[] = "emergencyPhone='$emergencyPhone'";
        if ($allergies) $medical_fields[] = "allergies='$allergies'";
        if ($currentMedications) $medical_fields[] = "currentMedications='$currentMedications'";
        
        if (!empty($medical_fields)) {
            $sql = "UPDATE users SET " . implode(', ', $medical_fields) . " WHERE id=$user_id";
            
            if (mysqli_query($conn, $sql)) {
                $_SESSION['success_message'] = "Medical information updated successfully!";
            } else {
                $_SESSION['error_message'] = "Error updating medical information: " . mysqli_error($conn);
            }
        } else {
            $_SESSION['success_message'] = "Medical information updated successfully!";
        }
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    
    if (isset($_POST['change_password'])) {
        $currentPassword = $_POST['currentPassword'];
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];
        
        if (password_verify($currentPassword, $user['password'])) {
            if ($newPassword === $confirmPassword) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password='$hashedPassword' WHERE id=$user_id";
                
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = "Password changed successfully!";
                } else {
                    $_SESSION['error_message'] = "Error changing password: " . mysqli_error($conn);
                }
            } else {
                $_SESSION['error_message'] = "New passwords do not match!";
            }
        } else {
            $_SESSION['error_message'] = "Current password is incorrect!";
        }
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Get updated user data
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Get messages from session and then clear them
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// Get user stats (mock data for now)
$total_orders = 47;
$total_prescriptions = 12;
$total_spent = 2450;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Sahana Medicals</title>
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
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: #2c3e50;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header .navbar-brand {
            font-size: 2rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar-header {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .user-details h6 {
            margin: 0;
            font-weight: 600;
        }

        .user-details small {
            opacity: 0.9;
        }

        /* Main Container */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .page-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-title h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .page-title p {
            font-size: 1.1rem;
            color: #666;
        }

        /* Profile Cards */
        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        /* Profile Header Card */
        .profile-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 20px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 2;
        }

        .avatar-upload {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 35px;
            height: 35px;
            background: #ff6b6b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid white;
        }

        .avatar-upload:hover {
            transform: scale(1.1);
            background: #ee5a24;
        }

        .profile-name {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .profile-status {
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .profile-stats {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
            position: relative;
            z-index: 2;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Form Styles */
        .form-section {
            margin-bottom: 40px;
        }

        .form-section h4 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-control {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 15px 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
            background: white;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            background: transparent;
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        /* Security Section */
        .security-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .security-item:hover {
            background: #e9ecef;
        }

        .security-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .security-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .security-details h6 {
            margin: 0;
            color: #2c3e50;
            font-weight: 600;
        }

        .security-details small {
            color: #666;
        }

        /* Medical Info Cards */
        .medical-card {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .medical-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 80%;
            height: 80%;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .medical-card h5 {
            margin-bottom: 15px;
            font-weight: 600;
            position: relative;
            z-index: 2;
        }

        .medical-card p {
            margin: 0;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        /* Preferences */
        .preference-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .preference-item:last-child {
            border-bottom: none;
        }

        .preference-info h6 {
            margin: 0;
            color: #2c3e50;
            font-weight: 600;
        }

        .preference-info p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        .form-switch .form-check-input {
            width: 3rem;
            height: 1.5rem;
        }

        .form-switch .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        /* Activity Timeline */
        .activity-timeline {
            position: relative;
            padding-left: 30px;
        }

        .activity-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .activity-item {
            position: relative;
            margin-bottom: 25px;
        }

        .activity-item::before {
            content: '';
            position: absolute;
            left: -22px;
            top: 8px;
            width: 12px;
            height: 12px;
            background: #667eea;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 0 3px #667eea;
        }

        .activity-content {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .activity-time {
            color: #666;
            font-size: 0.85rem;
        }

        /* Alert Messages */
        .alert {
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: none;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-stats {
                flex-direction: column;
                gap: 15px;
            }
            
            .stat-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: left;
            }

            .security-item {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .page-title h1 {
                font-size: 2rem;
            }
        }

        /* Animations */
        .fade-in {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a class="navbar-brand" href="home.html">
                    <i class="fas fa-arrow-left me-3"></i>SAHANA MEDICALS
                </a>
                
                <div class="user-info">
                    <div class="user-details text-end">
                        <h6><?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></h6>
                        <small><?php echo ucfirst($user['role']); ?> Member</small>
                    </div>
                    <div class="user-avatar-header">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Page Title -->
        <div class="page-title fade-in">
            <h1>My Profile</h1>
            <p>Manage your personal information and account settings</p>
        </div>

        <!-- Alert Messages -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success fade-in">
                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger fade-in">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Profile Header -->
            <div class="col-lg-4">
                <div class="profile-card profile-header fade-in">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                        <div class="avatar-upload" data-bs-toggle="modal" data-bs-target="#avatarModal">
                            <i class="fas fa-camera"></i>
                        </div>
                    </div>
                    <div class="profile-name"><?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?></div>
                    <div class="profile-status"><?php echo ucfirst($user['role']); ?> Member</div>
                    <p style="opacity: 0.9; margin-bottom: 0;">
                        <i class="fas fa-calendar me-2"></i>Member since <?php echo date('F Y', strtotime($user['created_at'] ?? '2023-01-01')); ?>
                    </p>
                    
                    <div class="profile-stats">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $total_orders; ?></span>
                            <span class="stat-label">Orders</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $total_prescriptions; ?></span>
                            <span class="stat-label">Prescriptions</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">$<?php echo number_format($total_spent); ?></span>
                            <span class="stat-label">Total Spent</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="profile-card fade-in">
                    <h5 class="mb-3"><i class="fas fa-bolt text-warning me-2"></i>Quick Actions</h5>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary">
                            <i class="fas fa-shopping-cart me-2"></i>View Orders
                        </button>
                        <button class="btn btn-outline-primary">
                            <i class="fas fa-prescription-bottle-alt me-2"></i>My Prescriptions
                        </button>
                        <button class="btn btn-outline-primary">
                            <i class="fas fa-heart me-2"></i>Wishlist
                        </button>
                        <button class="btn btn-outline-primary">
                            <i class="fas fa-headset me-2"></i>Support
                        </button>
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="col-lg-8">
                <!-- Personal Information -->
                <div class="profile-card fade-in">
                    <div class="form-section">
                        <h4><i class="fas fa-user-edit text-primary"></i>Personal Information</h4>
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="firstName" value="<?php echo htmlspecialchars($user['firstName']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="lastName" value="<?php echo htmlspecialchars($user['lastName']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" name="dateOfBirth" value="<?php echo $user['dateOfBirth'] ?? ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gender</label>
                                    <select class="form-control" name="gender">
                                        <option value="male" <?php echo ($user['gender'] ?? '') == 'male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo ($user['gender'] ?? '') == 'female' ? 'selected' : ''; ?>>Female</option>
                                        <option value="other" <?php echo ($user['gender'] ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
                                        <option value="prefer-not-to-say" <?php echo ($user['gender'] ?? '') == 'prefer-not-to-say' ? 'selected' : ''; ?>>Prefer not to say</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                                </div>
                            </div>
                            <button type="submit" name="update_personal" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Medical Information -->
                <div class="profile-card fade-in">
                    <div class="form-section">
                        <h4><i class="fas fa-heartbeat text-danger"></i>Medical Information</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="medical-card">
                                    <h5><i class="fas fa-tint me-2"></i>Blood Type</h5>
                                    <p><?php echo $user['bloodType'] ?? 'Not specified'; ?></p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="medical-card">
                                    <h5><i class="fas fa-weight me-2"></i>Weight</h5>
                                    <p><?php echo $user['weight'] ?? 'Not specified'; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Blood Type</label>
                                    <input type="text" class="form-control" name="bloodType" value="<?php echo htmlspecialchars($user['bloodType'] ?? ''); ?>" placeholder="e.g., O+ Positive">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Weight</label>
                                    <input type="text" class="form-control" name="weight" value="<?php echo htmlspecialchars($user['weight'] ?? ''); ?>" placeholder="e.g., 75 kg (165 lbs)">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Emergency Contact</label>
                                    <input type="text" class="form-control" name="emergencyContact" value="<?php echo htmlspecialchars($user['emergencyContact'] ?? ''); ?>" placeholder="Name and relationship">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Emergency Phone</label>
                                    <input type="tel" class="form-control" name="emergencyPhone" value="<?php echo htmlspecialchars($user['emergencyPhone'] ?? ''); ?>" placeholder="Emergency contact number">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Allergies</label>
                                    <textarea class="form-control" name="allergies" rows="2" placeholder="List any drug allergies or medical conditions"><?php echo htmlspecialchars($user['allergies'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Current Medications</label>
                                    <textarea class="form-control" name="currentMedications" rows="2" placeholder="List current medications"><?php echo htmlspecialchars($user['currentMedications'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            <button type="submit" name="update_medical" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Medical Info
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Account Security -->
                <div class="profile-card fade-in">
                    <div class="form-section">
                        <h4><i class="fas fa-shield-alt text-success"></i>Account Security</h4>
                        
                        <div class="security-item">
                            <div class="security-info">
                                <div class="security-icon">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div class="security-details">
                                    <h6>Password</h6>
                                    <small>Last updated 30 days ago</small>
                                </div>
                            </div>
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#passwordModal">
                                Change Password
                            </button>
                        </div>

                        <div class="security-item">
                            <div class="security-info">
                                <div class="security-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="security-details">
                                    <h6>Two-Factor Authentication</h6>
                                    <small>Enabled via SMS</small>
                                </div>
                            </div>
                            <button class="btn btn-outline-primary">
                                Manage 2FA
                            </button>
                        </div>

                        <div class="security-item">
                            <div class="security-info">
                                <div class="security-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="security-details">
                                    <h6>Email Verification</h6>
                                    <small>Verified</small>
                                </div>
                            </div>
                            <span class="badge bg-success">Verified</span>
                        </div>
                    </div>
                </div>

                <!-- Notification Preferences -->
                <div class="profile-card fade-in">
                    <div class="form-section">
                        <h4><i class="fas fa-bell text-warning"></i>Notification Preferences</h4>
                        
                        <div class="preference-item">
                            <div class="preference-info">
                                <h6>Order Updates</h6>
                                <p>Get notified about order status changes</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </div>

                        <div class="preference-item">
                            <div class="preference-info">
                                <h6>Prescription Reminders</h6>
                                <p>Remind me when it's time to reorder medications</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </div>

                        <div class="preference-item">
                            <div class="preference-info">
                                <h6>Health Tips</h6>
                                <p>Receive health tips and wellness advice</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </div>

                        <div class="preference-item">
                            <div class="preference-info">
                                <h6>Promotional Offers</h6>
                                <p>Get notified about discounts and special offers</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="profile-card fade-in">
                    <div class="form-section">
                        <h4><i class="fas fa-history text-info"></i>Recent Activity</h4>
                        
                        <div class="activity-timeline">
                            <div class="activity-item">
                                <div class="activity-content">
                                    <h6>Order Delivered</h6>
                                    <p>Your order #ORD-001 has been delivered successfully</p>
                                    <div class="activity-time">2 hours ago</div>
                                </div>
                            </div>

                            <div class="activity-item">
                                <div class="activity-content">
                                    <h6>Prescription Uploaded</h6>
                                    <p>New prescription from Dr. Smith has been uploaded</p>
                                    <div class="activity-time">1 day ago</div>
                                </div>
                            </div>

                            <div class="activity-item">
                                <div class="activity-content">
                                    <h6>Profile Updated</h6>
                                    <p>Contact information has been updated</p>
                                    <div class="activity-time">3 days ago</div>
                                </div>
                            </div>

                            <div class="activity-item">
                                <div class="activity-content">
                                    <h6>Order Placed</h6>
                                    <p>New order #ORD-002 has been placed</p>
                                    <div class="activity-time">1 week ago</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Change Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="currentPassword" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="newPassword" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirmPassword" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="change_password" class="btn btn-primary">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Avatar Upload Modal -->
    <div class="modal fade" id="avatarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Update Profile Picture</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <div class="profile-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                            <i class="fas fa-user"></i>
                        </div>
                        <input type="file" class="form-control" accept="image/*">
                    </div>
                    <p class="text-muted">Upload a square image for best results. Max size: 5MB</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Upload Picture</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
    // Form validation and submission
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            // Add loading state to submit button
            const submitBtn = this.querySelector('button[type="submit"]');
            if (!submitBtn) return; // safeguard
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
            submitBtn.disabled = true;
            
            // Simulate API call (2s delay)
            setTimeout(() => {
                // Reset button back to normal
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        });
    });
</script>
</body>
</html>