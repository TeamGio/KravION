<?php
session_start();

header('Content-Type: application/json');

$env_origins = getenv('ALLOWED_ORIGINS');
$allowed_origins = $env_origins ? array_map('trim', explode(',', $env_origins)) : [];
$request_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

if ($request_origin && !empty($allowed_origins) && in_array($request_origin, $allowed_origins, true)) {
    header('Access-Control-Allow-Origin: ' . $request_origin);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY');
    header('Vary: Origin');
}

require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = isset($_SERVER['PATH_INFO']) ? explode('/', trim($_SERVER['PATH_INFO'], '/')) : [];

$database = new Database();
$conn = $database->getConnection();

$response = ['success' => false, 'message' => 'Invalid request'];

function requireAuthentication() {
    global $response;
    
    $api_key = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : null;
    
    if ($api_key && $api_key === getenv('API_KEY')) {
        return true;
    }
    
    if (!isset($_SESSION['staff_id'])) {
        $response = ['success' => false, 'message' => 'Authentication required. Please provide a valid API key or staff session.'];
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit();
    }
    
    return true;
}

if ($method === 'OPTIONS') {
    http_response_code(204);
    exit();
}

try {
    if (empty($request)) {
        $response = ['success' => true, 'message' => 'Healthcare Management API', 'version' => '1.0', 'status' => 'Authentication required for protected endpoints'];
    } elseif ($request[0] === 'patients' && $method === 'GET') {
        requireAuthentication();
        $stmt = $conn->query("SELECT id, personal_number, first_name, last_name, email, phone, date_of_birth FROM patients");
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response = ['success' => true, 'data' => $patients];
        
        
    } elseif ($request[0] === 'appointments' && $method === 'GET') {
        requireAuthentication();
        $stmt = $conn->query("
            SELECT a.*, p.first_name as patient_first_name, p.last_name as patient_last_name
            FROM appointments a
            JOIN patients p ON a.patient_id = p.id
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
            LIMIT 100
        ");
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response = ['success' => true, 'data' => $appointments];
        
        
    } elseif ($request[0] === 'prescriptions' && $method === 'GET') {
        requireAuthentication();
        $stmt = $conn->query("
            SELECT pr.*, p.first_name as patient_first_name, p.last_name as patient_last_name
            FROM prescriptions pr
            JOIN patients p ON pr.patient_id = p.id
            ORDER BY pr.prescribed_date DESC
            LIMIT 100
        ");
        $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response = ['success' => true, 'data' => $prescriptions];
        
        
    } elseif ($request[0] === 'inventory' && $method === 'GET') {
        requireAuthentication();
        $stmt = $conn->query("SELECT * FROM medicine_inventory ORDER BY medicine_name");
        $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response = ['success' => true, 'data' => $inventory];
        
        
    } elseif ($request[0] === 'stats' && $method === 'GET') {
        requireAuthentication();
        $stats = [];
        
        $stmt = $conn->query("SELECT COUNT(*) as count FROM patients");
        $stats['total_patients'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $stmt = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE appointment_date >= CURRENT_DATE");
        $stats['upcoming_appointments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $stmt = $conn->query("SELECT COUNT(*) as count FROM prescriptions WHERE status = 'active'");
        $stats['active_prescriptions'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $stmt = $conn->query("SELECT COUNT(*) as count FROM medicine_inventory WHERE quantity <= reorder_level");
        $stats['low_stock_medicines'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $response = ['success' => true, 'data' => $stats];
        
    } else {
        $response = ['success' => false, 'message' => 'Endpoint not found'];
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response, JSON_PRETTY_PRINT);
