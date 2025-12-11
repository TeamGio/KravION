<?php
// Anpassa sökvägen om exempelfil_erp.php ligger någon annanstans
require_once __DIR__ . '/../exempelfil_erp.php';

// Kolla att vi fått rätt POST-data från formuläret
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_name'])) {
    $appointment_name = $_POST['appointment_name'];

    // Skapa klient mot ERPNext
    $erp_client = new ERPNextClient();

    // Försök avboka tiden
    $result = $erp_client->cancelAppointment($appointment_name);

    if ($result) {
        $message = "Tiden har avbokats.";
    } else {
        $message = "Kunde inte avboka tiden. Försök igen senare.";
    }
} else {
    $message = "Ogiltig förfrågan.";
}
?>