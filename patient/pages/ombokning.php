<?php
// Se till att sessionen är startad och språkfilen är inkluderad om den inte redan är det
if (session_status() === PHP_SESSION_NONE) session_start();
// (Antag att language.php inkluderas i dashboard.php som laddar denna, annars kan du behöva include 'language.php'; här)

$uid = $_SESSION['personal_number'] ?? $_SESSION['patient_id'] ?? ''; 
$old_appointment_name = $_POST['appointment_name'] ?? '';

if (empty($uid) || empty($old_appointment_name)) {
    // Notera: Felmeddelandet i URL:en är fortfarande hårdkodat, men det syns sällan för användaren.
    header('Location: ../dashboard.php?page=appointments&error=Tid eller patient kunde inte identifieras.');
    exit();
}
?>

<h2><?php echo $t['reschedule_title']; ?></h2>
<p><?php echo $t['reschedule_desc']; ?> <strong><?php echo htmlspecialchars($old_appointment_name); ?></strong></p>

<form method="post" action="ombokning_submit.php">
    <input type="hidden" name="uid" value="<?php echo htmlspecialchars($uid); ?>">
    <input type="hidden" name="old_appointment_name" value="<?php echo htmlspecialchars($old_appointment_name); ?>">

    <label><?php echo $t['preferred_period_label']; ?></label><br>
    <select name="preferred_period" required>
        <option value="Förmiddag (08:00-12:00) (Ingen garanti)"><?php echo $t['morning_opt']; ?> <?php echo $t['no_guarantee']; ?></option>
        <option value="Eftermiddag (13:00-16:00) (Ingen garanti)"><?php echo $t['afternoon_opt']; ?> <?php echo $t['no_guarantee']; ?></option>
    </select>
    <br><br>

    <label><?php echo $t['preferred_day_label']; ?></label><br>
    <select name="preferred_day" required>
        <option value="Måndag (Ingen garanti)"><?php echo $t['mon']; ?> <?php echo $t['no_guarantee']; ?></option>
        <option value="Tisdag (Ingen garanti)"><?php echo $t['tue']; ?> <?php echo $t['no_guarantee']; ?></option>
        <option value="Onsdag (Ingen garanti)"><?php echo $t['wed']; ?> <?php echo $t['no_guarantee']; ?></option>
        <option value="Torsdag (Ingen garanti)"><?php echo $t['thu']; ?> <?php echo $t['no_guarantee']; ?></option>
        <option value="Fredag (Ingen garanti)"><?php echo $t['fri']; ?> <?php echo $t['no_guarantee']; ?></option>
    </select>
    <br><br>

    <label><?php echo $t['department_label']; ?></label><br>
    <select name="department" required>
        <option value="Läkarmottagning"><?php echo $t['dept_doctor']; ?></option>
        <option value="Sjuksköterskemottagning"><?php echo $t['dept_nurse']; ?></option>
        <option value="Kurator mottagning"><?php echo $t['dept_curator']; ?></option>
        <option value="Fysioterapi och dietist"><?php echo $t['dept_physio']; ?></option>
        <option value="Nybesök"><?php echo $t['dept_new_visit']; ?></option>
    </select>
    <br><br>

    <button type="submit"><?php echo $t['send_reschedule_request']; ?></button>
</form>