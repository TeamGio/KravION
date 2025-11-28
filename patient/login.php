<?php
session_start();
require_once '../config/database.php';
require_once '../config/exempelfil_erp.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $personal_number = $_POST['personal_number'] ?? '';
    
    if (empty($personal_number)) {
        $error = 'Please enter personalnumber';
    } else {
        $database = new Database();
        $conn = $database->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM patients WHERE personal_number = :personal_number");
        $stmt->bindParam(':personal_number', $personal_number);
        $stmt->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Login - Healthcare System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h2>Patient Portal</h2>
                <p>Access your medical information</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="dashboard.php">
                <div class="form-group">
                    <label for="personal_number">Personal Number (YYYYMMDDXXXX)</label>
                    <input type="text" id="personal_number" name="personal_number" class="form-control" required maxlength="12" placeholder="199001011234">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 16px;">Login</button>
                
                <div style="text-align: center;">
                    <a href="../index.php" class="btn btn-outline">Back to Home</a>
                </div>
            </form>
            
            <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #E9ECEF; text-align: center; color: #6C757D;">
                <p><strong>Demo Credentials:</strong></p>
                <p>Personal Number: 199001011234</p>
            </div>
        </div>
    </div>
</body>
</html>
