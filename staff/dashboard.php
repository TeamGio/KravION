<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['staff_id'])) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

$staff_id = $_SESSION['staff_id'];
$page = $_GET['page'] ?? 'overview';

$stmt = $conn->prepare("SELECT * FROM staff WHERE id = :id");
$stmt->bindParam(':id', $staff_id);
$stmt->execute();
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE appointment_date >= CURRENT_DATE AND status = 'scheduled'");
$total_appointments = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $conn->query("SELECT COUNT(*) as count FROM patients");
$total_patients = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $conn->query("SELECT COUNT(*) as count FROM prescriptions WHERE renewal_requested = true");
$pending_renewals = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $conn->query("SELECT COUNT(*) as count FROM medicine_inventory WHERE quantity <= reorder_level");
$low_stock_medicines = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$lang = $staff['language'] ?? 'en';
$translations = [
    'en' => [
        'welcome' => 'Welcome',
        'logout' => 'Logout',
        'overview' => 'Overview',
        'patients' => 'Patients',
        'appointments' => 'Appointments',
        'prescriptions' => 'Prescriptions',
        'inventory' => 'Medicine Inventory',
        'reports' => 'Reports',
        'scheduled_appointments' => 'Scheduled Appointments',
        'total_patients' => 'Total Patients',
        'pending_renewals' => 'Pending Prescription Renewals',
        'low_stock_alert' => 'Low Stock Medicines',
    ],
    'sv' => [
        'welcome' => 'Välkommen',
        'logout' => 'Logga ut',
        'overview' => 'Översikt',
        'patients' => 'Patienter',
        'appointments' => 'Tidsbokning',
        'prescriptions' => 'Recept',
        'inventory' => 'Medicinlager',
        'reports' => 'Rapporter',
        'scheduled_appointments' => 'Schemalagda Besök',
        'total_patients' => 'Totalt Antal Patienter',
        'pending_renewals' => 'Väntande Receptförnyelser',
        'low_stock_alert' => 'Lågt Lager Mediciner',
    ]
];

$t = $translations[$lang];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Healthcare Center Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="dashboard">
            <div class="dashboard-header">
                <div>
                    <h1><?php echo $t['welcome']; ?>, <?php echo htmlspecialchars($staff['first_name']); ?>!</h1>
                    <p style="color: #6C757D;"><?php echo ucfirst(htmlspecialchars($staff['role'])); ?> - <?php echo htmlspecialchars($staff['specialization'] ?? 'General'); ?></p>
                </div>
                <div>
                    <a href="logout.php" class="btn btn-alert"><?php echo $t['logout']; ?></a>
                </div>
            </div>
            
            <div class="dashboard-nav">
                <ul>
                    <li><a href="?page=overview" class="<?php echo $page === 'overview' ? 'active' : ''; ?>"><?php echo $t['overview']; ?></a></li>
                    <li><a href="?page=patients" class="<?php echo $page === 'patients' ? 'active' : ''; ?>"><?php echo $t['patients']; ?></a></li>
                    <li><a href="?page=appointments" class="<?php echo $page === 'appointments' ? 'active' : ''; ?>"><?php echo $t['appointments']; ?></a></li>
                    <li><a href="?page=prescriptions" class="<?php echo $page === 'prescriptions' ? 'active' : ''; ?>"><?php echo $t['prescriptions']; ?></a></li>
                    <li><a href="?page=inventory" class="<?php echo $page === 'inventory' ? 'active' : ''; ?>"><?php echo $t['inventory']; ?></a></li>
                    <li><a href="?page=reports" class="<?php echo $page === 'reports' ? 'active' : ''; ?>"><?php echo $t['reports']; ?></a></li>
                </ul>
            </div>
            
            <?php if ($page === 'overview'): ?>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h4><?php echo $total_appointments; ?></h4>
                        <p><?php echo $t['scheduled_appointments']; ?></p>
                    </div>
                    <div class="stat-card secondary">
                        <h4><?php echo $total_patients; ?></h4>
                        <p><?php echo $t['total_patients']; ?></p>
                    </div>
                    <div class="stat-card">
                        <h4><?php echo $pending_renewals; ?></h4>
                        <p><?php echo $t['pending_renewals']; ?></p>
                    </div>
                    <?php if ($low_stock_medicines > 0): ?>
                    <div class="stat-card" style="background: linear-gradient(135deg, #DC3545, #C82333);">
                        <h4><?php echo $low_stock_medicines; ?></h4>
                        <p><?php echo $t['low_stock_alert']; ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="card">
                    <h3>Quick Actions</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 16px;">
                        <a href="?page=patients" class="btn btn-primary">View Patients</a>
                        <a href="?page=appointments" class="btn btn-secondary">Manage Appointments</a>
                        <a href="?page=prescriptions" class="btn btn-accent">Review Prescriptions</a>
                        <a href="?page=inventory" class="btn btn-primary">Check Inventory</a>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($page === 'patients'): 
                include 'pages/patients.php';
            endif; ?>
            
            <?php if ($page === 'appointments'): 
                include 'pages/appointments.php';
            endif; ?>
            
            <?php if ($page === 'prescriptions'): 
                include 'pages/prescriptions.php';
            endif; ?>
            
            <?php if ($page === 'inventory'): 
                include 'pages/inventory.php';
            endif; ?>
            
            <?php if ($page === 'reports'): 
                include 'pages/reports.php';
            endif; ?>
        </div>
    </div>
</body>
</html>
