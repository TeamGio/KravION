<?php
$records = $erp_client->getJournalRecordsForPatient($patient_erp_id);
$records = $erp_client->getVitalSignsForPatient($patient_erp_id);
?>

<div class="card">
    <h3><?php echo $t['medical_journal']; ?></h3>

    <?php
    if (isset($error_message) && !empty($error_message)): ?>
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($records)): ?>
        
        <?php foreach ($records as $record): ?>
            <?php
                $encounter_datetime_str = ($record['encounter_date'] ?? 'N/A') . ' ' . ($record['encounter_time'] ?? '00:00:00');
                $encounter_datetime = strtotime($encounter_datetime_str);
                $date_time_display = date('Y-m-d H:i', $encounter_datetime);
            ?>

            <div class="card" style="margin-bottom: 25px; border-left: 5px solid #007bff; padding: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #dee2e6; padding-bottom: 8px; margin-bottom: 10px;">
                    <h4 style="margin: 0; font-size: 1.1em;">
                        <?php echo $t['encounter_on'] ?? 'Besök den'; ?>: 
                        <?php echo htmlspecialchars($date_time_display); ?>
                    </h4>
                    <span style="font-size: 0.9em; color: #6c757d;">
                        <?php echo $t['provider']; ?>: 
                        <?php echo htmlspecialchars($record['practitioner_name'] ?? 'N/A'); ?>
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; font-size: 0.9em;">
                    <div>
                        <p style="margin: 0;"><?php echo $t['height']; ?> (m): <?php echo htmlspecialchars($record['height'] ?? 'N/A'); ?></p>
                        <p style="margin: 0;"><?php echo $t['weight']; ?> (kg): <?php echo htmlspecialchars($record['weight'] ?? 'N/A'); ?></p>
                        <p style="margin: 0;"> BMI: <?php echo htmlspecialchars($record['bmi'] ?? 'N/A'); ?></p>
                    </div>

                    <div>
                        <p style="margin: 0;"><?php echo $t['temperature']; ?> : <?php echo htmlspecialchars($record['temperature'] ?? 'N/A'); ?></p>
                        <p style="margin: 0;"><?php echo $t['pulse']; ?> : <?php echo htmlspecialchars($record['pulse'] ?? 'N/A'); ?></p>
                        <p style="margin: 0;"><?php echo $t['respiratory rate']; ?> : <?php echo htmlspecialchars($record['respiratory_rate'] ?? 'N/A'); ?></p>
                    </div>
                    
                    <div>
                        <p style="margin: 0;"><?php echo $t['tongue']; ?> : <?php echo htmlspecialchars($record['tongue'] ?? 'N/A'); ?></p>
                        <p style="margin: 0;"><?php echo $t['abdomen']; ?> : <?php echo htmlspecialchars($record['abdomen'] ?? 'N/A'); ?></p>
                        <p style="margin: 0;"><?php echo $t['reflexes']; ?> : <?php echo htmlspecialchars($record['reflexes'] ?? 'N/A'); ?></p>
                    </div>
                </div>
                
                <div style="margin-top: 15px; border-top: 1px dashed #ced4da; padding-top: 10px;">
                    <p style="margin-bottom: 8px;"><?php echo $t['symptoms']; ?>: <?php echo htmlspecialchars($record['symptoms'] ?? 'N/A'); ?></p>
                    
                    <h5 style="margin-bottom: 5px; font-size: 1em;"><?php echo $t['notes']; ?>:</h5>
                    <div style="background-color: #f8f9fa; border: 1px solid #e9ecef; padding: 10px; min-height: 50px; white-space: pre-wrap; word-wrap: break-word;">
                        <?php echo htmlspecialchars($record['notes'] ?? $t['vital_signs_note']); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <div class="alert alert-info">
            <?php echo $t['no_records']; ?>
        </div>
    <?php endif; ?>
</div>