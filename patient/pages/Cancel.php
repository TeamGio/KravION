<?php
// Cancel.php


session_start();
// Samma mappstruktur som i renewPrescription.php
require_once '../../config/exempelfil_erp.php';

$erp_client = new ERPNextClient();

// Tillåt bara POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Endast POST är tillåten.";
    exit();
}

// appointment_name kommer från hidden-fältet i appointments.php
if (empty($_POST['appointment_name'])) {
    http_response_code(400);
    echo "Saknar boknings-ID.";
    exit();
}

// Kontrollera att patienten är inloggad
if (!isset($_SESSION['patient_id'])) {
    $_SESSION['error_message'] = "Du måste vara inloggad för att avboka en tid.";
    header('Location: ../login.php'); // Omdirigera till inloggningssidan
    exit();
}

$patient_erp_id = $_SESSION['patient_id'];
$appointment_id = $_POST['appointment_name'];

$result = [
    'success' => false,
    'message' => 'Okänt fel vid avbokning.'
];
error_log("Avbokning av $appointment_id för patient $patient_erp_id");
// Kontrollera att ERP-klienten och metoden finns
if (!isset($erp_client) || !method_exists($erp_client, 'cancelAppointment')) {
    $result['message'] = 'ERP-klienten eller metoden för avbokning saknas.';
} else {
    // Utför API-anropet (PUT/DELETE inuti cancelAppointment)
       $tmp = $erp_client->cancelAppointment($appointment_id);
    if (is_array($tmp)) {
        $result = array_merge($result, $tmp);
    } else {
        $result['message'] = 'Ogiltigt svar från ERP-klienten vid avbokning.';
    }
}

// Om avbokningen lyckades
if (!empty($result['success']) && $result['success'] === true) {
    // Spara meddelandet i sessionen (så det kan visas på appointments-sidan)
    $_SESSION['success_message'] = $result['message'];

    // Omdirigera tillbaka till appointments-sidan via dashboard.php
    $redirect_url = '/wwwit-utv/Grupp4/patient/dashboard.php?page=appointments';
    header('Location: ' . $redirect_url);
    exit();
}

// Vid fel – spara felmeddelande och omdirigera tillbaka
if (!empty($result['message'])) {
    $_SESSION['error_message'] = $result['message'];

    $redirect_url = '/wwwit-utv/Grupp4/patient/dashboard.php?page=appointments';
    header('Location: ' . $redirect_url);
    exit();
}

// Fallback
echo "Kunde inte avboka tiden. Försök igen senare.";
?>
