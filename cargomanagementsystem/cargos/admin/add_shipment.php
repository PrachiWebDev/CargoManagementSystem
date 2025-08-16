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
    $customer_id = trim($_POST["customer_id"]);
    $vehicle_id = trim($_POST["vehicle_id"]);
    $pickup_location_id = trim($_POST["pickup_location_id"]);
    $delivery_location_id = trim($_POST["delivery_location_id"]);
    $weight = trim($_POST["weight"]);
    $dimensions = trim($_POST["dimensions"]);
    $status = trim($_POST["status"]);
    $description = trim($_POST["description"]);
    
    // Generate tracking number
    $tracking_number = 'TRK' . date('Ymd') . rand(1000, 9999);
    
    // Insert into shipments table
    $sql = "INSERT INTO shipments (tracking_number, customer_id, vehicle_id, pickup_location_id, delivery_location_id, weight, dimensions, status, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "siiisssss", $tracking_number, $customer_id, $vehicle_id, $pickup_location_id, $delivery_location_id, $weight, $dimensions, $status, $description);
        if(mysqli_stmt_execute($stmt)){
            $shipment_id = mysqli_insert_id($conn);
            
            // Update vehicle status
            $sql = "UPDATE vehicles SET status = 'in_use' WHERE id = ?";
            if($stmt = mysqli_prepare($conn, $sql)){
                mysqli_stmt_bind_param($stmt, "i", $vehicle_id);
                mysqli_stmt_execute($stmt);
            }
            
            // Add initial tracking record
            $sql = "INSERT INTO tracking (shipment_id, status, location, remarks) VALUES (?, ?, ?, ?)";
            if($stmt = mysqli_prepare($conn, $sql)){
                $location = "Pickup location";
                $remarks = "Shipment created and assigned to vehicle";
                mysqli_stmt_bind_param($stmt, "isss", $shipment_id, $status, $location, $remarks);
                mysqli_stmt_execute($stmt);
            }
            
            header("location: shipments.php");
            exit();
        }
    }
    
    echo "Something went wrong. Please try again later.";
}

mysqli_close($conn);
?> 