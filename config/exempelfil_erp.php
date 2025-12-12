<?php
class ERPNextClient {
    private $baseurl = 'http://193.93.250.83:8080/'; 
    private $cookiepath = '/tmp/erpnext_cookies.txt'; // Kontrollera skrivrättigheter till denna fil!
    private $tmeout = 3600; 

    private $erp_usr = "a24leoli@student.his.se"; 
    private $erp_pwd = "Arvid123!"; 

    private $is_authenticated = false;

    public function __construct() {
        $this->authenticateSession();
    }


    // Kontroll av session och inloggning
    private function authenticateSession() {
        $ch = curl_init($this->baseurl . 'api/method/login');
        
        if ($ch === false) {
            $this->is_authenticated = false;
            return;
        }
        
        curl_setopt($ch, CURLOPT_POST, true);
        // Använder http_build_query för att skicka form-data, vilket ERPNext förväntar sig för login.
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([ 
            'usr' => $this->erp_usr,
            'pwd' => $this->erp_pwd
        ])); 
    
        // Vi behöver inte Content-Type för form-data, men Accept är bra.
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json')); 
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        // VIKTIGT: Sätt cookie-burk och ladda cookie-fil
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiepath);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiepath);
        
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->tmeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200) {
            $this->is_authenticated = true;
            error_log("ERPNext: Inloggning lyckades! HTTP " . $http_code);
        } else {
            $this->is_authenticated = false;
            // Fånga felmeddelande om inloggning misslyckas.
            error_log("ERPNext: Inloggning misslyckades! HTTP " . $http_code . " Svar: " . $response); 
        }
    }
    

    // Hämta patientinformation baserat på personnummer
    public function findPatientByPNR($personal_number) {
        if (!$this->is_authenticated) {
            return null;
        }

        $filters = json_encode([
            ["uid", "=", $personal_number],
            ["name", "LIKE", "G4%"]
        ]);

        $url = $this->baseurl . 'api/resource/Patient?filters=' . urlencode($filters) . 
               '&fields=["name","first_name","uid","language"]'; 

        $ch = curl_init($url);
        if ($ch === false) { return null; }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json'));
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

// Hämta förnyelsebara recept för en patient
    public function getPrescriptionsForPatient($patient_erp_id, $statuses=["Godkänd","Ej godkänd","Behandlas"]) {
        if (!$this->is_authenticated) {
            return [];
        }

        $RESOURCE_NAME = 'G4FornyaRecept';
        
        $filters = json_encode([
            ["patient_name", "=", $patient_erp_id],
            ["data_rsjo", "in", $statuses]
        ]);

        $url = $this->baseurl . 'api/resource/' . $RESOURCE_NAME . 
               '?filters=' . urlencode($filters) . 
               '&fields=["*"]';

        $ch = curl_init($url);
        if ($ch === false) { return []; }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));
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

    // Förnya recept för en patient (PUT-anrop)
    public function renewPrescription($prescription_id, $patient_erp_id) {
        if (!$this->is_authenticated) {
            return [
                'success' => false,
                'message' => 'Inte inloggad i ERP-systemet.'
            ];
        }

        $RESOURCE_NAME = 'G4FornyaRecept'; 
        
        // 1. Skapa den fullständiga URL:en med ID (name) på receptet.
        $url = $this->baseurl . 'api/resource/' . $RESOURCE_NAME . '/' . urlencode($prescription_id);

        // 2. Skapa den data (payload) du vill uppdatera
        $update_data = [
            'name' => $prescription_id, 
            'last_renewal_request' => date('Y-m-d H:i:s'), 
            
            // Sätter den tillåtna statusen som indikerar att en begäran är gjord
            'data_rsjo' => 'Behandlas', 
            // Sätter det önskade uttagssaldot
            'uttag' => 4        
        ];
        $json_payload = json_encode($update_data);


        $ch = curl_init($url);
        if ($ch === false) {
            return [
                'success' => false,
                'message' => 'Kunde inte initiera curl.'
            ];
        }

        // 3. Använd PUT för uppdatering
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); 
        
        // 4. Lägg till JSON-payloaden i body
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload); 
        
        // Sätt headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json', 
            'Accept: application/json',
            'Content-Length: ' . strlen($json_payload)
        ));
        
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiepath);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->tmeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $data = json_decode($response, true);
        curl_close($ch);

        // 5. Förbättrad felsökning vid icke-200-svar (inkl. 500)
        $error_message = 'Okänt fel i ERPNext.';
        if (isset($data['exc'])) {
            $error_message = strip_tags($data['exc']); 
        } elseif (isset($data['message'])) {
            $error_message = $data['message'];
        }

        // Kontrollerar om svaret var 200 (OK)
        if ($http_code === 200 && isset($data['data'])) {
            return [
                'success' => true,
                'message' => 'Receptet är nu satt till "Behandlas" med 4 uttag. Det kan ta en stund innan det godkänns.', // <-- Detta meddelande skickas till prescriptions.php
                'data' => $data['data']
            ];
        }
        
        return [
            'success' => false,
            // Returnera det detaljerade felmeddelandet
            'message' => 'Misslyckades med att förnyas receptet. HTTP-kod: ' . $http_code . '. Meddelande: ' . $error_message
        ];
    }


