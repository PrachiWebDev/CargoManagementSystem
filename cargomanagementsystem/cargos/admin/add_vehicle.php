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
    $vehicle_number = trim($_POST["vehicle_number"]);
    $vehicle_type = trim($_POST["vehicle_type"]);
    $capacity = trim($_POST["capacity"]);
    
    // Check if vehicle number already exists
    $sql = "SELECT id FROM vehicles WHERE vehicle_number = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "s", $vehicle_number);
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) > 0){
                echo "This vehicle number is already registered.";
                exit();
            }
        }
    }
    
    // Insert into vehicles table
    $sql = "INSERT INTO vehicles (vehicle_number, vehicle_type, capacity, status) VALUES (?, ?, ?, 'available')";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "ssd", $vehicle_number, $vehicle_type, $capacity);
        if(mysqli_stmt_execute($stmt)){
            header("location: vehicles.php");
            exit();
        }
    }
    
    echo "Something went wrong. Please try again later.";
}

mysqli_close($conn);
?> 