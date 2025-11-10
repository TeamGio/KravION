<?php
$stmt = $conn->query("
    SELECT p.*, 
           COUNT(DISTINCT a.id) as appointment_count,
           COUNT(DISTINCT pr.id) as prescription_count
    FROM patients p
    LEFT JOIN appointments a ON p.id = a.patient_id
    LEFT JOIN prescriptions pr ON p.id = pr.patient_id
    GROUP BY p.id
    ORDER BY p.last_name, p.first_name
");
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h3>Patient Records</h3>
    
    <?php if (count($patients) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Personal Number</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Date of Birth</th>
                    <th>Appointments</th>
                    <th>Prescriptions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                <tr>
                    <td><?php echo htmlspecialchars($patient['personal_number']); ?></td>
                    <td><strong><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($patient['email']); ?></td>
                    <td><?php echo htmlspecialchars($patient['phone'] ?? 'N/A'); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($patient['date_of_birth'])); ?></td>
                    <td><span class="badge badge-info"><?php echo $patient['appointment_count']; ?></span></td>
                    <td><span class="badge badge-success"><?php echo $patient['prescription_count']; ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #6C757D; margin-top: 16px;">No patients found.</p>
    <?php endif; ?>
</div>
