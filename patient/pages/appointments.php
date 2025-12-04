<?php

$appointments = $erp_client->getAppointmentsForPatient($patient_erp_id); 
?>


<div class="card" style="margin-top: 20px;">
    <h3><?php echo $t['appointments'] ?? 'Tidsbokning'; ?></h3>
    <p>Här visas alla dina kommande inbokade tider.</p>
    <?php if (!empty($appointments)): ?>
<table class="table-striped">
    <thead>
        <tr>
            <th><?php echo $t['date'] ?? 'Datum'; ?></th>
            <th><?php echo $t['time'] ?? 'Tid'; ?></th>
            <th><?php echo $t['practitioner'] ?? 'Behandlare'; ?></th>
            <th><?php echo $t['reason'] ?? 'Ärende/Titel'; ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($appointments as $app): 
            $date = !empty($app['appointment_date']) 
                ? date('Y-m-d', strtotime($app['appointment_date'])) 
                : 'N/A';
            $practitioner = $app['practitioner'] ?? 'N/A';
            $title        = $app['title']        ?? 'N/A';
        ?>
        <tr>
            <td><?php echo htmlspecialchars($date); ?></td>
            <td><?php echo htmlspecialchars($time); ?></td>
            <td><?php echo htmlspecialchars($department); ?></td>
            <td><?php echo htmlspecialchars($practitioner); ?></td>
            <td><?php echo htmlspecialchars($title); ?></td>
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

<div class="card" style="margin-top: 20px;">
    <h3>Kontaktformulär</h3>
    <iframe src="http://193.93.250.83:8080/kontakt-formular"
            style="border: none; width: 100%; height: 600px;">
    </iframe>
</div>