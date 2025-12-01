<?php
session_start();

// Om användaren klickar på toggle-länken
if (isset($_GET['lang'])) {
    $_SESSION['language'] = $_GET['lang'];
}

// Standard: svenska
$lang = $_SESSION['language'] ?? 'sv';

$texts = [
    'sv' => [
        'title' => 'Mölndal vårdcentral',
        'tagline' => 'Din lokala vårdcentral',
        'patient_portal' => 'Patientportal',
        'patient_desc' => 'Hantera din journal, boka tid eller förnya dina recept',
        'patient_login' => 'Logga in som patient',
    ],
    'en' => [
        'title' => 'Mölndal Health Center',
        'tagline' => 'Your local healthcare center',
        'patient_portal' => 'Patient Portal',
        'patient_desc' => 'Manage your records, book appointments or renew prescriptions',
        'patient_login' => 'Patient Login',
    ]
];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $texts[$lang]['title']; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1><?php echo $texts[$lang]['title']; ?></h1>
            <p class="tagline"><?php echo $texts[$lang]['tagline']; ?></p>
        </header>

        <div class="portal-selection">
            <div class="portal-card patient-portal">
                <h2><?php echo $texts[$lang]['patient_portal']; ?></h2>
                <p><?php echo $texts[$lang]['patient_desc']; ?></p>
                <a href="patient/login.php" class="btn btn-primary">
                    <?php echo $texts[$lang]['patient_login']; ?>
                </a>
            </div>
        </div>

        <!-- Språktoggle -->
        <div style="margin-top:20px; text-align:center;">
            <a href="?lang=sv">🇸🇪 Svenska</a> | <a href="?lang=en">🇬🇧 English</a>
        </div>

        <footer>
            <p>&copy; 2025 KravION ERPSystems. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>