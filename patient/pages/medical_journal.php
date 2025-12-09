<?php
require_once '../config/exempelfil_erp.php';

// Hämta patientens ERP ID
$patient = $erp_client->findPatientByPNR($_SESSION['pnr']);
$patient_erp_id = $patient['name'] ?? null;

$records = [];

if ($patient_erp_id) {
    // 1. Hämta alla Patient Encounters
    $encounters = $erp_client->getData("Patient Encounter", [
        ["patient", "=", $patient_erp_id]
    ], [
        "name","encounter_date","encounter_time","practitioner_name",
        "symptoms","chief_complaint","vital_signs"
    ]);

    foreach ($encounters as $enc) {
        $vs = [];

        // 2. Om encounter har en länkad Vital Signs-post → hämta den
        if (!empty($enc['vital_signs'])) {
            $vs = $erp_client->getDataByName("Vital Signs", $enc['vital_signs'], [
                "temperature","pulse","respiratory_rate","oxygen_saturation",
                "height","weight","bmi","notes","tongue","abdominal_examination","reflexes"
            ]);
        }

        // 3. Bygg upp journalposten
        $records[] = [
            "date" => $enc['encounter_date'] ?? '',
            "time" => $enc['encounter_time'] ?? '',
            "practitioner" => $enc['practitioner_name'] ?? 'Unknown',
            "symptoms" => $enc['chief_complaint'] ?? $enc['symptoms'] ?? 'N/A',

            // Vital signs
            "temperature" => $vs['temperature'] ?? '',
            "pulse" => $vs['pulse'] ?? '',
            "respiratory_rate" => $vs['respiratory_rate'] ?? '',
            "height" => $vs['height'] ?? '',
            "weight" => $vs['weight'] ?? '',
            "bmi" => $vs['bmi'] ?? '',
            "notes" => $vs['notes'] ?? '',
            "tongue" => $vs['tongue'] ?? '',
            "abdomen" => $vs['abdominal_examination'] ?? '',
            "reflexes" => $vs['reflexes'] ?? ''
        ];
    }
}
?>

<div class="card">
    <h3>Journal</h3>
</div>

<div class="card" style="margin-top: 20px;">
<?php if (!empty($records)): ?>
<table class="table-striped">
    <thead>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Practitioner</th>
            <th>Symptoms</th>
            <th>Details</th>
        </tr>
    </thead>

    <tbody>
    <?php foreach ($records as $r): ?>
        <tr>
            <td><strong><?php echo htmlspecialchars($r['date']); ?></strong></td>
            <td><?php echo htmlspecialchars($r['time']); ?></td>
            <td><?php echo htmlspecialchars($r['practitioner']); ?></td>
            <td><?php echo htmlspecialchars($r['symptoms']); ?></td>
            <td>
                <button class="btn btn-primary" onclick="toggleRow('<?php echo $r['date'] . $r['time']; ?>')">
                    View
                </button>
            </td>
        </tr>

        <!-- Hidden detail row -->
        <tr id="<?php echo $r['date'] . $r['time']; ?>" style="display:none;">
            <td colspan="5" style="background:#F8F9FA; padding:15px;">

                <strong>Body Temperature:</strong> <?php echo htmlspecialchars($r['temperature'] ?? ''); ?> °C<br>
                <strong>Heart Rate / Pulse:</strong> <?php echo htmlspecialchars($r['pulse'] ?? ''); ?> bpm<br>
                <strong>Respiratory Rate:</strong> <?php echo htmlspecialchars($r['respiratory_rate'] ?? ''); ?> breaths/min<br>
                <strong>Height:</strong> <?php echo htmlspecialchars($r['height'] ?? ''); ?> m<br>
                <strong>Weight:</strong> <?php echo htmlspecialchars($r['weight'] ?? ''); ?> kg<br>
                <strong>BMI:</strong> <?php echo htmlspecialchars($r['bmi'] ?? ''); ?><br>
                <br>

                <strong>Tongue:</strong> <?php echo htmlspecialchars($r['tongue'] ?? ''); ?><br>
                <strong>Abdomen:</strong> <?php echo htmlspecialchars($r['abdomen'] ?? ''); ?><br>
                <strong>Reflexes:</strong> <?php echo htmlspecialchars($r['reflexes'] ?? ''); ?><br>
                <br>

                <strong>Notes:</strong><br>
                <div style="padding:8px; background:white; border-radius:4px;">
                    <?php echo nl2br(htmlspecialchars($r['notes'] ?? '')); ?>
                </div>

            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>
<div class="alert alert-info" style="padding: 15px; border: 1px solid #d4edda; background-color: #f0fff0; color: #155724; border-radius: 4px;">
    No journal entries found.
</div>
<?php endif; ?>
</div>

<script>
function toggleRow(id) {
    var row = document.getElementById(id);
    row.style.display = row.style.display === "none" ? "table-row" : "none";
}
</script>
