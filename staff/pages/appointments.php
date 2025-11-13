<?php
require_once '../config/exempelfil_erp.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'assign') {
        $appointment_id = $_POST['appointment_id'];
        
        $stmt = $conn->prepare("UPDATE appointments SET staff_id = :staff_id, status = 'confirmed' WHERE id = :id");
        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->bindParam(':id', $appointment_id);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Appointment assigned successfully!</div>';
        }
    } elseif ($_POST['action'] === 'complete') {
        $appointment_id = $_POST['appointment_id'];
        
        $stmt = $conn->prepare("UPDATE appointments SET status = 'completed' WHERE id = :id");
        $stmt->bindParam(':id', $appointment_id);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Appointment marked as completed!</div>';
        }
    }
}

$stmt = $conn->query("
    SELECT a.*, 
           p.first_name as patient_first_name, 
           p.last_name as patient_last_name,
           p.personal_number,
           s.first_name as staff_first_name,
           s.last_name as staff_last_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    LEFT JOIN staff s ON a.staff_id = s.id
    WHERE a.appointment_date >= CURRENT_DATE - INTERVAL '7 days'
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h3>Appointment Management</h3>
    <?php echo $message; ?>
    
    <?php if (count($appointments) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Patient</th>
                    <th>Category</th>
                    <th>Reason</th>
                    <th>Assigned To</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?php echo date('Y-m-d', strtotime($appointment['appointment_date'])); ?></td>
                    <td><?php echo date('H:i', strtotime($appointment['appointment_time'])); ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']); ?></strong><br>
                        <small><?php echo htmlspecialchars($appointment['personal_number']); ?></small>
                    </td>
                    <td><span class="badge badge-info"><?php echo htmlspecialchars(ucfirst($appointment['category'])); ?></span></td>
                    <td><?php echo htmlspecialchars($appointment['reason'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars(($appointment['staff_first_name'] ?? 'Not assigned') . ' ' . ($appointment['staff_last_name'] ?? '')); ?></td>
                    <td>
                        <?php 
                        $status = $appointment['status'];
                        $badge_class = $status === 'scheduled' ? 'badge-warning' : ($status === 'confirmed' ? 'badge-info' : ($status === 'completed' ? 'badge-success' : 'badge-danger'));
                        ?>
                        <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars(ucfirst($status)); ?></span>
                    </td>
                    <td>
                        <?php if ($appointment['status'] === 'scheduled'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="assign">
                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                            <button type="submit" class="btn btn-accent" style="padding: 4px 12px; font-size: 0.9em;">Assign to Me</button>
                        </form>
                        <?php elseif ($appointment['status'] === 'confirmed' || $appointment['status'] === 'scheduled'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="complete">
                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                            <button type="submit" class="btn btn-secondary" style="padding: 4px 12px; font-size: 0.9em;">Mark Complete</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #6C757D; margin-top: 16px;">No appointments found.</p>
    <?php endif; ?>
</div>
