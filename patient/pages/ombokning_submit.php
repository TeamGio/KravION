<?php
session_start();
require_once '../../config/exempelfil_erp.php';

if (!isset($_SESSION['patient_id'])) {
    header('Location: ../../login.php'); 
    exit();
}

// Hämta data
// Vi använder personnummer om det finns, annars ID
$uid = $_SESSION['personal_number'] ?? $_SESSION['patient_id'];

$preferred_period   = $_POST['preferred_period'] ?? '';
$department         = $_POST['department'] ?? '';
$preferred_day      = $_POST['preferred_day'] ?? '';
$old_appointment_name = $_POST['old_appointment_name'] ?? '';

// --- KORRIGERAD DATASTRUKTUR ---
$data = [
    "doctype"          => "G4BokaTid", 
    
    // HÄR VAR FELET: ERPNext vill ha "patient_id", inte "uid"
    "patient_id"       => $uid,            
    "preferred_period" => $preferred_period,
    "preferred_day"    => $preferred_day,
    "department"       => $department,
    "old_appointment"  => $old_appointment_name
];

// Validering
if (empty($preferred_period) || empty($department) || empty($preferred_day)) {
    $_SESSION['error_message'] = "Alla fält måste fyllas i.";
    header('Location: ../../dashboard.php?page=appointments');
    exit();
}

try {
    $erp = new ERPNextClient();
    $RESOURCE_NAME = 'G4BokaTid';

    // Skicka till ERPNext
    $result = $erp->createNewDoc($RESOURCE_NAME, $data);

} catch (Throwable $e) {
    $_SESSION['error_message'] = "Kritiskt fel: " . htmlspecialchars($e->getMessage());
    header('Location: /wwwit-utv/Grupp4/patient/dashboard.php?page=appointments');
    exit();
}

if ($result['success']) {
    $_SESSION['success_message'] = "Tack! Din förfrågan har skickats. (Gäller bokning: $old_appointment_name)";
    header('Location: /wwwit-utv/Grupp4/patient/dashboard.php?page=appointments'); 
    exit();
} else {
    $_SESSION['error_message'] = "Fel vid ombokning: " . htmlspecialchars($result['message']);
    header('Location: /wwwit-utv/Grupp4/patient/dashboard.php?page=appointments'); 
    exit();
}
?>