<?php
session_start();
require_once '../../config/exempelfil_erp.php';

$erp_client = new ERPNextClient();

// Måste vara inloggad
if (!isset($_SESSION['patient_id'])) {
    $_SESSION['error_message'] = "Du måste vara inloggad för att boka om en tid.";
    header('Location: ../login.php');
    exit();
}

// Måste finnas ett id
$appointment_id = $_SESSION['reschedule_appointment_id'] ?? null;
if (!$appointment_id) {
    $_SESSION['error_message'] = "Ingen tid vald för ombokning.";
    header('Location: ../dashboard.php?page=appointments');
    exit();
}

$patient_erp_id = $_SESSION['patient_id'];


// === NYTT: hämta och visa vald tid (precis som i appointments.php) ===
$appt_res = $erp_client->getAppointmentById($appointment_id);

if (empty($appt_res['success']) || empty($appt_res['data'])) {
    $_SESSION['error_message'] = $appt_res['message'] ?? "Kunde inte hämta vald tid.";
    header('Location: ../dashboard.php?page=appointments');
    exit();
}

$selected = $appt_res['data'];

// Säkerhet: kontrollera att bokningen tillhör inloggad patient
if (($selected['patient'] ?? null) !== $patient_erp_id) {
    $_SESSION['error_message'] = "Du kan inte omboka en tid som inte tillhör dig.";
    header('Location: ../dashboard.php?page=appointments');
    exit();
}

// Samma tänk som i appointments.php (foreach ($appointments as $app))
$appointments = [$selected];


// När användaren klickar på knappen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_reschedule') {

    $tmp = $erp_client->deleteAppointment($appointment_id);

    if (!empty($tmp['success'])) {
        unset($_SESSION['reschedule_appointment_id']);
        $_SESSION['success_message'] = "Ombokning begärd. Tiden är avbokad.";
        header('Location: ../dashboard.php?page=appointments');
        exit();
    }

    $_SESSION['error_message'] = $tmp['message'] ?? "Kunde inte begära ombokning.";
    header('Location: ../dashboard.php?page=appointments');
    exit();
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Ombokning</title>

</head>
<body>

<div class="card">
    <h2>Ombokning</h2>

    <p>Du är på väg att begära ombokning för din valda tid. 
    Notera att om du ändrar tid 24h innan den bokade tiden kommer du faktureras för ombokningen
    </p>

    
    <table class="table-striped">
        <thead>
            <tr>
                <th>Datum</th>
                <th>Tid</th>
                <th>Behandlare</th>
                <th>Titel</th>
                <th>Patient</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($appointments as $app): 
                $date = !empty($app['appointment_date']) 
                    ? date('Y-m-d', strtotime($app['appointment_date'])) 
                    : 'N/A';
                $practitioner = $app['practitioner_name'] ?? 'N/A';
                $title = $app['title'] ?? 'N/A';
                $patient_name = $app['patient'] ?? 'N/A';
                $time = $app['appointment_time'] ?? '';
            ?>
            <tr>
                <td><?php echo htmlspecialchars($date); ?></td>
                <td><?php echo htmlspecialchars($time); ?></td>
                <td><?php echo htmlspecialchars($practitioner); ?></td>
                <td><?php echo htmlspecialchars($title); ?></td>
                <td><?php echo htmlspecialchars($patient_name); ?></td>
                <td>-</td>
                <td>-</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <form method="post">
        <input type="hidden" name="action" value="request_reschedule">
        <button class="btn" type="submit" onclick="return confirm('Är du säker på att du vill begära ombokning?');">
            Begär ombokning
        </button>
    </form>

    <div style="margin-top:20px;">
        <a class="btn" href="../dashboard.php?page=appointments">← Tillbaka</a>
    </div>
</div>

</body>
</html>
