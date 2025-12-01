<?php
session_start();
require_once '../config/database.php';
require_once '../config/exempelfil_erp.php';
if (!isset($_SESSION['patient_id'])) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

$patient_id = $_SESSION['patient_id'];
$page = $_GET['page'] ?? 'overview';

$stmt = $conn->prepare("SELECT * FROM patients WHERE id = :id");
$stmt->bindParam(':id', $patient_id);
$stmt->execute();
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM appointments WHERE patient_id = :patient_id AND status = 'scheduled'");
$stmt->bindParam(':patient_id', $patient_id);
$stmt->execute();
$upcoming_appointments = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM prescriptions WHERE patient_id = :patient_id AND status = 'active'");
$stmt->bindParam(':patient_id', $patient_id);
$stmt->execute();
$active_prescriptions = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM medical_records WHERE patient_id = :patient_id");
$stmt->bindParam(':patient_id', $patient_id);
$stmt->execute();
$medical_records_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$lang = $patient['language'] ?? 'en';
$translations = [
    'en' => [
        'welcome' => 'Welcome',
        'logout' => 'Logout',
        'overview' => 'Overview',
        'medical_journal' => 'Medical Journal',
        'appointments' => 'Appointments',
        'prescriptions' => 'Prescriptions',
        'upcoming_appointments' => 'Upcoming Appointments',
        'active_prescriptions' => 'Active Prescriptions',
        'medical_records' => 'Medical Records',
    ],
    'sv' => [
        'welcome' => 'Välkommen',
        'logout' => 'Logga ut',
        'overview' => 'Översikt',
        'medical_journal' => 'Medicinsk Journal',
        'appointments' => 'Tidsbokning',
        'prescriptions' => 'Recept',
        'upcoming_appointments' => 'Kommande Besök',
        'active_prescriptions' => 'Aktiva Recept',
        'medical_records' => 'Medicinska Journaler',
    ]
];


$t = $translations[$lang];
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
                    <h1><?php echo $t['welcome']; ?>, <?php echo htmlspecialchars($patient['first_name']); ?>!</h1>
                    <p style="color: #6C757D;">Patient ID: <?php echo htmlspecialchars($patient['personal_number']); ?></p>
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
                    <h3>Quick Actions</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 16px;">
                        <a href="?page=appointments" class="btn btn-primary">Book Appointment</a>
                        <a href="?page=prescriptions" class="btn btn-accent">Request Prescription Renewal</a>
                        <a href="?page=medical_journal" class="btn btn-secondary">View Medical Records</a>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($page === 'medical_journal'): 
                include 'pages/medical_journal.php';
            endif; ?>
            
            <?php if ($page === 'appointments'): 
                include 'pages/appointments.php';
            endif; ?>
            
            <?php if ($page === 'prescriptions'): 
                include 'pages/prescriptions.php';
            endif; ?>
        </div>
    </div>
</body>
</html>
