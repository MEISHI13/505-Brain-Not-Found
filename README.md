# 🌊 EcoStream - Smart Energy Management System

[![PHP Version](https://img.shields.io/badge/PHP-7.4+-purple)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-blue)](https://mysql.com)
[![Status](https://img.shields.io/badge/status-Production-green)]()
[![License](https://img.shields.io/badge/license-Proprietary-red)](LICENSE)

<p align="center">
  <a href="#"><b>Website</b></a> •
  <a href="#"><b>Documentation</b></a> •
  <a href="#"><b>Demo</b></a>
</p>

> A comprehensive energy monitoring system for tracking consumption, detecting anomalies, and managing alerts in real-time.

---

## 📋 Overview

EcoStream is a comprehensive energy management and monitoring system designed to help organizations track energy consumption, detect anomalies, and manage alerts in real-time. The system features a modern, responsive dashboard with light/dark mode support and persistent alert management.

**Demo Credentials:**
- Username: `admin`
- Password: `password`

---

## ✨ Features

| Feature | Description |
|---------|-------------|
| 📊 Dashboard | Real-time monitoring with summary cards and charts |
| 🔔 Alerts | Automatic alerts when consumption > 100 kWh |
| 📈 Analytics | Peak hours, most active building, anomaly detection |
| 📁 Data Logs | Searchable table with export to CSV |
| 🌓 Theme | Light/Dark mode toggle (Ctrl+T) |
| 🔐 Auth | Secure login with password hashing |

---

## 📁 Project Structure
ecostream/
│
├── config/
│   └── database.php          # Database configuration
│
├── css/
│   └── style.css             # Custom styles
│
├── includes/
│   └── auth.php              # Authentication functions
│
├── js/
│   └── script.js             # JavaScript functionality
│
├── api.php                   # API endpoints
├── dashboard.php             # Main dashboard page
├── index.php                 # Landing/redirect page
├── login.php                 # Login page
├── logout.php                # Logout handler
├── register.php              # Registration page
├── README.md                 # Project documentation
└── ecostream.sql             # Database schema


---

## 🗄️ Database Setup

### 1. Create Database

```sql
CREATE DATABASE ecostream;
USE ecostream;
```

### 2. Create Tables
```sql
-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'user',
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Energy data table
CREATE TABLE energy_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    building_name VARCHAR(50) NOT NULL,
    floor VARCHAR(50),
    department VARCHAR(50),
    consumption_kwh DECIMAL(10,2) NOT NULL,
    voltage DECIMAL(10,2),
    current_amps DECIMAL(10,2),
    power_factor DECIMAL(10,2),
    temperature DECIMAL(10,2),
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);
```
### 3. Insert Admin User
```sql
-- Password: password
INSERT INTO users (username, password, full_name, email, role) 
VALUES ('admin', '$2y$10$e5L5z8FQyIksPL2B9cHZ7uFnMQtLdXcVYjB8K5Cx.qzq9Qz6.yy5G', 'Administrator', 'admin@ecostream.com', 'admin');
```
### 4. Insert Sample Data
```sql
INSERT INTO energy_data (building_name, floor, department, consumption_kwh, voltage, current_amps, power_factor, temperature) VALUES
('Tower A', 'Floor 1', 'IT', 45.50, 220.50, 12.30, 0.92, 24.50),
('Tower A', 'Floor 2', 'Finance', 78.90, 221.00, 15.70, 0.88, 23.80),
('Tower A', 'Floor 3', 'Marketing', 67.80, 221.20, 14.50, 0.89, 23.50),
('Tower A', 'Floor 4', 'R&D', 93.70, 220.70, 19.30, 0.93, 23.90),
('Tower B', 'Floor 1', 'HR', 32.40, 219.80, 8.90, 0.85, 25.10),
('Tower B', 'Floor 2', 'Admin', 56.70, 220.20, 11.20, 0.90, 24.20),
('Tower B', 'Floor 3', 'Operations', 88.20, 220.00, 16.80, 0.91, 24.10),
('Tower B', 'Floor 4', 'Customer Service', 41.30, 221.50, 8.70, 0.84, 24.70),
('Tower C', 'Floor 1', 'Retail', 120.50, 219.50, 22.10, 0.82, 26.50),
('Tower C', 'Floor 2', 'Engineering', 95.30, 220.80, 18.40, 0.87, 24.90),
('Tower C', 'Floor 3', 'Sales', 52.10, 219.30, 10.20, 0.86, 25.30),
('Tower C', 'Floor 4', 'Logistics', 76.40, 219.90, 14.90, 0.88, 25.50);
```
###  Configuration
Database (config/database.php)
```sql
<?php
$host = 'localhost';
$dbname = 'ecostream';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```
### Alert Threshold
Default threshold is 100 kWh. To change:
```sql
// In dashboard.php (JavaScript section)
const ALERT_THRESHOLD = 100; // Change this value
```


**|API |Endpoints|**
|Method	|Endpoint|	Description|
|GET|	/api.php?action=all	|Fetch all records|
|POST|	/api.php	|Add new record|
|GET|	/api.php?action=export	|Export CSV|

**Troubleshooting**
|Issue|	Solution|
|Database connection error|	Check MySQL is running, verify credentials in config/database.php|
|Login fails|	Ensure admin user exists with password 'password'|
|No data shown|	Check energy_data table has data|
|Theme not saving|	Enable localStorage in browser|
