-- HMIS Demo Database
-- Import this file in phpMyAdmin or MySQL command line.
-- Database name: hmis_demo

CREATE DATABASE IF NOT EXISTS hmis_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hmis_demo;

DROP TABLE IF EXISTS bill_items;
DROP TABLE IF EXISTS bills;
DROP TABLE IF EXISTS visits;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_no VARCHAR(30) NOT NULL UNIQUE,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    gender ENUM('Male','Female','Other') NOT NULL,
    date_of_birth DATE NULL,
    phone VARCHAR(40) NULL,
    email VARCHAR(160) NULL,
    address VARCHAR(255) NULL,
    blood_group VARCHAR(10) NULL,
    allergy_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    appointment_date DATETIME NOT NULL,
    department VARCHAR(120) NOT NULL,
    reason VARCHAR(255) NOT NULL,
    status ENUM('Scheduled','Completed','Cancelled') NOT NULL DEFAULT 'Scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_appointments_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    visit_date DATETIME NOT NULL,
    doctor_name VARCHAR(120) NOT NULL,
    diagnosis VARCHAR(255) NOT NULL,
    treatment_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_visits_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(140) NOT NULL,
    cost DECIMAL(10,2) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE bills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    bill_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Unpaid','Paid','Cancelled') NOT NULL DEFAULT 'Unpaid',
    notes VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bills_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE bill_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bill_id INT NOT NULL,
    service_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_bill_items_bill FOREIGN KEY (bill_id) REFERENCES bills(id) ON DELETE CASCADE,
    CONSTRAINT fk_bill_items_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO services (service_name, cost) VALUES
('Consultation', 1000.00),
('Laboratory Test', 1500.00),
('X-Ray', 2500.00),
('Pharmacy / Medication', 800.00),
('Admission Deposit', 5000.00),
('Follow-up Review', 700.00);

INSERT INTO patients (patient_no, first_name, last_name, gender, date_of_birth, phone, email, address, blood_group, allergy_notes) VALUES
('PAT-2026-1001', 'Amina', 'Otieno', 'Female', '1999-04-12', '0712000001', 'amina@example.com', 'Nakuru', 'O+', 'No known allergies'),
('PAT-2026-1002', 'Brian', 'Mwangi', 'Male', '1988-09-20', '0712000002', 'brian@example.com', 'Nairobi', 'A-', 'Penicillin allergy'),
('PAT-2026-1003', 'Grace', 'Wanjiku', 'Female', '2004-01-08', '0712000003', 'grace@example.com', 'Eldoret', 'B+', 'Asthma history');

INSERT INTO appointments (patient_id, appointment_date, department, reason, status) VALUES
(1, DATE_ADD(NOW(), INTERVAL 1 DAY), 'Outpatient', 'General consultation', 'Scheduled'),
(2, DATE_ADD(NOW(), INTERVAL 2 DAY), 'Laboratory', 'Blood test review', 'Scheduled'),
(3, DATE_ADD(NOW(), INTERVAL 3 DAY), 'Pharmacy', 'Medication follow-up', 'Scheduled');

INSERT INTO visits (patient_id, visit_date, doctor_name, diagnosis, treatment_notes) VALUES
(1, NOW(), 'Dr. Demo', 'Upper respiratory infection', 'Rest, fluids and follow-up if symptoms persist.'),
(2, NOW(), 'Dr. Demo', 'Routine review', 'Lab test requested.');
