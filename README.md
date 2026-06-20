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
│ └── database.php # Database configuration
│
├── css/
│ └── style.css # Custom styles
│
├── includes/
│ └── auth.php # Authentication functions
│
├── js/
│ └── script.js # JavaScript functionality
│
├── api.php # API endpoints
├── dashboard.php # Main dashboard page
├── index.php # Landing/redirect page
├── login.php # Login page
├── logout.php # Logout handler
├── register.php # Registration page
├── README.md # Project documentation
└── ecostream.sql # Database schema
