<?php
function calculateAirCargoCostWithDimensions($actualWeightKg, $lengthCm, $widthCm, $heightCm, $ratePerKg = 75, $fuelSurchargeRate = 15, $handlingCharge = 100, $gstRate = 18) {
    // Step 1: Calculate volumetric weight
    $volumetricWeight = ($lengthCm * $widthCm * $heightCm) / 5000;

    // Step 2: Determine chargeable weight
    $chargeableWeight = max($actualWeightKg, $volumetricWeight);

    // Step 3: Base cost
    $base = $chargeableWeight * $ratePerKg;

    // Step 4: Fuel surcharge
    $fuelSurcharge = $base * ($fuelSurchargeRate / 100);

    // Step 5: Subtotal
    $subtotal = $base + $fuelSurcharge + $handlingCharge;

    // Step 6: GST
    $gst = $subtotal * ($gstRate / 100);

    // Step 7: Total cost
    $totalCost = $subtotal + $gst;

    // Return results
    return [
        'actual_weight' => round($actualWeightKg, 2),
        'volumetric_weight' => round($volumetricWeight, 2),
        'chargeable_weight' => round($chargeableWeight, 2),
        'base' => round($base, 2),
        'fuel_surcharge' => round($fuelSurcharge, 2),
        'handling_charge' => round($handlingCharge, 2),
        'subtotal' => round($subtotal, 2),
        'gst' => round($gst, 2),
        'total_cost' => round($totalCost, 2)
    ];
}

$result = null;
$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $actualWeightKg = floatval($_POST["weight"]);
    $lengthCm = floatval($_POST["length"]);
    $widthCm = floatval($_POST["width"]);
    $heightCm = floatval($_POST["height"]);
    
    if($actualWeightKg > 0 && $lengthCm > 0 && $widthCm > 0 && $heightCm > 0){
        $result = calculateAirCargoCostWithDimensions($actualWeightKg, $lengthCm, $widthCm, $heightCm);
    } else {
        $error = "Please enter valid dimensions and weight.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Air Cargo Cost Calculator</title>
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
            <h1 class="text-center mb-4">Air Cargo Cost Calculator</h1>
            
            <?php if(!empty($error)){ echo '<div class="alert alert-danger">' . $error . '</div>'; } ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-3">
                    <label for="weight" class="form-label">Weight (in kg)</label>
                    <input type="number" step="0.01" name="weight" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label for="length" class="form-label">Length (in cm)</label>
                    <input type="number" step="0.01" name="length" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label for="width" class="form-label">Width (in cm)</label>
                    <input type="number" step="0.01" name="width" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label for="height" class="form-label">Height (in cm)</label>
                    <input type="number" step="0.01" name="height" class="form-control" required>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Calculate Cost</button>
                </div>
            </form>
            
            <?php if($result): ?>
            <div class="mt-4 p-3 bg-light rounded">
                <h4 class="text-center">Cost Breakdown</h4>
                <table class="table table-bordered">
                    <tr>
                        <td>Actual Weight:</td>
                        <td><?php echo $result['actual_weight']; ?> kg</td>
                    </tr>
                    <tr>
                        <td>Volumetric Weight:</td>
                        <td><?php echo $result['volumetric_weight']; ?> kg</td>
                    </tr>
                    <tr>
                        <td>Chargeable Weight:</td>
                        <td><?php echo $result['chargeable_weight']; ?> kg</td>
                    </tr>
                    <tr>
                        <td>Base Cost:</td>
                        <td>₹<?php echo $result['base']; ?></td>
                    </tr>
                    <tr>
                        <td>Fuel Surcharge:</td>
                        <td>₹<?php echo $result['fuel_surcharge']; ?></td>
                    </tr>
                    <tr>
                        <td>Handling Charge:</td>
                        <td>₹<?php echo $result['handling_charge']; ?></td>
                    </tr>
                    <tr>
                        <td>Subtotal:</td>
                        <td>₹<?php echo $result['subtotal']; ?></td>
                    </tr>
                    <tr>
                        <td>GST (18%):</td>
                        <td>₹<?php echo $result['gst']; ?></td>
                    </tr>
                    <tr class="table-primary">
                        <td><strong>Total Cost:</strong></td>
                        <td><strong>₹<?php echo $result['total_cost']; ?></strong></td>
                    </tr>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
