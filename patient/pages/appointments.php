<?php

$appointments = $erp_client->getAppointmentsForPatient($patient_erp_id); 
?>

<div class="card">
    <h3><?php echo $t['appointments'] ?? 'Tidsbokning'; ?></h3>
    <p>Här visas alla dina kommande inbokade tider.</p>
</div>

<div class="card" style="margin-top: 20px;">
    <?php if (!empty($appointments)): ?>
        <table class="table-striped">
            <thead>
                <tr>
                    <th><?php echo $t['date'] ?? 'Datum'; ?></th>
                    <th><?php echo $t['time'] ?? 'Tid'; ?></th>
                    <th><?php echo $t['department'] ?? 'Avdelning'; ?></th>
                    <th><?php echo $t['practitioner'] ?? 'Behandlare'; ?></th>
                    <th><?php echo $t['reason'] ?? 'Ärende/Titel'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $app): 
                    // Konvertera Datetime-fält om ERPNext använder standardformat
                    $date = date('Y-m-d', strtotime($app['appointment_date'] ?? 'N/A'));
                    $time = date('H:i', strtotime($app['appointment_time'] ?? 'N/A'));
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($date); ?></td>
                    <td><?php echo htmlspecialchars($time); ?></td>
                    <td><?php echo htmlspecialchars($app['department'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($app['practitioner'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($app['title'] ?? 'N/A'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">
            <?php echo $t['no_upcoming_appointments'] ?? 'Inga kommande tidsbokningar hittades.'; ?>
        </div>
    <?php endif; ?>
</div>