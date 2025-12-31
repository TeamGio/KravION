<?php
$encounters = $erp_client->getJournalRecordsForPatient($patient_erp_id);
$vitals     = $erp_client->getVitalSignsForPatient($patient_erp_id);
$med_recs   = $erp_client->getMedicalrecords($patient_erp_id);
$records = [];

// hämtar personal
$practitioner_by_id = [];
$practitioner_by_date = [];
foreach ($encounters as $enc) {
    $name = $enc['practitioner_name'] ?? $enc['practitioner'] ?? null;
    if ($name) {
        if (!empty($enc['name'])) $practitioner_by_id[$enc['name']] = $name;
        if (!empty($enc['encounter_date'])) $practitioner_by_date[$enc['encounter_date']] = $name;
    }
}

foreach ($vitals as $vs) { // löser vital signs & kopplad personal
    $date = $vs['signs_date'] ?? '0000-00-00';
    $key = $date . ' ' . ($vs['signs_time'] ?? '00:00:00') . '_' . $vs['name'];
    
    $staff_name = $vs['practitioner_name'] ?? null;
    if (!$staff_name && !empty($vs['encounter'])) {
        $staff_name = $practitioner_by_id[$vs['encounter']] ?? null;
    }
    if (!$staff_name) {
        $staff_name = $practitioner_by_date[$date] ?? 'Ej angivet';
    }
    
    $records[$key] = ['type' => 'vitals', 'data' => $vs, 'staff' => $staff_name];
}

foreach ($med_recs as $mr) { // löser lab text
    if (($mr['reference_doctype'] ?? '') === 'Lab Test') {
        $lab_data = $erp_client->getDoc($mr['reference_doctype'], $mr['reference_name']);
        if ($lab_data) {
            $date = $lab_data['result_date'] ?? date('Y-m-d', strtotime($lab_data['creation']));
            $key = $date . ' ' . ($lab_data['result_time'] ?? '00:00:00') . '_' . $lab_data['name'];
            
            $l_staff = $lab_data['practitioner_name'] ?? $lab_data['employee_name'] ?? 'Laboratoriet';

            $records[$key] = ['type' => 'lab', 'data' => $lab_data, 'staff' => $l_staff];
        }
    }
}

krsort($records);
?>

<div class="card">
    <h3 style="margin-bottom:20px;"><?php echo $t['medical_journal']; ?></h3>
    
    <?php if (!empty($records)): ?>
        <?php foreach ($records as $key => $bundle): ?>
        <?php
            $datetime = explode('_', $key)[0];
            $date_time_display = date('Y-m-d H:i', strtotime($datetime));
            $type = $bundle['type'];
            $data = $bundle['data'];
            $staff = $bundle['staff'];
        ?>
        
        <details style="margin-bottom:15px; border:1px solid #e0e0e0; border-radius:8px; background:white; overflow:hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <summary style="padding:15px; cursor:pointer; background-color:#f8f9fa; display:flex; justify-content:space-between; align-items:center; list-style:none;">
                <div style="display:flex; align-items:center; gap:15px;">
                    <span style="background:#007bff; color:white; padding:5px 10px; border-radius:4px; font-weight:bold; font-size:0.9em;">
                        <?php echo htmlspecialchars($date_time_display); ?>
                    </span>
                    
                    <div style="display: flex; flex-direction: column;">
                        <strong style="color:#333; font-size:1.1em;">
                            <?php echo ($type === 'lab') ? "Labbsvar" : "Vitaltecken"; ?>
                        </strong>
                        <span style="font-size:0.85em; color:#666;">
                            Ansvarig: <?php echo htmlspecialchars($staff); ?>
                        </span>
                    </div>
                </div>
                <span style="color:black; font-size:0.9em;"> ▼ <?php echo $t['show_details'] ?? 'Visa mer'; ?></span>
            </summary>

            <div style="padding:20px; border-top:1px solid #eee; background:#fff;">
                
                <?php if ($type === 'lab'): ?>
                    <table style="width:100%; border-collapse: collapse; font-size:0.9em;">
                        <thead>
                            <tr>
                                <th>Test</th>
                                <th>Resultat</th>
                                <th>Enhet</th>
                                <th>Normalintervall</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $lab_items = $data['normal_test_items'] ?? $data['descriptive_test_items'] ?? [];
                            foreach ($lab_items as $item): ?>
                            <tr>
                                <td style="font-weight:bold;"><?php echo htmlspecialchars($item['lab_test_name'] ?? $item['test_parameter'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($item['result_value'] ?? $item['result'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($item['uom'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($item['normal_range'] ?? '-'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                <?php elseif ($type === 'vitals'): ?>
                    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:20px; font-size:0.9em; margin-bottom:20px;">
                        <div style="background:#f1faff; padding:10px; border-radius:5px;">
                            <p><strong><?php echo $t['height']; ?> (m):</strong> <?php echo htmlspecialchars($data['height'] ?? 'N/A'); ?></p>
                            <p><strong><?php echo $t['weight']; ?> (kg):</strong> <?php echo htmlspecialchars($data['weight'] ?? 'N/A'); ?></p>
                            <p><strong>BMI:</strong> <?php echo htmlspecialchars($data['bmi'] ?? 'N/A'); ?></p>
                        </div>
                        <div style="background:#f1faff; padding:10px; border-radius:5px;">
                            <p><strong><?php echo $t['temperature']; ?>:</strong> <?php echo htmlspecialchars($data['temperature'] ?? 'N/A'); ?></p>
                            <p><strong><?php echo $t['pulse']; ?>:</strong> <?php echo htmlspecialchars($data['pulse'] ?? 'N/A'); ?></p>
                            <p><strong><?php echo $t['respiratory rate']; ?>:</strong> <?php echo htmlspecialchars($data['respiratory_rate'] ?? 'N/A'); ?></p>
                        </div>
                    </div>

                    <div style="border-top:1px dashed #ced4da; padding-top:15px;">
                        <h5 style="margin:0 0 5px 0;"><?php echo $t['notes']; ?>:</h5>
                        <div style="background:#f8f9fa; border:1px solid #e9ecef; padding:12px; border-radius:4px; font-style:italic; color:#444;">
                            <?php echo htmlspecialchars($data['vital_signs_note'] ?? 'Ingen anteckning.'); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </details>
        
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info"><?php echo $t['no_records']; ?></div>
    <?php endif; ?>
</div>