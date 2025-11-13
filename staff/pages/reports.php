<?php
require_once '../config/exempelfil_erp.php';
$stmt = $conn->query("
    SELECT 
        category,
        COUNT(*) as total_appointments,
        COUNT(CASE WHEN status = 'scheduled' THEN 1 END) as scheduled,
        COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
        COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled
    FROM appointments
    WHERE appointment_date >= CURRENT_DATE - INTERVAL '30 days'
    GROUP BY category
");
$appointment_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->query("
    SELECT 
        DATE(appointment_date) as date,
        COUNT(*) as count
    FROM appointments
    WHERE appointment_date >= CURRENT_DATE - INTERVAL '14 days'
        AND appointment_date <= CURRENT_DATE
    GROUP BY DATE(appointment_date)
    ORDER BY date
");
$daily_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->query("
    SELECT COUNT(*) as total FROM appointments WHERE appointment_date >= CURRENT_DATE - INTERVAL '30 days'
");
$total_appointments_month = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->query("
    SELECT COUNT(*) as total FROM prescriptions WHERE prescribed_date >= CURRENT_DATE - INTERVAL '30 days'
");
$total_prescriptions_month = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->query("
    SELECT COUNT(*) as total FROM prescriptions WHERE prescribed_date >= CURRENT_DATE - INTERVAL '30 days' AND is_antibiotic = true
");
$antibiotic_prescriptions = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
?>

<div class="stats-grid">
    <div class="stat-card">
        <h4><?php echo $total_appointments_month; ?></h4>
        <p>Appointments (30 days)</p>
    </div>
    <div class="stat-card secondary">
        <h4><?php echo $total_prescriptions_month; ?></h4>
        <p>Prescriptions (30 days)</p>
    </div>
</div>

<div class="card">
    <h3>Appointments by Category (Last 30 Days)</h3>
    
    <?php if (count($appointment_stats) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Total</th>
                    <th>Scheduled</th>
                    <th>Completed</th>
                    <th>Cancelled</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointment_stats as $stat): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars(ucfirst($stat['category'])); ?></strong></td>
                    <td><span class="badge badge-info"><?php echo $stat['total_appointments']; ?></span></td>
                    <td><span class="badge badge-warning"><?php echo $stat['scheduled']; ?></span></td>
                    <td><span class="badge badge-success"><?php echo $stat['completed']; ?></span></td>
                    <td><span class="badge badge-danger"><?php echo $stat['cancelled']; ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #6C757D; margin-top: 16px;">No data available.</p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Daily Appointment Trend (Last 14 Days)</h3>
    
    <?php if (count($daily_appointments) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Appointments</th>
                    <th>Visual</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $max_count = max(array_column($daily_appointments, 'count'));
                foreach ($daily_appointments as $daily): 
                    $percentage = ($daily['count'] / $max_count) * 100;
                ?>
                <tr>
                    <td><?php echo date('Y-m-d', strtotime($daily['date'])); ?></td>
                    <td><strong><?php echo $daily['count']; ?></strong></td>
                    <td>
                        <div style="background: #E9ECEF; border-radius: 4px; overflow: hidden;">
                            <div style="background: #0066CC; height: 20px; width: <?php echo $percentage; ?>%; min-width: 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.8em;">
                                <?php echo $daily['count']; ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #6C757D; margin-top: 16px;">No data available.</p>
    <?php endif; ?>
</div>
