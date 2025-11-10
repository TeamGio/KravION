<?php
require_once 'database.php';

$database = new Database();
$conn = $database->getConnection();

$sql = "
-- Patients table
CREATE TABLE IF NOT EXISTS patients (
    id SERIAL PRIMARY KEY,
    personal_number VARCHAR(12) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE NOT NULL,
    address TEXT,
    password_hash VARCHAR(255) NOT NULL,
    language VARCHAR(2) DEFAULT 'en',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Healthcare staff table
CREATE TABLE IF NOT EXISTS staff (
    id SERIAL PRIMARY KEY,
    staff_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(20),
    role VARCHAR(50) NOT NULL,
    specialization VARCHAR(100),
    password_hash VARCHAR(255) NOT NULL,
    language VARCHAR(2) DEFAULT 'en',
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medical records table
CREATE TABLE IF NOT EXISTS medical_records (
    id SERIAL PRIMARY KEY,
    patient_id INTEGER REFERENCES patients(id) ON DELETE CASCADE,
    staff_id INTEGER REFERENCES staff(id),
    record_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diagnosis TEXT,
    symptoms TEXT,
    treatment TEXT,
    notes TEXT,
    record_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Lab results table
CREATE TABLE IF NOT EXISTS lab_results (
    id SERIAL PRIMARY KEY,
    patient_id INTEGER REFERENCES patients(id) ON DELETE CASCADE,
    test_name VARCHAR(200) NOT NULL,
    test_date DATE NOT NULL,
    result TEXT,
    normal_range VARCHAR(100),
    status VARCHAR(50),
    ordered_by INTEGER REFERENCES staff(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id SERIAL PRIMARY KEY,
    patient_id INTEGER REFERENCES patients(id) ON DELETE CASCADE,
    staff_id INTEGER REFERENCES staff(id),
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    duration INTEGER DEFAULT 30,
    appointment_type VARCHAR(50) NOT NULL,
    category VARCHAR(20) NOT NULL,
    status VARCHAR(20) DEFAULT 'scheduled',
    reason TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Prescriptions table
CREATE TABLE IF NOT EXISTS prescriptions (
    id SERIAL PRIMARY KEY,
    patient_id INTEGER REFERENCES patients(id) ON DELETE CASCADE,
    prescribed_by INTEGER REFERENCES staff(id),
    medication_name VARCHAR(200) NOT NULL,
    dosage VARCHAR(100) NOT NULL,
    frequency VARCHAR(100),
    duration VARCHAR(100),
    quantity INTEGER,
    status VARCHAR(20) DEFAULT 'active',
    prescribed_date DATE NOT NULL,
    expiry_date DATE,
    renewal_requested BOOLEAN DEFAULT false,
    renewal_date TIMESTAMP,
    is_antibiotic BOOLEAN DEFAULT false,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medicine inventory table
CREATE TABLE IF NOT EXISTS medicine_inventory (
    id SERIAL PRIMARY KEY,
    medicine_name VARCHAR(200) NOT NULL,
    medicine_type VARCHAR(100),
    quantity INTEGER NOT NULL DEFAULT 0,
    unit VARCHAR(50),
    reorder_level INTEGER DEFAULT 10,
    expiry_date DATE,
    batch_number VARCHAR(100),
    supplier VARCHAR(200),
    location VARCHAR(100),
    is_antibiotic BOOLEAN DEFAULT false,
    age_restricted BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Prescription tracking (for quality monitoring)
CREATE TABLE IF NOT EXISTS prescription_tracking (
    id SERIAL PRIMARY KEY,
    prescription_id INTEGER REFERENCES prescriptions(id) ON DELETE CASCADE,
    staff_id INTEGER REFERENCES staff(id),
    patient_age INTEGER,
    is_antibiotic BOOLEAN DEFAULT false,
    age_appropriate BOOLEAN DEFAULT true,
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert demo data
INSERT INTO patients (personal_number, first_name, last_name, email, phone, date_of_birth, address, password_hash, language) 
VALUES 
    ('199001011234', 'Anna', 'Andersson', 'anna.andersson@email.com', '+46701234567', '1990-01-01', 'Storgatan 1, Stockholm', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sv'),
    ('198505155678', 'Erik', 'Eriksson', 'erik.eriksson@email.com', '+46709876543', '1985-05-15', 'Vasagatan 2, Stockholm', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sv')
ON CONFLICT (personal_number) DO NOTHING;

INSERT INTO staff (staff_id, first_name, last_name, email, phone, role, specialization, password_hash, language) 
VALUES 
    ('DOC001', 'Dr. Maria', 'Svensson', 'maria.svensson@healthcare.se', '+46708888888', 'doctor', 'General Practice', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sv'),
    ('NUR001', 'Sofia', 'Karlsson', 'sofia.karlsson@healthcare.se', '+46707777777', 'nurse', 'Primary Care', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sv')
ON CONFLICT (staff_id) DO NOTHING;

INSERT INTO medicine_inventory (medicine_name, medicine_type, quantity, unit, reorder_level, expiry_date, batch_number, is_antibiotic) 
VALUES 
    ('Amoxicillin 500mg', 'Antibiotic', 150, 'tablets', 50, '2026-12-31', 'AMX2024001', true),
    ('Paracetamol 500mg', 'Pain Relief', 500, 'tablets', 100, '2027-06-30', 'PAR2024001', false),
    ('Ibuprofen 400mg', 'Anti-inflammatory', 300, 'tablets', 80, '2026-09-30', 'IBU2024001', false)
ON CONFLICT DO NOTHING;
";

try {
    $conn->exec($sql);
    echo "Database initialized successfully!\n";
} catch(PDOException $e) {
    echo "Database initialization error: " . $e->getMessage() . "\n";
}
