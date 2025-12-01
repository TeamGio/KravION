<?php
session_start();
require_once '../config/database.php'; 
require_once '../config/exempelfil_erp.php';

$INACTIVITY_LIMIT = 300; 

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $INACTIVITY_LIMIT)) {
    session_unset();
    session_destroy();
    header('Location: login.php?error=Du har loggats ut på grund av inaktivitet.');
    exit();
}

$_SESSION['last_activity'] = time();


if (!isset($_SESSION['patient_id'])) {
    header('Location: login.php');
    exit();
}

$patient_erp_id = $_SESSION['patient_id']; 
$patient_pnr = $_SESSION['personal_number'] ?? 'N/A';
$page = $_GET['page'] ?? 'overview';

$erp_client = new ERPNextClient();
$patient = $erp_client->findPatientByPNR($patient_pnr); 

if (!$patient) {
    session_destroy();
    header('Location: login.php?error=Kunde inte hämta patientdata från ERPNext.');
    exit();
}

$patient_data = [
    'first_name' => $patient['first_name'] ?? 'Patient',
    'personal_number' => $patient_pnr,
    'language' => $patient['language'] ?? $_SESSION['language'] ?? 'sv', 
];

$lang = $patient_data['language'] ?? 'sv';
$translations = [
    'en' => [
        'welcome' => 'Welcome',
        'logout' => 'Logout',
        'overview' => 'Overview',
        'medical_journal' => 'Medical Journal',
        'lab_results' => 'Lab Results',
        'appointments' => 'Appointments',
        'prescriptions' => 'Prescriptions',
        'upcoming_appointments' => 'Upcoming Appointments',
        'active_prescriptions' => 'Active Prescriptions',
        'medical_records' => 'Medical Records',
        'quick_actions' => 'Quick Actions',
        'book_appointment' => 'Book Appointment',
        'request_renewal' => 'Request Prescription Renewal',
        'view_records' => 'View Medical Records',
    ],
    'sv' => [
        'welcome' => 'Välkommen',
        'logout' => 'Logga ut',
        'overview' => 'Översikt',
        'medical_journal' => 'Medicinsk Journal',
        'lab_results' => 'Laboratorieresultat',
        'appointments' => 'Tidsbokning',
        'prescriptions' => 'Recept',
        'upcoming_appointments' => 'Kommande Besök',
        'active_prescriptions' => 'Aktiva Recept',
        'medical_records' => 'Medicinska Journaler',
        'quick_actions' => 'Snabba Åtgärder',
        'book_appointment' => 'Boka Tid',
        'request_renewal' => 'Begär Receptförnyelse',
        'view_records' => 'Visa Journaler',
    ]
];

$t = $translations[$lang];

$upcoming_appointments = 2;
$medical_records_count = 15;


$recept = $erp_client->getPrescriptionsForPatient($patient_erp_id); // 1. Hämta listan
$active_prescriptions = count($recept); // 2. Räkna listan

$recept = $erp_client->getPrescriptionsForPatient($patient_erp_id); 

// --- DEBUG: KLISTRA IN DETTA TILLFÄLLIGT ---
echo "<pre style='background: white; padding: 20px; border: 2px solid red; position: absolute; z-index: 9999;'>";
print_r($recept);
echo "</pre>";
// --------------------------------------------

$active_prescriptions = count($recept);

?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - Healthcare System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="dashboard">
            <div class="dashboard-header">
                <div>
                    <h1><?php echo $t['welcome']; ?>, <?php echo htmlspecialchars($patient_data['first_name']); ?>!</h1>
                    <p style="color: #6C757D;">Patient ID: <?php echo htmlspecialchars($patient_pnr); ?></p>
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
                </ul>
            </div>
            
            <?php if ($page === 'overview'): ?>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h4><?php echo $upcoming_appointments; ?></h4>
                        <p><?php echo $t['upcoming_appointments']; ?></p>
                    </div>
                    <div class="stat-card secondary">
                        <h4><?php echo $active_prescriptions; ?></h4>
                        <p><?php echo $t['active_prescriptions']; ?></p>
                    </div>
                    <div class="stat-card">
                        <h4><?php echo $medical_records_count; ?></h4>
                        <p><?php echo $t['medical_records']; ?></p>
                    </div>
                </div>
                
                <div class="card">
                    <h3><?php echo $t['quick_actions']; ?></h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 16px;">
                        <a href="?page=appointments" class="btn btn-primary"><?php echo $t['book_appointment']; ?></a>
                        <a href="?page=prescriptions" class="btn btn-accent"><?php echo $t['request_renewal']; ?></a>
                        <a href="?page=medical_journal" class="btn btn-secondary"><?php echo $t['view_records']; ?></a>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php 
            if ($page === 'medical_journal'): 
                include 'pages/medical_journal.php';
            elseif ($page === 'appointments'): 
                include 'pages/appointments.php';
            elseif ($page === 'prescriptions'): 
                include 'pages/prescriptions.php';
            endif; 
            ?>
        </div>
    </div>
</body>
</html>