// Hämta kommande bokade tider för en patient
    public function getAppointmentsForPatient($patient_erp_id) {
        if (!$this->is_authenticated) {
            return [];
        }

   
        $RESOURCE_NAME = 'Patient Appointment';

        $filters = json_encode([
            ["patient", "=", $patient_erp_id],
            ["appointment_date", ">=", date('Y-m-d')],
            ["status", "!=", "Cancelled"] // Visa inte avbokade tider
        ]);

        $url = $this->baseurl . 'api/resource/' . rawurlencode($RESOURCE_NAME) .
               '?filters=' . urlencode($filters) .
               '&fields=["name","title","practitioner_name","department","appointment_date","appointment_time","patient"]';

        $ch = curl_init($url);
        if ($ch === false) { return []; }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));
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


    /**
     * Hämtar en specifik bokning (Patient Appointment) via ERPNext REST API.
     * Används för kontroll att bokningen finns och ev. tillhör patienten.
     */
    public function getAppointmentById($appointment_id) {
        if (!$this->is_authenticated) {
            return [
                'success' => false,
                'message' => 'Inte inloggad i ERP-systemet.'
            ];
        }

        $RESOURCE_NAME = 'Patient Appointment';
        $url = $this->baseurl . 'api/resource/' . rawurlencode($RESOURCE_NAME) . '/' . urlencode($appointment_id);

        $ch = curl_init($url);
        if ($ch === false) {
            return [
                'success' => false,
                'message' => 'Kunde inte initiera curl.'
            ];
        }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiepath);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->tmeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $data = json_decode($response, true);
        curl_close($ch);

        if ($http_code === 200 && isset($data['data'])) {
            return [
                'success' => true,
                'data' => $data['data']
            ];
        }

        $err = $data['message'] ?? $response ?? 'Okänt fel.';
        return [
            'success' => false,
            'message' => 'Kunde inte hämta bokningen. HTTP: ' . $http_code . ' - ' . $err
        ];
    }

    /**
     * Avbokar en bokning genom att sätta status=Cancelled.
     * Om $patient_erp_id skickas in så validerar vi att tiden tillhör patienten.
     *
     * @param string $appointment_id  ERPNext "name" för Patient Appointment
     * @param string|null $patient_erp_id patientens ERPNext-id (valfritt)
     */
    public function cancelAppointment($appointment_id, $patient_erp_id = null) {
        if (!$this->is_authenticated) {
            return [
                'success' => false,
                'message' => 'Inte inloggad i ERP-systemet.'
            ];
        }

        // 1) Validera att bokningen finns
        $check = $this->getAppointmentById($appointment_id);
        if (empty($check['success'])) {
            return [
                'success' => false,
                'message' => $check['message'] ?? 'Kunde inte verifiera bokningen.'
            ];
        }

        $appointment = $check['data'];

        // 2) Validera ägarskap om patient-id skickas
        if (!empty($patient_erp_id)) {
            $appointment_patient = $appointment['patient'] ?? null;
            if ($appointment_patient !== $patient_erp_id) {
                return [
                    'success' => false,
                    'message' => 'Du kan inte avboka en tid som inte tillhör dig.'
                ];
            }
        }

        // 3) Om redan Cancelled, returnera OK (idempotent)
        $current_status = $appointment['status'] ?? '';
        if ($current_status === 'Cancelled') {
            return [
                'success' => true,
                'message' => 'Tiden är redan avbokad.'
            ];
        }

        // 4) Uppdatera status till Cancelled
        $RESOURCE_NAME = 'Patient Appointment';
        $url = $this->baseurl . 'api/resource/' . rawurlencode($RESOURCE_NAME) . '/' . urlencode($appointment_id);

        // Sätt "status" (det är den du filtrerar på i getAppointmentsForPatient)
        // Lägg även custom_status_copy om du fortfarande vill hålla den synkad
        $update_data = [
            'status' => 'Cancelled',
            'custom_status_copy' => 'Cancelled'
        ];

        $json_payload = json_encode($update_data);

        $ch = curl_init($url);
        if ($ch === false) {
            return [
                'success' => false,
                'message' => 'Kunde inte initiera curl.'
            ];
        }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Content-Length: ' . strlen($json_payload)
        ]);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiepath);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->tmeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $data = json_decode($response, true);
        curl_close($ch);

        if ($http_code === 200 && isset($data['data'])) {
            return [
                'success' => true,
                'message' => 'Tiden är nu avbokad.'
            ];
        }

        $err = $data['message'] ?? $response ?? 'Okänt fel.';
        return [
            'success' => false,
            'message' => 'Misslyckades med att avboka tiden. HTTP: ' . $http_code . ' - ' . $err
        ];
    }




















