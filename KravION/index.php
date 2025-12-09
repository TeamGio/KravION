<?php
session_start();

// Ladda globala språkfilen
require_once 'config/language.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- SPRÅKKNAPP – ENDA NYA ELEMENTET -->
    <div style="position: absolute; top: 10px; right: 10px;">
        <a href="?lang=<?php echo ($lang === 'sv') ? 'en' : 'sv'; ?>" 
           class="btn btn-outline btn-sm">
           <?php echo $t['language_toggle']; ?>
        </a>
    </div>

    <div class="container">
        <header class="main-header">
            <h1><?php echo $t['title']; ?></h1>
            <p class="tagline"><?php echo $t['tagline']; ?></p>
        </header>

        <div class="portal-selection">
            <div class="portal-card patient-portal">
                <div class="icon">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <h2><?php echo $t['patient_portal']; ?></h2>
                <p><?php echo $t['patient_desc']; ?></p>
                <a href="patient/login.php" class="btn btn-primary">
                    <?php echo $t['patient_login']; ?>
                </a>
            </div>
        </div>

        <footer>
            <p>&copy; 2025 KravION ERPSystems. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>