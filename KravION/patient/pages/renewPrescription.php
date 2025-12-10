<?php

// Ny fil för att kunna förnya recept


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../../config/exempelfil_erp.php';

$renewPrescriptions = $erp_client->renewPrescriptions($prescription_id);


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

$prescription_id = $_POST['prescription_id'];

$result = [
    'success' => false,
    'message' => 'Okänt fel vid förnyelse.'
];

if (!isset($erp_client)) {
    $result['message'] = 'ERP-klienten hittas ej.';
} elseif (!method_exists($erp_client, 'renewPrescription')) {
    $result['message'] = 'Funktionen för förnyelse av recept saknas i ERP-klienten.';
} else {
    $tmp = $erp_client->renewPrescription($prescription_id);

    if (is_array($tmp)) {

        $result = array_merge($result, $tmp);
    } else {
        $result['message'] = 'Ogiltigt svar från ERP-klienten vid förnyelse.';
    }
}

if (!empty($result['success']) && $result['success'] === true) {
    header('Location: ../prescriptions.php?msg=' . urlencode("Receptet har förnyats framgångsrikt."));

    exit();
}

echo "Kunde inte förnya receptet. Försök igen senare.";
echo "<br>";
echo "Meddelande från systemet: " . htmlspecialchars($result['message']);

echo "<pre>";
var_dump($_POST);
echo "</pre>";

?>