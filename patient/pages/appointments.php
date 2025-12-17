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
    <h3>Mina pågående ombokningsärenden</h3>
    <table class="table-striped">
        <thead>
            <tr>
                <th>Datum skickad</th>
                <th>Önskad dag</th>
                <th>Avdelning</th>
                <th>Status</th>
                <th>Svar från vårdgivare</th> </tr>
        </thead>
<tbody>
            <?php foreach ($requests as $req): ?>
            <tr>
                <td><?php echo date('Y-m-d', strtotime($req['creation'])); ?></td>
                <td><?php echo htmlspecialchars($req['preferred_day']); ?></td>
                <td><?php echo htmlspecialchars($req['department']); ?></td>
                <td>
                    <?php 
                        $status = $req['status'] ?? 'Mottagen'; 
                        
                        $bg = '#fff3cd'; // bakgrund
                        $txt = '#141313ff'; // text

                        if ($status === 'Åtgärdad') {
                            $bg = '#d4edda'; //  bakgrund
                            $txt = '#1d1b1bff'; //  text
                        } elseif ($status === 'Nekad') {
                            $bg = '#f8d7da'; //  bakgrund
                            $txt = '#222020ff'; //  text
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
                        <?php echo htmlspecialchars($status); ?>
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
                    <th><?php echo $t['reschedule_booking']?></th>
                </tr>
            </thead>
            <tbody>
<?php foreach ($appointments as $app): 
                    // Hämta datum och tid
                    $date = !empty($app['appointment_date']) ? date('Y-m-d', strtotime($app['appointment_date'])) : 'N/A';
                    $time = $app['appointment_time'] ?? '00:00'; // Sätt standardtid om den saknas
                    
                    $practitioner = $app['practitioner_name'] ?? 'N/A';
                    $title = $app['title'] ?? 'N/A';
                    $patient_name = $app['patient'] ?? 'N/A';
                    
                    // Slå ihop datum och tid till en tidsstämpel
                    $appointment_timestamp = strtotime("$date $time");
                    $current_timestamp = time();
                    
                    // Räkna ut skillnaden i timmar
                    $hours_until_appointment = ($appointment_timestamp - $current_timestamp) / 3600;
                    
                    // Kontrollera om det är mindre än 24h kvar (och att tiden inte redan passerat)
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
                                    onclick="alert('Det är mindre än 24 timmar kvar till din bokning. För att avboka måste du kontakta mottagningen via telefon.')">
                                Avboka
                            </button>
                        <?php else: ?>
                            <form method="post" action="pages/Cancel.php">
                                <input type="hidden" name="name" value="<?php echo htmlspecialchars($app['name']); ?>">
                               <button
                                     type="submit"
                                     name="action"
                                     value="cancel"
                                     title="Om avbokning sker inom 24h innan möte, debiteras du för tiden"
                                     onclick="return confirm('Är du säker på att du vill avboka?')">
                                    Avboka
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ($is_too_late): ?>
                            <button type="button" 
                                    style="background-color: #ccc; cursor: not-allowed; border:1px solid #999;"
                                    onclick="alert('Ombokning ej tillåten.\n\nDet är mindre än 24 timmar kvar till din tid. Vänligen kontakta mottagningen om det är akut.')">
                                Boka om
                            </button>
                        <?php else: ?>
                            <form method="post" action="pages/ombokning.php"> 
                                <input type="hidden" name="appointment_name" value="<?php echo htmlspecialchars($app['name']); ?>">
                                <button type="submit" name="action" value="reschedule">Boka om</button>
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $personnummer = $_POST["personnummer"] ?? "";

    // Kontroll: personnummer obligatoriskt och exakt 12 siffror
    if (!preg_match("/^[0-9]{12}$/", $personnummer)) {
        $fel = "Personnummer måste vara 12 siffror.";
    } else {
        echo "<p>Formuläret är skickat.</p>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
<meta charset="UTF-8">
<title>Formulär</title>
<style>
    body { font-family: Arial; }
    label { display: block; margin-top: 10px; }
</style>
</head>
<body>

<?php if ($fel) echo "<p style='color:red;'>$fel</p>"; ?>

<form method="post">

<label>Personnummer (12 siffror) *</label>
<input type="text" name="personnummer" required pattern="[0-9]{12}">

<label>Har du haft feber mer än sju dagar?</label>
<input type="radio" name="feber" value="ja" required> Ja
<input type="radio" name="feber" value="nej"> Nej

<label>Har du hosta?</label>
<input type="radio" name="hosta" value="ja" required> Ja
<input type="radio" name="hosta" value="nej"> Nej

<label>Hostar du blod?</label>
<input type="radio" name="blod" value="ja" required> Ja
<input type="radio" name="blod" value="nej"> Nej

<label>Känns det tungt att andas?</label>
<input type="radio" name="andas" value="ja" required> Ja
<input type="radio" name="andas" value="nej"> Nej

<label>Har du muskelsmärta eller huvudvärk?</label>
<input type="radio" name="smarta" value="ja" required> Ja
<input type="radio" name="smarta" value="nej"> Nej

<label>Har du varit sjuk mer än 7 dagar?</label>
<input type="radio" name="sjuk" value="ja" required> Ja
<input type="radio" name="sjuk" value="nej"> Nej

<label>Beskriv dina symptom (max 150 ord)</label>
<textarea name="symptom" rows="4"></textarea>

<h3>Kontakta kurator</h3>

<label>Känner du dig nedstämd?</label>
<input type="radio" name="nedstamd" value="ja" required> Ja
<input type="radio" name="nedstamd" value="nej"> Nej

<label>Känner du ångest och oro?</label>
<input type="radio" name="angest" value="ja" required> Ja
<input type="radio" name="angest" value="nej"> Nej

<label>Beskriv symptom (max 150 ord)</label>
<textarea name="kurator_symptom" rows="4"></textarea>

<br><br>
<button type="submit">Skicka</button>

</form>

</body>
</html>
