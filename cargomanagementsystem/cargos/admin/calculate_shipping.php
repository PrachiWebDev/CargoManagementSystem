<?php
session_start();
require_once "../config/database.php";

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    header("location: ../login.php");
    exit;
}

$shipping_cost = 0;
$error = "";
$success = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $weight = floatval($_POST["weight"]);
    $distance = floatval($_POST["distance"]);
    $cargo_type = $_POST["cargo_type"];
    $priority = $_POST["priority"];
    $zone = $_POST["zone"];
    $package_type = $_POST["package_type"];
    
    // Base rates (in rupees)
    $base_rate = 50; // Base rate for 1 kg and 1 km
    
    // Zone multipliers (like DTDC, Blue Dart)
    $zone_multiplier = [
        "local" => 1.0,      // Same city
        "metro" => 1.2,      // Metro to metro
        "state" => 1.5,      // Within state
        "national" => 2.0,   // National
        "international" => 3.0 // International
    ];
    
    // Package type multipliers
    $package_multiplier = [
        "document" => 1.0,   // Documents
        "parcel" => 1.2,     // Regular parcels
        "bulk" => 1.5,       // Bulk shipments
        "special" => 2.0     // Special handling
    ];
    
    // Cargo type multipliers
    $cargo_type_multiplier = [
        "general" => 1.0,
        "fragile" => 1.5,
        "hazardous" => 2.0,
        "perishable" => 1.8
    ];
    
    // Priority multipliers
    $priority_multiplier = [
        "normal" => 1.0,     // 3-5 days
        "express" => 1.5,    // 1-2 days
        "urgent" => 2.0      // Same day/Next day
    ];
    
    // Weight slabs (like FedEx, DHL)
    $weight_slab_multiplier = 1.0;
    if($weight <= 0.5) {
        $weight_slab_multiplier = 1.0;
    } elseif($weight <= 1) {
        $weight_slab_multiplier = 1.2;
    } elseif($weight <= 5) {
        $weight_slab_multiplier = 1.5;
    } elseif($weight <= 10) {
        $weight_slab_multiplier = 1.8;
    } else {
        $weight_slab_multiplier = 2.0;
    }
    
    // Calculate shipping cost
    $shipping_cost = $base_rate * $weight * $distance * 
                    $zone_multiplier[$zone] *
                    $package_multiplier[$package_type] *
                    $cargo_type_multiplier[$cargo_type] * 
                    $priority_multiplier[$priority] *
                    $weight_slab_multiplier;
    
    // Add GST (18%)
    $shipping_cost = $shipping_cost * 1.18;
    
    // Round to 2 decimal places
    $shipping_cost = round($shipping_cost, 2);
    
    $success = "Shipping cost calculated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculate Shipping Cost - Cargo Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .calculator-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="calculator-container">
            <h1 class="text-center mb-4">Shipping Cost Calculator</h1>
            
            <?php 
            if(!empty($error)){ echo '<div class="alert alert-danger">' . $error . '</div>'; }
            if(!empty($success)){ echo '<div class="alert alert-success">' . $success . '</div>'; }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-3">
                    <label for="weight" class="form-label">Weight (in kg)</label>
                    <input type="number" step="0.01" name="weight" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label for="distance" class="form-label">Distance (in km)</label>
                    <input type="number" step="0.01" name="distance" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label for="zone" class="form-label">Delivery Zone</label>
                    <select name="zone" class="form-select" required>
                        <option value="local">Local (Same City)</option>
                        <option value="metro">Metro to Metro</option>
                        <option value="state">Within State</option>
                        <option value="national">National</option>
                        <option value="international">International</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="package_type" class="form-label">Package Type</label>
                    <select name="package_type" class="form-select" required>
                        <option value="document">Document</option>
                        <option value="parcel">Parcel</option>
                        <option value="bulk">Bulk Shipment</option>
                        <option value="special">Special Handling</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="cargo_type" class="form-label">Cargo Type</label>
                    <select name="cargo_type" class="form-select" required>
                        <option value="general">General</option>
                        <option value="fragile">Fragile</option>
                        <option value="hazardous">Hazardous</option>
                        <option value="perishable">Perishable</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="priority" class="form-label">Shipping Priority</label>
                    <select name="priority" class="form-select" required>
                        <option value="normal">Normal (3-5 days)</option>
                        <option value="express">Express (1-2 days)</option>
                        <option value="urgent">Urgent (Same/Next day)</option>
                    </select>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Calculate Cost</button>
                </div>
            </form>
            
            <?php if($shipping_cost > 0): ?>
            <div class="mt-4 p-3 bg-light rounded">
                <h4 class="text-center">Estimated Shipping Cost</h4>
                <h2 class="text-center text-primary">₹<?php echo number_format($shipping_cost, 2); ?></h2>
                <p class="text-center text-muted">Inclusive of 18% GST</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 