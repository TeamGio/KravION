<?php
session_start();

// Ladda global språkfil
require_once '../config/language.php';

// ERP + DB
require_once '../config/database.php';
require_once '../config/exempelfil_erp.php';

// *** NYTT: skapa databaskoppling så medical_journal.php får $conn ***
$database = new Database();
$conn = $database->getConnection();

// Inaktivitet
$INACTIVITY_LIMIT = 300;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $INACTIVITY_LIMIT)) {
    session_unset();
    session_destroy();
    header('Location: login.php?error=Du har loggats ut pga inaktivitet.');
    exit();
}
$_SESSION['last_activity'] = time();

// Kräver inloggning
if (!isset($_SESSION['patient_id'])) {
    header('Location: login.php');
    exit();
}

$patient_erp_id = $_SESSION['patient_id'];
$patient_pnr = $_SESSION['personal_number'] ?? 'N/A';
$page = $_GET['page'] ?? 'overview';

$patient_id = $patient_erp_id;

// Hämta patient från ERPNext
$erp_client = new ERPNextClient();
$patient = $erp_client->findPatientByPNR($patient_pnr);

if (!$patient) {
    session_destroy();
    header('Location: login.php?error=Kunde inte hämta patientdata.');
    exit();
}

$patient_data = [
    'first_name' => $patient['first_name'] ?? 'Patient',
    'personal_number' => $patient_pnr,
];

// Statistik
$prescriptions = $erp_client->getPrescriptionsForPatient($patient_erp_id);   // ← FIX
$active_prescriptions = count($prescriptions);

$upcoming_appointments = 2;
$medical_records_count = 15;

// Språkknapp (samma logik som index/login)
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

<!-- SPRÅKKNAPP – ENDA NYA TILLÄGGET -->
<div style="position:absolute; top:10px; right:10px;">
    <a href="?lang=<?php echo $new_lang; ?>" class="btn btn-outline btn-sm">
        <?php echo $t['language_toggle']; ?>
    </a>
</div>

<div class="container">
    <div class="dashboard">

        <!-- HEADER -->
        <div class="dashboard-header">
            <div>
                <h1><?php echo $t['welcome']; ?>, <?php echo htmlspecialchars($patient_data['first_name']); ?>!</h1>
                <p style="color:#6C757D;">Patient ID: <?php echo htmlspecialchars($patient_pnr); ?></p>
            </div>
            <div>
                <a href="logout.php" class="btn btn-alert"><?php echo $t['logout']; ?></a>
            </div>
        </div>

        <!-- MENY -->
        <div class="dashboard-nav">
            <ul>
                <li><a href="?page=overview" class="<?php echo $page === 'overview' ? 'active' : ''; ?>"><?php echo $t['overview']; ?></a></li>
                <li><a href="?page=medical_journal" class="<?php echo $page === 'medical_journal' ? 'active' : ''; ?>"><?php echo $t['medical_journal']; ?></a></li>
                <li><a href="?page=appointments" class="<?php echo $page === 'appointments' ? 'active' : ''; ?>"><?php echo $t['appointments']; ?></a></li>
                <li><a href="?page=prescriptions" class="<?php echo $page === 'prescriptions' ? 'active' : ''; ?>"><?php echo $t['prescriptions']; ?></a></li>
            </ul>
        </div>

        <!-- OVERSIKT -->
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
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; margin-top:16px;">
                <a href="?page=appointments" class="btn btn-primary"><?php echo $t['book_appointment']; ?></a>
                <a href="?page=prescriptions" class="btn btn-accent"><?php echo $t['request_renewal']; ?></a>
                <a href="?page=medical_journal" class="btn btn-secondary"><?php echo $t['view_records']; ?></a>
            </div>
        </div>
        <?php endif; ?>

        <!-- UNDER-SIDOR -->
        <?php 
            if ($page === 'medical_journal') include 'pages/medical_journal.php';
            elseif ($page === 'appointments') include 'pages/appointments.php';
            elseif ($page === 'prescriptions') include 'pages/prescriptions.php';
        ?>

    </div>
</div>
</body>
</html>