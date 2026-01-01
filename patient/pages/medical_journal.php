<?php
$encounters = $erp_client->getJournalRecordsForPatient($patient_erp_id);
$vitals     = $erp_client->getVitalSignsForPatient($patient_erp_id);
$med_recs   = $erp_client->getMedicalrecords($patient_erp_id);

$records = [];

foreach ($encounters as $enc) {
   $key = ($enc['encounter_date'] ?? '') . ' ' . ($enc['encounter_time'] ?? '');
   $records[$key] = ['type' => 'encounter', 'encounter' => $enc];
}

foreach ($vitals as $vs) {
   $key = ($vs['signs_date'] ?? '') . ' ' . ($vs['signs_time'] ?? '');
   if (isset($records[$key])) {
       $records[$key]['vitals'] = $vs;
   } else {
       $records[$key] = ['type' => 'encounter', 'vitals' => $vs];
   }
}

foreach ($med_recs as $mr) { // tar fram labbsvar
   if (($mr['reference_doctype'] ?? '') === 'Lab Test') {
       $lab_data = $erp_client->getDoc($mr['reference_doctype'], $mr['reference_name']);
       if ($lab_data) {
           $date = $lab_data['result_date'] ?? date('Y-m-d', strtotime($lab_data['creation']));
           $time = $lab_data['result_time'] ?? '00:00:00';
           $key = $date . ' ' . $time . '_' . $lab_data['name'];
          
           $l_staff = $lab_data['practitioner_name'] ?? $lab_data['employee_name'] ?? 'Laboratoriet';
           $records[$key] = ['type' => 'lab', 'data' => $lab_data, 'staff' => $l_staff];
       }
   }
}

krsort($records);
?>
<div class="card">
    <h3><?php echo $t['medical_journal']; ?></h3>
    <?php if (!empty($records)): ?>
        <?php foreach ($records as $datetime => $bundle): ?>
        <?php // tar fram ALLA små flikar
            $type = $bundle['type'] ?? 'encounter';
            $date_time_display = date('Y-m-d H:i', strtotime(substr($datetime, 0, 16)));
            $enc = $bundle['encounter'] ?? [];
            $vs  = $bundle['vitals'] ?? [];
            $data = $bundle['data'] ?? [];
        ?>
        
        <div class="card" style="margin-bottom:25px; border-left:5px solid <?php echo $type === 'lab' ? '#28a745' : '#007bff'; // för labb flikarna?>; padding:15px;">
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #dee2e6; padding-bottom:8px;">
                <h4 style="margin:0;">
                    <?php echo $type === 'lab' ? ($t['lab_test'] ?? 'Labb') : ($t['encounter_on'] ?? 'Besök den / Visited on'); ?>:
                    <?php echo htmlspecialchars($date_time_display); ?>
                </h4>
                
                <span style="font-size:0.9em; color:#6c757d;">
                    <?php echo $t['practitioner_name']; ?>:
                    <?php echo htmlspecialchars($bundle['staff'] ?? $enc['practitioner_name'] ?? 'N/A'); ?>
                </span>
            </div>
            
            <details style="margin-top: 10px;">
                <summary style="cursor: pointer; color: <?php echo $type === 'lab' ? '#28a745' : '#007bff'; ?>; font-weight: bold; outline: none; margin-bottom: 10px;">
                    <?php echo $t['read_more'] ?? 'Visa detaljer'; ?>
                </summary>

                <?php if ($type === 'lab'): // tar fram alla labbsvar?>
                    <div style="overflow-x:auto;">
                        <table style="width:100%; border-collapse: collapse; font-size:0.9em; margin-bottom:10px;">
                            <thead>
                                <tr style="background:#f8f9fa; text-align:left;">
                                    <th><?php echo htmlspecialchars($t['test'] ?? $t['lab_test_name'] ?? 'Test'); ?></th>
                                    <th><?php echo htmlspecialchars($t['result'] ?? $t['result_value'] ?? 'Resultat'); ?></th>
                                    <th><?php echo htmlspecialchars($t['normal_range'] ?? $t['reference'] ?? 'Normalintervall'); ?></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $lab_items = $data['normal_test_items'] ?? $data['descriptive_test_items'] ?? [];
                                foreach ($lab_items as $item): ?>
                                <tr>
                                    <td style="font-weight:bold;"><?php echo htmlspecialchars($item['lab_test_name'] ?? $item['test_parameter'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($item['result_value'] ?? $item['result'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($item['normal_range'] ?? '-'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <?php else: // tar fram alla besök ?>
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
                    </div>
                    
                    <div style="margin-top:15px; border-top:1px dashed #ced4da; padding-top:10px;">
                        <h5><?php echo $t['notes']; ?>:</h5>
                        <div style="background:#f8f9fa; border:1px solid #e9ecef; padding:10px; border-radius:4px;">
                            <?php echo nl2br(htmlspecialchars($vs['vital_signs_note'] ?? $enc['notes'] ?? 'Ingen anteckning.')); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </details>
        </div>
        <?php endforeach; ?>

    <?php else: ?>
        <div class="alert alert-info">
            <?php echo $t['no_records']; ?>
        </div>
    <?php endif; ?>
</div>