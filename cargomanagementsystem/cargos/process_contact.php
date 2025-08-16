<?php
require_once "config/database.php";

// Initialize response array
$response = array(
    'success' => false,
    'message' => ''
);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Validate inputs
    if (empty($name) || empty($email) || empty($phone) || empty($subject) || empty($message)) {
        $response['message'] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Invalid email format.";
    } else {
        // Insert into database
        $sql = "INSERT INTO contact_messages (name, email, phone, subject, message) 
                VALUES ('$name', '$email', '$phone', '$subject', '$message')";
        
        if(mysqli_query($conn, $sql)) {
            $response['success'] = true;
            $response['message'] = "Thank you for your message. We will get back to you soon!";
        } else {
            $response['message'] = "Something went wrong. Please try again later.";
        }
    }
} else {
    $response['message'] = "Invalid request method.";
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?> 