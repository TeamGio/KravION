<?php
$appointments = $erp_client->getAppointmentsForPatient($patient_erp_id);
?>



<div class="card" style="margin-top: 20px;">
    <h3><?php echo $t['appointments']; ?></h3>
    <p><?php echo $t['appointments_info']; ?></p>
    <?php if (!empty($appointments)): ?>
        <table class="table-striped">
            <thead>
                <tr>
                    <th><?php echo $t['date']; ?></th>
                    <th><?php echo $t['time']; ?></th>
                    <th><?php echo $t['practitioner_name']; ?></th>
                    <th><?php echo $t['reason']; ?></th>
                    <th><?php echo $t['patient']; ?></th>
                    <th><?php echo $t['cancel_booking']; ?></th>
                    <th><?php echo $t['reschedule_booking']?></th>
                </tr>
            </thead>


    


            <tbody>
                <?php foreach ($appointments as $app): 
                    $date = !empty($app['appointment_date']) 
                        ? date('Y-m-d', strtotime($app['appointment_date'])) 
                        : 'N/A';
                    $practitioner = $app['practitioner_name'] ?? 'N/A';
                    $title = $app['title'] ?? 'N/A';
                    $patient_name = $app['patient'] ?? 'N/A';
                    $time = $app['appointment_time'] ??'';
                ?>


                <tr>
                    <td><?php echo htmlspecialchars($date); ?></td>
                    <td><?php echo htmlspecialchars($time); ?></td>
                    <td><?php echo htmlspecialchars($practitioner); ?></td>
                    <td><?php echo htmlspecialchars($title); ?></td>
                    <td><?php echo htmlspecialchars($patient_name); ?></td>
                    <td> 
                        <form method="post" action="pages/Cancel.php">
                            <!-- SKICKA BOKNINGENS ID (name) -->
                            <input type="hidden" name="appointment_name" value="<?php echo htmlspecialchars($app['name']); ?>">
                            <button type="submit" name="action" value="cancel">Avboka</button>
                        </form>
                    </td>
                    <td>
                        <form method="post" action="pages/Reschedule.php">
                            <input type="hidden" name="appointment_name" value="<?php echo htmlspecialchars($app['name']); ?>">
                            <button type="submit" name="action" value="reschedule">Boka om</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <div class="alert alert-info">
            <?php echo $t['no_upcoming_appointments']; ?>
        </div>
    <?php endif; ?>
</div>

<div class="card" style="margin-top: 20px;">
    <h3><?php echo $t['contact_form']; ?></h3>
    <iframe src="http://193.93.250.83:8080/kontakt-formular"
            style="border: none; width: 100%; height: 600px;">
    </iframe>
</div>