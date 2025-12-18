<?php
// 1. Hämta vanliga bokningar
$appointments = $erp_client->getAppointmentsForPatient($patient_erp_id);

// 2. Hämta ombokningsförfrågningar
$my_pnr = $_SESSION['personal_number'] ?? ''; 
if (empty($my_pnr)) {
    $my_pnr = $_SESSION['patient_id']; 
}
$requests = $erp_client->getRescheduleRequests($my_pnr);
?>


<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?php 
            echo htmlspecialchars($_SESSION['success_message']);
            unset($_SESSION['success_message']);
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?php 
            echo htmlspecialchars($_SESSION['error_message']);
            unset($_SESSION['error_message']);
        ?>
    </div>
<?php endif; ?>


<?php if (!empty($requests)): ?>
<div class="card" style="margin-top: 20px; margin-bottom: 20px; border-left: 5px solid #ffc107;">
    <h3><?php echo $t['my_reschedule_cases']; ?></h3>
    <table class="table-striped">
        <thead>
            <tr>
                <th><?php echo $t['date_sent']; ?></th>
                <th><?php echo $t['desired_day']; ?></th>
                <th><?php echo $t['department']; ?></th>
                <th><?php echo $t['status']; ?></th>
                <th><?php echo $t['reply_from_provider']; ?></th> 
            </tr>
        </thead>
<tbody>
            <?php foreach ($requests as $req): ?>
            <tr>
                <td><?php echo date('Y-m-d', strtotime($req['creation'])); ?></td>
                <td><?php echo htmlspecialchars($req['preferred_day']); ?></td>
                <td><?php echo htmlspecialchars($req['department']); ?></td>
                <td>
                    <?php 
                        $status_raw = $req['status'] ?? 'Mottagen'; 
                        
                        // Översätt status för visning
                        $status_display = $status_raw;
                        if ($status_raw === 'Mottagen') $status_display = $t['status_received'];
                        if ($status_raw === 'Åtgärdad') $status_display = $t['status_resolved'];
                        if ($status_raw === 'Nekad')    $status_display = $t['status_denied'];

                        $bg = '#fff3cd'; // bakgrund default
                        $txt = '#141313ff'; // text default

                        if ($status_raw === 'Åtgärdad') {
                            $bg = '#d4edda'; 
                            $txt = '#1d1b1bff';
                        } elseif ($status_raw === 'Nekad') {
                            $bg = '#f8d7da'; 
                            $txt = '#222020ff';
                        }
                    ?>
                    <span style="
                        background-color: <?php echo $bg; ?>; 
                        color: <?php echo $txt; ?>;
                        padding: 6px 12px; 
                        border-radius: 20px; 
                        font-weight: bold; 
                        font-size: 0.9rem;
                        display: inline-block;">
                        <?php echo htmlspecialchars($status_display); ?>
                    </span>
                </td>
                
                <td>
                    <?php echo !empty($req['svar']) ? htmlspecialchars($req['svar']) : '-'; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>


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
                    <th><?php echo $t['reschedule_booking']; ?></th>
                </tr>
            </thead>
            <tbody>
<?php foreach ($appointments as $app): 
                    // Hämta datum och tid
                    $date = !empty($app['appointment_date']) ? date('Y-m-d', strtotime($app['appointment_date'])) : 'N/A';
                    $time = $app['appointment_time'] ?? '00:00'; 
                    
                    $practitioner = $app['practitioner_name'] ?? 'N/A';
                    $title = $app['title'] ?? 'N/A';
                    $patient_name = $app['patient'] ?? 'N/A';
                    
                    // Slå ihop datum och tid till en tidsstämpel
                    $appointment_timestamp = strtotime("$date $time");
                    $current_timestamp = time();
                    
                    // Räkna ut skillnaden i timmar
                    $hours_until_appointment = ($appointment_timestamp - $current_timestamp) / 3600;
                    
                    // Kontrollera om det är mindre än 24h kvar
                    $is_too_late = ($hours_until_appointment < 24);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($date); ?></td>
                    <td><?php echo htmlspecialchars($time); ?></td>
                    <td><?php echo htmlspecialchars($practitioner); ?></td>
                    <td><?php echo htmlspecialchars($title); ?></td>
                    <td><?php echo htmlspecialchars($patient_name); ?></td>
                    
                    <td> 
                        <?php if ($is_too_late): ?>
                            <button type="button" 
                                    style="background-color: #ccc; cursor: not-allowed; border:1px solid #999;"
                                    onclick="alert('<?php echo $t['alert_too_late_cancel']; ?>')">
                                <?php echo $t['cancel_booking']; ?>
                            </button>
                        <?php else: ?>
                            <form method="post" action="pages/Cancel.php">
                                <input type="hidden" name="name" value="<?php echo htmlspecialchars($app['name']); ?>">
                               <button
                                     type="submit"
                                     name="action"
                                     value="cancel"
                                     title="Avbokningsregel"
                                     onclick="return confirm('<?php echo $t['confirm_cancel']; ?>')">
                                    <?php echo $t['cancel_booking']; ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ($is_too_late): ?>
                            <button type="button" 
                                    style="background-color: #ccc; cursor: not-allowed; border:1px solid #999;"
                                    onclick="alert('<?php echo $t['alert_too_late_reschedule']; ?>')">
                                <?php echo $t['reschedule_booking']; ?>
                            </button>
                        <?php else: ?>
                            <form method="post" action="pages/ombokning.php"> 
                                <input type="hidden" name="appointment_name" value="<?php echo htmlspecialchars($app['name']); ?>">
                                <button type="submit" name="action" value="reschedule"><?php echo $t['reschedule_booking']; ?></button>
                            </form>
                        <?php endif; ?>
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


