<?php
session_start();

// Ladda global språkfil
require_once '../config/language.php';

// ERP + DB
require_once '../config/database.php';
require_once '../config/exempelfil_erp.php';

$error = '';
$success = '';
$personal_number = '';

if (isset($_SESSION['patient_id'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $personal_number = $_POST['personal_number'] ?? '';

    if (empty($personal_number)) {
        $error = $t['err_empty_pnr'];
    } else {
        $erp = new ERPNextClient();
        $patient = $erp->findPatientByPNR($personal_number);

        if ($patient) {
            $_SESSION['patient_id'] = $patient['name'];
            $_SESSION['personal_number'] = $personal_number;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = $t['err_pnr_not_found'];
        }
    }
}

// Språkknapp
$new_lang = ($lang === 'sv') ? 'en' : 'sv';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['patient_portal']; ?> - Healthcare System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <!-- SPRÅKKAPP -->
    <div style="position:absolute; top:10px; right:10px;">
        <a href="?lang=<?php echo $new_lang; ?>" class="btn btn-outline btn-sm">
            <?php echo $t['language_toggle']; ?>
        </a>
    </div>

    <div class="container">
        <header class="main-header">
            <h1><?php echo $t['title']; ?></h1>
            <p class="tagline"><?php echo $t['tagline']; ?></p>
        </header>

        <div class="login-container">
            <div class="login-header">
                <h2><?php echo $t['patient_portal']; ?></h2>
                <p><?php echo $t['bankid_login_desc']; ?></p>
            </div>

            <img src="../patient/BankID_logo.png" 
                 alt="BankID Logo" 
                 style="height: 280px; width: auto;">

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div style="display:flex; align-items:center; justify-content:center; gap:10px;
                        margin-bottom:20px; padding:10px; background:#F8F9FA; border-radius:4px;">
                <img src="../patient/BankID_logo.png"
                     alt="BankID Logo"
                     style="height:25px; width:auto;">
                <p style="margin:0; font-weight:600; color:#343A40;">
                    <?php echo $t['safe_bankid']; ?>
                </p>
            </div>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="personal_number"><?php echo $t['pnr_label']; ?></label>
                    <input type="text" id="personal_number" name="personal_number"
                           class="form-control" required maxlength="12"
                           placeholder="199001011234"
                           value="<?php echo htmlspecialchars($personal_number); ?>">
                </div>

                <button type="submit" name="bankid_login" class="btn btn-primary" style="width: 100%;">
                    <?php echo $t['continue_bankid']; ?>
                </button>
            </form>

            <div style="text-align:center; margin-top:16px;">
                <a href="../index.php" class="btn btn-outline"><?php echo $t['back_home']; ?></a>
            </div>

        </div>
    </div>
</body>
</html>