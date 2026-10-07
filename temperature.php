<?php
include "db.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $shipment_id = $_POST["shipment_id"];
    $temperature = $_POST["temperature"];
    $sensor_id = $_POST["sensor_id"];
    $battery = $_POST["battery"];

    // Safe temperature range for vaccines
    if ($temperature >= 2 && $temperature <= 8) {
        $status = "SAFE";
        $messageType = "safe";
    } else {
        $status = "ALERT";
        $messageType = "alert";
    }

    $stmt = $conn->prepare(
        "INSERT INTO temperature_readings
        (shipment_id, temperature, sensor_id, battery)
        VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "idsi",
        $shipment_id,
        $temperature,
        $sensor_id,
        $battery
    );

    if ($stmt->execute()) {
        $message = "Temperature reading recorded successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>

<head>

<title>ColdChain Guard - Temperature Monitoring</title>

<style>

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f5f8fc;
    color: #10213d;
}

.container {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
}

.header {
    margin-bottom: 30px;
}

.header p {
    color: #246bfd;
    letter-spacing: 3px;
    font-size: 13px;
    font-weight: bold;
}

.header h1 {
    font-size: 36px;
    margin: 5px 0;
}

.card {
    background: white;
    padding: 30px;
    border-radius: 18px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.07);
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

input {
    width: 100%;
    box-sizing: border-box;
    padding: 13px;
    border: 1px solid #d6dce5;
    border-radius: 8px;
    font-size: 15px;
}

button {
    background: #246bfd;
    color: white;
    border: none;
    padding: 14px 24px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #1855d1;
}

.safe {
    margin-top: 20px;
    padding: 15px;
    border-radius: 10px;
    background: #e8fff4;
    color: #00875a;
    font-weight: bold;
}

.alert {
    margin-top: 20px;
    padding: 15px;
    border-radius: 10px;
    background: #ffe9e9;
    color: #d92d20;
    font-weight: bold;
}

.range {
    background: #f0f4fa;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 25px;
}

</style>

</head>

<body>

<div class="container">

<div class="header">

<p>COLD CHAIN MONITORING</p>

<h1>Temperature Monitoring</h1>

</div>

<div class="card">

<div class="range">

<strong>Vaccine Safe Temperature Range:</strong>
2°C – 8°C

</div>

<form method="POST">

<div class="form-group">

<label>Shipment ID</label>

<input
type="number"
name="shipment_id"
placeholder="Example: 1"
required
>

</div>

<div class="form-group">

<label>Temperature (°C)</label>

<input
type="number"
step="0.1"
name="temperature"
placeholder="Example: 6.5"
required
>

</div>

<div class="form-group">

<label>Sensor ID</label>

<input
type="text"
name="sensor_id"
placeholder="Example: SNS-88421"
required
>

</div>

<div class="form-group">

<label>Battery (%)</label>

<input
type="number"
name="battery"
min="0"
max="100"
placeholder="Example: 87"
required
>

</div>

<button type="submit">
Record Temperature
</button>

</form>

<?php if ($message != ""): ?>

<div class="<?php echo $messageType; ?>">

<?php echo $message; ?>

<?php if ($messageType == "safe"): ?>

— Temperature is within the safe range.

<?php else: ?>

— WARNING: Temperature excursion detected!

<?php endif; ?>

</div>

<?php endif; ?>

</div>

</div>

</body>

</html>