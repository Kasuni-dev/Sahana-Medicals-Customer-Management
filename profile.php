<?php
session_start();

// Guard: must be logged in as a non-admin customer
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';
$userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';
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
    <style>
        /* Small utility to ensure links look okay on gradient header */
        .header a { color: white; text-decoration: none; }
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
                        <h6><?php echo htmlspecialchars($userName); ?></h6>
                        <small><?php echo htmlspecialchars($userEmail); ?></small>
                    </div>
                    <div class="user-avatar-header">
                        <i class="fas fa-user"></i>
                    </div>
                    <a href="logout.php" class="btn btn-sm btn-light ms-2">Logout</a>
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
                    <div class="profile-name"><?php echo htmlspecialchars($userName); ?></div>
                    <div class="profile-status">Premium Member</div>
                    <p style="opacity: 0.9; margin-bottom: 0;">
                        <i class="fas fa-calendar me-2"></i>Member since January 2023
                    </p>
                    
                    <div class="profile-stats">
                        <div class="stat-item">
                            <span class="stat-number">47</span>
                            <span class="stat-label">Orders</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">12</span>
                            <span class="stat-label">Prescriptions</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">$2,450</span>
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
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" value="John" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" value="Doe" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($userEmail ?: 'john.doe@email.com'); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" value="+1 (555) 123-4567" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" value="1990-05-15">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gender</label>
                                    <select class="form-control">
                                        <option value="male" selected>Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                        <option value="prefer-not-to-say">Prefer not to say</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" rows="3">123 Medical Street, Health City, HC 12345</textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
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
                                    <p>O+ Positive</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="medical-card">
                                    <h5><i class="fas fa-weight me-2"></i>Weight</h5>
                                    <p>75 kg (165 lbs)</p>
                                </div>
                            </div>
                        </div>
                        
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Emergency Contact</label>
                                    <input type="text" class="form-control" value="Jane Doe - Wife">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Emergency Phone</label>
                                    <input type="tel" class="form-control" value="+1 (555) 987-6543">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Allergies</label>
                                    <textarea class="form-control" rows="2" placeholder="List any drug allergies or medical conditions">Penicillin, Shellfish</textarea>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Current Medications</label>
                                    <textarea class="form-control" rows="2" placeholder="List current medications">Metformin 500mg daily, Lisinopril 10mg daily</textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
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
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Update Password</button>
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
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            if (!submitBtn) return;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                alert("Changes saved successfully!");
            }, 1500);
        });
    });
    </script>
</body>
</html>
