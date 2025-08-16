<?php
session_start();

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    header("location: ../index.php");
    exit;
}

require_once "../config/database.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate input
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $city = trim($_POST["city"]);
    $state = trim($_POST["state"]);
    $pincode = trim($_POST["pincode"]);
    
    // Generate username and password
    $username = strtolower(str_replace(' ', '', $full_name)) . rand(100, 999);
    $password = substr(md5(rand()), 0, 8);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert into users table
    $sql = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'customer')";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "sss", $username, $hashed_password, $email);
        if(mysqli_stmt_execute($stmt)){
            $user_id = mysqli_insert_id($conn);
            
            // Insert into customers table
            $sql = "INSERT INTO customers (user_id, full_name, phone, address, city, state, pincode) VALUES (?, ?, ?, ?, ?, ?, ?)";
            if($stmt = mysqli_prepare($conn, $sql)){
                mysqli_stmt_bind_param($stmt, "issssss", $user_id, $full_name, $phone, $address, $city, $state, $pincode);
                if(mysqli_stmt_execute($stmt)){
                    // Send email with credentials
                    $to = $email;
                    $subject = "Your Cargo Management System Account";
                    $message = "Dear " . $full_name . ",\n\n";
                    $message .= "Your account has been created successfully.\n";
                    $message .= "Username: " . $username . "\n";
                    $message .= "Password: " . $password . "\n\n";
                    $message .= "Please login and change your password.\n\n";
                    $message .= "Best regards,\nCargo Management System";
                    $headers = "From: noreply@cargomanagement.com";
                    
                    mail($to, $subject, $message, $headers);
                    
                    header("location: customers.php");
                    exit();
                }
            }
        }
    }
    
    echo "Something went wrong. Please try again later.";
}

mysqli_close($conn);
?> 