// Hämta Patient Encounter, journal
    public function getJournalRecordsForPatient($patient_erp_id) {
        if (!$this->is_authenticated) {
            return [];
        }

        $RESOURCE_NAME = 'Patient Encounter';
        
        $fields = ['name', 'patient_name', 'encounter_date', 'encounter_time', 'practitioner_name'];

        $filters = json_encode([
            ["patient_name", "=", $patient_erp_id]
        ]);

        $url = $this->baseurl . 'api/resource/' . rawurlencode($RESOURCE_NAME) .
               '?filters=' . urlencode($filters) .
               '&fields=' . urlencode(json_encode($fields)) .
               '&order_by=encounter_date%20desc,encounter_time%20desc'; // Sortera efter datum o tid

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

// Hämta Vital Signs, journal
    public function getVitalSignsForPatient($patient_erp_id) {
        if (!$this->is_authenticated) {
            return [];
        }

        $RESOURCE_NAME = 'Vital Signs';
        
        $fields = ['name','patient_name', 'height','weight', 'bmi', 'pulse', 'respiratory_rate', 'tongue', 'abdomen', 'reflexes'. 'vital_signs_note'];

        $filters = json_encode([
            ["patient_name", "=", $patient_erp_id]
        ]);

        $url = $this->baseurl . 'api/resource/' . rawurlencode($RESOURCE_NAME) .
               '?filters=' . urlencode($filters) .
               '&fields=' . urlencode(json_encode($fields)) .
               '&order_by=signs_date%20desc,signs_time%20desc'; // Sortera efter datum o tid

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

    public function getMedicalrecords($patient_erp_id) {
        if (!$this->is_authenticated) return [];

        $RESOURCE_NAME = 'Patient Medical Record';

        $url = $this->baseurl . 'api/resource/' . rawurlencode($RESOURCE_NAME) .
               '?filters=' . urlencode(json_encode([["patient", "=", $patient_erp_id]])) . 
               '&fields=' . urlencode(json_encode(["name", "status"]));

        $ch = curl_init($url);
        
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEFILE     => $this->cookiepath,
            CURLOPT_TIMEOUT        => $this->tmeout, // <--- ANVÄNDER DIN STAVNING (utan 'i')
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json']
        ]);

        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $result['data'] ?? [];
    }

    public function getMessagesForPatient($patient_erp_id) {
        if (!$this->is_authenticated) {
            return [];
        }


        $RESOURCE_NAME = 'G4PatientMeddelande';






        $filters = json_encode([
            ["patient_name", "=", $patient_erp_id]
        ]);




        // 2. Skapa fält-listan snyggt (Det formella sättet)
        $fields = json_encode([
            "patient_name",
            "practitioner",
            "subject",
            "message",
            "creation",
        ]);


        // 3. Bygg URL:en med urlencode
        $url = $this->baseurl . 'api/resource/' . $RESOURCE_NAME .
            '?filters=' . urlencode($filters) .
            '&fields=' . urlencode($fields) .
            '&order_by=' . urlencode('creation desc');


        $ch = curl_init($url);
        if ($ch === false) {
            return [];
        }


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
