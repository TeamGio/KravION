<?php
$encounters = $erp_client->getJournalRecordsForPatient($patient_erp_id);
$vitals     = $erp_client->getVitalSignsForPatient($patient_erp_id);

$records = [];

foreach ($encounters as $enc) {
    $key = ($enc['encounter_date'] ?? '') . ' ' . ($enc['encounter_time'] ?? '');
    $records[$key]['encounter'] = $enc;
}

foreach ($vitals as $vs) {
    $key = ($vs['signs_date'] ?? '') . ' ' . ($vs['signs_time'] ?? '');
    $records[$key]['vitals'] = $vs;
}

krsort($records);
?>

<div class="card">
    <h3><?php echo $t['medical_journal']; ?></h3>

    <?php if (!empty($records)): ?>
        <?php foreach ($records as $datetime => $bundle): ?>
            <?php
                $date_time_display = date('Y-m-d H:i', strtotime($datetime));
                $enc = $bundle['encounter'] ?? [];
                $vs  = $bundle['vitals'] ?? [];
            ?>

            <div class="card" style="margin-bottom:25px; border-left:5px solid #007bff; padding:15px;">
                <div style="display:flex; justify-content:space-between; border-bottom:1px solid #dee2e6; padding-bottom:8px;">
                    <h4 style="margin:0;">
                        <?php echo $t['encounter_on'] ?? 'Besök den'; ?>:
                        <?php echo htmlspecialchars($date_time_display); ?>
                    </h4>
                    <span style="font-size:0.9em; color:#6c757d;">
                        <?php echo $t['provider']; ?>:
                        <?php echo htmlspecialchars($enc['practitioner_name'] ?? 'N/A'); ?>
                    </span>
                </div>

                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:10px; font-size:0.9em;">
                    <div>
                        <p><?php echo $t['height']; ?> (m): <?php echo htmlspecialchars($vs['height'] ?? 'N/A'); ?></p>
                        <p><?php echo $t['weight']; ?> (kg): <?php echo htmlspecialchars($vs['weight'] ?? 'N/A'); ?></p>
                        <p>BMI: <?php echo htmlspecialchars($vs['bmi'] ?? 'N/A'); ?></p>
                    </div>
                    <div>
                        <p><?php echo $t['temperature']; ?>: <?php echo htmlspecialchars($vs['temperature'] ?? 'N/A'); ?></p>
                        <p><?php echo $t['pulse']; ?>: <?php echo htmlspecialchars($vs['pulse'] ?? 'N/A'); ?></p>
                        <p><?php echo $t['respiratory rate']; ?>: <?php echo htmlspecialchars($vs['respiratory_rate'] ?? 'N/A'); ?></p>
                    </div>
                    <div>
                        <p><?php echo $t['tongue']; ?>: <?php echo htmlspecialchars($vs['tongue'] ?? 'N/A'); ?></p>
                        <p><?php echo $t['abdomen']; ?>: <?php echo htmlspecialchars($vs['abdomen'] ?? 'N/A'); ?></p>
                        <p><?php echo $t['reflexes']; ?>: <?php echo htmlspecialchars($vs['reflexes'] ?? 'N/A'); ?></p>
                    </div>
                </div>

                <div style="margin-top:15px; border-top:1px dashed #ced4da; padding-top:10px;">
                    <p><?php echo $t['symptoms']; ?>: <?php echo htmlspecialchars($enc['custom_symtom'] ?? 'N/A'); ?></p>
                    <h5><?php echo $t['notes']; ?>:</h5>
                    <div style="background:#f8f9fa; border:1px solid #e9ecef; padding:10px;">
                        <?php echo htmlspecialchars($vs['vital_signs_note'] ?? $enc['notes'] ?? 'Ingen anteckning.'); ?>
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