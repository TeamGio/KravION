<?php
// 1. PHP-LOGIK
if (session_status() === PHP_SESSION_NONE) session_start();

// Fallback för språk
if (!isset($t)) {
    $t = [
        'reschedule_title' => 'Omboka tid',
        'reschedule_desc' => 'Du håller på att begära ombokning för:',
        'preferred_period_label' => 'Önskad period',
        'morning_opt' => 'Förmiddag (08:00-12:00)',
        'afternoon_opt' => 'Eftermiddag (13:00-16:00)',
        'no_guarantee' => '(Ingen garanti)',
        'preferred_day_label' => 'Önskad dag',
        'mon' => 'Måndag', 'tue' => 'Tisdag', 'wed' => 'Onsdag', 'thu' => 'Torsdag', 'fri' => 'Fredag',
        'department_label' => 'Avdelning',
        'dept_doctor' => 'Läkarmottagning',
        'dept_nurse' => 'Sjuksköterskemottagning',
        'dept_curator' => 'Kurator mottagning',
        'dept_physio' => 'Fysioterapi och dietist',
        'dept_new_visit' => 'Nybesök',
        'send_reschedule_request' => 'Skicka ombokningsförfrågan'
    ];
}

$uid = $_SESSION['personal_number'] ?? $_SESSION['patient_id'] ?? ''; 
$old_appointment_name = $_POST['appointment_name'] ?? 'Okänd bokning';
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['reschedule_title']; ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../../assets/css/style.css">

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: var(--background-color); 
            margin: 0;
            font-family: 'Open Sans', sans-serif; 
        }
    </style>
</head>
<body>

    <div class="card" style="width: 100%; max-width: 500px;">
        
        <h3 style="color: var(--primary-color); border-bottom: 2px solid var(--gray-light); padding-bottom: 15px; margin-bottom: 20px;">
            <?php echo $t['reschedule_title']; ?>
        </h3>

        <div class="alert alert-info">
            <?php echo $t['reschedule_desc']; ?> <br>
            <strong><?php echo htmlspecialchars($old_appointment_name); ?></strong>
        </div>

        <form method="post" action="ombokning_submit.php">
            <input type="hidden" name="uid" value="<?php echo htmlspecialchars($uid); ?>">
            <input type="hidden" name="old_appointment_name" value="<?php echo htmlspecialchars($old_appointment_name); ?>">

            <div class="form-group">
                <label for="preferred_period"><?php echo $t['preferred_period_label']; ?></label>
                <select name="preferred_period" id="preferred_period" class="form-control" required>
                    <option value="Förmiddag"><?php echo $t['morning_opt']; ?> <?php echo $t['no_guarantee']; ?></option>
                    <option value="Eftermiddag"><?php echo $t['afternoon_opt']; ?> <?php echo $t['no_guarantee']; ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="preferred_day"><?php echo $t['preferred_day_label']; ?></label>
                <select name="preferred_day" id="preferred_day" class="form-control" required>
                    <option value="Måndag"><?php echo $t['mon']; ?> <?php echo $t['no_guarantee']; ?></option>
                    <option value="Tisdag"><?php echo $t['tue']; ?> <?php echo $t['no_guarantee']; ?></option>
                    <option value="Onsdag"><?php echo $t['wed']; ?> <?php echo $t['no_guarantee']; ?></option>
                    <option value="Torsdag"><?php echo $t['thu']; ?> <?php echo $t['no_guarantee']; ?></option>
                    <option value="Fredag"><?php echo $t['fri']; ?> <?php echo $t['no_guarantee']; ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="department"><?php echo $t['department_label']; ?></label>
                <select name="department" id="department" class="form-control" required>
                    <option value="Läkarmottagning"><?php echo $t['dept_doctor']; ?></option>
                    <option value="Sjuksköterskemottagning"><?php echo $t['dept_nurse']; ?></option>
                    <option value="Kurator mottagning"><?php echo $t['dept_curator']; ?></option>
                    <option value="Fysioterapi och dietist"><?php echo $t['dept_physio']; ?></option>
                    <option value="Nybesök"><?php echo $t['dept_new_visit']; ?></option>
                </select>
            </div>

            <div style="margin-top: 30px; display: flex; gap: 15px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <?php echo $t['send_reschedule_request']; ?>
                </button>
                
                <a href="../dashboard.php?page=appointments" class="btn btn-outline" style="text-align:center;">
                    Avbryt
                </a>
            </div>
        </form>
    </div>

</body>
</html>