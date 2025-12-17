<?php
// Hämta data från ERPNext
$encounters = $erp_client->getJournalRecordsForPatient($patient_erp_id);
$vitals     = $erp_client->getVitalSignsForPatient($patient_erp_id);


$records = [];

// Slå ihop Encounters (Besök) baserat på tid
foreach ($encounters as $enc) {
    // Skapa en unik nyckel av datum + tid
    $key = ($enc['encounter_date'] ?? '') . ' ' . ($enc['encounter_time'] ?? '');
    $records[$key]['encounter'] = $enc;
}

// Slå ihop Vitals (Värden) baserat på tid
foreach ($vitals as $vs) {
    $key = ($vs['signs_date'] ?? '') . ' ' . ($vs['signs_time'] ?? '');
    $records[$key]['vitals'] = $vs;
}

// Sortera så att senaste besöket hamnar överst
krsort($records);
?>

<div class="card">
    <h3><?php echo $t['medical_journal']; ?></h3>
    <p style="color:#666; font-size:0.9em; margin-bottom:20px;">
        Klicka på en rad i listan för att läsa detaljerna om besöket.
    </p>

    <?php if (!empty($records)): ?>
        <?php foreach ($records as $datetime => $bundle): ?>
            <?php
                // Formatera datum och tid för rubriken
                $date_display = date('Y-m-d', strtotime($datetime));
                $time_display = date('H:i', strtotime($datetime));
                
                $enc = $bundle['encounter'] ?? [];
                $vs  = $bundle['vitals'] ?? [];

                // --- NY LOGIK: KOMPLETTERA DATA ---
                // Hämta läkare från Encounter först, annars försök med Vitals
                $practitioner = $enc['practitioner_name'] ?? $vs['practitioner_name'] ?? '';

                // Hämta status från Encounter först, annars Vitals
                $status = $enc['status'] ?? $vs['status'] ?? ''; 
                // ----------------------------------

                // 1. Kolla om vi har några mätvärden alls att visa
                $has_vitals = !empty($vs['height']) || !empty($vs['weight']) || !empty($vs['bmi']) || 
                              !empty($vs['temperature']) || !empty($vs['pulse']) || !empty($vs['bp_systolic']);
                
                // 2. Kolla om vi har några anteckningar att visa
                $has_notes = !empty($enc['custom_symtom']) || !empty($enc['notes']) || !empty($vs['vital_signs_note']);
            ?>

            <details style="margin-bottom:15px; background: white; border:1px solid #e0e0e0; border-radius:8px; overflow:hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                
                <summary style="padding:15px; cursor:pointer; background-color:#f8f9fa; display:flex; justify-content:space-between; align-items:center; outline:none; user-select: none;">
                    <div style="font-weight:600; color:#333;">
                        <span style="color:#007bff; margin-right:10px; font-size: 1.1em;">
                             <?php echo $date_display; ?>
                        </span>
                        <span style="color:#666; font-weight:normal; font-size:0.9em;">
                            (kl <?php echo $time_display; ?>)
                        </span>
                        
                        <?php if(!empty($practitioner)): ?>
                           <span style="margin-left:15px; font-weight:normal; color:#555; display:inline-block;">
                                <?php echo htmlspecialchars($practitioner); ?>
                           </span>
                        <?php endif; ?>
                    </div>
                    
                    <div style="display:flex; align-items:center;">
                        <?php if ($status): ?>
                            <span style="background:#e9ecef; color:#495057; padding:4px 10px; border-radius:15px; font-size:0.75em; text-transform:uppercase; letter-spacing:0.5px; margin-right:15px; font-weight:bold;">
                                <?php echo htmlspecialchars($status); ?>
                            </span>
                        <?php endif; ?>
                        <span style="font-size:0.8em; color:#999;">▼ Läs mer</span>
                    </div>
                </summary>

                <div style="padding:25px; border-top:1px solid #e0e0e0; background-color: #fff;">
                    
                    <?php if ($has_vitals): ?>
                        <h5 style="border-bottom:2px solid #f1f8ff; padding-bottom:10px; margin-bottom:15px; color:#0056b3;">
                            Mätvärden & Status
                        </h5>
                        
                        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap:15px; margin-bottom:25px;">
                            <?php 
                                function printVitalBox($label, $value, $unit = '') {
                                    if ($value === null || $value === '') return; 
                                    echo "
                                    <div style='background:#f4f9ff; padding:12px; border-radius:8px; border:1px solid #e1ecf7; text-align:center;'>
                                        <div style='font-size:0.7em; color:#6c757d; text-transform:uppercase; letter-spacing:1px; margin-bottom:5px;'>$label</div>
                                        <div style='font-size:1.2em; font-weight:bold; color:#2c3e50;'>
                                            ".htmlspecialchars($value)." <span style='font-size:0.6em; color:#888; vertical-align:middle;'>$unit</span>
                                        </div>
                                    </div>";
                                }

                                printVitalBox($t['height'] ?? 'Längd', $vs['height'] ?? '', 'm');
                                printVitalBox($t['weight'] ?? 'Vikt', $vs['weight'] ?? '', 'kg');
                                printVitalBox('BMI', $vs['bmi'] ?? '');
                                printVitalBox($t['temperature'] ?? 'Temp', $vs['temperature'] ?? '', '°C');
                                printVitalBox($t['pulse'] ?? 'Puls', $vs['pulse'] ?? '', 'slag/min');
                                printVitalBox($t['respiratory rate'] ?? 'Andning', $vs['respiratory_rate'] ?? '', '/min');
                                
                                if (!empty($vs['bp_systolic']) && !empty($vs['bp_diastolic'])) {
                                    printVitalBox('Blodtryck', $vs['bp_systolic'].'/'.$vs['bp_diastolic'], 'mmHg');
                                }
                            ?>
                        </div>
                        
                        <?php if(!empty($vs['tongue']) || !empty($vs['abdomen']) || !empty($vs['reflexes'])): ?>
                            <div style="background:#fff3cd; color:#856404; padding:15px; border-radius:6px; margin-bottom:20px; font-size:0.95em; border:1px solid #ffeeba;">
                                <strong> Observationer:</strong><br>
                                <ul style="margin:5px 0 0 20px; padding:0;">
                                    <?php if(!empty($vs['tongue'])) echo "<li><strong>Tunga:</strong> " . htmlspecialchars($vs['tongue']) . "</li>"; ?>
                                    <?php if(!empty($vs['abdomen'])) echo "<li><strong>Buk:</strong> " . htmlspecialchars($vs['abdomen']) . "</li>"; ?>
                                    <?php if(!empty($vs['reflexes'])) echo "<li><strong>Reflexer:</strong> " . htmlspecialchars($vs['reflexes']) . "</li>"; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>


                    <?php if ($has_notes): ?>
                        <h5 style="border-bottom:2px solid #e9f7ef; padding-bottom:10px; margin-bottom:15px; margin-top:10px; color:#28a745;">
                            Journalanteckning
                        </h5>
                        
                        <?php if (!empty($enc['custom_symtom'])): ?>
                            <div style="margin-bottom:15px;">
                                <strong style="color:#555;"><?php echo $t['symptoms'] ?? 'Sökorsak / Symptom'; ?>:</strong>
                                <div style="font-style:italic; color:#333; margin-top:4px;">
                                    "<?php echo htmlspecialchars($enc['custom_symtom']); ?>"
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php 
                            $note_text = $enc['notes'] ?? $vs['vital_signs_note'] ?? '';
                        ?>
                        <?php if (!empty($note_text)): ?>
                            <div>
                                <strong style="color:#555;"><?php echo $t['notes'] ?? 'Anteckning'; ?>:</strong>
                                <div style="background:#f9f9f9; border-left:4px solid #28a745; padding:15px; margin-top:8px; border-radius:4px; line-height:1.6; white-space: pre-wrap; color:#212529;">
                                    <?php echo htmlspecialchars($note_text); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if (!$has_vitals && !$has_notes): ?>
                        <p style="color:#999; font-style:italic; text-align:center;">
                            Inga detaljer registrerade för detta besök.
                        </p>
                    <?php endif; ?>

                </div>
            </details>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info" style="text-align:center; padding:30px;">
            <h4>Ingen journaldata hittades</h4>
            <p>Det finns inga registrerade besök eller provsvar i din journal ännu.</p>
        </div>
    <?php endif; ?>
</div>