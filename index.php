<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mölndal Vårdcentral</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1>Mölndal vårdcentral</h1>
            <p class="tagline">Din lokala vårdecentral</p>
        </header>

        <div class="portal-selection">
            <div class="portal-card patient-portal">
                <div class="icon">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <h2>Patient Portal</h2>
                <p>Hantera din journal, boka tid eller förnya dina recept</p>
                <a href="patient/login.php" class="btn btn-primary">Patient Login</a>
            </div>

            <div class="portal-card staff-portal">
                <div class="icon">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <h2>Personal portal</h2>
                <p>Hantera patientjournal, bokningar och läkemedel.</p>
                <a href="staff/login.php" class="btn btn-secondary">Staff Login</a>
            </div>
        </div>

        <footer>
            <p>&copy; 2025 KravION ERPSystems. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
