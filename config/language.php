<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_GET['lang']) && in_array($_GET['lang'], ['sv','en'])) {
    $_SESSION['language'] = $_GET['lang'];
}

$lang = $_SESSION['language'] ?? 'sv';

$texts = [
    'sv' => [
        // INDEX
        'title' => 'Mölndal vårdcentral',
        'tagline' => 'Din lokala vårdcentral',
        'patient_portal' => 'Patientportal',
        'patient_desc' => 'Hantera din journal, boka tid eller förnya dina recept',
        'patient_login' => 'Patient Login',
        'language_toggle' => 'English',

        // LOGIN
        'bankid_login_desc' => 'Logga in via BankID',
        'safe_bankid' => 'Logga in säkert med BankID',
        'pnr_label' => 'personnummer 12 siffror (ÅÅÅÅMMDDXXXX)',
        'continue_bankid' => 'Fortsätt till BankID',
        'back_home' => 'Tillbaka till startsida',
        'err_empty_pnr' => 'Vänligen ange ditt personnummer.',
        'err_pnr_not_found' => 'BankID-verifiering misslyckades. Personnumret hittades inte.',

        // DASHBOARD
        'welcome' => 'Välkommen',
        'logout' => 'Logga ut',
        'overview' => 'Översikt',
        'medical_journal' => 'Medicinsk journal',
        'appointments' => 'Tidsbokning',
        'prescriptions' => 'Recept',
        'upcoming_appointments' => 'Kommande besök',
        'active_prescriptions' => 'Aktiva recept',
        'medical_records' => 'Medicinska journaler',
        'quick_actions' => 'Snabba åtgärder',
        'book_appointment' => 'Boka tid',
        'request_renewal' => 'Begär receptförnyelse',
        'view_records' => 'Visa journaler',

        // APPOINTMENTS
        'appointments_info' => 'Här visas alla dina kommande inbokade tider.',
        'date' => 'Datum',
        'time' => 'Tid',
        'practitioner_name' => 'Behandlare',
        'reason' => 'Titel',
        'no_upcoming_appointments' => 'Inga kommande tidsbokningar hittades.',
        'contact_form' => 'Kontaktformulär',
        'patient' => 'Patient',

        // MEDICAL JOURNAL
        'record_type' => 'Typ',
        'diagnosis' => 'Diagnos',
        'treatment' => 'Behandling',
        'provider' => 'Vårdgivare',
        'symptoms' => 'Symtom',
        'notes' => 'Anteckningar',
        'general' => 'Allmänt',
        'not_available' => 'N/A',
        'no_records' => 'Inga medicinska journaler hittades.',

        // PRESCRIPTIONS
        'medication' => 'Läkemedel',
        'personnummer' => 'Personnummer',
        'withdrawal' => 'Uttag',
        'expiration_date' => 'Utgångsdatum',
        'strength' => 'Styrka',
        'status' => 'Status',
        'approved' => 'Godkänd',
        'no_prescriptions' => 'Inga aktiva recept hittades.',
        'renew_prescription' => 'Förnya recept',
    ],

    'en' => [
        // INDEX
        'title' => 'Mölndal Health Center',
        'tagline' => 'Your local healthcare center',
        'patient_portal' => 'Patient Portal',
        'patient_desc' => 'Manage your journal, book appointments or renew prescriptions',
        'patient_login' => 'Patient Login',
        'language_toggle' => 'Svenska',

        // LOGIN
        'bankid_login_desc' => 'Login using BankID',
        'safe_bankid' => 'Secure login with BankID',
        'pnr_label' => 'Personal number 12 digits (YYYYMMDDXXXX)',
        'continue_bankid' => 'Continue to BankID',
        'back_home' => 'Back to Home',
        'err_empty_pnr' => 'Please enter your personal number.',
        'err_pnr_not_found' => 'BankID verification failed. Personal number not found.',

        // DASHBOARD
        'welcome' => 'Welcome',
        'logout' => 'Logout',
        'overview' => 'Overview',
        'medical_journal' => 'Medical Journal',
        'appointments' => 'Appointments',
        'prescriptions' => 'Prescriptions',
        'upcoming_appointments' => 'Upcoming appointments',
        'active_prescriptions' => 'Active prescriptions',
        'medical_records' => 'Medical records',
        'quick_actions' => 'Quick actions',
        'book_appointment' => 'Book appointment',
        'request_renewal' => 'Request prescription renewal',
        'view_records' => 'View medical records',

        // APPOINTMENTS
        'appointments_info' => 'Here you can see all your upcoming appointments.',
        'date' => 'Date',
        'time' => 'Time',
        'practitioner' => 'Practitioner',
        'reason' => 'Title',
        'no_upcoming_appointments' => 'No upcoming appointments found.',
        'contact_form' => 'Contact Form',
        'patient' => 'Patient',

        // MEDICAL JOURNAL
        'record_type' => 'Type',
        'diagnosis' => 'Diagnosis',
        'treatment' => 'Treatment',
        'provider' => 'Provider',
        'symptoms' => 'Symptoms',
        'notes' => 'Notes',
        'general' => 'General',
        'not_available' => 'N/A',
        'no_records' => 'No medical records found.',

        // PRESCRIPTIONS
        'medication' => 'Medication',
        'personnummer' => 'Personal number',
        'withdrawal' => 'Withdrawals',
        'expiration_date' => 'Expiration date',
        'strength' => 'Strength',
        'status' => 'Status',
        'approved' => 'Approved',
        'no_prescriptions' => 'No active prescriptions found.',
        'renew_prescription' => 'Renew prescription',
    ]
];

$t = $texts[$lang];