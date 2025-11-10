<?php
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'book') {
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        $category = $_POST['category'];
        $reason = $_POST['reason'];
        
        $stmt = $conn->prepare("
            INSERT INTO appointments (patient_id, appointment_date, appointment_time, category, appointment_type, reason, status)
            VALUES (:patient_id, :appointment_date, :appointment_time, :category, 'consultation', :reason, 'scheduled')
        ");
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->bindParam(':appointment_date', $appointment_date);
        $stmt->bindParam(':appointment_time', $appointment_time);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':reason', $reason);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Appointment booked successfully!</div>';
        }
    } elseif ($_POST['action'] === 'cancel') {
        $appointment_id = $_POST['appointment_id'];
        
        $stmt = $conn->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = :id AND patient_id = :patient_id");
        $stmt->bindParam(':id', $appointment_id);
        $stmt->bindParam(':patient_id', $patient_id);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Appointment cancelled successfully!</div>';
        }
    }
}

$stmt = $conn->prepare("
    SELECT a.*, s.first_name, s.last_name, s.role 
    FROM appointments a
    LEFT JOIN staff s ON a.staff_id = s.id
    WHERE a.patient_id = :patient_id
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$stmt->bindParam(':patient_id', $patient_id);
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h3>Book New Appointment</h3>
    <?php echo $message; ?>
    
    <form method="POST" style="margin-top: 16px;">
        <input type="hidden" name="action" value="book">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div class="form-group">
                <label>Appointment Date</label>
                <input type="date" name="appointment_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label>Appointment Time</label>
                <input type="time" name="appointment_time" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category" class="form-control" required>
                    <option value="nurse">Nurse</option>
                    <option value="doctor">Doctor</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Reason for Visit</label>
            <textarea name="reason" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Book Appointment</button>
    </form>
</div>

<div class="card">
    <h3>My Appointments</h3>
    
    <?php if (count($appointments) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Category</th>
                    <th>Provider</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?php echo date('Y-m-d', strtotime($appointment['appointment_date'])); ?></td>
                    <td><?php echo date('H:i', strtotime($appointment['appointment_time'])); ?></td>
                    <td><span class="badge badge-info"><?php echo htmlspecialchars(ucfirst($appointment['category'])); ?></span></td>
                    <td><?php echo htmlspecialchars(($appointment['first_name'] ?? 'Not assigned') . ' ' . ($appointment['last_name'] ?? '')); ?></td>
                    <td>
                        <?php 
                        $status = $appointment['status'];
                        $badge_class = $status === 'scheduled' ? 'badge-success' : ($status === 'cancelled' ? 'badge-danger' : 'badge-warning');
                        ?>
                        <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars(ucfirst($status)); ?></span>
                    </td>
                    <td>
                        <?php if ($appointment['status'] === 'scheduled' && strtotime($appointment['appointment_date']) >= strtotime(date('Y-m-d'))): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="cancel">
                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                            <button type="submit" class="btn btn-alert" style="padding: 4px 12px; font-size: 0.9em;" onclick="return confirm('Are you sure you want to cancel this appointment?')">Cancel</button>
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
