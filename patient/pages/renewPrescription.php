<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Endast POST är tillåten.";
    exit();
}

if (empty($_POST['prscriptionID'])) {
    http_response_code(400);
    echo "Saknar recept-ID.";
    exit();
}      

$prescriptionID = $_POST['prscriptionID'];

$result = [
    'success' => false,
    'message' => 'Okänt fel vid förnyelse.'
];

if (!isset($erp_client)) {
    $result['message'] = 'ERP-klienten hittas ej.';
} elseif (!method_exists($erp_client, 'renewPrescription')) {
    $result['message'] = 'Funktionen för förnyelse av recept saknas i ERP-klienten.';
} else {
    $tmp = $erp_client->renewPrescription($prescriptionID);

    if (is_array($tmp)) {

        $result = array_merge($result, $tmp)
    } else {
        $result['message'] = 'Ogiltigt svar från ERP-klienten vid förnyelse.';
    }
}

if (!empty($result['success']) && $result['success'] === true) {
    header('Location: ../dashboard.pgp?msg= . urlencode("Receptet har förnyats framgångsrikt.")');

    exit();
}

echo "Kunde inte förnya receptet. Försök igen senare.";
echo "<br>";
echo "Meddelande från systemet: " . htmlspecialchars($result['message']);

?>