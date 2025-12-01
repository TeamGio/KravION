<?php
require_once '../config/exempelfil_erp.php';
$message = '';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$cookiepath = "/tmp/cookies.txt";
$tmeout = 3600; // (3600=1hr)
// här sätter ni er domän
$baseurl = 'http://193.93.250.83:8080/'; 

try {
  $ch = curl_init($baseurl . 'api/method/login');
} catch (Exception $e) {
  echo 'Caught exception: ',  $e->getMessage(), "\n";
}

curl_setopt($ch, CURLOPT_POST, true);
//  ----------  Här sätter ni era login-data ------------------ //
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"a24leoli@student.his.se", "pwd":"Arvid123!"}'); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$response = json_decode($response, true);

$error_no = curl_errno($ch);
$error = curl_error($ch);
curl_close($ch);

if (!empty($error_no)) {
  echo "<div style='background-color:red'>";
  echo '$error_no<br>';
  var_dump($error_no);
  echo "<hr>";
  echo '$error<br>';
  var_dump($error);
  echo "<hr>";
  echo "</div>";
}

$ch = curl_init($baseurl . 'api/resource/G4FornyaRecept?'); 

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


$response = curl_exec($ch);
//echo $response;
$response = json_decode($response, true);

$error_no = curl_errno($ch);
$error = curl_error($ch);
curl_close($ch);

if (!empty($error_no)) {
  echo "<div style='background-color:red'>";
  echo '$error_no<br>';
  var_dump($error_no);
  echo "<hr>";
  echo '$error<br>';
  var_dump($error);
  echo "<hr>";
  echo "</div>";
}


//här väljer jag att loopa över alla poster i [data] och för varje resultat så skriver jag ut name
echo "<strong>LISTA:</strong><br>";
foreach($response['data'] AS $key => $value){
  echo $value["name"]."<br>";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_renewal') {
    $prescription_id = $_POST['prescription_id'];
    
    $stmt = $conn->prepare("UPDATE prescriptions SET renewal_requested = true, renewal_date = CURRENT_TIMESTAMP WHERE id = :id AND patient_id = :patient_id");
    $stmt->bindParam(':id', $prescription_id);
    $stmt->bindParam(':patient_id', $patient_id);
    
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Prescription renewal request submitted successfully!</div>';
    }
}

$stmt = $conn->prepare("
    SELECT p.*, s.first_name, s.last_name 
    FROM prescriptions p
    LEFT JOIN staff s ON p.prescribed_by = s.id
    WHERE p.patient_id = :patient_id
    ORDER BY p.prescribed_date DESC
");

$stmt->bindParam(':patient_id', $patient_id);
$stmt->execute();
$prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <h3>My Prescriptions</h3>
    <?php echo $message; ?>
    
    <?php if (count($prescriptions) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Medication</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Duration</th>
                    <th>Prescribed Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescriptions as $prescription): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($prescription['medication_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($prescription['dosage']); ?></td>
                    <td><?php echo htmlspecialchars($prescription['frequency'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($prescription['duration'] ?? 'N/A'); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($prescription['prescribed_date'])); ?></td>
                    <td>
                        <?php 
                        if ($prescription['renewal_requested']) {
                            echo '<span class="badge badge-warning">Renewal Pending</span>';
                        } else {
                            $status = $prescription['status'];
                            $badge_class = $status === 'active' ? 'badge-success' : 'badge-info';
                            echo '<span class="badge ' . $badge_class . '">' . htmlspecialchars(ucfirst($status)) . '</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($prescription['status'] === 'active' && !$prescription['renewal_requested']): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="request_renewal">
                            <input type="hidden" name="prescription_id" value="<?php echo $prescription['id']; ?>">
                            <button type="submit" class="btn btn-accent" style="padding: 4px 12px; font-size: 0.9em;">Request Renewal</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="background: #F8F9FA; padding: 12px;">
                        <strong>Prescribed by:</strong> <?php echo htmlspecialchars(($prescription['first_name'] ?? '') . ' ' . ($prescription['last_name'] ?? '')); ?><br>
                        <strong>Notes:</strong> <?php echo htmlspecialchars($prescription['notes'] ?? 'N/A'); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #6C757D; margin-top: 16px;">No prescriptions found.</p>
    <?php endif; ?>
</div>
