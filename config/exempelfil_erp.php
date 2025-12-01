<?php
class ERPNextClient {
    private $baseurl = 'http://193.93.250.83:8080/'; 
    // OBS: Ändra denna sökväg om /tmp/ inte har skrivrättigheter!
    private $cookiepath = '/tmp/erpnext_cookies.txt'; 
    private $tmeout = 3600; 

    // ERPNext inloggningsuppgifter för API-användaren
    private $erp_usr = "a24leoli@student.his.se"; 
    private $erp_pwd = "Arvid123!"; 

    private $is_authenticated = false;

    public function __construct() {
        $this->authenticateSession();
    }

    private function authenticateSession() {
        // Deklarera $ch och initiera cURL. Kontrollerar om initieringen lyckas.
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

        $url = $this->baseurl . 'api/resource/Patient?filters=' . $encoded_filters . '&fields=["name","first_name","uid","language"]'; 

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


public function getPrescriptionsForPatient($patient_erp_id) {
        if (!$this->is_authenticated) {
            return [];
        }

        $RESOURCE_NAME = 'G4FornyaRecept'; 
        
        // --- NYTT FILTER: Filtrerar på patientens ERPNext ID (DocName) ---
        $filters = json_encode([
            // Använd fältet 'patient_name' (som länkar till patientens DocType-namn)
            ["patient_name", "=", $patient_erp_id], 
            // Lägger till filter för status Godkänd
            ["data_rsjo", "=", "Godkänd"] 
        ]);
        
        $encoded_filters = urlencode($filters);

        // Vi lägger även till personnummer-fältet i fields så att vi kan visa det:
        $url = $this->baseurl . 'api/resource/' . $RESOURCE_NAME . 
               '?filters=' . $encoded_filters . 
               '&fields=["name","personnummer","medicin","data_rsjo","behandlare"]'; 
               
        $ch = curl_init($url);
        // ... (Resten av cURL-inställningarna och exekveringen) ...

        // ... (Koden för att exekvera cURL och returnera data) ...
        
        // För enkelhets skull, lägger jag in resten av cURL-logiken här:
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
}
?>