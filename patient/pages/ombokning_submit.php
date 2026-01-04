<?php
session_start();
require_once '../../config/exempelfil_erp.php';

if (!isset($_SESSION['patient_id'])) {
    header('Location: ../../login.php'); 
    exit();
}

// Hämta data från session och POST
$uid = $_SESSION['personal_number'] ?? $_SESSION['patient_id'];
$preferred_period     = $_POST['preferred_period'] ?? '';
$preferred_day        = $_POST['preferred_day'] ?? '';
$department           = $_POST['department'] ?? '';
$old_appointment_name = $_POST['old_appointment_name'] ?? '';

// Mappa om värdena så de matchar DocType-alternativen exakt
$map_period = [
    "Förmiddag" => "Förmiddag (08:00-12:00) (Ingen garanti)",
    "Eftermiddag" => "Eftermiddag(13:00-16:00) (Ingen garanti)"
];

$map_day = [
    "Måndag"  => "Måndag (Ingen garanti)",
    "Tisdag"  => "Tisdag (Ingen garanti)",
    "Onsdag"  => "Onsdag (Ingen garanti)",
    "Torsdag" => "Torsdag (Ingen garanti)",
    "Fredag"  => "Fredag (Ingen garanti)"
];

// Validering och mappning
$final_period = $map_period[$preferred_period] ?? $preferred_period;
$final_day    = $map_day[$preferred_day] ?? $preferred_day;

// Datastruktur baserad på din fältlista
$data = [
    "patient_id"       => $uid,           
    "preferred_period" => $final_period,   
    "preferred_day"    => $final_day,      
    "department"       => $department,     
    "old_appointment"  => $old_appointment_name, 
    "status"           => "Mottagen"       
];

try {
    $erp = new ERPNextClient();
    $RESOURCE_NAME = 'G4BokaTid';

    $result = $erp->createNewDoc($RESOURCE_NAME, $data);

    if ($result['success']) {
        $_SESSION['success_message'] = "Tack! Din förfrågan har skickats.";
    } else {
        $_SESSION['error_message'] = "Fel vid ombokning: " . $result['message'];
    }

} catch (Throwable $e) {
    $_SESSION['error_message'] = "Kritiskt fel: " . htmlspecialchars($e->getMessage());
}

header('Location: /wwwit-utv/Grupp4/patient/dashboard.php?page=appointments'); 
exit();