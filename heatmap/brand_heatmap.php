<?php

include "includes/db.php";

// Query for complaints per brand
$query1 = "SELECT p.brand, COUNT(c.id) AS complaint_count
           FROM products p
           LEFT JOIN complaints c ON p.id = c.product_id
           GROUP BY p.brand
           ORDER BY complaint_count DESC";
$result1 = $conn->query($query1);

$brands = [];
$complaintCounts = [];

while ($row = $result1->fetch_assoc()) {
    $brands[] = $row['brand'];
    $complaintCounts[] = $row['complaint_count'];
}

// Query for solved vs pending
$query2 = "SELECT p.brand,
                  COUNT(c.id) AS total_complaints,
                  SUM(CASE WHEN c.status = 'resolved' THEN 1 ELSE 0 END) AS solved,
                  SUM(CASE WHEN c.status != 'resolved' THEN 1 ELSE 0 END) AS pending
           FROM products p
           LEFT JOIN complaints c ON p.id = c.product_id
           GROUP BY p.brand
           ORDER BY total_complaints DESC";
$result2 = $conn->query($query2);

$brands2 = [];
$solved = [];
$pending = [];

while ($row = $result2->fetch_assoc()) {
    $brands2[] = $row['brand'];
    $solved[] = $row['solved'];
    $pending[] = $row['pending'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Brand Heatmap</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: linear-gradient(120deg, #f9f9f9, #e3f2fd);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #1B3C53;
            margin-bottom: 20px;
        }
        .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .chart-card {
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        canvas {
            max-width: 100% !important;
            height: 400px !important;
        }
        @media (max-width: 900px) {
            .container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <h2>Brand Complaint Analysis</h2>
    <div class="container">
        <!-- Chart 1: Bubble Heatmap -->
        <div class="chart-card">
            <h3>Total Complaints per Brand</h3>
            <canvas id="heatmapChart"></canvas>
        </div>

        <!-- Chart 2: Solved vs Pending -->
        <div class="chart-card">
            <h3>Solved vs Pending Complaints</h3>
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <script>
        // Chart 1: Bubble Heatmap
        const heatmapCtx = document.getElementById('heatmapChart').getContext('2d');
        new Chart(heatmapCtx, {
            type: 'bubble',
            data: {
                datasets: [
                    <?php for ($i = 0; $i < count($brands); $i++): ?>
                    {
                        label: "<?php echo $brands[$i]; ?>",
                        data: [{x: <?php echo $i*2; ?>, y: <?php echo $i*2; ?>, r: <?php echo $complaintCounts[$i]*5; ?>}],
                        backgroundColor: "rgba(54, 162, 235, 0.6)",
                        borderColor: "rgba(54, 162, 235, 1)"
                    },
                    <?php endfor; ?>
                ]
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ": " + context.dataset.data[0].r/5 + " complaints";
                            }
                        }
                    }
                },
                scales: {
                    x: { display: false },
                    y: { display: false }
                }
            }
        });

        // Chart 2: Stacked Bar for Solved vs Pending
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($brands2); ?>,
                datasets: [
                    {
                        label: 'Solved',
                        data: <?php echo json_encode($solved); ?>,
                        backgroundColor: 'rgba(76, 175, 80, 0.8)'
                    },
                    {
                        label: 'Pending',
                        data: <?php echo json_encode($pending); ?>,
                        backgroundColor: 'rgba(244, 67, 54, 0.8)'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: { mode: 'index', intersect: false },
                    legend: { position: 'top' }
                },
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>
