<?php
session_start();

require_once '../config/language.php';
require_once '../config/exempelfil_erp.php';

$INACTIVITY_LIMIT = 300;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $INACTIVITY_LIMIT)) {
    session_unset();
    session_destroy();
    header('Location: login.php?error=Du har loggats ut pga inaktivitet.');
    exit();
}
$_SESSION['last_activity'] = time();

// Om ej inloggad, skicka till login
if (!isset($_SESSION['patient_id'])) {
    header('Location: login.php');
    exit();
}

$patient_erp_id = $_SESSION['patient_id'];
$patient_pnr = $_SESSION['personal_number'] ?? 'N/A';
$page = $_GET['page'] ?? 'overview';

// Hämta patient från ERPNext
$erp_client = new ERPNextClient();
$patient = $erp_client->findPatientByPNR($patient_pnr);

if (!$patient) {
    session_destroy();
    header('Location: login.php?error=Kunde inte hämta patientdata.');
    exit();
}

$patient_erp_id = $patient['name'];

$patient_data = [
    'first_name' => $patient['first_name'] ?? 'Patient',
    'personal_number' => $patient_pnr,
];


// -------------------------------------------------------------
// 1. HÄMTA DATA FRÅN API
// -------------------------------------------------------------

$prescriptions = $erp_client->getPrescriptionsForPatient($patient_erp_id);   
$active_prescriptions = count($prescriptions);

$appointments = $erp_client->getAppointmentsForPatient($patient_erp_id);     
$upcoming_appointments = count($appointments);

// HÄR ÄR ÄNDRINGEN: Vi döper dem direkt till namnen din kod vill ha ($med_recs, $vitals, $encounters)
$med_recs   = $erp_client->getMedicalrecords($patient_erp_id);
$vitals     = $erp_client->getVitalSignsForPatient($patient_erp_id);
$encounters = $erp_client->getJournalRecordsForPatient($patient_erp_id);


// -------------------------------------------------------------
// 2. LOGIK FRÅN MEDICAL JOURNAL (Klistra in din kod exakt som den var)
// -------------------------------------------------------------

$records = [];

foreach ($encounters as $enc) {
   $key = ($enc['encounter_date'] ?? '') . ' ' . ($enc['encounter_time'] ?? '');
   $records[$key] = ['type' => 'encounter', 'encounter' => $enc];
}

foreach ($vitals as $vs) {
   $key = ($vs['signs_date'] ?? '') . ' ' . ($vs['signs_time'] ?? '');
   if (isset($records[$key])) {
       $records[$key]['vitals'] = $vs;
   } else {
       $records[$key] = ['type' => 'encounter', 'vitals' => $vs];
   }
}

foreach ($med_recs as $mr) { 
   if (($mr['reference_doctype'] ?? '') === 'Lab Test') {
       $lab_data = $erp_client->getDoc($mr['reference_doctype'], $mr['reference_name']);
       if ($lab_data) {
           $date = $lab_data['result_date'] ?? date('Y-m-d', strtotime($lab_data['creation']));
           $time = $lab_data['result_time'] ?? '00:00:00';
           $key = $date . ' ' . $time . '_' . $lab_data['name'];
          
           $l_staff = $lab_data['practitioner_name'] ?? $lab_data['employee_name'] ?? 'Laboratoriet';
           $records[$key] = ['type' => 'lab', 'data' => $lab_data, 'staff' => $l_staff];
       }
   }
}

krsort($records); // Sorterar listan

// Nu räknar vi antalet bakade händelser
$display_journal_count = count($records);


// Språkknapp
$new_lang = ($lang === 'sv') ? 'en' : 'sv';

?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div style="position:absolute; top:10px; right:10px;">
    <a href="?lang=<?php echo $new_lang; ?>" class="btn btn-outline btn-sm">
        <?php echo $t['language_toggle']; ?>
    </a>
</div>

<div class="container">
    <div class="dashboard">

        <div class="dashboard-header">
            <div>
                <h1><?php echo $t['welcome']; ?>, <?php echo htmlspecialchars($patient_data['first_name']); ?>!</h1>
                <p style="color:#6C757D;">Patient ID: <?php echo htmlspecialchars($patient_pnr); ?></p>
            </div>
            <div>
                <a href="logout.php" class="btn btn-alert"><?php echo $t['logout']; ?></a>
            </div>
        </div>

        <div class="dashboard-nav">
            <ul>
                <li><a href="?page=overview" class="<?php echo $page === 'overview' ? 'active' : ''; ?>"><?php echo $t['overview']; ?></a></li>
                <li><a href="?page=medical_journal" class="<?php echo $page === 'medical_journal' ? 'active' : ''; ?>"><?php echo $t['medical_journal']; ?></a></li>
                <li><a href="?page=appointments" class="<?php echo $page === 'appointments' ? 'active' : ''; ?>"><?php echo $t['appointments']; ?></a></li>
                <li><a href="?page=prescriptions" class="<?php echo $page === 'prescriptions' ? 'active' : ''; ?>"><?php echo $t['prescriptions']; ?></a></li>
                <li><a href="?page=inbox" class="<?php echo $page === 'inbox' ? 'active' : ''; ?>"><?php echo $t['inbox']; ?></a></li>
            </ul>
        </div>

        <?php if ($page === 'overview'): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <h4><?php echo $upcoming_appointments; ?></h4>
                <p><?php echo $t['upcoming_appointments']; ?></p>
            </div>
            <div class="stat-card">
                <h4><?php echo $active_prescriptions; ?></h4>
                <p><?php echo $t['prescriptions']; ?></p>
            </div>
            
            <div class="stat-card">
                <h4><?php echo $display_journal_count; ?></h4>
                <p><?php echo $t['medical_records']; ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php 
            // Medical journal kan nu använda $records direkt (om du städat den filen), 
            // eller köra sin egen logik igen (om du lät den vara kvar). Båda funkar.
            if ($page === 'medical_journal') include 'pages/medical_journal.php';
            elseif ($page === 'appointments') include 'pages/appointments.php';
            elseif ($page === 'prescriptions') include 'pages/prescriptions.php';
            elseif ($page === 'inbox') include 'pages/inbox.php';
        ?>

    </div>
</div>
</body>
</html>