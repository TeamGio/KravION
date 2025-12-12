<?php
session_start();

// Kommer från hidden-fältet i appointments.php
if (empty($_POST['appointment_name'])) {
    $_SESSION['error_message'] = "Saknar boknings-ID.";
    header('Location: ../dashboard.php?page=appointments');
    exit();
}

// Spara boknings-id i session så ombokning.php kan använda det
$_SESSION['reschedule_appointment_id'] = $_POST['appointment_name'];

// Bara vidare till ombokningssidan
header('Location: ombokning.php');
exit();
