<?php
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $shipment_id = $_POST["shipment_id"];
    $incident_type = $_POST["incident_type"];
    $description = $_POST["description"];
    $severity = $_POST["severity"];
    $status = "Open";

    $stmt = $conn->prepare(
        "INSERT INTO incidents
        (shipment_id, incident_type, description, severity, status)
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "issss",
        $shipment_id,
        $incident_type,
        $description,
        $severity,
        $status
    );

    if ($stmt->execute()) {
        $message = "Incident recorded successfully.";
    }

    $stmt->close();
}

$result = $conn->query("
    SELECT incidents.*, shipments.shipment_code
    FROM incidents
    LEFT JOIN shipments
    ON incidents.shipment_id = shipments.id
    ORDER BY incidents.id DESC
");
?>

<!DOCTYPE html>
<html>

<head>

<title>ColdChain Guard - Incidents</title>

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

input,
select,
textarea {
    width: 100%;
    box-sizing: border-box;
    padding: 12px;
    border: 1px solid #d6dce5;
    border-radius: 8px;
    font-family: Arial;
}

textarea {
    min-height: 90px;
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

th,
td {
    padding: 14px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

th {
    color: #667085;
}

.open {
    background: #ffe9e9;
    color: #d92d20;
    padding: 6px 10px;
    border-radius: 15px;
}

.high {
    color: #d92d20;
    font-weight: bold;
}

.medium {
    color: #d97706;
    font-weight: bold;
}

.low {
    color: #00875a;
    font-weight: bold;
}

</style>

</head>

<body>

<div class="container">

<div class="header">

<p>SAFETY MANAGEMENT</p>

<h1>Incidents & Alerts</h1>

</div>

<?php if ($message != ""): ?>

<div class="success">
<?php echo $message; ?>
</div>

<?php endif; ?>


<div class="card">

<h2>Report New Incident</h2>

<form method="POST">

<div class="form-grid">

<div>

<label>Shipment ID</label>

<input
type="number"
name="shipment_id"
required
>

</div>


<div>

<label>Incident Type</label>

<select name="incident_type">

<option value="Temperature Excursion">
Temperature Excursion
</option>

<option value="Route Delay">
Route Delay
</option>

<option value="Vehicle Breakdown">
Vehicle Breakdown
</option>

<option value="Handling Issue">
Handling Issue
</option>

<option value="Other">
Other
</option>

</select>

</div>


<div>

<label>Severity</label>

<select name="severity">

<option value="High">High</option>
<option value="Medium">Medium</option>
<option value="Low">Low</option>

</select>

</div>


<div>

<label>Description</label>

<textarea
name="description"
placeholder="Describe the incident..."
required
></textarea>

</div>

</div>

<button type="submit">
+ Report Incident
</button>

</form>

</div>


<div class="card">

<h2>Incident History</h2>

<table>

<tr>

<th>Shipment</th>
<th>Incident</th>
<th>Description</th>
<th>Severity</th>
<th>Status</th>
<th>Date</th>

</tr>


<?php while ($row = $result->fetch_assoc()): ?>

<tr>

<td>
<?php echo htmlspecialchars(
$row['shipment_code'] ?? 'Shipment'
); ?>
</td>

<td>
<strong>
<?php echo htmlspecialchars(
$row['incident_type']
); ?>
</strong>
</td>

<td>
<?php echo htmlspecialchars(
$row['description']
); ?>
</td>

<td>

<span class="<?php echo strtolower($row['severity']); ?>">

<?php echo htmlspecialchars(
$row['severity']
); ?>

</span>

</td>

<td>

<span class="open">

<?php echo htmlspecialchars(
$row['status']
); ?>

</span>

</td>

<td>

<?php echo $row['created_at']; ?>

</td>

</tr>

<?php endwhile; ?>

</table>

</div>

</div>

</body>

</html>