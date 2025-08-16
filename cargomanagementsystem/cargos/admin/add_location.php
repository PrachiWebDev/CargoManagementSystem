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
    $name = trim($_POST["name"]);
    $address = trim($_POST["address"]);
    $city = trim($_POST["city"]);
    $state = trim($_POST["state"]);
    $pincode = trim($_POST["pincode"]);
    $phone = trim($_POST["phone"]);
    
    // Check if location name already exists
    $sql = "SELECT id FROM locations WHERE name = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "s", $name);
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) > 0){
                echo "This location name is already registered.";
                exit();
            }
        }
    }
    
    // Insert into locations table
    $sql = "INSERT INTO locations (name, address, city, state, pincode, phone) VALUES (?, ?, ?, ?, ?, ?)";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "ssssss", $name, $address, $city, $state, $pincode, $phone);
        if(mysqli_stmt_execute($stmt)){
            header("location: locations.php");
            exit();
        }
    }
    
    echo "Something went wrong. Please try again later.";
}

mysqli_close($conn);
?> 