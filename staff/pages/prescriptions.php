<?php
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'approve_renewal') {
        $prescription_id = $_POST['prescription_id'];
        
        $stmt = $conn->prepare("UPDATE prescriptions SET renewal_requested = false, prescribed_date = CURRENT_DATE WHERE id = :id");
        $stmt->bindParam(':id', $prescription_id);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Prescription renewal approved!</div>';
        }
    } elseif ($_POST['action'] === 'create') {
        $patient_id = $_POST['patient_id'];
        $medication_name = $_POST['medication_name'];
        $dosage = $_POST['dosage'];
        $frequency = $_POST['frequency'];
        $duration = $_POST['duration'];
        $quantity = $_POST['quantity'];
        $notes = $_POST['notes'];
        
        $stmt = $conn->prepare("
            INSERT INTO prescriptions (patient_id, prescribed_by, medication_name, dosage, frequency, duration, quantity, prescribed_date, notes)
            VALUES (:patient_id, :staff_id, :medication_name, :dosage, :frequency, :duration, :quantity, CURRENT_DATE, :notes)
        ");
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->bindParam(':medication_name', $medication_name);
        $stmt->bindParam(':dosage', $dosage);
        $stmt->bindParam(':frequency', $frequency);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':notes', $notes);
        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Prescription created successfully!</div>';
        }
    }
}

$stmt = $conn->query("
    SELECT p.*, 
           pt.first_name as patient_first_name, 
           pt.last_name as patient_last_name,
           pt.personal_number,
           s.first_name as staff_first_name,
           s.last_name as staff_last_name
    FROM prescriptions p
    JOIN patients pt ON p.patient_id = pt.id
    LEFT JOIN staff s ON p.prescribed_by = s.id
    WHERE p.renewal_requested = true OR p.prescribed_date >= CURRENT_DATE - INTERVAL '30 days'
    ORDER BY p.renewal_requested DESC, p.prescribed_date DESC
");
$prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->query("SELECT id, first_name, last_name, personal_number FROM patients ORDER BY last_name, first_name");
$all_patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h3>Prescription Management</h3>
    <?php echo $message; ?>
    
    <?php if (count($prescriptions) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Medication</th>
                    <th>Dosage</th>
                    <th>Prescribed Date</th>
                    <th>Prescribed By</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescriptions as $prescription): ?>
                <tr style="<?php echo $prescription['renewal_requested'] ? 'background: #FFF3CD;' : ''; ?>">
                    <td>
                        <strong><?php echo htmlspecialchars($prescription['patient_first_name'] . ' ' . $prescription['patient_last_name']); ?></strong><br>
                        <small><?php echo htmlspecialchars($prescription['personal_number']); ?></small>
                    </td>
                    <td><strong><?php echo htmlspecialchars($prescription['medication_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($prescription['dosage']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($prescription['prescribed_date'])); ?></td>
                    <td><?php echo htmlspecialchars(($prescription['staff_first_name'] ?? '') . ' ' . ($prescription['staff_last_name'] ?? '')); ?></td>
                    <td>
                        <?php 
                        if ($prescription['renewal_requested']) {
                            echo '<span class="badge badge-warning">Renewal Requested</span>';
                        } else {
                            $status = $prescription['status'];
                            $badge_class = $status === 'active' ? 'badge-success' : 'badge-info';
                            echo '<span class="badge ' . $badge_class . '">' . htmlspecialchars(ucfirst($status)) . '</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($prescription['renewal_requested']): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="approve_renewal">
                            <input type="hidden" name="prescription_id" value="<?php echo $prescription['id']; ?>">
                            <button type="submit" class="btn btn-secondary" style="padding: 4px 12px; font-size: 0.9em;">Approve Renewal</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #6C757D; margin-top: 16px;">No recent prescriptions found.</p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Create New Prescription</h3>
    <form method="POST" style="margin-top: 16px;">
        <input type="hidden" name="action" value="create">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div class="form-group">
                <label>Patient</label>
                <select name="patient_id" class="form-control" required>
                    <option value="">Select Patient</option>
                    <?php foreach ($all_patients as $patient): ?>
                    <option value="<?php echo $patient['id']; ?>"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name'] . ' (' . $patient['personal_number'] . ')'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Medication Name</label>
                <input type="text" name="medication_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Dosage</label>
                <input type="text" name="dosage" class="form-control" required placeholder="e.g., 500mg">
            </div>
            <div class="form-group">
                <label>Frequency</label>
                <input type="text" name="frequency" class="form-control" required placeholder="e.g., 3 times daily">
            </div>
            <div class="form-group">
                <label>Duration</label>
                <input type="text" name="duration" class="form-control" required placeholder="e.g., 7 days">
            </div>
            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" class="form-control" required min="1">
            </div>
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Create Prescription</button>
    </form>
</div>
