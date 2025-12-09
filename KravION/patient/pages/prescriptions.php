<?php 

$message = '';

$prescriptions = $erp_client->getPrescriptionsForPatient($patient_erp_id);
?>
<div class="card" style="margin-top: 20px;">
    <h3><?php echo $t['prescriptions']; ?></h3>

    <?php if (!empty($prescriptions)): ?>
        <table class="table-striped">
            <thead>
                <tr>
                    <th><?php echo $t['medication']; ?></th>
                    <th><?php echo $t['personnummer']; ?></th>
                    <th><?php echo $t['provider']; ?></th>
                    <th><?php echo $t['date']; ?></th>
                    <th><?php echo $t['withdrawal']; ?></th>
                    <th><?php echo $t['expiration_date']; ?></th>
                    <th><?php echo $t['strength']; ?></th>
                    <th><?php echo $t['status']; ?></th>
                    <th><?php echo $t['renew_prescription']; ?></th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($prescriptions as $prescription): ?>
                    <?php 
                        // Originalstatus från ERP
                        $status = $prescription['data_rsjo'] ?? 'Ej satt';
                        $raw_status = strtolower(trim($status));

                        // Bestäm badge-färg baserat på originalstatus
                        $is_approved = ($raw_status === 'godkänd');
                        $badge_class = $is_approved ? 'badge-success' : 'badge-danger';

                        // Översätt status vid engelska
                        if ($lang === 'en') {
                            if ($raw_status === 'godkänd') {
                                $status = 'Approved';
                            } elseif ($raw_status === 'ej godkänd') {
                                $status = 'Not approved';
                            } elseif ($raw_status === 'behandlas') {
                                $status = 'Processing';
                            }
                        }
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
                            <span class="badge <?php echo $badge_class; ?>">
                                <?php echo htmlspecialchars($status); ?>
                            </span>
                        </td>

                        <!-- La till en knapp för att förnya recept -->
                        <td> 
                            <form method="post" action="pages/renewPrescription.php">
                            <input type="hidden" name="prescription_id" value="<?php echo htmlspecialchars($prescription['name']); ?>">
                            <button type="submit">Förnya</button>
                            </form>
                        </td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <div class="alert alert-info">
            <?php echo $t['no_prescriptions']; ?>
        </div>
    <?php endif; ?>
</div>

