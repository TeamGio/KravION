<?php
require_once __DIR__ . '/../../config/exempelfil_erp.php'; // justera sökväg vid behov

// Om du mappar lokalt $patient_id till ERP patient-identifier måste du ha den värdet här.
// Exempel: $erp_patient_identifier = 'Patient/0001' eller patientens name/email i ERP
$erp_patient_identifier = $patient_external_id ?? null; // sätt korrekt värde

// Hämta appointments från ERP (justera endpoint/filters efter er ERPNext)
try {
    // exempel: filtrera på patient fält (ändra fältnamn efter era ERPNext-modeller)
    if ($erp_patient_identifier) {
        $filter = urlencode('[["Appointment","patient","=","' . $erp_patient_identifier . '"]]');
        $res = erp_request("api/resource/Appointment?filters=$filter");
    } else {
        // fallback: hämta alla eller patientens objekt med annan query
       // $res = erp_request("api/resource/Appointment");
    }
    $appointments = $res['data']['data'] ?? []; // ERPNext brukar nestla data i ['data']['data'] 
} catch (Exception $e) {
    $appointments = [];
    $message = '<div class="alert alert-danger">Kunde inte hämta bokningar från ERP: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

$message = '';



?>

<div class="card">
    <h3>Book New Appointment</h3>
    <?php echo $message; ?>
    
    <form method="POST" style="margin-top: 16px;">
        <input type="hidden" name="action" value="book">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div class="form-group">
                <label>Appointment Date</label>
                <input type="date" name="appointment_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label>Appointment Time</label>
                <input type="time" name="appointment_time" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category" class="form-control" required>
                    <option value="nurse">Nurse</option>
                    <option value="doctor">Doctor</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Reason for Visit</label>
            <textarea name="reason" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Book Appointment</button>
    </form>
</div>

<div class="card">
    <h3>My Appointments</h3>
    
    <?php if (count($appointments) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Category</th>
                    <th>Provider</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?php echo date('Y-m-d', strtotime($appointment['appointment_date'])); ?></td>
                    <td><?php echo date('H:i', strtotime($appointment['appointment_time'])); ?></td>
                    <td><span class="badge badge-info"><?php echo htmlspecialchars(ucfirst($appointment['category'])); ?></span></td>
                    <td><?php echo htmlspecialchars(($appointment['first_name'] ?? 'Not assigned') . ' ' . ($appointment['last_name'] ?? '')); ?></td>
                    <td>
                        <?php 
                        $status = $appointment['status'];
                        $badge_class = $status === 'scheduled' ? 'badge-success' : ($status === 'cancelled' ? 'badge-danger' : 'badge-warning');
                        ?>
                        <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars(ucfirst($status)); ?></span>
                    </td>
                    <td>
                        <?php if ($appointment['status'] === 'scheduled' && strtotime($appointment['appointment_date']) >= strtotime(date('Y-m-d'))): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="cancel">
                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                            <button type="submit" class="btn btn-alert" style="padding: 4px 12px; font-size: 0.9em;" onclick="return confirm('Are you sure you want to cancel this appointment?')">Cancel</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #6C757D; margin-top: 16px;">No appointments found.</p>
    <?php endif; ?>
</div>
