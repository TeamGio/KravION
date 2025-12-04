<?php

$message = '';


$prescriptions = $erp_client->getPrescriptionsForPatient($patient_erp_id); 
?>
<div class="card" style="margin-top: 20px;">
    <h3><?php echo $t['prescriptions'] ?? 'Recept'; ?></h3>
    <?php if (!empty($prescriptions)): ?>
        <table class="table-striped">
            <thead>
                <tr>
                    <th><?php echo $t['medication'] ?? 'Läkemedel'; ?></th>
                    <th><?php echo $t['personnummer'] ?? 'Personnummer'; ?></th>
                    <th><?php echo $t['läkare'] ?? 'Läkare'; ?></th>
                    <th><?php echo $t['datum'] ?? 'Datum'; ?></th>
                    <th><?php echo $t['uttag'] ?? 'Uttag'; ?></th>
                    <th><?php echo $t['expiration_date'] ?? 'Utgångsdatum'; ?></th>
                    <th><?php echo $t['strenght'] ?? 'Styrka'; ?></th>
                    <th><?php echo $t['data_rsjo'] ?? 'Status'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescriptions as $prescription): 
                    $status = $prescription['data_rsjo'] ?? 'Ej satt';
                ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($prescription['medicin'] ?? 'N/A'); ?></strong></td>
                        <td><?php echo htmlspecialchars($prescription['personnummer'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($prescription['behandlare'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($prescription['datum'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($prescription['uttag'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($prescription['expiration_date'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($prescription['strenght'] ?? 'N/A'); ?></td>
                        <td>
                            <?php 
                                $is_approved = strtolower($status) === 'godkänd';
                                $badge_class = $is_approved ? 'badge-success' : 'badge-danger';
                                echo '<span class="badge ' . $badge_class . '">' . htmlspecialchars($status) . '</span>';
                            ?>
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

<div class="card" style="margin-top: 20px;">
    <h3>Förnya recept</h3>
    <iframe src="http://193.93.250.83:8080/g4fornya-recept" style="border: none; width: 100%; height: 400px;"></iframe>
</div>