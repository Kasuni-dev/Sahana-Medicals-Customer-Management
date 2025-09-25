<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Sahana Medicals</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,.05); }
        .dashboard { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .card-link { text-decoration: none; color: inherit; }
        .card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,.08); }
    </style>
    </head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="admin_dashboard.php">SAHANA Admin</a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="text-muted"><i class="fa fa-user"></i> <?php echo htmlspecialchars($userName); ?></span>
                <a href="logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
            </div>
        </div>
    </nav>
    <div class="dashboard">
        <h1 class="h3 mb-4">Welcome, <?php echo htmlspecialchars($userName); ?></h1>
        <div class="row g-3">
            <div class="col-md-3">
                <a class="card-link" href="user_management.php">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fa fa-users text-primary me-2"></i>Users</h5>
                            <p class="card-text text-muted">Manage registered users</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a class="card-link" href="#">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fa fa-briefcase text-success me-2"></i>Employees</h5>
                            <p class="card-text text-muted">Employee management</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a class="card-link" href="#">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fa fa-pills text-danger me-2"></i>Products</h5>
                            <p class="card-text text-muted">Manage inventory</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a class="card-link" href="#">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fa fa-truck text-warning me-2"></i>Suppliers</h5>
                            <p class="card-text text-muted">Supplier records</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
