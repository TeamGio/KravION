<?php
class ERPNextClient {
    private $baseurl = 'http://193.93.250.83:8080/'; 
    private $cookiepath = '/tmp/erpnext_cookies.txt'; 
    private $tmeout = 3600; 

    private $erp_usr = "a24leoli@student.his.se"; 
    private $erp_pwd = "Arvid123!"; 

    private $is_authenticated = false;

    public function __construct() {
        $this->authenticateSession();
    }

    private function authenticateSession() {
        $ch = curl_init($this->baseurl . 'api/method/login');
        
        if ($ch === false) {
            $this->is_authenticated = false;
            return;
        }
        
        curl_setopt($ch, CURLOPT_POST, true);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'usr' => $this->erp_usr,
            'pwd' => $this->erp_pwd
        ])); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        
        // Spara och skicka cookien
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiepath);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiepath);
        
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->tmeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200) {
            $this->is_authenticated = true;
        } else {
            $this->is_authenticated = false;
        }
    }

    public function findPatientByPNR($personal_number) {
        if (!$this->is_authenticated) {
            return null;
        }
        $filters = json_encode([
            ["uid", "=", $personal_number],
            ["name", "LIKE", "G4%"] 
        ]);
        $encoded_filters = urlencode($filters);

        $url = $this->baseurl . 'api/resource/Patient?filters=' . $encoded_filters . '&fields=["name","uid","language"]'; 

        $ch = curl_init($url);
        if ($ch === false) { return null; }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiepath); 
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->tmeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $data = json_decode($response, true);
        curl_close($ch);

        if ($http_code === 200 && !empty($data['data'])) {
            return $data['data'][0];
        }
        return null;
    }


public function getPrescriptionsForPatient($patient_erp_id, $statuses=["Godkänd","Ej godkänd","Behandlas"]) {
        if (!$this->is_authenticated) {
            return [];
        }

        $RESOURCE_NAME = 'G4FornyaRecept'; 
        
        $filters = json_encode([
            ["patient_name", "=", $patient_erp_id], 
            ["data_rsjo", "in", $statuses]
        ]);
        
        $encoded_filters = urlencode($filters);

        $url = $this->baseurl . 'api/resource/' . $RESOURCE_NAME . 
               '?filters=' . $encoded_filters . 
               '&fields=["*"]';

        $ch = curl_init($url);

        
        if ($ch === false) { return []; }
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiepath); 
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->tmeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $data = json_decode($response, true);
        curl_close($ch);

        if ($http_code === 200 && isset($data['data'])) {
            return $data['data'];
        }
        return [];
    }

public function getAppointmentsForPatient($patient_erp_id) {
    if (!$this->is_authenticated) {
        return [];
    }

      $RESOURCE_NAME = 'Patient Appointment';

    $filters = json_encode([
        ["patient", "=", $patient_erp_id],
    ]);

    $encoded_filters = urlencode($filters);


$url = $this->baseurl . 'api/resource/' . rawurlencode($RESOURCE_NAME) .
       '?filters=' . urlencode($filters) .
       '&fields=["*"]';

    $ch = curl_init($url);

    if ($ch === false) {
        return [];
    }

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiepath);
    curl_setopt($ch, CURLOPT_TIMEOUT, $this->tmeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $data = json_decode($response, true);
    curl_close($ch);


    if ($http_code === 200 && isset($data['data'])) {
        return $data['data'];
    }
    return [];
}



// --- DENNA SAKNADES ---
    public function getMedicalrecords($patient_erp_id) {
        if (!$this->is_authenticated) {
            return [];
        }

        // Namnet på tabellen i ERPNext (Dubbelkolla detta mot din URL om det är osäkert)
        $RESOURCE_NAME = 'Patient%20Medical%20Record'; 

        // Filter: Hämta journaler för rätt patient
        $filters = json_encode([
            ["patient", "=", $patient_erp_id]
        ]);

        $encoded_filters = urlencode($filters);

        // Fält vi vill ha
        $url = $this->baseurl . 'api/resource/' . $RESOURCE_NAME . 
               '?filters=' . $encoded_filters . 
               '&fields=["name","status"]'; 

        $ch = curl_init($url);
        if ($ch === false) { return []; }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->tmeout);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiepath); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $data = json_decode($response, true);
        curl_close($ch);

        if ($http_code === 200 && isset($data['data'])) {
            return $data['data'];
        }
        return [];
    }










public function getMessagesForPatient($patient_erp_id) {
        if (!$this->is_authenticated) {
            return [];
        }

        $RESOURCE_NAME = 'Communication';

 
        $filters = json_encode([
            ["subject", "like", "%" . $patient_erp_id . "%"]
        ]);

        // 2. Skapa fält-listan snyggt (Det formella sättet)
        $fields = json_encode([
            "name",
            "subject",
            "content",
            "sender",
            "creation",
            "sent_or_received"
        ]);

        // 3. Bygg URL:en med urlencode
        $url = $this->baseurl . 'api/resource/' . $RESOURCE_NAME . 
               '?filters=' . urlencode($filters) . 
               '&fields=' . urlencode($fields) . 
               '&order_by=creation desc';

        $ch = curl_init($url);
        if ($ch === false) { return []; }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->tmeout);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiepath); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $data = json_decode($response, true);
        curl_close($ch);

        if ($http_code === 200 && isset($data['data'])) {
            return $data['data'];
        }
        return [];
    }
}
?>