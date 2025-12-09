<?php

// Hämta journaler från databasen
$stmt = $conn->prepare("
    SELECT mr.*, s.first_name, s.last_name, s.role 
    FROM medical_records mr
    LEFT JOIN staff s ON mr.staff_id = s.id
    WHERE mr.patient_id = :patient_id
    ORDER BY mr.record_date DESC
");
$stmt->bindParam(':patient_id', $patient_id);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h3><?php echo $t['medical_journal']; ?></h3>

    <?php if (count($records) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th><?php echo $t['date']; ?></th>
                    <th><?php echo $t['record_type']; ?></th>
                    <th><?php echo $t['diagnosis']; ?></th>
                    <th><?php echo $t['treatment']; ?></th>
                    <th><?php echo $t['provider']; ?></th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($records as $record): ?>
                <tr>
                    <td><?php echo date('Y-m-d', strtotime($record['record_date'])); ?></td>

                    <td>
                        <span class="badge badge-info">
                            <?php echo htmlspecialchars($record['record_type'] ?? $t['general']); ?>
                        </span>
                    </td>

                    <td><?php echo htmlspecialchars($record['diagnosis'] ?? $t['not_available']); ?></td>
                    <td><?php echo htmlspecialchars($record['treatment'] ?? $t['not_available']); ?></td>

                    <td>
                        <?php echo htmlspecialchars(($record['first_name'] ?? '') . ' ' . ($record['last_name'] ?? '')); ?>
                    </td>
                </tr>

                <tr>
                    <td colspan="5" style="background: #F8F9FA; padding: 12px;">
                        <strong><?php echo $t['symptoms']; ?>:</strong>
                        <?php echo htmlspecialchars($record['symptoms'] ?? $t['not_available']); ?><br>

                        <strong><?php echo $t['notes']; ?>:</strong>
                        <?php echo htmlspecialchars($record['notes'] ?? $t['not_available']); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <p style="color: #6C757D; margin-top: 16px;">
            <?php echo $t['no_records']; ?>
        </p>
    <?php endif; ?>
</div>