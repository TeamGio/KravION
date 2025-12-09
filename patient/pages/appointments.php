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
            <th><?php echo $t['appointment_date'] ?? 'Datum'; ?></th>
            <th><?php echo $t['practitioner'] ?? 'Behandlare'; ?></th>
            <th><?php echo $t['title'] ?? 'Titel'; ?></th>
            <th><?php echo $t['patient'] ?? 'Patient'; ?></th>
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
            <td><?php echo htmlspecialchars($app['appointment_date'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($app['practitioner'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($app['title'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($app['patient'] ?? 'N/A'); ?></td>
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




<div class="card" style="margin-top: 20px; padding: 15px;">
    <h3>Hälsobedömning</h3>
    <p>Fyll i formuläret nedan.</p>

    <form method="post" action="skickaerp.php">
        <input type="hidden" name="action" value="health_assessment">





        <!-- Besökstyp -->
        <div class="form-group">
            <label for="visit_type">Besökstyp</label>
            <select name="visit_type" id="visit_type" class="form-control" required>
             
            </select>
        </div>
        <!-- 
        <div class="form-group">
            <label for="patient_id">Patient</label>
            <select name="patient_id" id="patient_id" class="form-control" required>
                <option value="">Välj patient...</option>

                <?php if (!empty($all_patients)): ?>
                    <?php foreach ($all_patients as $p): ?>
                        <?php
                            $id   = htmlspecialchars($p['name']);
                            $name = htmlspecialchars($p['patient_name'] ?? $p['name']);
                            $uid  = htmlspecialchars($p['uid'] ?? '');
                        ?>
                        <option value="<?php echo $id; ?>">
                            <?php echo $name . ($uid ? " ({$uid})" : ""); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>

            </select>
        </div>
                    -->

        <!-- Personnummer -->
        <div class="form-group">
            <label for="personal_number">Personnummer (12 siffror)</label>
            <input type="text"
                   name="personal_number"
                   id="personal_number"
                   class="form-control"
                   maxlength="12"
                   minlength="12"
                   required>
        </div>

        <hr>

        <h4>Medicinska frågor</h4>

        <div class="form-group">
            <label>Feber i sju dygn?</label><br>
            <label style="margin-right:10px;">
                <input type="radio" name="fever7" value="ja" required> Ja
            </label>
            <label>
                <input type="radio" name="fever7" value="nej" required> Nej
            </label>
        </div>

        <div class="form-group">
            <label>Hosta?</label><br>
            <label style="margin-right:10px;">
                <input type="radio" name="cough" value="ja" required>  Ja
            </label>
            <label>
                <input type="radio" name="cough" value="nej" required> Nej
            </label>
        </div>

        <div class="form-group">
            <label>Blod när du hostar?</label><br>
            <label style="margin-right:10px;">
                <input type="radio" name="blood_cough" value="ja" required> Ja
            </label>
            <label>
                <input type="radio" name="blood_cough" value="nej" required> Nej
            </label>
        </div>

        <div class="form-group">
            <label>Tungt att andas?</label><br>
            <label style="margin-right:10px;">
                <input type="radio" name="breathing" value="ja" required> Ja
            </label>
            <label>
                <input type="radio" name="breathing" value="nej" required> Nej
            </label>
        </div>

        <div class="form-group">
            <label>Muskel- eller huvudvärk?</label><br>
            <label style="margin-right:10px;">
                <input type="radio" name="pain" value="ja" required> Ja
            </label>
            <label>
                <input type="radio" name="pain" value="nej" required> Nej
            </label>
        </div>

        <div class="form-group">
            <label>Varit sjuk i över en vecka?</label><br>
            <label style="margin-right:10px;">
                <input type="radio" name="sick_week" value="ja" required> Ja
            </label>
            <label>
                <input type="radio" name="sick_week" value="nej" required> Nej
            </label>
        </div>

        <div class="form-group">
            <label>Andra symtom?</label><br>
            <label style="margin-right:10px;">
                <input type="radio" name="other_symptom" value="ja" required> Ja
            </label>
            <label>
                <input type="radio" name="other_symptom" value="nej" required> Nej
            </label>
        </div>

        <div class="form-group">
            <label for="desc_medical">Beskriv dina besvär (max 150 ord)</label>
            <textarea name="desc_medical"
                      id="desc_medical"
                      class="form-control"
                      rows="3"></textarea>
        </div>

        <hr>

        <h4>Kontakt kurator</h4>

        <div class="form-group">
            <label>Nedstämd?</label><br>
            <label style="margin-right:10px;">
                <input type="radio" name="low_mood" value="ja" required> Ja
            </label>
            <label>
                <input type="radio" name="low_mood" value="nej" required> Nej
            </label>
        </div>

        <div class="form-group">
            <label>Ångest/oro?</label><br>
            <label style="margin-right:10px;">
                <input type="radio" name="anxiety" value="ja" required> Ja
            </label>
            <label>
                <input type="radio" name="anxiety" value="nej" required> Nej
            </label>
        </div>

        <div class="form-group">
            <label for="desc_counselor">Beskriv dina besvär (max 150 ord)</label>
            <textarea name="desc_counselor"
                      id="desc_counselor"
                      class="form-control"
                      rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top:10px;">
            Skicka formulär
        </button>
    </form>
</div>
