<?php
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $shipment_id = $_POST["shipment_id"];
    $location_name = $_POST["location_name"];
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];
    $location_type = $_POST["location_type"];

    $stmt = $conn->prepare(
        "INSERT INTO locations
        (shipment_id, location_name, latitude, longitude, location_type)
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "isdds",
        $shipment_id,
        $location_name,
        $latitude,
        $longitude,
        $location_type
    );

    if ($stmt->execute()) {
        $message = "Location recorded successfully.";
    }

    $stmt->close();
}

$result = $conn->query("
    SELECT locations.*, shipments.shipment_code
    FROM locations
    LEFT JOIN shipments
    ON locations.shipment_id = shipments.id
    ORDER BY locations.id DESC
");
?>

<!DOCTYPE html>
<html>

<head>

<title>ColdChain Guard - Route Tracking</title>

<style>

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f5f8fc;
    color: #10213d;
}

.container {
    max-width: 1100px;
    margin: 40px auto;
    padding: 20px;
}

.header p {
    color: #246bfd;
    letter-spacing: 3px;
    font-weight: bold;
}

.header h1 {
    font-size: 36px;
}

.card {
    background: white;
    padding: 25px;
    border-radius: 16px;
    margin-bottom: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.06);
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 7px;
}

input, select {
    width: 100%;
    box-sizing: border-box;
    padding: 12px;
    border: 1px solid #d6dce5;
    border-radius: 8px;
}

button {
    margin-top: 20px;
    background: #246bfd;
    color: white;
    border: none;
    padding: 13px 22px;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
}

.success {
    background: #e8fff4;
    color: #00875a;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 14px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

th {
    color: #667085;
}

.badge {
    background: #eef4ff;
    color: #246bfd;
    padding: 6px 10px;
    border-radius: 15px;
}

</style>

</head>

<body>

<div class="container">

<div class="header">

<p>LIVE LOCATION</p>

<h1>Journey Tracking</h1>

</div>

<?php if ($message != ""): ?>

<div class="success">
<?php echo $message; ?>
</div>

<?php endif; ?>

<div class="card">

<h2>Add Route Checkpoint</h2>

<form method="POST">

<div class="form-grid">

<div>
<label>Shipment ID</label>
<input type="number" name="shipment_id" required>
</div>

<div>
<label>Location Name</label>
<input type="text" name="location_name"
placeholder="Example: Salem Highway" required>
</div>

<div>
<label>Latitude</label>
<input type="number" step="0.0000001"
name="latitude"
placeholder="11.6643" required>
</div>

<div>
<label>Longitude</label>
<input type="number" step="0.0000001"
name="longitude"
placeholder="78.1460" required>
</div>

<div>
<label>Location Type</label>

<select name="location_type">

<option value="Origin">Origin</option>
<option value="Checkpoint">Checkpoint</option>
<option value="Current Location">Current Location</option>
<option value="Destination">Destination</option>

</select>

</div>

</div>

<button type="submit">
+ Add Location
</button>

</form>

</div>


<div class="card">

<h2>Shipment Route History</h2>

<table>

<tr>

<th>Shipment</th>
<th>Location</th>
<th>Type</th>
<th>Latitude</th>
<th>Longitude</th>
<th>Recorded</th>

</tr>

<?php while ($row = $result->fetch_assoc()): ?>

<tr>

<td>
<?php echo htmlspecialchars($row['shipment_code'] ?? 'Shipment'); ?>
</td>

<td>
<strong>
<?php echo htmlspecialchars($row['location_name']); ?>
</strong>
</td>

<td>
<span class="badge">
<?php echo htmlspecialchars($row['location_type']); ?>
</span>
</td>

<td>
<?php echo $row['latitude']; ?>
</td>

<td>
<?php echo $row['longitude']; ?>
</td>

<td>
<?php echo $row['recorded_at']; ?>
</td>

</tr>

<?php endwhile; ?>

</table>

</div>

</div>

</body>

</html>