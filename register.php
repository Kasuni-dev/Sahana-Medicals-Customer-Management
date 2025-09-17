<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $fname = trim($_POST['first_name']);
    $lname = trim($_POST['last_name']);
    $mobilenum = trim($_POST['mobile']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    $errors = [];
    
    if (empty($fname)) $errors[] = "First name is required.";
    if (empty($lname)) $errors[] = "Last name is required.";
    if (empty($mobilenum)) $errors[] = "Mobile number is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (empty($address)) $errors[] = "Address is required.";
    if (empty($password)) $errors[] = "Password is required.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";
    
    // Check if email already exists
    if (empty($errors)) {
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "Email already exists.";
        }
        mysqli_stmt_close($stmt);
    }
    
    // If no errors, insert user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (firstName, lastName, email, phone, address, password) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssss", $fname, $lname, $email, $mobilenum, $address, $hashed_password);
            
            if (mysqli_stmt_execute($stmt)) {
                // Registration successful
                echo json_encode([
                    'success' => true,
                    'message' => 'Registration successful! You can now login.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Database error: ' . mysqli_error($conn)
                ]);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . mysqli_error($conn)
            ]);
        }
    } else {
        // Return errors
        echo json_encode([
            'success' => false,
            'message' => implode('<br>', $errors)
        ]);
    }
} else {
    // Invalid request method
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}

// Close connection
mysqli_close($conn);
?>