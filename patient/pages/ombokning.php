<?php
session_start();
// Hämta data från session/post
$uid = $_SESSION['personal_number'] ?? $_SESSION['patient_id'] ?? ''; 
$old_appointment_name = $_POST['appointment_name'] ?? '';

if (empty($uid) || empty($old_appointment_name)) {
    header('Location: ../dashboard.php?page=appointments&error=Tid eller patient kunde inte identifieras.');
    exit();
}
?>

<h2>Ombokning av tid</h2>
<p>Du begär ombokning för bokning med ID: <strong><?php echo htmlspecialchars($old_appointment_name); ?></strong></p>

<form method="post" action="ombokning_submit.php">
    <input type="hidden" name="uid" value="<?php echo htmlspecialchars($uid); ?>">
    <input type="hidden" name="old_appointment_name" value="<?php echo htmlspecialchars($old_appointment_name); ?>">

    <label>Vilken period på dagen föredrar du?</label><br>
    <select name="preferred_period" required>
        <option value="Förmiddag (08:00-12:00) (Ingen garanti)">Förmiddag (08:00-12:00)</option>
        
        <option value="Eftermiddag(13:00-16:00) (Ingen garanti)">Eftermiddag (13:00-16:00)</option>
    </select>
    <br><br>

    <label>Vilken veckodag föredrar du?</label><br>
    <select name="preferred_day" required>
        <option value="Måndag (Ingen garanti)">Måndag</option>
        <option value="Tisdag (Ingen garanti)">Tisdag</option>
        <option value="Onsdag (Ingen garanti)">Onsdag</option>
        <option value="Torsdag (Ingen garanti)">Torsdag</option>
        <option value="Fredag (Ingen garanti)">Fredag</option>
    </select>
    <br><br>

    <label>Vilken avdelning var du inbokad på?</label><br>
    <select name="department" required>
        <option value="Läkarmottagning">Läkarmottagning</option>
        <option value="Sjuksköterskemottagning">Sjuksköterskemottagning</option>
        <option value="Kurator mottagning">Kurator mottagning</option>
        <option value="Fysioterapi och dietist">Fysioterapi och dietist</option>
        <option value="Nybesök">Nybesök</option>
    </select>
    <br><br>

    <button type="submit">Skicka ombokningsförfrågan</button>
</form>