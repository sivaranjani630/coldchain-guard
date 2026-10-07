<?php
require_once "db.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $shipment_code = trim($_POST["shipment_code"]);
    $product = trim($_POST["product"]);
    $origin = trim($_POST["origin"]);
    $destination = trim($_POST["destination"]);
    $vehicle_number = trim($_POST["vehicle_number"]);
    $driver_name = trim($_POST["driver_name"]);
    $status = $_POST["status"];
    $safe_min = floatval($_POST["safe_min"]);
    $safe_max = floatval($_POST["safe_max"]);

    if (
        $shipment_code === "" ||
        $product === "" ||
        $origin === "" ||
        $destination === "" ||
        $vehicle_number === "" ||
        $driver_name === ""
    ) {
        $error = "Please fill in all required fields.";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO shipments
            (
                shipment_code,
                product,
                origin,
                destination,
                vehicle_number,
                driver_name,
                status,
                safe_min,
                safe_max
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssssssdd",
            $shipment_code,
            $product,
            $origin,
            $destination,
            $vehicle_number,
            $driver_name,
            $status,
            $safe_min,
            $safe_max
        );

        if ($stmt->execute()) {
            $message = "Shipment added successfully!";
        } else {
            $error = "Could not add shipment. Shipment code may already exist.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>ColdChain Guard - Add Shipment</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f7fb;
    color: #17233c;
}

.container {
    width: 90%;
    max-width: 900px;
    margin: 40px auto;
}

.back {
    color: #2563eb;
    text-decoration: none;
    display: inline-block;
    margin-bottom: 20px;
}

.card {
    background: white;
    padding: 35px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
}

h1 {
    margin-top: 0;
}

.subtitle {
    color: #667085;
    margin-bottom: 30px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

label {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 8px;
}

input,
select {
    padding: 12px;
    border: 1px solid #d0d5dd;
    border-radius: 8px;
    font-size: 14px;
}

input:focus,
select:focus {
    outline: none;
    border-color: #2563eb;
}

.full {
    grid-column: 1 / 3;
}

button {
    margin-top: 25px;
    padding: 13px 25px;
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 15px;
    font-weight: bold;
}

button:hover {
    background: #1d4ed8;
}

.success {
    background: #dcfce7;
    color: #15803d;
    padding: 14px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.error {
    background: #fee2e2;
    color: #dc2626;
    padding: 14px;
    border-radius: 8px;
    margin-bottom: 20px;
}

@media (max-width: 700px) {

    .form-grid {
        grid-template-columns: 1fr;
    }

    .full {
        grid-column: auto;
    }

}

</style>

</head>

<body>

<div class="container">

<a href="shipments.php" class="back">
← Back to Shipments
</a>

<div class="card">

<h1>Add New Shipment</h1>

<div class="subtitle">
Register a new cold-chain shipment in the system.
</div>

<?php if ($message): ?>

<div class="success">
    <?php echo htmlspecialchars($message); ?>
</div>

<?php endif; ?>

<?php if ($error): ?>

<div class="error">
    <?php echo htmlspecialchars($error); ?>
</div>

<?php endif; ?>


<form method="POST">

<div class="form-grid">

<div class="form-group">

<label>Shipment Code *</label>

<input
    type="text"
    name="shipment_code"
    placeholder="CC-2026-005"
    required
>

</div>


<div class="form-group">

<label>Product *</label>

<input
    type="text"
    name="product"
    placeholder="Vaccines"
    required
>

</div>


<div class="form-group">

<label>Origin *</label>

<input
    type="text"
    name="origin"
    placeholder="Coimbatore"
    required
>

</div>


<div class="form-group">

<label>Destination *</label>

<input
    type="text"
    name="destination"
    placeholder="Chennai"
    required
>

</div>


<div class="form-group">

<label>Vehicle Number *</label>

<input
    type="text"
    name="vehicle_number"
    placeholder="TN-38-AB-1234"
    required
>

</div>


<div class="form-group">

<label>Driver Name *</label>

<input
    type="text"
    name="driver_name"
    placeholder="Driver Name"
    required
>

</div>


<div class="form-group">

<label>Status</label>

<select name="status">

<option value="In Transit">
In Transit
</option>

<option value="Delivered">
Delivered
</option>

<option value="Alert">
Alert
</option>

</select>

</div>


<div class="form-group">

<label>Safe Minimum Temperature (°C)</label>

<input
    type="number"
    name="safe_min"
    value="2"
    step="0.1"
>

</div>


<div class="form-group">

<label>Safe Maximum Temperature (°C)</label>

<input
    type="number"
    name="safe_max"
    value="8"
    step="0.1"
>

</div>

</div>

<button type="submit">
+ Add Shipment
</button>

</form>

</div>

</div>

</body>

</html>