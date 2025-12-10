<?php
public function cancelAppointment($appointment_name) {
    if (!$this->is_authenticated) {
        return ['success' => false, 'message' => 'Inte inloggad.'];
    }

    $doctype = 'Patient Appointment';
    $url = $this->baseurl . 'api/resource/' . rawurlencode($doctype) . '/' . rawurlencode($appointment_name);

    
    $data = json_encode(['status' => 'Cancelled']);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiepath);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $this->tmeout);

    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status === 200) {
        return ['success' => true];
    }

    return [
        'success' => false,
        'message' => 'Misslyckades att avboka (HTTP ' . $status . ')'
    ];
}

?>