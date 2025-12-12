<?php
// Reschedule.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../../config/exempelfil_erp.php';

$erp_client = new ERPNextClient();

// Tillåt bara POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Endast POST är tillåten.";
    exit();
}

// Kontrollera att boknings-ID finns
if (empty($_POST['appointment_name'])) {
    http_response_code(400);
    echo "Saknar boknings-ID.";
    exit();
}

// Kontrollera att patienten är inloggad
if (!isset($_SESSION['patient_id'])) {
    $_SESSION['error_message'] = "Du måste vara inloggad för att boka om en tid.";
    header('Location: ../login.php');
    exit();
}

$appointment_id = $_POST['appointment_name'];
$patient_erp_id = $_SESSION['patient_id'];

$result = [
    'success' => false,
    'message' => 'Okänt fel vid ombokning.'
];

// Kontrollera att metoden finns
if (!isset($erp_client) || !method_exists($erp_client, 'cancelAppointment')) {
    $result['message'] = 'ERP-klienten eller avbokningsmetoden saknas.';
} else {
    // 🔴 STEG 1: Avboka tiden
    $tmp = $erp_client->cancelAppointment($appointment_id, $patient_erp_id);

    if (is_array($tmp)) {
        $result = array_merge($result, $tmp);
    } else {
        $result['message'] = 'Ogiltigt svar från ERP-klienten vid avbokning.';
    }
}

//  Om avbokningen lyckades till ombokning.php
if (!empty($result['success']) && $result['success'] === true) {
    $_SESSION['success_message'] = 'Tiden är avbokad. Välj nu en ny tid.';

    //  HIT SKICKAS ANVÄNDAREN
   header('Location: ombokning.php');
    exit();

}

// Om något gick fel → tillbaka till appointments
if (!empty($result['message'])) {
    $_SESSION['error_message'] = $result['message'];

    $redirect_url = '/wwwit-utv/Grupp4/patient/dashboard.php?page=appointments';
    header('Location: ' . $redirect_url);
    exit();
}

// Fallback
echo "Kunde inte boka om tiden. Försök igen senare.";
?>