<?php
$fel = "";
$ok  = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Skicka till ERP (DocType: G4KontaktForm)
    $result = $erp_client->submitG4KontaktForm($_POST);

    if (!empty($result['success'])) {
        $ok = $t['contact_form_header'] . " skickat!";
    } else {
        // Visa fel från ERP om det finns, annars fallback
        $fel = $result['message'] ?? ($t['err_empty_pnr'] ?? 'Ett fel uppstod.');
    }
}
?>

<div class="hejhej" style="margin-top:20px;">
    <h3><?php echo $t['contact_form_header']; ?></h3>
    <?php if ($fel) echo "<p style='color:red;'>$fel</p>"; ?>

    <form method="post">

    <div style="margin-bottom: 20px;">
        <label style="display:block; font-weight:bold;"><?php echo $t['pnr_req_label']; ?></label>
        <input type="text" name="personnummer" required pattern="[0-9]{12}" style="padding: 5px; width: 100%; max-width: 300px;">
    </div>

    <div style="margin-bottom: 15px;">
        <label style="display:block; font-weight:bold;"><?php echo $t['fever_q']; ?></label>
        <label style="margin-right: 15px;"><input type="radio" name="feber" value="ja" required> <?php echo $t['yes']; ?></label>
        <label><input type="radio" name="feber" value="nej"> <?php echo $t['no']; ?></label>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="display:block; font-weight:bold;"><?php echo $t['cough_q']; ?></label>
        <label style="margin-right: 15px;"><input type="radio" name="hosta" value="ja" required> <?php echo $t['yes']; ?></label>
        <label><input type="radio" name="hosta" value="nej"> <?php echo $t['no']; ?></label>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="display:block; font-weight:bold;"><?php echo $t['cough_blood_q']; ?></label>
        <label style="margin-right: 15px;"><input type="radio" name="blod" value="ja" required> <?php echo $t['yes']; ?></label>
        <label><input type="radio" name="blod" value="nej"> <?php echo $t['no']; ?></label>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="display:block; font-weight:bold;"><?php echo $t['heavy_breath_q']; ?></label>
        <label style="margin-right: 15px;"><input type="radio" name="andas" value="ja" required> <?php echo $t['yes']; ?></label>
        <label><input type="radio" name="andas" value="nej"> <?php echo $t['no']; ?></label>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="display:block; font-weight:bold;"><?php echo $t['muscle_pain_q']; ?></label>
        <label style="margin-right: 15px;"><input type="radio" name="smarta" value="ja" required> <?php echo $t['yes']; ?></label>
        <label><input type="radio" name="smarta" value="nej"> <?php echo $t['no']; ?></label>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="display:block; font-weight:bold;"><?php echo $t['sick_long_q']; ?></label>
        <label style="margin-right: 15px;"><input type="radio" name="sjuk" value="ja" required> <?php echo $t['yes']; ?></label>
        <label><input type="radio" name="sjuk" value="nej"> <?php echo $t['no']; ?></label>
    </div>

    <div style="margin-bottom: 30px;">
        <label style="display:block; font-weight:bold;"><?php echo $t['describe_symptom_label']; ?></label>
        <textarea name="symptom" rows="4" style="width: 100%; max-width: 500px;"></textarea>
    </div>

    <hr style="border: 1px solid #007bff; margin: 20px 0;">

    <h3><?php echo $t['contact_curator_header']; ?></h3>

    <div style="margin-bottom: 15px;">
        <label style="display:block; font-weight:bold;"><?php echo $t['feeling_down_q']; ?></label>
        <label style="margin-right: 15px;"><input type="radio" name="nedstamd" value="ja" required> <?php echo $t['yes']; ?></label>
        <label><input type="radio" name="nedstamd" value="nej"> <?php echo $t['no']; ?></label>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="display:block; font-weight:bold;"><?php echo $t['anxiety_q']; ?></label>
        <label style="margin-right: 15px;"><input type="radio" name="angest" value="ja" required> <?php echo $t['yes']; ?></label>
        <label><input type="radio" name="angest" value="nej"> <?php echo $t['no']; ?></label>
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display:block; font-weight:bold;"><?php echo $t['describe_symptom_label']; ?></label>
        <textarea name="kurator_symptom" rows="4" style="width: 100%; max-width: 500px;"></textarea>
    </div>

    <button type="submit" style="padding: 10px 20px; font-size: 16px; cursor: pointer; background-color: #007bff; color: white; border: none; border-radius: 4px;">
        <?php echo $t['send']; ?>
    </button>

    </form>
</div>