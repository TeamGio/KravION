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

// När användaren klickar på knappen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_reschedule') {

    $tmp = $erp_client->cancelAppointment($appointment_id, $patient_erp_id);

    if (!empty($tmp['success'])) {
        unset($_SESSION['reschedule_appointment_id']);
        $_SESSION['success_message'] = "Ombokning begärd. Tiden är avbokad – välj nu en ny tid.";
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
    <style>
        .card { background:#fff; padding:20px; border-radius:6px; margin-top:20px; }
        .btn { display:inline-block; padding:10px 16px; background:#e0e0e0; color:#000; text-decoration:none; border-radius:4px; border:1px solid #aaa; cursor:pointer; }
        .btn:hover { background:#d5d5d5; }
    </style>
</head>
<body>

<div class="card">
    <h2>Ombokning</h2>

    <p>Du är på väg att begära ombokning för din valda tid. 
    Notera att om du ändrar tid 24h innan den bokade tiden kommer du faktureras för ombokningen
    </p>

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
