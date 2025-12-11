<?php 
// prescriptions.php

$message = '';
// VIKTIGT: Vi tar bort den URL-baserade rensningslogiken.
// session_start() antas köras i föräldern (dashboard.php)
$success_message = null; 
$error_message = null;   

// --- NY LOGIK FÖR ATT VISA SESSION-BASERAT MEDDELANDE ---

// 1. Hantera Framgångsmeddelanden (Flash Message)
if (isset($_SESSION['success_message'])) {
    $success_message = htmlspecialchars($_SESSION['success_message']);
    // Rensa sessionen direkt efter att ha lagrat meddelandet lokalt
    unset($_SESSION['success_message']);
}

// 2. Hantera Felmeddelanden (Om renewPrescription skickade ett fel)
if (isset($_SESSION['error_message'])) {
    $error_message = htmlspecialchars($_SESSION['error_message']);
    // Rensa sessionen
    unset($_SESSION['error_message']);
}
// -----------------------------------------------------------


$prescriptions = $erp_client->getPrescriptionsForPatient($patient_erp_id);
?>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success" role="alert" style="margin-top: 20px;">
         Klart! <?php echo $success_message; ?>
    </div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-danger" role="alert" style="margin-top: 20px;">
         Fel! <?php echo $error_message; ?>
    </div>
<?php endif; ?>


<div class="card" style="margin-top: 20px;">
    <h3><?php echo $t['prescriptions']; ?></h3>

    <?php if (!empty($prescriptions)): ?>
        <table class="table-striped">
            <thead>
                <tr>
                    <th><?php echo $t['medication']; ?></th>
                    <th><?php echo $t['personnummer']; ?></th>
                    <th><?php echo $t['practitioner_name']; ?></th>
                    <th><?php echo $t['date']; ?></th>
                    <th><?php echo $t['withdrawal']; ?></th>
                    <th><?php echo $t['expiration_date']; ?></th>
                    <th><?php echo $t['strength']; ?></th>
                    <th><?php echo $t['status']; ?></th>
                    <th><?php echo $t['renew_prescription']; ?></th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($prescriptions as $prescription): ?>
                    <?php 
                        // Originalstatus från ERP
                        $status = $prescription['data_rsjo'] ?? 'Ej satt';
                        $raw_status = strtolower(trim($status));

                        // Bestäm badge-färg baserat på originalstatus
                        // Lägg till 'behandlas' till villkoren för badge-färg om du vill ha en annan färg för pågående.
                        $is_approved = ($raw_status === 'godkänd');
                        $badge_class = $is_approved ? 'badge-success' : 'badge-danger';

                        // Översätt status vid engelska
                        if ($lang === 'en') {
                            if ($raw_status === 'godkänd') {
                                $status = 'Approved';
                            } elseif ($raw_status === 'ej godkänd') {
                                $status = 'Not approved';
                            } elseif ($raw_status === 'behandlas') {
                                $status = 'Processing';
                                // Uppdatera badge-klassen för 'behandlas'
                                $badge_class = 'badge-warning'; 
                            }
                        }
                    ?>

                    <tr>
                        <td><strong><?php echo htmlspecialchars($prescription['medicin'] ?? 'N/A'); ?></strong></td>
                        <td><?php echo htmlspecialchars($prescription['personnummer'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($prescription['vårdgivare_namn'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($prescription['datum'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($prescription['uttag'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($prescription['expiration_date'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($prescription['strenght'] ?? 'N/A'); ?></td>

                        <td>
                            <span class="badge <?php echo $badge_class; ?>">
                                <?php echo htmlspecialchars($status); ?>
                            </span>
                        </td>

                        <td> 
                            <form method="post" action="/wwwit-utv/Grupp%204/patient/pages/renewPrescription.php">
                            <input type="hidden" name="prescription_id" value="<?php echo htmlspecialchars($prescription['name']); ?>">
                            <button type="submit">Förnya</button>
                            </form>
                        </td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <div class="alert alert-info">
            <?php echo $t['no_prescriptions']; ?>
        </div>
    <?php endif; ?>
</div>