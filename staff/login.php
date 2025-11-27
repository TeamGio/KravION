<?php
session_start();
require_once '../config/database.php';
require_once '../config/exempelfil_erp.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id = $_POST['staff_id'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($staff_id) || empty($password)) {
        $error = 'Please enter both staff ID and password';
    } else {
        $database = new Database();
        $conn = $database->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = :staff_id AND is_active = true");
        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $staff = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $staff['password_hash'])) {
                $_SESSION['staff_id'] = $staff['id'];
                $_SESSION['staff_name'] = $staff['first_name'] . ' ' . $staff['last_name'];
                $_SESSION['staff_role'] = $staff['role'];
                $_SESSION['language'] = $staff['language'];
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid staff ID or password';
            }
        } else {
            $error = 'Invalid staff ID or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - Healthcare System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h2>Healthcare Center</h2>
                <p>Staff Access Portal</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="staff_id">Staff ID</label>
                    <input type="text" id="staff_id" name="staff_id" class="form-control" required placeholder="DOC001">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="Enter your password">
                </div>
                
                <button type="submit" class="btn btn-secondary" style="width: 100%; margin-bottom: 16px;">Login</button>
                
                <div style="text-align: center;">
                    <a href="../index.php" class="btn btn-outline">Back to Home</a>
                </div>
            </form>
            
            <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #E9ECEF; text-align: center; color: #6C757D;">
                <p><strong>Demo Credentials:</strong></p>
                <p>Doctor - Staff ID: DOC001, Password: password</p>
                <p>Nurse - Staff ID: NUR001, Password: password</p>
            </div>
        </div>
    </div>
</body>
</html>
