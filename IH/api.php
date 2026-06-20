<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    require_once 'config/database.php';
} catch (Exception $e) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    // Get all data from energy_data
    $stmt = $pdo->query("SELECT * FROM energy_data ORDER BY id DESC LIMIT 100");
    $data = $stmt->fetchAll();
    
    // If no data, return sample
    if (empty($data)) {
        $data = getSampleData();
    }
    
    echo json_encode($data);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

function getSampleData() {
    $data = [];
    $buildings = ['Tower A', 'Tower B', 'Tower C'];
    $departments = ['IT', 'Finance', 'HR', 'Retail', 'Admin'];
    $floors = ['Floor 1', 'Floor 2', 'Floor 3', 'Floor 4'];
    
    for ($i = 1; $i <= 10; $i++) {
        $data[] = [
            'id' => $i,
            'building_name' => $buildings[array_rand($buildings)],
            'floor' => $floors[array_rand($floors)],
            'department' => $departments[array_rand($departments)],
            'consumption_kwh' => rand(20, 150) + rand(0, 99) / 100,
            'voltage' => rand(215, 225) + rand(0, 9) / 10,
            'current_amps' => rand(5, 25) + rand(0, 9) / 10,
            'power_factor' => rand(80, 95) / 100,
            'temperature' => rand(20, 28),
            'timestamp' => date('Y-m-d H:i:s', strtotime("-$i hours"))
        ];
    }
    return $data;
}
?>