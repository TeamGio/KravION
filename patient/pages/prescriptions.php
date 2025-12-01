<?php

$message = '';


    $prescriptions = $erp_client->getPrescriptionsForPatient($patient_erp_id); 
    ?>

    <div class="card">
    <h3><?php echo $t['prescriptions'] ?? 'Recept'; ?></h3>
    </div>

    <div class="card" style="margin-top: 20px;">
    <?php if (!empty($prescriptions)): ?>
    <table class="table-striped">
    <thead>
    <tr>
    <th><?php echo $t['medication'] ?? 'Läkemedel'; ?></th>
    <th><?php echo $t['dosage'] ?? 'Dosering'; ?></th>
    <th><?php echo $t['personnummer'] ?? 'Personnummer'; ?></th>
    <th><?php echo $t['behandlare'] ?? 'Behandlare'; ?></th>
    <th><?php echo $t['status'] ?? 'Status'; ?></th>
    <th><?php echo $t['action'] ?? 'Åtgärd'; ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($prescriptions as $prescription): 
    $status = $prescription['data_rsjo'] ?? 'Ej satt';
    ?>
<tr>
<td><strong><?php echo htmlspecialchars($prescription['medicin'] ?? 'N/A'); ?></strong></td>
<td><?php echo htmlspecialchars($prescription['dosage'] ?? 'N/A'); ?></td>
<td><?php echo htmlspecialchars($prescription['personnummer'] ?? 'N/A'); ?></td>
<td><?php echo htmlspecialchars($prescription['behandlare'] ?? 'N/A'); ?></td>
<td>
<?php 
$is_approved = strtolower($status) === 'godkänd';
$badge_class = $is_approved ? 'badge-success' : 'badge-danger';
echo '<span class="badge ' . $badge_class . '">' . htmlspecialchars($status) . '</span>';
?>
</td>
<td>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<div class="alert alert-info" style="padding: 15px; border: 1px solid #d4edda; background-color: #f0fff0; color: #155724; border-radius: 4px;">
<?php echo $t['no_prescriptions'] ?? 'Inga aktiva recept hittades.'; ?>
</div>
<?php endif; ?>
</div>