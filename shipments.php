<?php
require_once "db.php";

/* Fetch all shipments */
$sql = "SELECT * FROM shipments ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ColdChain Guard - Shipments</title>

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
            width: 92%;
            max-width: 1300px;
            margin: 40px auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        h1 {
            margin: 0;
            font-size: 32px;
        }

        .subtitle {
            color: #667085;
            margin-top: 8px;
        }

        .btn {
            background: #2563eb;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .table-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            background: #f8fafc;
            padding: 15px;
            color: #667085;
            font-size: 13px;
        }

        td {
            padding: 16px 15px;
            border-bottom: 1px solid #edf0f5;
            font-size: 14px;
        }

        tr:hover {
            background: #f8fbff;
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .transit {
            background: #dcfce7;
            color: #15803d;
        }

        .delivered {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .alert {
            background: #fee2e2;
            color: #dc2626;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #667085;
        }

        .back {
            display: inline-block;
            margin-bottom: 20px;
            color: #2563eb;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="container">

    <a href="index.php" class="back">← Back to Dashboard</a>

    <div class="header">
        <div>
            <h1>Shipment Management</h1>
            <div class="subtitle">
                Live shipment records from ColdChain Guard database
            </div>
        </div>

        <a href="add_shipment.php" class="btn">
            + Add Shipment
        </a>
    </div>

    <div class="table-card">

        <table>

            <thead>
                <tr>
                    <th>Shipment ID</th>
                    <th>Product</th>
                    <th>Route</th>
                    <th>Vehicle</th>
                    <th>Driver</th>
                    <th>Status</th>
                    <th>Safe Range</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($result && $result->num_rows > 0): ?>

                <?php while ($shipment = $result->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <strong>
                                <?php echo htmlspecialchars($shipment['shipment_code']); ?>
                            </strong>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($shipment['product']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($shipment['origin']); ?>
                            →
                            <?php echo htmlspecialchars($shipment['destination']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($shipment['vehicle_number']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($shipment['driver_name']); ?>
                        </td>

                        <td>

                            <?php
                            $status = $shipment['status'];

                            if ($status == 'In Transit') {
                                $class = 'transit';
                            } elseif ($status == 'Delivered') {
                                $class = 'delivered';
                            } else {
                                $class = 'alert';
                            }
                            ?>

                            <span class="status <?php echo $class; ?>">
                                <?php echo htmlspecialchars($status); ?>
                            </span>

                        </td>

                        <td>
                            <?php echo $shipment['safe_min']; ?>°C
                            –
                            <?php echo $shipment['safe_max']; ?>°C
                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="7" class="empty">
                        No shipments found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>