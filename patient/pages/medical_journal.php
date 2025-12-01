<?php
require_once '../config/exempelfil_erp.php';

?>

<div class="card">
    <h3>Medical Journal</h3>
    
    <?php if (count($records) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Diagnosis</th>
                    <th>Treatment</th>
                    <th>Provider</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                <tr>
                    <td><?php echo date('Y-m-d', strtotime($record['record_date'])); ?></td>
                    <td><span class="badge badge-info"><?php echo htmlspecialchars($record['record_type'] ?? 'General'); ?></span></td>
                    <td><?php echo htmlspecialchars($record['diagnosis'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($record['treatment'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars(($record['first_name'] ?? '') . ' ' . ($record['last_name'] ?? '')); ?></td>
                </tr>
                <tr>
                    <td colspan="5" style="background: #F8F9FA; padding: 12px;">
                        <strong>Symptoms:</strong> <?php echo htmlspecialchars($record['symptoms'] ?? 'N/A'); ?><br>
                        <strong>Notes:</strong> <?php echo htmlspecialchars($record['notes'] ?? 'N/A'); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #6C757D; margin-top: 16px;">No medical records found.</p>
    <?php endif; ?>
</div>
