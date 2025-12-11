<?php
// Cancel.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// Samma require-struktur som i renewPrescription.php
require_once '../../config/exempelfil_erp.php';

$erp_client = new ERPNextClient();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Endast POST är tillåten.";
    exit();
}

// appointment_name kommer från din knapp i appointments.php
if (empty($_POST['appointment_name'])) {
    http_response_code(400);
    echo "Saknar boknings-ID.";
    exit();
}

if (!isset($_SESSION['patient_id'])) {
    // Säkerställ att patienten är inloggad
    $_SESSION['error_message'] = "Du måste vara inloggad för att avboka en tid.";
    header('Location: ../login.php');
    exit();
}

$patient_erp_id = $_SESSION['patient_id'];
$appointment_id = $_POST['appointment_name'];

$result = [
    'success' => false,
    'message' => 'Okänt fel vid avbokning.'
];

if (!isset($erp_client) || !method_exists($erp_client, 'cancelAppointment')) {
    $result['message'] = 'ERP-klienten eller metoden för avbokning saknas.';
} else {
    // Utför API-anropet (PUT eller DELETE beroende på hur vi implementerar metoden)
    $tmp = $erp_client->cancelAppointment($appointment_id, $patient_erp_id);

    if (is_array($tmp)) {
        $result = array_merge($result, $tmp);
    } else {
        $result['message'] = 'Ogiltigt svar från ERP-klienten vid avbokning.';
    }
}

if (!empty($result['success']) && $result['success'] === true) {
    // Spara meddelandet i sessionen (popup-känsla)
    $_SESSION['success_message'] = $result['message'];

    // Omdirigera tillbaka till appointments-sidan via dashboard.php
    $redirect_url = '/wwwit-utv/Grupp%204/patient/dashboard.php?page=appointments';
    header('Location: ' . $redirect_url);
    exit();
}

// Om anropet misslyckades, spara felet i sessionen och omdirigera
if (!empty($result['message'])) {
    $_SESSION['error_message'] = $result['message'];

    $redirect_url = '/wwwit-utv/Grupp%204/patient/dashboard.php?page=appointments';
    header('Location: ' . $redirect_url);
    exit();
}

// Fallback
echo "Kunde inte avboka tiden. Försök igen senare.";
?>
