<?php
$stmt = $conn->prepare("
    SELECT lr.*, s.first_name, s.last_name 
    FROM lab_results lr
    LEFT JOIN staff s ON lr.ordered_by = s.id
    WHERE lr.patient_id = :patient_id
    ORDER BY lr.test_date DESC
");
$stmt->bindParam(':patient_id', $patient_id);
$stmt->execute();
$lab_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h3>Laboratory Results</h3>
    
    <?php if (count($lab_results) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Test Name</th>
                    <th>Date</th>
                    <th>Result</th>
                    <th>Normal Range</th>
                    <th>Status</th>
                    <th>Ordered By</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lab_results as $result): ?>
                <tr>
                    <td><?php echo htmlspecialchars($result['test_name']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($result['test_date'])); ?></td>
                    <td><strong><?php echo htmlspecialchars($result['result']); ?></strong></td>
                    <td><?php echo htmlspecialchars($result['normal_range'] ?? 'N/A'); ?></td>
                    <td>
                        <?php 
                        $status = $result['status'] ?? 'completed';
                        $badge_class = $status === 'normal' ? 'badge-success' : ($status === 'abnormal' ? 'badge-warning' : 'badge-info');
                        ?>
                        <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars(ucfirst($status)); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars(($result['first_name'] ?? '') . ' ' . ($result['last_name'] ?? '')); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #6C757D; margin-top: 16px;">No lab results found.</p>
    <?php endif; ?>
</div>
