<?php
session_start();
// Du behöver inte 'database.php' för inloggning längre, men behålls om du använder det någon annanstans.
require_once '../config/database.php'; 
require_once '../config/exempelfil_erp.php'; // Inkluderar ERPNextClient-klassen
// require_once '../config/i18n.php'; // Om du väljer den centrala språkfilen istället, ta bort $texts nedan.

$error = '';
$success = '';
$personal_number = ''; 

if (isset($_SESSION['patient_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Hantera språkbyte via URL-parameter
if (isset($_GET['lang']) && ($_GET['lang'] === 'sv' || $_GET['lang'] === 'en')) {
    $_SESSION['language'] = $_GET['lang'];
}

// Sätt det aktuella språket. Standard är 'sv'.
$lang = $_SESSION['language'] ?? 'sv';

$texts = [
    'sv' => [
        'title' => 'Mölndal vårdcentral',
        'tagline' => 'Din lokala vårdcentral',
        'patient_portal' => 'Patientportal',
        'lang_toggle' => 'English', 
        'pnr_label' => 'personnummer 12 siffror (ÅÅÅÅMMDDXXXX)',
        'continue_bankid' => 'Fortsätt till BankID',
        'back_home' => 'Tillbaka till startsida',
        'demo_credentials' => 'Demouppgifter',
        'bankid_login_desc' => 'Logga in via BankID',
        'err_empty_pnr' => 'Vänligen ange ditt personnummer.',
        'err_pnr_not_found' => 'BankID-verifiering misslyckades. Personnumret hittades inte.'
    ],
    'en' => [
        'title' => 'Mölndal Health Center',
        'tagline' => 'Your local healthcare center',
        'patient_portal' => 'Patient Portal',
        'lang_toggle' => 'Svenska',
        'pnr_label' => 'Personal Number 12 digits (YYYYMMDDXXXX)',
        'continue_bankid' => 'Continue to BankID',
        'back_home' => 'Back to Home',
        'demo_credentials' => 'Demo Credentials',
        'bankid_login_desc' => 'Login using BankID',
        'err_empty_pnr' => 'Please enter your personal number.',
        'err_pnr_not_found' => 'BankID verification failed. Personal number not found.'
    ]
];

$t = $texts[$lang]; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $personal_number = $_POST['personal_number'] ?? '';
    $is_bankid_login = isset($_POST['bankid_login']); 
    
    if (empty($personal_number)) {
        $error = $t['err_empty_pnr'];
    } else {
        

        $erp_client = new ERPNextClient();
        
        $patient_data = $erp_client->findPatientByPNR($personal_number);

        if ($patient_data) {
            $_SESSION['patient_id'] = $patient_data['name']; 
            $_SESSION['personal_number'] = $personal_number;
            
            header('Location: dashboard.php');
            exit();
        } else {
            $error = $t['err_pnr_not_found'];
        }
        

    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['patient_portal']; ?> - Healthcare System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1><?php echo $t['title']; ?></h1>
            <p class="tagline"><?php echo $t['tagline']; ?></p>
            <?php 
                $new_lang = ($lang === 'sv' ? 'en' : 'sv');
                $toggle_text = $t['lang_toggle'];
            ?>
            <div style="position: absolute; top: 10px; right: 10px;">
                <a href="?lang=<?php echo $new_lang; ?>" class="btn btn-outline btn-sm">
                    <?php echo $toggle_text; ?>
                </a>
            </div>
        </header>
        <div class="login-container">
            <div class="login-header">
                <h2><?php echo $t['patient_portal']; ?></h2>
                <p><?php echo $t['bankid_login_desc']; ?></p>
            </div>
                            <img src="/wwwit-utv/Grupp4/Grupp4/patient/BankID_logo.png" alt="BankID Logo" style="height: 280px; width: auto;">

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 20px; padding: 10px; background-color: #F8F9FA; border-radius: 4px;">
                <img src="/wwwit-utv/Grupp4/Grupp4/patient/BankID_logo.png" alt="BankID Logo" style="height: 25px; width: auto;">
                <p style="margin: 0; font-weight: 600; color: #343A40;">Logga in säkert med BankID</p>
            </div>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="personal_number"><?php echo $t['pnr_label']; ?></label>
                    <input type="text" id="personal_number" name="personal_number" class="form-control" required maxlength="12" placeholder="199001011234" value="<?php echo htmlspecialchars($personal_number); ?>">
                </div>

                <button type="submit" name="bankid_login" class="btn btn-primary" style="width: 100%;">
                    <?php echo $t['continue_bankid']; ?>
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 16px;">
                <a href="../index.php" class="btn btn-outline"><?php echo $t['back_home']; ?></a>
            </div>