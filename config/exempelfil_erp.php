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
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Används för HTTP

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200) {
            $this->is_authenticated = true;
        } else {
            $this->is_authenticated = false;
        }
    }

    /**
     * Kontrollerar om en patient finns inom "G4"-avdelningen baserat på deras UID (PNR).
     * @param string $personal_number Användarens inmatade PNR.
     * @return array|null Patientdata (om patient hittades), annars null.
     */
    public function findPatientByPNR($personal_number) {
        if (!$this->is_authenticated) {
            return null;
        }

        // Filters: [["uid", "=", PNR], ["name", "LIKE", "G4%"]]
        $filters = json_encode([
            ["uid", "=", $personal_number],
            ["name", "LIKE", "G4%"] 
        ]);
        
        $encoded_filters = urlencode($filters);

        // Bygg API-frågan för att söka patienter med rätt UID och G4-filter
        $url = $this->baseurl . 'api/resource/Patient?filters=' . $encoded_filters . '&fields=["name","first_name","uid","language"]'; 

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        
        // ANVÄND COOKIE-FILEN för att skicka sessionskakan från inloggningen
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiepath); 
        
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->tmeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $data = json_decode($response, true);
        curl_close($ch);

        if ($http_code === 200 && !empty($data['data'])) {
            // Patienten hittades! Returnera den första matchningen.
            return $data['data'][0];
        }

        return null;
    }

}
?>