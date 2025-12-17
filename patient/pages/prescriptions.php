<?php 
// prescriptions.php

$message = '';
$success_message = null; 
$error_message = null;   

// 1. Hantera Framgångsmeddelanden (Flash Message)
if (isset($_SESSION['success_message'])) {
    $success_message = htmlspecialchars($_SESSION['success_message']);
    unset($_SESSION['success_message']);
}

// 2. Hantera Felmeddelanden
if (isset($_SESSION['error_message'])) {
    $error_message = htmlspecialchars($_SESSION['error_message']);
    unset($_SESSION['error_message']);
}

$prescriptions = $erp_client->getPrescriptionsForPatient($patient_erp_id);
?>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success" role="alert" style="margin-top: 20px;">
         <?php echo $success_message; ?>
    </div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-danger" role="alert" style="margin-top: 20px;">
         <?php echo $error_message; ?>
    </div>
<?php endif; ?>


<div class="card" style="margin-top: 20px;">
    <h3><?php echo $t['prescriptions']; ?></h3>

    <?php if (!empty($prescriptions)): ?>
        <table class="table-striped">
            <thead>
                <tr>
                    <th><?php echo $t['medication']; ?></th>
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
                        // Originalstatus från ERP (ofta svenska)
                        $status_raw = strtolower(trim($prescription['data_rsjo'] ?? ''));

                        // Default värden
                        $status_text = $status_raw; // Fallback till originaltext
                        $bg = '#e2e3e5'; // grå default
                        $txt = '#000';   // svart text

                        // Mappa status till språk och färg
                        if ($status_raw === 'godkänd' || $status_raw === 'approved') {
                            $status_text = $t['status_approved'];
                            $bg = '#d4edda'; // grön bakgrund
                        } elseif ($status_raw === 'behandlas' || $status_raw === 'processing') {
                            $status_text = $t['status_processing'];
                            $bg = '#fff3cd'; // gul bakgrund
                        } elseif ($status_raw === 'ej godkänd' || $status_raw === 'not approved') {
                            $status_text = $t['status_denied'];
                            $bg = '#f8d7da'; // röd bakgrund
                        }
                    ?>

                    <tr>
                        <td><strong><?php echo htmlspecialchars($prescription['medicin_names'] ?? 'N/A'); ?></strong></td>
                        <td><?php echo htmlspecialchars($prescription['vårdgivare_namn'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($prescription['datum'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($prescription['uttag'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($prescription['expiration_date'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($prescription['strenght'] ?? 'N/A'); ?></td>

                        <td>
                            <span style="
                                background-color: <?php echo $bg; ?>;
                                color: <?php echo $txt; ?>;
                                padding: 6px 12px;
                                border-radius: 20px;
                                font-weight: bold;
                                font-size: 0.9rem;
                                display: inline-block;">
                                <?php echo htmlspecialchars($status_text); ?>
                            </span>
                        </td>

                        <td> 
                            <form method="post" action="/wwwit-utv/Grupp4/patient/pages/renewPrescription.php">
                                <input type="hidden" name="prescription_id" value="<?php echo htmlspecialchars($prescription['name']); ?>">
                                <button type="submit" class="btn btn-primary">
                                    <?php echo $t['renew_prescription']; ?>
                                </button>
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