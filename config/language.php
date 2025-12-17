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
        'medical_records' => 'Medicinska journaler',
        'quick_actions' => 'Snabba åtgärder',
        'book_appointment' => 'Boka tid',
        'request_renewal' => 'Begär receptförnyelse',
        'view_records' => 'Visa journaler',
        'inbox' => 'Inkorg',


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

        // INBOX
        'practitioner' => 'Avsändare',
        'subject' => 'Ämne',
        'date' => 'Datum',
        'status' => 'Status',
        'read message' => 'Läs meddelande',
        'my inbox' => 'Min inkorg',
        'g4form' => 'Blev du nöjd med ditt besök?',
        // FORM i inbox
        'form_title' => 'Blev du nöjd med ditt senaste besök?',
        'form_age' => 'Ålder:',
       
        'form_gender' => 'Kön:',
       
        'gender_male' => 'Man',
        'gender_female' => 'Kvinna',
        'gender_other' => 'Annat',
        'gender_no_say' => 'Vill ej uppge',

        'option_yes' => 'Ja',
        'option_no' => 'Nej',
        'option_partly' => 'Delvis',
        'form_q1' => 'Fick du möjlighet att ställa frågorna du önskade?',
        'form_q2' => 'Var det enkelt att ta till sig informationen?',
        'form_q3' => 'Är du nöjd med hur du kan kontakta oss?',
        'form_q4' => 'Fick du besöka vårdcentralen inom rimlig tid?',
        'form_q5' => 'Var väntan i väntrummet längre än 20 min?',
        'form_q6' => 'Fick du tillräcklig info om behandling/bieffekter?',
        'form_q7' => 'Fick du svar från personalen som du förstod?',
        'form_q8' => 'Förklarade personalen behandlingen på ett bra sätt?',
        'form_comments' => 'Övriga kommentarer:',
        'form_submit' => 'Skicka in svar',
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
        'medical_records' => 'Medical records',
        'quick_actions' => 'Quick actions',
        'book_appointment' => 'Book appointment',
        'request_renewal' => 'Request prescription renewal',
        'view_records' => 'View medical records',
        'inbox' => 'Inbox',

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

        // INBOX
        'practitioner' => 'Sender',
        'subject' => 'Subject',
        'date' => 'Date',
        'status' => 'Status',
        'read message' => 'Read message',
        'my inbox' => 'My inbox',
        'g4form' => 'Were you satisfied with your visit?',
        // form in inbox
        'form_title' => 'Were you satisfied with your latest visit?',
        'form_age' => 'Age:',

        'form_gender' => 'Gender:',

        'gender_male' => 'Male',
        'gender_female' => 'Female',
        'gender_other' => 'Other',
        'gender_no_say' => 'Prefer not to say',

        'option_yes' => 'Yes',
        'option_no' => 'No',
        'option_partly' => 'Partly',
        'form_q1' => 'Did you get the opportunity to ask the questions you wanted?',
        'form_q2' => 'Was the information easy to understand?',
        'form_q3' => 'Are you satisfied with how you can contact us?',
        'form_q4' => 'Did you get to visit the healthcare center within a reasonable time?',
        'form_q5' => 'Was the wait in the waiting room longer than 20 min?',
        'form_q6' => 'Did you get enough info about treatment/side effects?',
        'form_q7' => 'Did you get answers from the staff that you understood?',
        'form_q8' => 'Did the staff explain the treatment in a good way?',
        'form_comments' => 'Other comments:',
        'form_submit' => 'Submit answers',
    ]

];

$t = $texts[$lang];