<?php
// renewPrescription.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// Korrekt require_once baserat på din filstruktur (../patient/pages/ -> ../../config/)
require_once '../../config/exempelfil_erp.php'; 

$erp_client = new ERPNextClient();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Endast POST är tillåten.";
    exit();
}

if (empty($_POST['prescription_id'])) {
    http_response_code(400);
    echo "Saknar recept-ID.";
    exit();
}      

if (!isset($_SESSION['patient_id'])) {
    // Felhantering för att säkerställa att patienten är inloggad
    $_SESSION['error_message'] = "Du måste vara inloggad för att förnya recept.";
    header('Location: ../login.php'); // Omdirigera till inloggningssidan
    exit();
}
$patient_erp_id = $_SESSION['patient_id'];

$prescription_id = $_POST['prescription_id'];

$result = [
    'success' => false,
    'message' => 'Okänt fel vid förnyelse.'
];

if (!isset($erp_client) || !method_exists($erp_client, 'renewPrescription')) {
    $result['message'] = 'ERP-klienten eller metoden för förnyelse saknas.';
} else {
    // Utför API-anropet (PUT)
    $tmp = $erp_client->renewPrescription($prescription_id, $patient_erp_id);

    if (is_array($tmp)) {
        $result = array_merge($result, $tmp);
    } else {
        $result['message'] = 'Ogiltigt svar från ERP-klienten vid förnyelse.';
    }
}

if (!empty($result['success']) && $result['success'] === true) {
    // --- LÖSNING FÖR POPUP: SPARA MEDDELANDET I SESSIONEN ---
    $_SESSION['success_message'] = $result['message'];
    
    // Omdirigera tillbaka till prescriptions-sidan via dashboard.php
    $redirect_url = '/wwwit-utv/Grupp4/patient/dashboard.php?page=prescriptions'; 

    header('Location: ' . $redirect_url);
    exit();
}

// --- FELHANTERING OCH DEBUG-VISNING (Vid misslyckat anrop) ---
// Om anropet misslyckades (t.ex. HTTP 500), spara felet i sessionen och omdirigera
if (!empty($result['message'])) {
    $_SESSION['error_message'] = $result['message']; 
    
    // Omdirigera tillbaka för att visa felet
    $redirect_url = '/wwwit-utv/Grupp4/patient/dashboard.php?page=prescriptions';
    header('Location: ' . $redirect_url);
    exit();
}

// Fallback: Om inget fel hanterades, visa det lokalt
echo "Kunde inte förnya receptet. Försök igen senare.";
?>