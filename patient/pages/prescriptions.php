<?php

require_once '../config/exempelfil_erp.php';
$message = '';

?>

<div class="card">
    <h3>My Prescriptions</h3>
    <?php echo $message; ?>
    
    <?php if (count($prescriptions) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Medication</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Duration</th>
                    <th>Prescribed Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescriptions as $prescription): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($prescription['medication_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($prescription['dosage']); ?></td>
                    <td><?php echo htmlspecialchars($prescription['frequency'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($prescription['duration'] ?? 'N/A'); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($prescription['prescribed_date'])); ?></td>
                    <td>
                        <?php 
                        if ($prescription['renewal_requested']) {
                            echo '<span class="badge badge-warning">Renewal Pending</span>';
                        } else {
                            $status = $prescription['status'];
                            $badge_class = $status === 'active' ? 'badge-success' : 'badge-info';
                            echo '<span class="badge ' . $badge_class . '">' . htmlspecialchars(ucfirst($status)) . '</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($prescription['status'] === 'active' && !$prescription['renewal_requested']): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="request_renewal">
                            <input type="hidden" name="prescription_id" value="<?php echo $prescription['id']; ?>">
                            <button type="submit" class="btn btn-accent" style="padding: 4px 12px; font-size: 0.9em;">Request Renewal</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="background: #F8F9FA; padding: 12px;">
                        <strong>Prescribed by:</strong> <?php echo htmlspecialchars(($prescription['first_name'] ?? '') . ' ' . ($prescription['last_name'] ?? '')); ?><br>
                        <strong>Notes:</strong> <?php echo htmlspecialchars($prescription['notes'] ?? 'N/A'); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #6C757D; margin-top: 16px;">No prescriptions found.</p>
        

    <?php endif; ?>
</div>
